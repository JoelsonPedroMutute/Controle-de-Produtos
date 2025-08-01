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
      Schema::create('products', function (Blueprint $table) {
        $table->uuid('id')->primary(); // Ok
        $table->string('name');
        $table->text('description')->nullable();
        $table->decimal('price', 10, 2);
        $table->string('sku', 50)->unique();
        $table->enum('status', ['active', 'inactive', 'discontinued'])->default('active');
        $table->unsignedBigInteger('category_id')->nullable();

        $table->timestampsTz();
        $table->softDeletes();

        $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
