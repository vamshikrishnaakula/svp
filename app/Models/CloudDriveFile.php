<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CloudDriveFile extends Model
{
    use HasFactory;

    /**
     * The primary key associated with the table.
     */
    protected $primaryKey = 'file_id';

    protected $fillable = [
        "folder_id",
        "original_name",
        "disk",
        "file_path",
        "file_extn",
        "created_by",
        "updated_by",
    ];
}
