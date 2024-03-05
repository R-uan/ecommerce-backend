<?php
namespace App\Services\Filters;

class ProductsQuery extends QueryFilter {
  protected $table          = 'products';
  protected $allowed_params = [
    'name'         => ['lk'],
    'category'     => ['eq'],
    'availability' => ['eq'],
    'price'        => ['eq', 'lt', 'gt', 'lte', 'gte'],
  ];
  protected $column_map = [
    'price' => 'unit_price',
  ];
}