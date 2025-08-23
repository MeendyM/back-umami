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
        Schema::table('sets', function (Blueprint $table) {
            $table->boolean('is_deleted')->default(false);
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->boolean('is_deleted')->default(false);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->boolean('is_deleted')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sets', function (Blueprint $table) {
            $table->dropColumn('is_deleted');
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn('is_deleted');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('is_deleted');
        });
    }
};
