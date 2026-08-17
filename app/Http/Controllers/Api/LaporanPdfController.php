<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LaporanPdfController extends Controller
{
    /**
     * Cetak PDF Laporan Stok Barang
     */
    public function stok(Request $request)
    {
        $query = Barang::with(['kategori', 'lokasi', 'satuan']);

        if ($request->filled('kategori')) {
            $query->whereHas('kategori', fn($q) => $q->where('nama', $request->kategori));
        }

        if ($request->filled('status')) {
            $status = $request->status;
            if ($status === 'kritis') {
                $query->whereRaw('stok < min_stok / 2');
            } elseif ($status === 'rendah') {
                $query->whereRaw('stok >= min_stok / 2 AND stok < min_stok');
            } elseif ($status === 'aman') {
                $query->whereRaw('stok >= min_stok');
            }
        }

        $barangs = $query->orderBy('kode')->get()->map(function ($b) {
            $statusKey = $b->stok < $b->min_stok / 2 ? 'Kritis' : ($b->stok < $b->min_stok ? 'Rendah' : 'Aman');
            return [
                'kode'     => $b->kode,
                'nama'     => $b->nama,
                'kategori' => $b->kategori->nama ?? '-',
                'lokasi'   => $b->lokasi->nama ?? '-',
                'satuan'   => $b->satuan->nama ?? '-',
                'min_stok' => $b->min_stok,
                'stok'     => $b->stok,
                'status'   => $statusKey,
            ];
        });

        $pdf = Pdf::loadView('laporan.stok', [
            'barangs'   => $barangs,
            'tanggal'   => Carbon::now()->translatedFormat('d F Y'),
            'waktu'     => Carbon::now()->format('H:i'),
            'filter'    => [
                'kategori' => $request->kategori ?? 'Semua',
                'status'   => $request->status ?? 'Semua',
            ],
        ]);

        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('laporan-stok-' . date('Ymd') . '.pdf');
    }

    /**
     * Cetak PDF Laporan Barang Masuk
     */
    public function masuk(Request $request)
    {
        $query = BarangMasuk::with(['barang.satuan', 'lokasi']);

        if ($request->filled('start_date')) {
            $query->whereDate('tanggal', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('tanggal', '<=', $request->end_date);
        }
        if ($request->filled('lokasi')) {
            $query->whereHas('lokasi', fn($q) => $q->where('nama', $request->lokasi));
        }

        $transaksis = $query->orderBy('tanggal', 'desc')->get()->map(function ($m) {
            return [
                'id'         => 'TRX-IN-' . str_pad($m->id, 3, '0', STR_PAD_LEFT),
                'tanggal'    => Carbon::parse($m->tanggal)->format('d/m/Y'),
                'barang'     => $m->barang->nama ?? '-',
                'kode'       => $m->barang->kode ?? '-',
                'lokasi'     => $m->lokasi->nama ?? '-',
                'jumlah'     => $m->jumlah,
                'satuan'     => $m->barang->satuan->nama ?? 'pcs',
                'keterangan' => $m->keterangan ?? '-',
            ];
        });

        $totalQty = $transaksis->sum('jumlah');

        $pdf = Pdf::loadView('laporan.masuk', [
            'transaksis' => $transaksis,
            'totalQty'   => $totalQty,
            'tanggal'    => Carbon::now()->translatedFormat('d F Y'),
            'waktu'      => Carbon::now()->format('H:i'),
            'filter'     => [
                'start_date' => $request->start_date ? Carbon::parse($request->start_date)->format('d/m/Y') : '-',
                'end_date'   => $request->end_date ? Carbon::parse($request->end_date)->format('d/m/Y') : '-',
                'lokasi'     => $request->lokasi ?? 'Semua',
            ],
        ]);

        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('laporan-masuk-' . date('Ymd') . '.pdf');
    }

    /**
     * Cetak PDF Laporan Barang Keluar
     */
    public function keluar(Request $request)
    {
        $query = BarangKeluar::with(['barang.satuan', 'lokasi']);

        if ($request->filled('start_date')) {
            $query->whereDate('tanggal', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('tanggal', '<=', $request->end_date);
        }
        if ($request->filled('lokasi')) {
            $query->whereHas('lokasi', fn($q) => $q->where('nama', $request->lokasi));
        }

        $transaksis = $query->orderBy('tanggal', 'desc')->get()->map(function ($k) {
            return [
                'id'           => 'TRX-OUT-' . str_pad($k->id, 3, '0', STR_PAD_LEFT),
                'tanggal'      => Carbon::parse($k->tanggal)->format('d/m/Y'),
                'barang'       => $k->barang->nama ?? '-',
                'kode'         => $k->barang->kode ?? '-',
                'dipakai_oleh' => $k->dipakai_oleh,
                'tujuan'       => $k->tujuan,
                'lokasi'       => $k->lokasi->nama ?? '-',
                'jumlah'       => $k->jumlah,
                'satuan'       => $k->barang->satuan->nama ?? 'pcs',
                'keterangan'   => $k->keterangan ?? '-',
            ];
        });

        $totalQty = $transaksis->sum('jumlah');

        $pdf = Pdf::loadView('laporan.keluar', [
            'transaksis' => $transaksis,
            'totalQty'   => $totalQty,
            'tanggal'    => Carbon::now()->translatedFormat('d F Y'),
            'waktu'      => Carbon::now()->format('H:i'),
            'filter'     => [
                'start_date' => $request->start_date ? Carbon::parse($request->start_date)->format('d/m/Y') : '-',
                'end_date'   => $request->end_date ? Carbon::parse($request->end_date)->format('d/m/Y') : '-',
                'lokasi'     => $request->lokasi ?? 'Semua',
            ],
        ]);

        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('laporan-keluar-' . date('Ymd') . '.pdf');
    }

    /**
     * Cetak PDF Laporan Riwayat Transaksi (Gabungan Masuk & Keluar)
     */
    public function riwayatTransaksi(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $kategori = $request->query('kategori');
        $lokasi = $request->query('lokasi');
        $jenis = $request->query('jenis');

        $transaksi = collect();

        if (empty($jenis) || $jenis === 'masuk') {
            $qMasuk = BarangMasuk::with(['barang.kategori', 'barang.satuan', 'lokasi']);
            if ($startDate) $qMasuk->whereDate('tanggal', '>=', $startDate);
            if ($endDate) $qMasuk->whereDate('tanggal', '<=', $endDate);
            if ($kategori) $qMasuk->whereHas('barang.kategori', fn($q) => $q->where('nama', $kategori));
            if ($lokasi) $qMasuk->whereHas('lokasi', fn($q) => $q->where('nama', $lokasi));

            $masuk = $qMasuk->get()->map(function ($m) {
                return [
                    'id' => 'TRX-IN-' . str_pad($m->id, 3, '0', STR_PAD_LEFT),
                    'jenis' => 'MASUK',
                    'tanggal' => Carbon::parse($m->tanggal)->format('d/m/Y'),
                    'tanggal_raw' => $m->tanggal,
                    'barang' => $m->barang->nama ?? '-',
                    'kategori' => $m->barang->kategori->nama ?? '-',
                    'lokasi' => $m->lokasi->nama ?? '-',
                    'qty' => $m->jumlah,
                    'satuan' => $m->barang->satuan->nama ?? 'pcs',
                    'keterangan' => $m->keterangan ?? '-',
                    'oleh_tujuan' => 'Supplier',
                    'created_at' => $m->created_at,
                ];
            });
            $transaksi = $transaksi->concat($masuk);
        }

        if (empty($jenis) || $jenis === 'keluar') {
            $qKeluar = BarangKeluar::with(['barang.kategori', 'barang.satuan', 'lokasi']);
            if ($startDate) $qKeluar->whereDate('tanggal', '>=', $startDate);
            if ($endDate) $qKeluar->whereDate('tanggal', '<=', $endDate);
            if ($kategori) $qKeluar->whereHas('barang.kategori', fn($q) => $q->where('nama', $kategori));
            if ($lokasi) $qKeluar->whereHas('lokasi', fn($q) => $q->where('nama', $lokasi));

            $keluar = $qKeluar->get()->map(function ($k) {
                return [
                    'id' => 'TRX-OUT-' . str_pad($k->id, 3, '0', STR_PAD_LEFT),
                    'jenis' => 'KELUAR',
                    'tanggal' => Carbon::parse($k->tanggal)->format('d/m/Y'),
                    'tanggal_raw' => $k->tanggal,
                    'barang' => $k->barang->nama ?? '-',
                    'kategori' => $k->barang->kategori->nama ?? '-',
                    'lokasi' => $k->lokasi->nama ?? '-',
                    'qty' => $k->jumlah,
                    'satuan' => $k->barang->satuan->nama ?? 'pcs',
                    'keterangan' => $k->keterangan ?? '-',
                    'oleh_tujuan' => $k->dipakai_oleh . ($k->tujuan ? ' (' . $k->tujuan . ')' : ''),
                    'created_at' => $k->created_at,
                ];
            });
            $transaksi = $transaksi->concat($keluar);
        }

        $sorted = $transaksi->sortByDesc(function ($item) {
            return $item['tanggal_raw'] . ' ' . ($item['created_at'] ? $item['created_at']->format('H:i:s') : '00:00:00');
        })->values();

        $pdf = Pdf::loadView('laporan.riwayat-transaksi', [
            'transaksis' => $sorted,
            'tanggal'    => Carbon::now()->translatedFormat('d F Y'),
            'waktu'      => Carbon::now()->format('H:i'),
            'filter'     => [
                'start_date' => $startDate ? Carbon::parse($startDate)->format('d/m/Y') : '-',
                'end_date'   => $endDate ? Carbon::parse($endDate)->format('d/m/Y') : '-',
                'kategori'   => $kategori ?? 'Semua',
                'lokasi'     => $lokasi ?? 'Semua',
                'jenis'      => $jenis ? strtoupper($jenis) : 'SEMUA TRANSAKSI',
            ],
        ]);

        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('laporan-riwayat-transaksi-' . date('Ymd') . '.pdf');
    }
}
