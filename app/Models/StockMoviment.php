<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Product;

class StockMoviment extends Model
{
    use HasFactory;

    protected $table = 'stock_movements';

    protected $fillable = [
        'user_id',
        'product_id',
        'type',
        'reason',
        'quantity',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }   


}