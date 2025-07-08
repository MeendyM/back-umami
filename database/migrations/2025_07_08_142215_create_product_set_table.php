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
        Schema::create('product_set', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_set')->nullable()->constrained('sets', 'id_set')->nullOnDelete();
            $table->foreignId('id_product')->nullable()->constrained('products', 'id_product')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_set', function (Blueprint $table) {
            $table->dropForeign(['id_set']);
            $table->dropForeign(['id_product']);
        });

        Schema::dropIfExists('product_set');
    }
};
