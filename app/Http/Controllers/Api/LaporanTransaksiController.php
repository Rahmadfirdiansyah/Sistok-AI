<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use Illuminate\Http\Request;

class LaporanTransaksiController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $kategori = $request->query('kategori');
        $lokasi = $request->query('lokasi');
        $jenis = $request->query('jenis'); // 'masuk', 'keluar', or empty for all

        $transaksi = collect();

        // 1. Ambil Barang Masuk
        if (empty($jenis) || $jenis === 'masuk') {
            $qMasuk = BarangMasuk::with(['barang.kategori', 'barang.satuan', 'lokasi', 'user']);
            
            if ($startDate) $qMasuk->whereDate('tanggal', '>=', $startDate);
            if ($endDate) $qMasuk->whereDate('tanggal', '<=', $endDate);
            if ($kategori) $qMasuk->whereHas('barang.kategori', fn($q) => $q->where('nama', $kategori));
            if ($lokasi) $qMasuk->whereHas('lokasi', fn($q) => $q->where('nama', $lokasi));

            $masuk = $qMasuk->get()->map(function ($m) {
                return [
                    'id' => 'TRX-IN-' . $m->id,
                    'jenis' => 'masuk',
                    'tanggal' => $m->tanggal,
                    'barang' => $m->barang->nama ?? '-',
                    'kategori' => $m->barang->kategori->nama ?? '-',
                    'lokasi' => $m->lokasi->nama ?? '-',
                    'qty' => $m->jumlah,
                    'satuan' => $m->barang->satuan->nama ?? 'pcs',
                    'keterangan' => $m->keterangan ?? '-',
                    'oleh_tujuan' => 'Supplier', // Untuk barang masuk, umumnya dari supplier
                    'created_at' => $m->created_at,
                    'petugas' => $m->user->name ?? 'Sistem',
                ];
            });
            $transaksi = $transaksi->concat($masuk);
        }

        // 2. Ambil Barang Keluar
        if (empty($jenis) || $jenis === 'keluar') {
            $qKeluar = BarangKeluar::with(['barang.kategori', 'barang.satuan', 'lokasi', 'user']);
            
            if ($startDate) $qKeluar->whereDate('tanggal', '>=', $startDate);
            if ($endDate) $qKeluar->whereDate('tanggal', '<=', $endDate);
            if ($kategori) $qKeluar->whereHas('barang.kategori', fn($q) => $q->where('nama', $kategori));
            if ($lokasi) $qKeluar->whereHas('lokasi', fn($q) => $q->where('nama', $lokasi));

            $keluar = $qKeluar->get()->map(function ($k) {
                return [
                    'id' => 'TRX-OUT-' . $k->id,
                    'jenis' => 'keluar',
                    'tanggal' => $k->tanggal,
                    'barang' => $k->barang->nama ?? '-',
                    'kategori' => $k->barang->kategori->nama ?? '-',
                    'lokasi' => $k->lokasi->nama ?? '-',
                    'qty' => $k->jumlah,
                    'satuan' => $k->barang->satuan->nama ?? 'pcs',
                    'keterangan' => $k->keterangan ?? '-',
                    'oleh_tujuan' => $k->dipakai_oleh . ($k->tujuan ? ' (' . $k->tujuan . ')' : ''),
                    'created_at' => $k->created_at,
                    'petugas' => $k->user->name ?? 'Sistem',
                ];
            });
            $transaksi = $transaksi->concat($keluar);
        }

        // 3. Urutkan berdasarkan tanggal (terbaru) lalu created_at
        $sorted = $transaksi->sortByDesc(function ($item) {
            return $item['tanggal'] . ' ' . ($item['created_at'] ? $item['created_at']->format('H:i:s') : '00:00:00');
        })->values();

        return response()->json($sorted);
    }
}
