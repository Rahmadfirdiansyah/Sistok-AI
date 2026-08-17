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
        Schema::table('barangs', function (Blueprint $table) {
            $table->index('nama');
        });

        Schema::table('barang_masuks', function (Blueprint $table) {
            $table->index('tanggal');
        });

        Schema::table('barang_keluars', function (Blueprint $table) {
            $table->index('tanggal');
            $table->index('dipakai_oleh');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barangs', function (Blueprint $table) {
            $table->dropIndex(['nama']);
        });

        Schema::table('barang_masuks', function (Blueprint $table) {
            $table->dropIndex(['tanggal']);
        });

        Schema::table('barang_keluars', function (Blueprint $table) {
            $table->dropIndex(['tanggal']);
            $table->dropIndex(['dipakai_oleh']);
        });
    }
};
