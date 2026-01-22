<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GradeParameter extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'grade_letter',
        'min_score',
        'max_score',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

}
