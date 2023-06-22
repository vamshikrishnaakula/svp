<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'events';

    protected $fillable = ['batch_id', 'competition', 'category', 'event_name', 'events_rounds', 'units', 'gender'];

}
