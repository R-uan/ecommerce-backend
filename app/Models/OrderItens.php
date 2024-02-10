<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderItens extends Model {
    use HasFactory;

    protected $fillable = ['product_id', 'orders_id', 'unit_price', 'amount', 'total_price'];

    public function orders(): BelongsTo {
        return $this->belongsTo(Orders::class);
    }

    public function products(): HasMany {
        return $this->hasMany(Products::class);
    }
}
