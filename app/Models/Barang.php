<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Support\Facades\DB;

#[Fillable(['kode', 'nama', 'kategori_id', 'lokasi_id', 'satuan_id', 'stok', 'min_stok', 'gambar', 'user_id'])]
class Barang extends Model
{
    protected static function booted()
    {
        static::creating(function ($barang) {
            // Hanya buat otomatis jika kode belum diisi (misal saat seeder berjalan)
            if (empty($barang->kode)) {
                $nextId = 1;
                try {
                    // Bypass cache stats MySQL 8.0 agar selalu real-time
                    try {
                        DB::statement("SET SESSION information_schema_stats_expiry = 0");
                    } catch (\Exception $e) {}

                    // Dapatkan nilai AUTO_INCREMENT terbaru dari MySQL agar nomor tidak dipakai ulang meski data terakhir dihapus
                    $status = DB::select("SHOW TABLE STATUS LIKE 'barangs'");
                    if (!empty($status)) {
                        $nextId = $status[0]->Auto_increment;
                    }
                } catch (\Exception $e) {
                    // Fallback aman jika driver database bukan MySQL
                    $maxId = DB::table('barangs')->max('id');
                    $nextId = $maxId ? $maxId + 1 : 1;
                }
                $barang->kode = 'BRG-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }

    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class);
    }

    public function satuan()
    {
        return $this->belongsTo(Satuan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
