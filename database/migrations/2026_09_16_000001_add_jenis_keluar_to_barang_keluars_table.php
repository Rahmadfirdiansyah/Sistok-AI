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
        Schema::table('barang_keluars', function (Blueprint $table) {
            if (!Schema::hasColumn('barang_keluars', 'jenis_keluar')) {
                $table->string('jenis_keluar')->default('pemakaian')->after('tujuan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barang_keluars', function (Blueprint $table) {
            if (Schema::hasColumn('barang_keluars', 'jenis_keluar')) {
                $table->dropColumn('jenis_keluar');
            }
        });
    }
};
