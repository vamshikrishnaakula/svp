<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'probationer_id',
        'date',
        'timetable_id',
        'attendance',
        'comment'
    ];

    /**
     * Get timetable for the attendance
     */
    public function timetable()
    {
        return $this->hasOne(Timetable::class);
    }
}
