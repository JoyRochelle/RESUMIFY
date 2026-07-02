<?php

namespace Database\Seeders;

use App\Models\SupportTicket;
use App\Models\User;
use App\Notifications\NewSupportTicket;
use App\Notifications\SupportTicketReplied;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseNotificationSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@resumify.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        $user = User::where('role', '!=', 'admin')->first()
            ?? User::firstOrCreate(
                ['email' => 'user1@example.com'],
                [
                    'name' => 'Demo User',
                    'password' => Hash::make('password'),
                    'role' => 'basic',
                    'email_verified_at' => now(),
                ]
            );

        $ticket = SupportTicket::firstOrCreate(
            [
                'user_id' => $user->id,
                'subject' => 'Demo seeded support ticket',
            ],
            [
                'status' => 'open',
                'assigned_to' => $admin->id,
            ]
        );

        $now = now();

        DB::table('notifications')->updateOrInsert(
            ['id' => '11111111-1111-4111-8111-111111111111'],
            [
                'type' => NewSupportTicket::class,
                'notifiable_type' => User::class,
                'notifiable_id' => $admin->id,
                'data' => json_encode([
                    'admin_ticket_id' => $ticket->id,
                    'ticket_id' => $ticket->id,
                    'subject' => $ticket->subject,
                    'user_name' => $user->name,
                    'message' => 'New support ticket from ' . $user->name . ': ' . $ticket->subject,
                ]),
                'read_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        DB::table('notifications')->updateOrInsert(
            ['id' => '22222222-2222-4222-8222-222222222222'],
            [
                'type' => SupportTicketReplied::class,
                'notifiable_type' => User::class,
                'notifiable_id' => $user->id,
                'data' => json_encode([
                    'ticket_id' => $ticket->id,
                    'subject' => $ticket->subject,
                    'message' => 'Your support ticket "' . $ticket->subject . '" has received a reply.',
                ]),
                'read_at' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );
    }
}
