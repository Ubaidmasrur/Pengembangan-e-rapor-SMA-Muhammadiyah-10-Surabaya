<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\UserSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            SchoolSeeder::class,
            AcademicYearSeeder::class,
            ClassSeeder::class,
            SubjectsTableSeeder::class,
            GradeParameterSeeder::class,
            StudentSeeder::class,
            TeacherSeeder::class,
            ClassStudentAssignmentSeeder::class,
            ClassTeacherAssignmentSeeder::class,
            StudentGradeDetailsSeeder::class,
        ]);
    }
}
