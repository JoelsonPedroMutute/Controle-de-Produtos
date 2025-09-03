<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class CategoryFilter extends QueryFilter
{
    public function applyFilters(): Builder
    {
        $this->filterByName();
        $this->filterByDeleted();
        $this->filterByStatus();

        // Só aplica status se NÃO for cliente
        if (Auth::check() && Auth::user()->role !== 'cliente') {
            $this->filterByStatus();
        }

        return $this->query;
    }

    protected function filterByName(): void
    {
        if ($this->request->filled('name')) {
            $this->query->where('name', 'like', '%' . $this->request->name . '%');
        }
    }

    protected function filterByStatus(): void
    {
        if ($this->request->filled('status')) {
            $status = strtolower($this->request->status);
            if (in_array($status, ['active', 'inactive'])) {
                $this->query->where('status', $status);
            }
        }
    }

    protected function filterByDeleted(): void
    {
        // cliente nunca vê deletados
        if (Auth::check() && Auth::user()->role === 'cliente') {
            return;
        }

        if (!$this->request->filled('deleted')) {
            return; // padrão: sem deletados
        }

        switch ($this->request->deleted) {
            case 'with':
                $this->query->withTrashed();
                break;
            case 'only':
                $this->query->onlyTrashed();
                break;
            case 'without':
            default:
                break;
        } 
    }
}
