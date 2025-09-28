<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PassportClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if the client already exists
        $clientExists = DB::table('oauth_clients')
            ->where('id', '01998b2f-e7e0-7158-b6c6-8ffb6d8747fc')
            ->exists();

        if (!$clientExists) {
            DB::table('oauth_clients')->insert([
                'id' => '01998b2f-e7e0-7158-b6c6-8ffb6d8747fc',
                'owner_type' => null,
                'owner_id' => null,
                'name' => 'spa client',
                'secret' => null, // Personal access clients don't need secrets
                'provider' => 'users',
                'redirect_uris' => json_encode([]),
                'grant_types' => json_encode(['personal_access']),
                'revoked' => false,
                'created_at' => Carbon::parse('2025-09-27T12:39:56.000000Z'),
                'updated_at' => Carbon::parse('2025-09-27T12:39:56.000000Z'),
            ]);

            $this->command->info('Passport personal access client created successfully.');
        } else {
            $this->command->info('Passport personal access client already exists.');
        }
    }
}
