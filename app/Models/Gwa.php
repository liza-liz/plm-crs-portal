<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Gwa extends Model
{
    use HasFactory;

    protected $primaryKey = 'gwa_id';

    protected $fillable = [
        'student_id',
        'gwa',
        'aysem_id',
    ];

    public function aysem(): BelongsTo
    {
        return $this->belongsTo(Aysem::class, 'aysem_id', 'aysem_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }
}
