<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InPatient extends Model
{
    use HasFactory;

    protected $fillable = [
        'probationer_id',
        'appointment_id',
        'admitted_date',
        'status',
    ];
}
