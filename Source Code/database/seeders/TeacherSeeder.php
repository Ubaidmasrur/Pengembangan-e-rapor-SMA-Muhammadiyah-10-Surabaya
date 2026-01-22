<?php

namespace Database\Seeders;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Database\Seeders\Concerns\TruncatesTables;

class TeacherSeeder extends Seeder
{
    use TruncatesTables;
    public function run(): void
    {
        $this->truncate(['teachers']);
        // delete only teacher users
        \DB::table('users')->where('role', 'guru')->delete();

        $faker = \Faker\Factory::create();

        foreach (range(1, 10) as $i) {
            $user = User::create([
                'name' => $faker->name,
                'email' => "teacher{$i}@example.com",
                'password' => Hash::make('password123'),
                'role' => 'guru',
            ]);

            Teacher::create([
                'user_id' => $user->id,
                'name' => $faker->name(),
                'nip' => $faker->unique()->numerify('198#####'),
                'subject_specialty' => $faker->word,
            ]);
        }
    }
}
