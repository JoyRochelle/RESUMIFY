<?php

namespace Database\Seeders;

use App\Models\Cv;
use App\Models\CvSection;
use App\Models\CvTemplate;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $password = Hash::make('password');

        $this->command->info('Creating 5 Admin users...');
        for ($i = 2; $i <= 6; $i++) {
            User::firstOrCreate(
                ['email' => "admin{$i}@resumify.com"],
                [
                    'name' => "Admin User $i",
                    'password' => $password,
                    'role' => 'admin',
                    'email_verified_at' => now(),
                ]
            );
        }

        $templates = CvTemplate::all();
        if ($templates->isEmpty()) {
            $this->call(CvTemplateSeeder::class);
            $templates = CvTemplate::all();
        }
        $templateIds = $templates->pluck('id')->toArray();

        $this->command->info('Creating 95 Users and their CVs...');
        for ($i = 1; $i <= 95; $i++) {
            $user = User::firstOrCreate(
                ['email' => "user{$i}@example.com"],
                [
                    'name' => $faker->name(),
                    'password' => $password,
                    'role' => $faker->boolean(20) ? 'premium' : 'basic',
                    'email_verified_at' => now(),
                ]
            );

            $numCvs = rand(1, 3);
            for ($c = 0; $c < $numCvs; $c++) {
                $cv = Cv::create([
                    'user_id' => $user->id,
                    'template_id' => $faker->randomElement($templateIds),
                    'title' => $faker->jobTitle() . ' Resume',
                    'is_public' => $faker->boolean(30),
                ]);

                // Personal Info
                CvSection::create([
                    'cv_id' => $cv->id,
                    'type' => 'personal_info',
                    'title' => 'Personal Information',
                    'order' => 1,
                    'content' => [
                        'name' => $user->name,
                        'email' => $user->email,
                        'phone' => $faker->phoneNumber(),
                        'location' => $faker->city() . ', ' . $faker->country(),
                        'summary' => $faker->paragraph(2),
                    ]
                ]);

                // Work Experience
                CvSection::create([
                    'cv_id' => $cv->id,
                    'type' => 'work_experience',
                    'title' => 'Experience',
                    'order' => 2,
                    'content' => [
                        [
                            'title' => $faker->jobTitle(),
                            'company' => $faker->company(),
                            'start_date' => $faker->year(),
                            'end_date' => 'Present',
                            'description' => $faker->paragraph(),
                        ],
                        [
                            'title' => $faker->jobTitle(),
                            'company' => $faker->company(),
                            'start_date' => (string)($faker->year() - 3),
                            'end_date' => $faker->year(),
                            'description' => $faker->paragraph(),
                        ]
                    ]
                ]);

                // Education
                CvSection::create([
                    'cv_id' => $cv->id,
                    'type' => 'education',
                    'title' => 'Education',
                    'order' => 3,
                    'content' => [
                        [
                            'degree' => 'Bachelor of ' . $faker->word(),
                            'school' => $faker->company() . ' University',
                            'start_date' => (string)($faker->year() - 7),
                            'end_date' => (string)($faker->year() - 3),
                            'description' => 'Graduated with honors.',
                        ]
                    ]
                ]);

                // Skills
                CvSection::create([
                    'cv_id' => $cv->id,
                    'type' => 'skills',
                    'title' => 'Skills',
                    'order' => 4,
                    'content' => [
                        ['name' => 'Communication', 'level' => 'Advanced'],
                        ['name' => 'Leadership', 'level' => 'Intermediate'],
                        ['name' => 'Programming', 'level' => 'Expert'],
                        ['name' => 'Project Management', 'level' => 'Advanced'],
                    ]
                ]);
            }
        }
        $this->command->info('Dummy data seeded successfully!');
    }
}
