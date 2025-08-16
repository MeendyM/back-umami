<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up()
    {
        // Cambiar los valores existentes en la columna status de recibos a inglés
        DB::table('receips')->where('status', 'aprobado')->update(['status' => 'approved']);
        DB::table('receips')->where('status', 'revisar')->update(['status' => 'review']);
        DB::table('receips')->where('status', 'enviado')->update(['status' => 'sent']);
        DB::table('receips')->where('status', 'reenviado')->update(['status' => 'resent']);
    }

    public function down()
    {
        // Revertir los valores a español
        DB::table('receips')->where('status', 'approved')->update(['status' => 'aprobado']);
        DB::table('receips')->where('status', 'review')->update(['status' => 'revisar']);
        DB::table('receips')->where('status', 'sent')->update(['status' => 'enviado']);
        DB::table('receips')->where('status', 'resent')->update(['status' => 'reenviado']);
    }
};
