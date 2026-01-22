<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Teacher;
use App\Models\Student;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'deleted_at' => 'datetime',
    ];

    public function teacher()
    {
        return $this->hasOne(Teacher::class, 'user_id');
    }

    public function getTeacherIdAttribute()
    {
        return $this->teacher ? $this->teacher->id : null;
    }

    public function ensureTeacher(array $attributes = [])
    {
        if ($this->teacher) {
            return $this->teacher;
        }

        $defaults = [
            'name' => $this->name,
            'user_id' => $this->id,
        ];

        return $this->teacher()->create(array_merge($defaults, $attributes));
    }

    public function student()
    {
        return $this->hasOne(Student::class, 'user_id');
    }

    public function getStudentIdAttribute()
    {
        return $this->student ? $this->student->id : null;
    }

    public function ensureStudent(array $attributes = [])
    {
        if ($this->student) {
            return $this->student;
        }

        $defaults = [
            'name' => $this->name,
            'user_id' => $this->id,
        ];

        return $this->student()->create(array_merge($defaults, $attributes));
    }
}
