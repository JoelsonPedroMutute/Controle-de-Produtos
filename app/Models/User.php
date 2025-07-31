<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
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
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * Atributos que podem ser preenchidos via mass assignment.
     * Protege contra falhas de segurança ao usar create() ou update().
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'phone',
        'address',
    ];

   /**
     * Atributos que devem ser ocultados ao serializar (ex: JSON).
     * Protege dados sensíveis como senhas.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

   /**
     * Converte atributos para tipos nativos.
     * O campo 'email_verified_at' é tratado como datetime.
     * O campo 'password' será automaticamente criptografado ao ser salvo.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    /**
     * Verifica se o usuário é um administrador.
     * Retorna true se o papel do usuário for 'admin', false caso contrário.
     */ 
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }


    /**
     * Relacionamento com o modelo stockMovements.
     * Um usuário pode ter muitos stockMovements associados.
     */
    public function stockMovements()
    {
        return $this->hasMany(StockMoviment::class);
    }
}
