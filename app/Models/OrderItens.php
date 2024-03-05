<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItens extends Model {
    use HasFactory;

    protected $fillable = ['product_id', 'orders_id', 'unit_price', 'amount', 'total_price'];

    protected $hidden = ['product_id', 'orders_id', 'created_at', 'updated_at'];

    public function Orders(): BelongsTo {
        return $this->belongsTo(Orders::class);
    }

    public function Products(): BelongsTo {
        return $this->belongsTo(Products::class, 'product_id');
    }
}
