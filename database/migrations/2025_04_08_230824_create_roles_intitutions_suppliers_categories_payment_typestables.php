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
            $table->string('code');
            $table->string('type');
            $table->string('value');
            $table->string('max_uses');
            $table->date('expires_at');
            $table->timestamps();
        });

        Schema::create('suppliers', function (Blueprint $table) {
            $table->id('id_supplier');
            $table->string('name');
            $table->timestamps();
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
