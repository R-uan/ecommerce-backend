<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Products extends Model {
    use HasFactory;

    public function manufacturer(): BelongsTo {
        return $this->belongsTo(Manufacturers::class);
    }

    public function productSpecs(): HasOne {
        return $this->hasOne(ProductSpecs::class);
    }
}
