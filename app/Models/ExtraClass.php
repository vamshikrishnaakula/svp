<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExtraClass extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_id',
        'activity_id',
        'subactivity_id',
        'component_id', // Not in use anymore
        'drillinspector_id',
        'date',
        'session_start',
        'session_end',
    ];

    /**
     * Get the Extra Class metas.
     */
    public function metas()
    {
        return $this->hasMany(ExtraClassmeta::class);
    }

    /**
     * Get the Extra Class components.
     */
    public function components()
    {
        return $this->hasMany(ExtraClassComponent::class);
    }
}
