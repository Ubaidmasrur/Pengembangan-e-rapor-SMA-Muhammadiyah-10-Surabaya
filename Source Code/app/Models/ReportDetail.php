<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReportDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'report_id', 'subject_id', 'score', 'grade_letter', 'notes'
    ];

    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
