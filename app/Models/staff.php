<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class staff extends Model
{
    protected $fillable = [
        'first_name','last_name', 'email', 'dob', 'mobile_number', 'username'
    ];
}
