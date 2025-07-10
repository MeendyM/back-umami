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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id('id_order_item');
            $table->foreignId('id_order')->nullable()->constrained('orders', 'id_order')->nullOnDelete();
            $table->foreignId('id_product')->nullable()->constrained('products', 'id_product')->nullOnDelete();
            $table->decimal('quantity', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->boolean('is_customized')->default(false)->nullable();
            $table->string('custom_text')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {   
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('id_order');
            $table->dropConstrainedForeignId('id_product');
        });
        Schema::dropIfExists('order_items');
    }
};
