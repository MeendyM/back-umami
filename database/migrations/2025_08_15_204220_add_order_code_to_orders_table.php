<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Order;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Verificar si la columna ya existe
        if (!Schema::hasColumn('orders', 'order_code')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->string('order_code', 5)->nullable()->after('id_order');
            });
        }

        // Generar códigos para órdenes existentes
        $this->generateOrderCodesForExistingOrders();

        // Agregar índice único si no existe
        if (!$this->hasUniqueIndex('orders', 'order_code')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->unique('order_code');
            });
        }

        // Hacer el campo no nullable después de generar códigos
        Schema::table('orders', function (Blueprint $table) {
            $table->string('order_code', 5)->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('order_code');
        });
    }

    /**
     * Generar códigos únicos para órdenes existentes
     */
    private function generateOrderCodesForExistingOrders(): void
    {
        $orders = Order::whereNull('order_code')->get();
        
        foreach ($orders as $order) {
            $order->order_code = $this->generateUniqueOrderCode();
            $order->save();
        }
    }

    /**
     * Generar un código único de 5 caracteres
     */
    private function generateUniqueOrderCode(): string
    {
        do {
            $code = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 5));
        } while (Order::where('order_code', $code)->exists());

        return $code;
    }

    /**
     * Verificar si existe un índice único en una columna
     */
    private function hasUniqueIndex(string $table, string $column): bool
    {
        $indexes = DB::select("SHOW INDEX FROM {$table} WHERE Column_name = ? AND Non_unique = 0", [$column]);
        return count($indexes) > 0;
    }
};
