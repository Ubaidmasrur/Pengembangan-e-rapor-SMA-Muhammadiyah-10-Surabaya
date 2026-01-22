<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Database\Seeders\Concerns\TruncatesTables;

class UserSeeder extends Seeder
{
    // use TruncatesTables;
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
    // Make seeding idempotent
    $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                array_merge($user, [
                    'updated_at' => now(),
                ])
            );
        }
    }
}
