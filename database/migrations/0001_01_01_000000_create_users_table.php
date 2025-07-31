<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Executa a migration (criação das tabelas).
     */
    public function up(): void
    {
        // Tabela de usuários
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary(); // Chave primária UUID
            $table->string('name', 100);
            $table->string('email', 150)->unique();
            $table->timestampTz('email_verified_at')->nullable(); // Timestamp com fuso horário
            $table->string('password');
            $table->string('phone', 20)->nullable();
            $table->string('address', 255)->nullable();

            // Campo de função: apenas 'admin' ou 'user'
            $table->enum('role', ['admin', 'user'])->default('user');

            // Status do usuário com valor padrão 'pending'
            $table->enum('status', ['active', 'inactive', 'pending'])->default('pending');

            $table->rememberToken();
            $table->timestampsTz();    // created_at e updated_at com timezone
            $table->softDeletesTz();   // deleted_at com timezone
        });

        // Tabela de redefinição de senha
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email', 255)->primary();
            $table->string('token');
            $table->timestampTz('created_at')->nullable();
        });

        // Tabela de sessões
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->uuid('user_id')->nullable()->index(); // Ajustado para UUID se referir ao campo da tabela users
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverte a migration (exclusão das tabelas).
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
