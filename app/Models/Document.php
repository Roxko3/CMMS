<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    protected $fillable = ['name', 'device_id', 'attachment'];

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }
}
