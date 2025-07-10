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
        Schema::create('receips', function (Blueprint $table) {
            $table->id('id_receip');
            $table->foreignId('id_order')->nullable()->constrained('orders', 'id_order')->nullOnDelete();
            $table->foreignId('id_user')->nullable()->constrained('users', 'id_user')->nullOnDelete();
            $table->decimal('amount', 10, 2);
            $table->string('id_transaction');
            $table->string('url_img');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('receips', function (Blueprint $table) {
            $table->dropConstrainedForeignId('id_order');
            $table->dropConstrainedForeignId('id_user');
        });
        Schema::dropIfExists('receips');
    }
};
