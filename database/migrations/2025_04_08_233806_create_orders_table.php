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
        Schema::create('orders', function (Blueprint $table) {
            $table->id('id_order');
            $table->foreignId('id_user')
                ->constrained('users', 'id_user')
                ->onDelete('cascade');
            $table->string('status');
            $table->decimal('total', 10, 2);
            $table->foreignId('id_discount')
                ->constrained('discounts', 'id_discount')
                ->onDelete('cascade');
            $table->foreignId('id_payment_type')
                ->constrained('payment_types', 'id_payment_type')
                ->onDelete('cascade');
            $table->decimal('discount_amount', 10, 2);
            $table->decimal('final_total', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
