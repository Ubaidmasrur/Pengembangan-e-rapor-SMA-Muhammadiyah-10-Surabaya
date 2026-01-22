<?php

namespace Database\Seeders\Concerns;

use Illuminate\Support\Facades\DB;

trait TruncatesTables
{
    /**
     * Truncate the given tables safely by disabling foreign key checks.
     * Accepts table names or full model table names.
     * @param array $tables
     */
    protected function truncate(array $tables): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        foreach ($tables as $table) {
            DB::table($table)->truncate();
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
