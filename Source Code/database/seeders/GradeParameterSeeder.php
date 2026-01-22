<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Database\Seeders\Concerns\TruncatesTables;

class GradeParameterSeeder extends Seeder
{
    use TruncatesTables;
    public function run()
    {
        $data = [
            [
                'grade_letter' => 'A',
                'min_score' => 85,
                'max_score' => 100,
            ],
            [
                'grade_letter' => 'B',
                'min_score' => 70,
                'max_score' => 84,
            ],
            [
                'grade_letter' => 'C',
                'min_score' => 55,
                'max_score' => 69,
            ],
            [
                'grade_letter' => 'D',
                'min_score' => 40,
                'max_score' => 54,
            ],
            [
                'grade_letter' => 'E',
                'min_score' => 0,
                'max_score' => 39,
            ],
        ];

        foreach ($data as $item) {
            DB::table('grade_parameters')->updateOrInsert(
                ['grade_letter' => $item['grade_letter']],
                array_merge($item, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }
}
