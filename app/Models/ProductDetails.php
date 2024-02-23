<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductDetails extends Model {
    use HasFactory;

    protected $hidden = ['created_at', 'updated_at', 'products_id', 'id'];

    public function product(): BelongsTo {
        return $this->BelongsTo(Products::class);
    }
}
