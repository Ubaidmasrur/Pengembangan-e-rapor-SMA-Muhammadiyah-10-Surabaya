<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Teacher extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'nip',
        'subject_specialty',
        'user_id', // jika pakai relasi user
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function classTeacherAssignments()
    {
        return $this->hasMany(ClassTeacherAssignment::class, 'teacher_id');
    }

    public function assignedClasses()
    {
        return $this->belongsToMany(
            SchoolClass::class,
            ClassTeacherAssignment::class,
            'teacher_id',
            'class_id'
        );
    }

    public function assignedAcademicYears()
    {
        return $this->belongsToMany(
            AcademicYear::class,
            ClassTeacherAssignment::class,
            'teacher_id',
            'academic_year_id'
        );
    }

    // Get only classes where teacher is wali
    public function waliClassAssignments()
    {
        return $this->hasMany(ClassTeacherAssignment::class, 'teacher_id')->where('is_wali', true);
    }

    public function waliClasses()
    {
        return $this->belongsToMany(
            SchoolClass::class,
            ClassTeacherAssignment::class,
            'teacher_id',
            'class_id'
        )->wherePivot('is_wali', true);
    }

    // Get all academic years from assignments
    public function allAcademicYears()
    {
        return $this->hasManyThrough(
            AcademicYear::class,
            ClassTeacherAssignment::class,
            'teacher_id',
            'id',
            'id',
            'academic_year_id'
        );
    }
}
