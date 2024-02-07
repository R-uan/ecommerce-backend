<?php

namespace App\Services\Filters;

use Illuminate\Http\Request;

class QueryFilter {
    protected $table         = '';
    protected $allowedParams = [];
    protected $columnMap     = [];

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
                        $fullQuery[] = [$this->table . '.' . $column, $foundOperator, $value];
                    } else {
                        $fullQuery[] = [$this->table . '.' . $column, $this->operatorMap[$operator], $query[$operator]];
                    }
                }
            }

        }
        return $fullQuery;
    }
}