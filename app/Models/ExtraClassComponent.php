<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExtraClassComponent extends Model
{
    use HasFactory;

    /**
     * The primary key associated with the table.
     */
    protected $primaryKey = 'session_component_id';

    protected $fillable = [
        "classmetas_id",
        "session_id",
        "probationer_id",
        "component_id",
        "count",
        "grade",
        "qualified",
    ];
}
