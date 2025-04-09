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
            $table->id('id_product');
            $table->string('name');
            $table->string('description');
            $table->decimal('price', 10, 2);
            $table->string('url_imagen');
            $table->foreignId('id_supplier')
                ->constrained('suppliers', 'id_supplier')
                ->onDelete('cascade');
            $table->foreignId('id_category')
                ->constrained('categories', 'id_category')
                ->onDelete('cascade');
            $table->timestamps();
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
