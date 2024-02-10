<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Products extends Model {
    use HasFactory;
    protected $hidden   = ['created_at', 'updated_at'];
    protected $fillable = [
        'name',
        'description',
        'image_url',
        'category',
        'availability',
        'unit_price',
        'manufacturers_id',
    ];
    public function manufacturer(): BelongsTo {
        return $this->belongsTo(Manufacturers::class);
    }

    public function productSpecs(): HasOne {
        return $this->hasOne(ProductSpecs::class);
    }
}
