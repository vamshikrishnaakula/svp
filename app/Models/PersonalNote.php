<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonalNote extends Model
{
    use HasFactory;

    /**
     * The primary key associated with the table.
     */
    protected $primaryKey = 'note_id';

    protected $fillable = [
        "user_id",
        "reference",
        "reference_id",
        "title",
        "text",
    ];
}
