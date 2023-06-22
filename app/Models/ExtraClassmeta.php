<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExtraClassmeta extends Model
{
    use HasFactory;

    protected $fillable = [
        'extra_class_id',
        'probationer_id',
        'attendance',
        'count',
        'grade',
    ];

    /**
     * Get the Extra Class.
     */
    public function extraclass()
    {
        return $this->belongsTo(ExtraClass::class);
    }
}
