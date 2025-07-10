<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use phpDocumentor\Reflection\Types\Nullable;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id('id_order');
            $table->foreignId('id_user')->nullable()->constrained('users', 'id_user')->nullOnDelete();
            $table->string('status');
            $table->decimal('total', 10, 2)->nullable();
            $table->foreignId('id_discount')->nullable()->constrained('discounts', 'id_discount')->nullOnDelete();
            $table->string('payment_type')->nullable();
            $table->decimal('discount_amount', 10, 2)->nullable();
            $table->decimal('final_total', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {   
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('id_user');
            $table->dropConstrainedForeignId('id_discount');

        });
        Schema::dropIfExists('orders');
    }
};
