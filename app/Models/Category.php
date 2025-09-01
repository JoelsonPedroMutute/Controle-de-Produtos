<?php

namespace App\Models;

use App\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'status'];

    protected $casts = [
        'status' => 'string',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // Query scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active')->whereNull('deleted_at');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive')->whereNull('deleted_at');
    }

    public function scopeFilter($query, QueryFilter $filters)
{
    return $filters->apply($query);

}
}

