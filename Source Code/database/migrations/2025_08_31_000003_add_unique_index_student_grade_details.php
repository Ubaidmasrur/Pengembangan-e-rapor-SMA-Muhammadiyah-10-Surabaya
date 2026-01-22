<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds a unique index to ensure a student_grade cannot have duplicate subject details.
     */
    public function up(): void
    {
        Schema::table('student_grade_details', function (Blueprint $table) {
            // Defensive: only add if not exists. Some DB drivers don't support conditional checks,
            // but Laravel will ignore if index name collides on many setups; use safe name.
            $table->unique(['student_grade_id', 'subject_id'], 'sgd_student_grade_subject_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_grade_details', function (Blueprint $table) {
            $table->dropUnique('sgd_student_grade_subject_unique');
        });
    }
};
