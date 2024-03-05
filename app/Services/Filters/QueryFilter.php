<?php

namespace App\Services\Filters;

use Illuminate\Http\Request;

class QueryFilter {
  protected $table          = '';
  protected $allowed_params = [];
  protected $column_map     = [];

  protected $operator_map = [
    'lk'  => 'ilike',
    'eq'  => '=',
    'lt'  => '<',
    'lte' => '<=',
    'gt'  => '>',
    'gte' => '>=',
  ];

  public function Transform(Request $request) {
    $full_query = [];
    foreach ($this->allowed_params as $param => $operators) {
      $query = $request->query($param);
      if (!isset($query)) {
        continue;
      }
      $column = $this->column_map[$param] ?? $param;
      foreach ($operators as $operator) {
        if (isset($query[$operator])) {
          $found_operator = $this->operator_map[$operator];
          if ($found_operator == 'ilike') {
            $value        = "%" . $query[$operator] . "%";
            $full_query[] = [$this->table . '.' . $column, $found_operator, $value];
          } else {
            $full_query[] = [$this->table . '.' . $column, $this->operator_map[$operator], $query[$operator]];
          }
        }
      }

    }
    return $full_query;
  }
}