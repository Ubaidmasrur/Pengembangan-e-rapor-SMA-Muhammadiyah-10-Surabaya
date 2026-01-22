<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Database\Seeders\Concerns\TruncatesTables;

class StudentSeeder extends Seeder
{
    // use TruncatesTables;
    public function run(): void
    {
    // Clear students for idempotent seed
    // $this->truncate(['students', 'users']);
    \App\Models\Student::query()->delete();
        $faker = \Faker\Factory::create();

        foreach (range(1, 20) as $i) {
            $user = User::create([
                'name' => $faker->name,
                'email' => "student{$i}@example.com",
                'password' => Hash::make('password123'),
                'role' => 'siswa',
            ]);

            Student::create([
                'user_id' => $user->id,
                'nisn' => $faker->unique()->numerify('200#####'),
                'name' => $user->name,
                'birth_date' => $faker->date(),
                'gender' => $faker->randomElement(['L', 'P']),
                'disability_type' => $faker->optional()->word,
            ]);
        }
    }
}
