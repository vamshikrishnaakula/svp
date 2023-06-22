<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneralAssesment extends Model
{
    use HasFactory;

    protected $fillable = [
        'probationer_id',
        'punctuality',
        'behaviour',
        'teamspirit',
        'learningefforts',
        'responsibility',
        'leadership',
        'commandcontrol',
        'sportsmanship',
        'month',
        'year',
        'staff_id',
        'staff_role',
    ];

    /**
     * Trim leading zero from month
     *
     * @param  string  $value
     * @return void
     */
    public function setmonthAttribute($value)
    {
        $this->attributes['month'] = ltrim($value, '0');
    }
}
