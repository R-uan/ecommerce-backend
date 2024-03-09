<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Orders extends Model {
  use HasFactory;

  protected $fillable = ['status', 'order_date', 'total', 'client_id', 'planet_destination_id', 'payment_method'];
  protected $hidden   = ['created_at', 'updated_at', 'client_id'];

  public function User(): BelongsTo {
    return $this->belongsTo(User::class);
  }

  public function PlanetDestination(): BelongsTo {
    return $this->belongsTo(PlanetDestination::class, 'planet_destination_id');
  }

  public function OrderItens(): HasMany {
    return $this->hasMany(OrderItens::class);
  }
}
