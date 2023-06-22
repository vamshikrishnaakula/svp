<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CloudDriveFolder extends Model
{
    use HasFactory;

    /**
     * The primary key associated with the table.
     */
    protected $primaryKey = 'folder_id';

    protected $fillable = [
        "name",
        "parent_id",
        "reference",
        "reference_id",
        "created_by",
        "updated_by",
    ];
}
