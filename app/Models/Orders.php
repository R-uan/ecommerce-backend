<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Orders extends Model {
    use HasFactory;

    protected $fillable = ['status', 'order_date', 'total', 'client_id'];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function orderItens(): HasMany {
        return $this->hasMany(OrderItens::class);
    }
}
