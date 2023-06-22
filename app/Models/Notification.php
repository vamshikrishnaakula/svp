<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        "recipient_type",
        "batch_id",
        "squad_id",
        "title",
        "message",
        "attachment",
        "created_by",
    ];
}
