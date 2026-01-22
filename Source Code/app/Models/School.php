<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class School extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'address', 'phone', 'email', 'principal_name'];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];
}
