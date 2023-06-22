<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fitness extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'fitness_meta';

    protected $fillable = [
        'probationer_id',
        'fitness_name',
        'fitness_value',
        'date',
    ];
}
