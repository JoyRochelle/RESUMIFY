<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class NotificationReadAllTest extends TestCase
{
    use RefreshDatabase;

    public function test_read_all_marks_notifications_with_one_bulk_update_query(): void
    {
        $user = User::factory()->create([
            'role' => 'basic',
            'email_verified_at' => now(),
        ]);

        $now = now();
        $rows = [];

        for ($i = 0; $i < 50; $i++) {
            $rows[] = [
                'id' => (string) Str::uuid(),
                'type' => 'test-notification',
                'notifiable_type' => User::class,
                'notifiable_id' => $user->id,
                'data' => json_encode(['message' => "Notification {$i}"]),
                'read_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('notifications')->insert($rows);

        $notificationUpdates = 0;
        DB::listen(function ($query) use (&$notificationUpdates): void {
            $sql = strtolower($query->sql);

            if (str_starts_with($sql, 'update') && str_contains($sql, 'notifications')) {
                $notificationUpdates++;
            }
        });

        $this->actingAs($user)
            ->from('/dashboard')
            ->post(route('notifications.readAll'))
            ->assertRedirect('/dashboard');

        $this->assertSame(1, $notificationUpdates, 'readAll should issue one bulk notification update.');
        $this->assertSame(0, $user->unreadNotifications()->count());
    }
}
