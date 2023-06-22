<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appoinments extends Model
{
    use HasFactory;

    protected $fillable = [
        'Probationer_Id', 'Doctor_Id', 'Symptoms', 'Appoinment_Time', 'Status'
    ];

}
