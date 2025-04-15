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
        Schema::create('roles', function (Blueprint $table) {
            $table->id('id_rol');
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('institutions', function (Blueprint $table) {
            $table->id('id_institution');
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('discounts', function (Blueprint $table) {
            $table->id('id_discount');
            $table->foreignId('id_institution')
                ->constrained('institutions', 'id_institution')
                ->onDelete('restrict');
            $table->string('code');
            $table->string('discount_type');
            $table->string('discount_value');
            $table->string('max_uses');
            $table->date('expires_at');
            $table->timestamps();
        });

        Schema::create('suppliers', function (Blueprint $table) {
            $table->id('id_supplier');
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id('id_category');
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('payment_types', function (Blueprint $table) {
            $table->id('id_payment_type');
            $table->string('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles_intitutions_suppliers_tables');
    }
};
