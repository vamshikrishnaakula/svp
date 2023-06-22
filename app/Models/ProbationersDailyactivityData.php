<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProbationersDailyactivityData extends Model
{
    use HasFactory;

    protected $fillable = [
        'Batch_id',
        'squad_id',
        'staff_id',
        'activity_id',
        'subactivity_id',
        'component_id',
        'probationer_id',
        'timetable_id',
        'grade',
        'count',
        'qualified',
        'attendance',
        'date',
    ];

    /**
     * Get timetable for the attendance
     */
    public function timetable()
    {
        return $this->hasOne(Timetable::class);
    }
}
