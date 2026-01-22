<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add period to student_grades
        if (Schema::hasTable('student_grades')) {
            Schema::table('student_grades', function (Blueprint $table) {
                if (!Schema::hasColumn('student_grades', 'period')) {
                    $table->enum('period', [
                        'Januari','Februari','Maret','April','Mei','Juni',
                        'Juli','Agustus','September','Oktober','November','Desember'
                    ])->nullable()->after('academic_year_id');
                }
            });
        }

        // Copy existing detail.period values into master student_grades (pick first found per master)
        if (Schema::hasTable('student_grade_details') && Schema::hasColumn('student_grade_details', 'period') && Schema::hasTable('student_grades')) {
            $mapping = [];
            DB::table('student_grade_details')->whereNotNull('period')->orderBy('id')->chunk(200, function ($rows) use (&$mapping) {
                foreach ($rows as $r) {
                    // prefer the first encountered period for a given student_grade_id
                    if (!empty($r->student_grade_id) && !isset($mapping[$r->student_grade_id])) {
                        $mapping[$r->student_grade_id] = $r->period;
                    }
                }
            });

            foreach ($mapping as $studentGradeId => $period) {
                DB::table('student_grades')->where('id', $studentGradeId)->update(['period' => $period]);
            }

            // now drop the column from details
            Schema::table('student_grade_details', function (Blueprint $table) {
                $table->dropColumn('period');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // add period back to student_grade_details if missing
        if (Schema::hasTable('student_grade_details') && !Schema::hasColumn('student_grade_details', 'period')) {
            Schema::table('student_grade_details', function (Blueprint $table) {
                $table->enum('period', [
                    'Januari','Februari','Maret','April','Mei','Juni',
                    'Juli','Agustus','September','Oktober','November','Desember'
                ])->nullable()->after('notes');
            });
        }

        // copy back period values from master into details when possible (sets all details for that master)
        if (Schema::hasTable('student_grades') && Schema::hasColumn('student_grades', 'period') && Schema::hasTable('student_grade_details') && Schema::hasColumn('student_grade_details', 'student_grade_id')) {
            DB::table('student_grades')->whereNotNull('period')->orderBy('id')->chunk(200, function ($rows) {
                foreach ($rows as $r) {
                    DB::table('student_grade_details')->where('student_grade_id', $r->id)->update(['period' => $r->period]);
                }
            });
        }

        // finally remove period from student_grades
        if (Schema::hasTable('student_grades') && Schema::hasColumn('student_grades', 'period')) {
            Schema::table('student_grades', function (Blueprint $table) {
                $table->dropColumn('period');
            });
        }
    }
};
