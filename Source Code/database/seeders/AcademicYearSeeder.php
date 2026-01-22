<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Database\Seeders\Concerns\TruncatesTables;

class AcademicYearSeeder extends Seeder
{
    // use TruncatesTables;
    public function run()
    {
    // $this->truncate(['academic_years']);
    \DB::table('academic_years')->delete();
        $data = [
            [
                'year' => '2023/2024',
                'semester' => 'Ganjil',
                'is_active' => true,
            ],
            [
                'year' => '2023/2024',
                'semester' => 'Genap',
                'is_active' => false,
            ],
            [
                'year' => '2022/2023',
                'semester' => 'Ganjil',
                'is_active' => false,
            ],
            [
                'year' => '2022/2023',
                'semester' => 'Genap',
                'is_active' => false,
            ],
        ];

        foreach ($data as $row) {
            $exists = DB::table('academic_years')
                ->where('year', $row['year'])
                ->where('semester', $row['semester'])
                ->exists();

            if (!$exists) {
                DB::table('academic_years')->insert(array_merge($row, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            }
        }
    }
}