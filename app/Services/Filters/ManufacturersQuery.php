<?php
namespace App\Services\Filters;

class ManufacturersQuery extends QueryFilter {
    protected $table         = 'manufacturers';
    protected $allowedParams = [
        'name' => ['lk'],
    ];
}