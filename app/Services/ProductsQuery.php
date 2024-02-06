<?php
namespace App\Services;

use Illuminate\Http\Request;

class ProductsQuery {
    protected $allowedParams = [
        'name'         => ['lk'],
        'category'     => ['eq'],
        'availability' => ['eq'],
        'price'        => ['eq', 'lt', 'gt', 'lte', 'gte'],
    ];

    protected $columnMap = [];

    protected $operatorMap = [
        'lk'  => 'ilike',
        'eq'  => '=',
        'lt'  => '<',
        'lte' => '<=',
        'gt'  => '>',
        'gte' => '>=',
    ];

    public function transform(Request $request) {
        $fullQuery = [];
        foreach ($this->allowedParams as $param => $operators) {
            $query = $request->query($param);
            if (!isset($query)) {
                continue;
            }
            $column = $this->columnMap[$param] ?? $param;
            foreach ($operators as $operator) {
                if (isset($query[$operator])) {
                    $foundOperator = $this->operatorMap[$operator];
                    if ($foundOperator == 'ilike') {
                        $value       = "%" . $query[$operator] . "%";
                        $fullQuery[] = ['products.' . $column, $foundOperator, $value];
                    } else {
                        $fullQuery[] = ['products.' . $column, $this->operatorMap[$operator], $query[$operator]];
                    }
                }
            }

        }
        /* dd($fullQuery); */
        return $fullQuery;
    }
}