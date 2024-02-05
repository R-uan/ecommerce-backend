<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductSpecs extends Model {
    use HasFactory;
    public function products(): BelongsTo {
        return $this->belongsTo(Products::class);
    }
}
