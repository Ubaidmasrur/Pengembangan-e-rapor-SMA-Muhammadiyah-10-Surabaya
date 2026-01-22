<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SchoolClass extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'school_id'];
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    public function students()
    {
        return $this->belongsToMany(
            Student::class,
            'class_student_assignments', // ✅ nama tabel pivot
            'class_id',                  // ✅ foreign key di pivot mengarah ke SchoolClass
            'student_id'                 // ✅ foreign key di pivot mengarah ke Student
        )->withTimestamps()->withPivot('academic_year_id');
    }

    public function teachers()
    {
        return $this->belongsToMany(
            Teacher::class,
            'class_teacher_assignments', // ✅ nama tabel pivot
            'class_id',                  // ✅ FK ke SchoolClass
            'teacher_id'                 // ✅ FK ke Teacher
        )->withTimestamps()->withPivot('academic_year_id', 'is_wali');
    }

    /**
     * Has-many relation to ClassTeacherAssignment records for this class.
     * Added to support whereHas('class_teacher_assignments') calls.
     */
    public function class_teacher_assignments()
    {
        return $this->hasMany(ClassTeacherAssignment::class, 'class_id');
    }

    /**
     * CamelCase alias for convenience.
     */
    public function classTeacherAssignments()
    {
        return $this->class_teacher_assignments();
    }
}
