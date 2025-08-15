<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class UserFilter extends QueryFilter
{
    public function applyFilters(): Builder
{
    $this->filterByStatus();
    $this->filterByDeleted();
    $this->filterByRole();
    $this->filterByEmail();
    $this->filterByName();

    return $this->query;
}


protected function filterByStatus(): void
{
    $status = $this->request('status');
    
    if (!$status) {
        return;
    }

    $allowed = ['active', 'inactive', 'pending'];

    if (is_array($status)) {
        // Normaliza para lowercase e filtra apenas valores válidos
        $status = array_map('strtolower', array_filter($status));
        $valid = array_intersect($status, $allowed);
        if (!empty($valid)) {
            $this->query->whereIn('status', $valid);
        }
    } else {
        $status = strtolower(trim($status));
        if (in_array($status, $allowed)) {
            $this->query->where('status', $status);
        }
    }
}


    protected function filterByDeleted(): void
    {
        $trashed = $this->request('trashed');

        if ($trashed === 'only') {
            $this->query->onlyTrashed();
        } elseif ($trashed === 'with') {
            $this->query->withTrashed();
        } else {
            $this->query->withoutTrashed();
        }
    }

    protected function filterByRole(): void
    {
        $role = $this->request('role');

        if (in_array($role, ['user', 'admin'])) {
            $this->query->where('role', $role);
        }
    }

    protected function filterByEmail(): void
    {
        $this->addLike('email', $this->request('email'));
    }

    protected function filterByName(): void
    {
        $this->addLike('name', $this->request('name'));
    }
}
