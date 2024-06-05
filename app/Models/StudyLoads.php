<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudyLoad extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'instructor_id',
        'study_units',
        'teaching_units',
        'aysem_id',
        'entered_by',
        'entered_on'
    ];

}