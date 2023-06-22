<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class probationersMytarget extends Model
{
    use HasFactory;

    protected $fillable = [
        'probationer_id',
        'activity_id',
        'subactivity_id',
        'component_id',
        'goal',
        'month'
    ];
}
