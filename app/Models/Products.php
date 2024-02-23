<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Products extends Model {
    use HasFactory;
    protected $hidden = ['created_at', 'updated_at'];

    protected $fillable = [
        'name',
        'insurace',
        'category',
        'image_url',
        'unit_price',
        'availability',
        'manufacturers_id',
        'long_description',
        'short_description',
    ];

    public function manufacturer(): BelongsTo {
        return $this->belongsTo(Manufacturers::class);
    }

    public function productDetails(): HasOne {
        return $this->hasOne(ProductDetails::class);
    }
}
