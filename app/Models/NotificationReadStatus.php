<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationReadStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        "notification_id",
        "user_id",
        "read_status",
    ];
}
