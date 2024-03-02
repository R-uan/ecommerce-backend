<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductDetails extends Model {
    use HasFactory;

    protected $hidden   = ['created_at', 'updated_at', 'id'];
    protected $fillable = [
        'energy_system',
        'landing_system',
        'emergency_system',
        'propulsion_system',
        'navigation_system',
        'external_structure',
        'termic_protection',
        'comunication_system',
    ];
    public function product(): BelongsTo {
        return $this->BelongsTo(Products::class);
    }
}
