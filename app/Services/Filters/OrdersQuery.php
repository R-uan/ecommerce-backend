<?php

namespace App\Services\Filters;

class OrdersQuery extends QueryFilter {
  protected $table          = 'orders';
  protected $allowed_params = [
    'total'  => ['gt', 'gte', 'lt', 'lte', 'eq'],
    'status' => ['lk'],
  ];
}