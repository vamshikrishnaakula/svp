<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignprobationerstosquad extends Model
{
    use HasFactory;
    protected $table = 'assign_probationers_to_squad';

    protected $fillable = [
        'Squad_Id', 'Probationer_Id',
    ];


}

