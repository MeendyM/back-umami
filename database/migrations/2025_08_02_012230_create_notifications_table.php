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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id('id_notification');
            $table->string('title'); // Título de la notificación
            $table->text('message'); // Mensaje de la notificación
            $table->enum('target_type', ['all', 'student', 'client']); // A quién va dirigida
            $table->unsignedBigInteger('target_institution_id')->nullable(); // Institución específica (null = todas)
            $table->unsignedBigInteger('sender_id'); // Usuario que envía (admin)
            $table->timestamp('sent_at')->nullable(); // Cuándo fue enviada
            $table->boolean('is_sent')->default(false); // Si ya fue enviada
            $table->timestamps();

            // Índices y foreign keys
            $table->foreign('target_institution_id')->references('id_institution')->on('institutions')->onDelete('cascade');
            $table->foreign('sender_id')->references('id_user')->on('users')->onDelete('cascade');
            
            // Índices para mejor rendimiento
            $table->index(['target_type', 'target_institution_id']);
            $table->index(['is_sent', 'sent_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
