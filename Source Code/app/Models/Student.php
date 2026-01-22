<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'nisn',
        'gender',
        'birth_date',
        'disability_type',
        'user_id',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
        'birth_date' => 'date',
    ];

    /**
     * Relasi ke User (satu user punya satu student).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke penugasan kelas (student ↔ class assignment).
     */
    public function classStudentAssignments(): HasMany
    {
        return $this->hasMany(ClassStudentAssignment::class, 'student_id');
    }

    /**
     * Relasi ke banyak kelas melalui penugasan.
     */
    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(
            SchoolClass::class,
            ClassStudentAssignment::class,
            'student_id',
            'class_id'
        );
    }

    /**
     * Relasi ke banyak tahun ajaran melalui penugasan.
     */
    public function academicYears(): BelongsToMany
    {
        return $this->belongsToMany(
            AcademicYear::class,
            ClassStudentAssignment::class,
            'student_id',
            'academic_year_id'
        );
    }

    /**
     * Relasi ke nilai siswa.
     */
    public function grades(): HasMany
    {
        return $this->hasMany(StudentGrade::class, 'student_id');
    }

    /**
     * Accessor: return kelas terakhir student.
     * Bisa dipanggil dengan $student->class
     */
    public function getClassAttribute()
    {
        $assignment = $this->classStudentAssignments()
            ->with('class')
            ->latest('id')
            ->first();

        return $assignment?->class;
    }
}
