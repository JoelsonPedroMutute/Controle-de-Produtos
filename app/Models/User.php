<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Filters\QueryFilter;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


    /**
 * Modelo que representa o usuário do sistema.
 * Inclui autenticação, notificações e relacionamentos com agendamentos.
 */
class User extends Authenticatable
{
    // Traits que adicionam funcionalidades ao modelo
     use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasUuids;

    // <-- ESTA PARTE FOI ADICIONADA
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'phone',
        'address',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMoviment::class);
    }
   
   public function scopeFilter($query, QueryFilter $filters)
{
    return $filters->apply($query);
}

}