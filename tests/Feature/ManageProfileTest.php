<?php

namespace Tests\Feature;

use App\Jobs\CleanupDeletedUserAccount;
use App\Models\Cv;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ManageProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_account_deletion_soft_deletes_user_and_dispatches_job()
    {
        Storage::fake('public');
        Queue::fake();

        // Create a user with a fake avatar
        $avatarPath = UploadedFile::fake()->image('avatar.jpg')->store('avatars', 'public');
        
        $user = User::factory()->create([
            'password' => Hash::make('password123'),
            'avatar_url' => $avatarPath,
            'role' => 'basic',
            'email_verified_at' => now(),
        ]);

        // Create a template first
        $templateId = (string) str()->ulid();
        \App\Models\CvTemplate::insert([
            'id' => $templateId,
            'name' => 'Basic Template',
            'category' => 'professional',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create an associated CV directly to bypass any missing factory logic
        $cvId = (string) str()->ulid();
        Cv::insert([
            'id' => $cvId,
            'user_id' => $user->id,
            'template_id' => $templateId,
            'title' => 'My Resume',
            'content' => json_encode([]),
            'is_public' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Authenticate as the user
        $this->actingAs($user);

        // Send a DELETE request with the correct password
        $response = $this->delete(route('profile.destroy'), [
            'password' => 'password123',
        ]);

        // Assert successful redirect
        $response->assertRedirect('/');
        
        // Assert the user is softly deleted
        $this->assertSoftDeleted('users', ['id' => $user->id]);

        // Assert that the job was dispatched
        Queue::assertPushed(CleanupDeletedUserAccount::class, function ($job) use ($user, $avatarPath) {
            return $job->userId === $user->id && $job->rawAvatar === $avatarPath;
        });

        // Now, manually run the job to verify its internal logic
        $job = new CleanupDeletedUserAccount($user->id, $avatarPath);
        $job->handle();

        // Assert the avatar file is no longer on the disk
        Storage::disk('public')->assertMissing($avatarPath);

        // Assert the associated CV is softly deleted
        $this->assertSoftDeleted('cvs', ['id' => $cvId]);
    }
}
