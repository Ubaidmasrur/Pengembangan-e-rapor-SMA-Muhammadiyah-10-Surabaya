<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('student_grade_details')) return;

        Schema::table('student_grade_details', function (Blueprint $table) {
            $table->enum('period', [
                'Januari','Februari','Maret','April','Mei','Juni',
                'Juli','Agustus','September','Oktober','November','Desember'
            ])->nullable()->after('notes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!Schema::hasTable('student_grade_details')) return;

        Schema::table('student_grade_details', function (Blueprint $table) {
            if (Schema::hasColumn('student_grade_details', 'period')) {
                $table->dropColumn('period');
            }
        });
    }
};
