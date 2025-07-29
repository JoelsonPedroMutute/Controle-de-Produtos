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
        Schema::create('_stock_moviment', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_id');
            $table->uuid('product_id');
            $table->enum('type', ['in', 'out','adjustment']);
            $table->string('reason', 100)->nullable();
            $table->decimal('quantity', 10, 2);
            $table->timestampTz('created_at')->useCurrent();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('_stock_moviment');
    }
};
