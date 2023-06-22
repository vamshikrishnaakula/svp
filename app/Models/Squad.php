<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Squad extends Model
{
    use HasFactory;

    protected $fillable = [
        'Batch_Id', 'SquadNumber', 'DrillInspector_Id', 'Probationer_Id',
    ];
}

