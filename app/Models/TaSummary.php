<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaSummary extends Model
{
    use HasFactory;

    protected $fillable = [
        'instructor_id',
        'regular',
        'extra',
        'administrative',
        'substitution',
        'off_campus',
        'study',
        'outside',
        'pro_bono',
        'aysem_id',
    ];
}
