<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Renombrar la columna set_id a id_set
        Schema::table('order_items', function (Blueprint $table) {
            if (Schema::hasColumn('order_items', 'set_id')) {
                $table->renameColumn('set_id', 'id_set');
            }
        });

        // Agregar nuevos campos relacionados con proveedor
        Schema::table('order_items', function (Blueprint $table) {
            $table->boolean('only_in_set')->default(false)->after('id_set');
            $table->date('supplier_order_date')->nullable()->after('only_in_set');
            $table->string('supplier_status', 50)->default('not_ordered')->after('supplier_order_date');
        });
    }

    public function down(): void
    {
        // Revertir nuevos campos
        Schema::table('order_items', function (Blueprint $table) {
            if (Schema::hasColumn('order_items', 'supplier_status')) {
                $table->dropColumn('supplier_status');
            }
            if (Schema::hasColumn('order_items', 'supplier_order_date')) {
                $table->dropColumn('supplier_order_date');
            }
            if (Schema::hasColumn('order_items', 'only_in_set')) {
                $table->dropColumn('only_in_set');
            }
        });

        // Renombrar de vuelta id_set a set_id
        Schema::table('order_items', function (Blueprint $table) {
            if (Schema::hasColumn('order_items', 'id_set')) {
                $table->renameColumn('id_set', 'set_id');
            }
        });
    }
};
