<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('sets', function (Blueprint $table) {
            $table->unsignedBigInteger('id_supplier')->nullable()->after('only_in_set');
            $table->foreign('id_supplier')->references('id_supplier')->on('suppliers')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('sets', function (Blueprint $table) {
            $table->dropForeign(['id_supplier']);
            $table->dropColumn('id_supplier');
        });
    }
};
