<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Activity extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        "batch_id",
        "name",
        "type",
        "parent_id",
        "unit",
        "has_grading",
        "has_qualify",
    ];

    /**
     * Return Empty inplace null
     *
     * @param  string  $value
     * @return string
     */
    public function getunitAttribute($value)
    {
        return ($value === null) ? "" : $value;
    }
}
