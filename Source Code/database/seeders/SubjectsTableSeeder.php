<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Database\Seeders\Concerns\TruncatesTables;

class SubjectsTableSeeder extends Seeder
{
    use TruncatesTables;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->truncate(['subjects']);
        $subjects = [
            ['name' => 'Matematika', 'type' => 'umum'],
            ['name' => 'Bahasa Indonesia', 'type' => 'umum'],
            ['name' => 'Bahasa Inggris', 'type' => 'umum'],
            ['name' => 'Ilmu Pengetahuan Alam', 'type' => 'umum'],
            ['name' => 'Ilmu Pengetahuan Sosial', 'type' => 'umum'],
            ['name' => 'Pendidikan Pancasila dan Kewarganegaraan', 'type' => 'umum'],
            ['name' => 'Seni Budaya', 'type' => 'umum'],
            ['name' => 'Pendidikan Jasmani', 'type' => 'umum'],
            ['name' => 'Teknologi Informasi dan Komunikasi', 'type' => 'umum'],
            ['name' => 'Matematika Terapan', 'type' => 'khusus'],
            ['name' => 'Fisika', 'type' => 'umum'],
            ['name' => 'Kimia', 'type' => 'umum'],
            ['name' => 'Biologi', 'type' => 'umum'],
            ['name' => 'Ekonomi', 'type' => 'umum'],
            ['name' => 'Geografi', 'type' => 'umum'],
            ['name' => 'Sejarah', 'type' => 'umum'],
            ['name' => 'Seni Musik', 'type' => 'umum'],
            ['name' => 'Seni Rupa', 'type' => 'umum'],
            ['name' => 'Pendidikan Agama', 'type' => 'umum'],
            ['name' => 'Keterampilan', 'type' => 'khusus'],
            ['name' => 'Prakarya', 'type' => 'khusus'],
        ];

        foreach ($subjects as $subject) {
            DB::table('subjects')->insert([
                'name' => $subject['name'],
                'type' => $subject['type'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}