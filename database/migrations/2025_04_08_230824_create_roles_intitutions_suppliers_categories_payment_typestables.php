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


        Schema::create('institutions', function (Blueprint $table) {
            $table->id('id_institution');
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('discounts', function (Blueprint $table) {
            $table->id('id_discount');
            $table->string('code')->unique(); // Para evitar cupones duplicados
            $table->string('type')->nullable(); // se conectara al enum de TypeDiscount
            $table->decimal('value', 5, 2); // Ej: 15.00 (% o monto fijo)
            $table->string('minimum_purchase')->nullable(); // minimo de compra dejar coomo nulo
            $table->unsignedInteger('max_uses')->nullable(); // NULL = ilimitado
            $table->dateTime('expires_at')->nullable(); // NULL = sin expiración
            $table->timestamps();
        });

        Schema::create('suppliers', function (Blueprint $table) {
            $table->id('id_supplier');
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id('id_category');
            $table->string('name')->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discounts');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('institutions');
        Schema::dropIfExists('categories');
    }
};
