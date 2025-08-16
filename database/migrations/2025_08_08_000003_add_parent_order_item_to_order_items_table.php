<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            // OrderItem padre al que pertenece (solo para items de producto que pertenecen a un set)
            $table->foreignId('id_parent_order_item')
                ->nullable()
                ->after('id_set')
                ->constrained('order_items', 'id_order_item')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            // Eliminar la FK y la columna
            if (Schema::hasColumn('order_items', 'id_parent_order_item')) {
                $table->dropForeign(['id_parent_order_item']);
                $table->dropColumn('id_parent_order_item');
            }
        });
    }
};
