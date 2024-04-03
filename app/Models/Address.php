<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Address extends Model {
  use HasFactory;
  protected $hidden   = ['created_at', 'updated_at'];
  protected $fillable = ['planet', 'nation', 'state', 'city', 'sector', 'residence_id'];

  public function User(): BelongsToMany {
    return $this->belongsToMany('user');
  }
}
