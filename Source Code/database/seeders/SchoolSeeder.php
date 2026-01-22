<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Database\Seeders\Concerns\TruncatesTables;

class SchoolSeeder extends Seeder
{
    // use TruncatesTables;
    public function run()
    {
    // $this->truncate(['schools']);
    \DB::table('schools')->delete();
        DB::table('schools')->insert([
            [
                'name' => 'SLB Negeri 1 Jakarta',
                'address' => 'Jl. Contoh No. 1, Jakarta',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'SLB Negeri 2 Bandung',
                'address' => 'Jl. Contoh No. 2, Bandung',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
