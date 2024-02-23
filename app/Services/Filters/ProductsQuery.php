<?php
namespace App\Services\Filters;

class ProductsQuery extends QueryFilter {
    protected $table         = 'products';
    protected $allowedParams = [
        'name'         => ['lk'],
        'category'     => ['eq'],
        'availability' => ['eq'],
        'price'        => ['eq', 'lt', 'gt', 'lte', 'gte'],
    ];
    protected $columnMap = [
        'price' => 'unit_price',
    ];
}