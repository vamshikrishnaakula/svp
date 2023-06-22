<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExtraSessionmeta extends Model
{
    use HasFactory;

    protected $fillable = [
        'extra_session_id',
        'probationer_id',
        'attendance',
        'count',
        'grade',
        'timetable_id',
        'qualified',
    ];

    /**
     * Get the Extra Session.
     */
    public function extrasession()
    {
        return $this->belongsTo(ExtraSession::class);
    }

    /**
     * Return Empty inplace null
     *
     * @param  string  $value
     * @return string
     */
    public function getHasQualifyAttribute($value)
    {
        return ($value === null) ? "" : $value;
    }
}
