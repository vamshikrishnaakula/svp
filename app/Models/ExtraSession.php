<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExtraSession extends Model
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
     * Get the Extra Session metas.
     */
    public function metas()
    {
        return $this->hasMany(ExtraSessionmeta::class);
    }

    /**
     * Get the Extra Session components.
     */
    public function components()
    {
        return $this->hasMany(ExtraSessionComponent::class);
    }
}
