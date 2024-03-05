<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Products extends Model {
    use HasFactory;
    protected $hidden = ['created_at', 'updated_at'];

    protected $fillable = [
        'name',
        'category',
        'image_url',
        'unit_price',
        'availability',
        'manufacturers_id',
        'long_description',
        'short_description',
    ];

    public function Manufacturer(): BelongsTo {
        return $this->belongsTo(Manufacturers::class, 'manufacturers_id');
    }

    public function ProductDetails(): HasOne {
        return $this->hasOne(ProductDetails::class);
    }

    public function OrderItens(): HasMany {
        return $this->hasMany(OrderItens::class, 'product_id');
    }
}
