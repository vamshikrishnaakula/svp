<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProbationereventScheduler extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'events_scheduler_probationers';

    protected $fillable = ['event_scheduler_id', 'probationers_id'];
}
