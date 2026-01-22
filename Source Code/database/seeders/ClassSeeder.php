<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Database\Seeders\Concerns\TruncatesTables;

class ClassSeeder extends Seeder
{
    // use TruncatesTables;
    public function run()
    {
        // Clear only school_classes table
        DB::table('school_classes')->delete();
        // Get seeded schools
        $schools = DB::table('schools')->get();
        $data = [
            ['name' => 'Kelas 1A', 'school_name' => 'SLB Negeri 1 Jakarta'],
            ['name' => 'Kelas 2A', 'school_name' => 'SLB Negeri 2 Bandung'],
        ];

        foreach ($data as $row) {
            $school = $schools->firstWhere('name', $row['school_name']);
            if ($school) {
                $exists = DB::table('school_classes')
                    ->where('name', $row['name'])
                    ->where('school_id', $school->id)
                    ->exists();

                if (!$exists) {
                    DB::table('school_classes')->insert([
                        'name' => $row['name'],
                        'school_id' => $school->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}