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
        Schema::create('discount_uses', function (Blueprint $table) {
            $table->id('id_discount_use');
            $table->foreignId('id_discount')->nullable()->constrained('discounts', 'id_discount')->nullOnDelete();
            $table->foreignId('id_order')->nullable()->constrained('orders', 'id_order')->nullOnDelete();
            $table->foreignId('id_user')->nullable()->constrained('users', 'id_user')->nullOnDelete();
            $table->date('use_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discount_uses');
    }
};
