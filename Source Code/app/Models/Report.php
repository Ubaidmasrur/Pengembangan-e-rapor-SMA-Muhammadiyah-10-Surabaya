<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Report extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title', 'student_id', 'academic_year_id', 'class_id', 'teacher_id', 'notes'
    ];

    public function details()
    {
        return $this->hasMany(ReportDetail::class);
    }
}
