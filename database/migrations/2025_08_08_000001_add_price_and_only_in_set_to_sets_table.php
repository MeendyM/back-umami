<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('sets', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->nullable()->after('id_discount');
            $table->boolean('only_in_set')->default(false)->after('price');
        });
    }

    public function down(): void
    {
        Schema::table('sets', function (Blueprint $table) {
            $table->dropColumn(['price', 'only_in_set']);
        });
    }
};
