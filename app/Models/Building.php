<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Building extends Model
{
    use HasFactory;

    protected $fillable = [
        'building_code',
        'building_name',
    ];

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class, 'building_id', 'building_id');
    }
}
