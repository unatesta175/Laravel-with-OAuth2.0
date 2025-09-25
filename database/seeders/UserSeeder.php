<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@kapasbeautyspa.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'phone' => '+60123456789',
            'email_verified_at' => now(),
        ]);

        // Create Therapist Users
        $therapists = [
            [
                'name' => 'Alicia Tan',
                'email' => 'alicia@kapasbeautyspa.com',
                'phone' => '+60123456790',
            ],
            [
                'name' => 'Siti Rahman',
                'email' => 'siti@kapasbeautyspa.com',
                'phone' => '+60123456791',
            ],
            [
                'name' => 'Nurul Izzah',
                'email' => 'nurul@kapasbeautyspa.com',
                'phone' => '+60123456792',
            ],
            [
                'name' => 'Maya Lee',
                'email' => 'maya@kapasbeautyspa.com',
                'phone' => '+60123456793',
            ],
            [
                'name' => 'Farah Lim',
                'email' => 'farah@kapasbeautyspa.com',
                'phone' => '+60123456794',
            ],
            [
                'name' => 'Lina Wong',
                'email' => 'lina@kapasbeautyspa.com',
                'phone' => '+60123456795',
            ],
        ];

        foreach ($therapists as $therapist) {
            User::create([
                'name' => $therapist['name'],
                'email' => $therapist['email'],
                'password' => Hash::make('password123'),
                'role' => 'therapist',
                'phone' => $therapist['phone'],
                'email_verified_at' => now(),
            ]);
        }

        // Create Client Users
        $clients = [
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah.johnson@gmail.com',
                'phone' => '+60123456800',
            ],
            [
                'name' => 'Michael Chen',
                'email' => 'michael.chen@gmail.com',
                'phone' => '+60123456801',
            ],
            [
                'name' => 'Emma Wilson',
                'email' => 'emma.wilson@gmail.com',
                'phone' => '+60123456802',
            ],
            [
                'name' => 'David Kumar',
                'email' => 'david.kumar@gmail.com',
                'phone' => '+60123456803',
            ],
            [
                'name' => 'Lisa Zhang',
                'email' => 'lisa.zhang@gmail.com',
                'phone' => '+60123456804',
            ],
        ];

        foreach ($clients as $client) {
            User::create([
                'name' => $client['name'],
                'email' => $client['email'],
                'password' => Hash::make('password123'),
                'role' => 'client',
                'phone' => $client['phone'],
                'email_verified_at' => now(),
            ]);
        }
    }
}




