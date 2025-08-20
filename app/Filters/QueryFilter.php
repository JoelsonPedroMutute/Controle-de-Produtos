<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

abstract class QueryFilter
{
    protected Request $request;
    protected Builder $query;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply(Builder $query): Builder
    {
        $this->query = $query;
        return $this->applyFilters(); 
    }

    // novo método que será implementado pelos filhos
    abstract protected function applyFilters(): Builder;

    protected function request(string $key, $default = null)
    {
        return $this->request->get($key, $default);
    }

    protected function addWhere(string $field, $value): void
    {
        if (!is_null($value)) {
            $this->query->where($field, $value);
        }
    }

    protected function addLike(string $field, $value): void
    {
        if (!empty($value)) {
            $this->query->where($field, 'LIKE', "%{$value}%");
        }
    }
}



