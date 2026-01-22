<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activity extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'activity_date',
        'thumbnail',
    ];

    protected $casts = [
        'activity_date' => 'date',
        'deleted_at' => 'datetime',
    ];
}
