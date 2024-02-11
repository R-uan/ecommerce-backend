<?php

namespace App\Services\Filters;

class OrdersQuery extends QueryFilter {
    protected $table         = 'orders';
    protected $allowedParams = [
        'total'  => ['gt', 'gte', 'lt', 'lte', 'eq'],
        'status' => ['lk'],
    ];
}