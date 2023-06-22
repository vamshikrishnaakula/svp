<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Labreports extends Model
{
    use HasFactory;

    protected $fillable = [
        'FileDirectory', 'ReportName', 'Probationer_Id' ,'report_type',
    ];
}
