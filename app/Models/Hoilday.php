<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hoilday extends Model
{
    use HasFactory;

    protected $table = 'hoildays';

    protected $fillable = ['batch_id', 'squad_id', 'date'];
}
