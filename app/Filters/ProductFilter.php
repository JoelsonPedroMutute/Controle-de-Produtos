<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ProductFilter extends QueryFilter
{
    public function applyFilters(): Builder
{
    $this->filterByStatus();
    $this->filterByDeleted();
    $this->filterByCategory();
    $this->filterByName();
    $this->filterByPrice();
    $this->filterBySku();
    $this->filterByDescription();

    return $this->query;
}

public function filterByStatus(): self
{
    if ($this->request->filled('status')) {
        $status = strtolower($this->request->status);
        if (in_array($status, ['active', 'inactive'])) {
            $this->query->where('status', $status);
        }
    } elseif (Auth::check() && Auth::user()->role === 'cliente') {
        // Clientes só veem produtos ativos
        $this->query->where('status', 'active');
    }

    return $this;
}

public function filterByDeleted(): self
{
    if ($deleted = request('deleted')) {
        $this->query->where('deleted', $deleted);
    }

    return $this;
}

public function filterByCategory(): self
{
    if ($category = request('category')) {
        $this->query->where('category_id', $category);
    }

    return $this;
}

public function filterByName(): self
{
    if ($name = request('name')) {
        $this->query->where('name', 'like', "%{$name}%");
    }

    return $this;
}

public function filterByPrice(): self
{
    if ($price = request('price')) {
        $this->query->where('price', $price);
    }

    return $this;
}

public function filterBySku(): self
{
    if ($sku = request('sku')) {
        $this->query->where('sku', 'like', "%{$sku}%");
    }

    return $this;
}

public function filterByDescription(): self
{
    if ($description = request('description')) {
        $this->query->where('description', 'like', "%{$description}%");
    }

    return $this;
}
}