<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timetable extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_id',
        'squad_id',
        'date',
        'session_number',
        'session_type',
        'activity_id',
        'subactivity_id',
        'session_start',
        'session_end',
    ];

    /**
     * Get attendances for the timetable
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
