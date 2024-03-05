<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Manufacturers extends Model {
    use HasFactory;

    protected $hidden   = ['created_at', 'updated_at'];
    protected $fillable = ['name', 'email', 'website'];

    public function Products(): HasMany {
        return $this->hasMany(Products::class, 'manufacturers_id');
    }

}
