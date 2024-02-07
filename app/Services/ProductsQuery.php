<?php
namespace App\Services;

class ProductsQuery extends QueryFilter {
    protected $table         = 'products';
    protected $allowedParams = [
        'name'         => ['lk'],
        'category'     => ['eq'],
        'availability' => ['eq'],
        'price'        => ['eq', 'lt', 'gt', 'lte', 'gte'],
    ];
}