<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use App\Filters\CategoryFilter;
use Illuminate\Http\Request;
use App\Http\Resources\CategoryResource;


class CategoryService
{
    public function create(array $data): Category
    {
        return Category::create($data);
    }

    public function update(Category $category, array $data): Category
    {
        $category->update($data);
        return $category;
    }

    public function delete(Category $category): bool
    {
        return $category->delete();
    }
}
