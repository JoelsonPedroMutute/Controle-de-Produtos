<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class UserFilter
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

        $this->filterByStatus();
        $this->filterByDeleted();
        $this->filterByRole();
        $this->filterByEmail();
        $this->filterByName();

        return $this->query;
    }

    protected function filterByStatus(): void
    {
        $status = $this->request->get('status');

        if (in_array($status, ['active', 'inactive', 'pending'])) {
            $this->query->where('status', $status);
        }
    }

    protected function filterByDeleted(): void
    {
        $trashed = $this->request->get('trashed');

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
        $role = $this->request->get('role');

        if (in_array($role, ['user', 'admin'])) {
            $this->query->where('role', $role);
        }
    }

    protected function filterByEmail(): void
    {
        if ($email = $this->request->get('email')) {
            $this->query->where('email', 'LIKE', "%{$email}%");
        }
    }

    protected function filterByName(): void
    {
        if ($name = $this->request->get('name')) {
            $this->query->where('name', 'LIKE', "%{$name}%");
        }
    }
}
