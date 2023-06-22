<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FamilyDependent extends Model
{
    use HasFactory;
    protected $table = 'familydependents';

    protected $fillable = [
        'Probationer_Id', 'DependentName', 'DependentAge', 'DependentGender', 'DependentRelationship', 
    ];

}
