<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExtraSessionComponent extends Model
{
    use HasFactory;

    /**
     * The primary key associated with the table.
     */
    protected $primaryKey = 'session_component_id';

    protected $fillable = [
        "sessionmetas_id",
        "session_id",
        "probationer_id",
        "component_id",
        "count",
        "grade",
        "qualified",
    ];
}
