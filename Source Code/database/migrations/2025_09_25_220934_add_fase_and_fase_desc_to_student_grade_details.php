<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('student_grade_details', function (Blueprint $table) {
            $table->enum('fase', ['A', 'B', 'C', 'D', 'E', 'F'])->nullable(); // ✅ New
            $table->string('fase_desc', 3000)->nullable();                   // ✅ New
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_grade_details', function (Blueprint $table) {
            $table->dropColumn(['fase', 'fase_desc']);
        });
    }
};
