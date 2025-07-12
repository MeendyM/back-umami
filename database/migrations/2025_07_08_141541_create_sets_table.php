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
        Schema::create('sets', function (Blueprint $table) {
            $table->id('id_set');
            $table->string('name')->unique(); // Evitar nombres duplicados
            $table->text('description')->nullable();
            $table->string('url_image')->nullable(); // Imagen del set
            $table->foreignId('id_discount')->nullable()->constrained('discounts', 'id_discount')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sets', function (Blueprint $table) {
            $table->dropConstrainedForeignId('id_discount');
        });

        Schema::dropIfExists('sets');
    }
};
