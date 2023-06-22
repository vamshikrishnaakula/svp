<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class probationer extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_id','RollNumber','Name', 'Dob', 'Email', 'gender', 'user_id' , 'squad_id', 'Cadre', 'MobileNumber'
    ];
}
