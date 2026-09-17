<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['tanggal', 'barang_id', 'lokasi_id', 'jumlah', 'dipakai_oleh', 'tujuan', 'jenis_keluar', 'keterangan', 'user_id'])]
class BarangKeluar extends Model
{
    public function barang()
    {
        return $this->belongsTo(Barang::class);
    }

    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
