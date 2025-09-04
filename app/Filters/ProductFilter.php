<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ProductFilter extends QueryFilter
{
    /**
     * Aplica todos os filtros de produto na query
     */
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
        if ($this->request->filled('trashed')) {
            if ($this->request->trashed === 'only') {
                $this->query->onlyTrashed();
            } elseif ($this->request->trashed === 'with') {
                $this->query->withTrashed();
            } else {
                $this->query->withoutTrashed();
            }
        } else {
            $this->query->withoutTrashed();
        }
        return $this;
    }

    public function filterByCategory(): self
    {
        if ($this->request->filled('category')) {
            $this->query->where('category_id', $this->request->category);
        }

        return $this;
    }

    public function filterByName(): self
    {
        if ($this->request->filled('name')) {
            $this->query->where('name', 'like', "%{$this->request->name}%");
        }

        return $this;
    }

    public function filterByPrice(): self
    {
        if ($this->request->filled('price')) {
            $this->query->where('price', $this->request->price);
        }

        return $this;
    }

    public function filterBySku(): self
    {
        if ($this->request->filled('sku')) {
            $this->query->where('sku', 'like', "%{$this->request->sku}%");
        }

        return $this;
    }

    public function filterByDescription(): self
    {
        if ($this->request->filled('description')) {
            $this->query->where('description', 'like', "%{$this->request->description}%");
        }

        return $this;
    }
}
