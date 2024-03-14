<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlanetDestination extends Model {
  use HasFactory;

  protected $fillable = ['name', 'delivery_price', 'special_conditions'];
  protected $hidden   = ['created_at', 'updated_at'];

  function orders(): HasMany {
    return $this->hasMany(Orders::class, 'planet_destination_id');
  }
}
