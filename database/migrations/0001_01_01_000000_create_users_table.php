<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tabela de usuários
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary(); // UUID como chave primáriia
            $table->string('name', 100);
            $table->string('email', 150)->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password', 255);
            $table->string('phone', 20)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('role', 20)->default('user'); // Papel do usuário, padrão é 'user'
            $table->enum('status', ['active', 'inactive', 'peding'])->default('peding'); // Status do usuário
            $table->rememberToken();
            $table->timestampsTz();
            $table->softDeletesTz(); // Adiciona suporte a soft deletes com fuso horário
        });
        // Tabela de tokens de redefinição de senha
        Schema::create('password_reset_tokens', function (Blueprint $table) {
           $table->string('email', 255)->primary(); // tamanho fixado para evitar erro no PostgreSQL
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
            // Tabela de sessões
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
