<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentReport extends Model
{
    protected $table = 'student_grades';

    protected $guarded = [];

    // Tidak memakai timestamps karena ini data khusus laporan
    public $timestamps = false;

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function schoolClass()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function details()
    {
        return $this->hasMany(StudentGradeDetail::class, 'student_grade_id');
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    /**
     * Ambil fase dari salah satu detail rapor
     */
    public function getFaseAttribute()
    {
        return $this->details()->value('fase');
    }

    public function getFaseDescAttribute()
    {
        return $this->details()->value('fase_desc');
    }

    /**
     * Ambil data sekolah (hanya sekolah pertama, misal 1 madrasah saja)
     */
    public function getSchoolAttribute()
    {
        return \App\Models\School::first();
    }

    /**
     * Ambil nama sekolah langsung
     */
    public function getSchoolNameAttribute()
    {
        return $this->school?->name ?? '-';
    }

    /**
     * Ambil alamat sekolah langsung
     */
    public function getSchoolAddressAttribute()
    {
        return $this->school?->address ?? '-';
    }

    /**
     * Format periode (ex: Januari 2025)
     */
    public function getPeriodLabelAttribute()
    {
        $period = $this->period;
        $year = $this->academicYear?->year;

        if ($period && $year) {
            return trim($period . ' ' . $year);
        }

        return $period ?? $year ?? '-';
    }
}
