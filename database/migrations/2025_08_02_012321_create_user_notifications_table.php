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
        Schema::create('user_notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('notification_id');
            $table->timestamp('read_at')->nullable(); // Cuándo fue leída
            $table->timestamps();

            // Foreign keys
            $table->foreign('user_id')->references('id_user')->on('users')->onDelete('cascade');
            $table->foreign('notification_id')->references('id_notification')->on('notifications')->onDelete('cascade');
            
            // Índice único para evitar duplicados
            $table->unique(['user_id', 'notification_id']);
            
            // Índices para mejor rendimiento
            $table->index(['user_id', 'read_at']);
            $table->index('notification_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_notifications');
    }
};
