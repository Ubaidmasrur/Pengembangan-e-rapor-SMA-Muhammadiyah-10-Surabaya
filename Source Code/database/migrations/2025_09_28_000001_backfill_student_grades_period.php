<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * We'll set `student_grades.period` to a YYYY-MM value derived from
     * the related academic_year's `year` (take the starting year before '/')
     * and `start_month` (fallback to '01' when missing).
     */
    public function up(): void
    {
        // Use a single UPDATE ... JOIN statement which is efficient in MySQL.
        DB::statement(<<<'SQL'
        UPDATE student_grades sg
        JOIN academic_years ay ON sg.academic_year_id = ay.id
        SET sg.period = CONCAT(
            LPAD(CAST(SUBSTRING_INDEX(ay.year, '/', 1) AS UNSIGNED), 4, '0'),
            '-',
            LPAD(CASE WHEN ay.start_month IS NULL OR ay.start_month = '' THEN '1' ELSE ay.start_month END, 2, '0')
        )
        WHERE (sg.period IS NULL OR sg.period = '');
        SQL
        );
    }

    /**
     * Reverse the migrations.
     *
     * We intentionally leave this as a no-op to avoid accidentally
     * removing previously valid period values. If you need to revert,
     * please inspect and run a targeted update.
     */
    public function down(): void
    {
        // no-op
    }
};
