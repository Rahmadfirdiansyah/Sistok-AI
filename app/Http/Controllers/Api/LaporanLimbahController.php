<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BarangKeluar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LaporanLimbahController extends Controller
{
    public function index(Request $request)
    {
        $query = BarangKeluar::with(['barang.kategori', 'barang.satuan', 'lokasi', 'user'])
            ->where('jenis_keluar', 'limbah');

        if ($request->filled('search')) {
            $search = strtolower($request->search);
            $query->whereHas('barang', function ($q) use ($search) {
                $q->where(DB::raw('LOWER(nama)'), 'like', "%{$search}%")
                  ->orWhere(DB::raw('LOWER(kode)'), 'like', "%{$search}%");
            });
        }

        if ($request->filled('kategori')) {
            $kategori = $request->kategori;
            $query->whereHas('barang.kategori', fn($q) => $q->where('nama', $kategori));
        }

        $allLimbah = $query->orderBy('tanggal', 'desc')->get();

        // Group by barang_id for aggregated view
        $grouped = $allLimbah->groupBy('barang_id')->map(function ($items) {
            $first = $items->first();
            $barang = $first->barang;

            return [
                'barang_id'       => $first->barang_id,
                'kode'            => $barang->kode ?? '-',
                'nama'            => $barang->nama ?? 'Barang Dihapus',
                'kategori'        => $barang->kategori->nama ?? '-',
                'satuan'          => $barang->satuan->nama ?? 'unit',
                'total_limbah'    => $items->sum('jumlah'),
                'frekuensi'       => $items->count(),
                'terakhir_dibuang' => Carbon::parse($items->max('tanggal'))->format('d/m/Y'),
                'riwayat'         => $items->map(function ($i) {
                    return [
                        'id'          => 'TRX-OUT-' . str_pad($i->id, 3, '0', STR_PAD_LEFT),
                        'tanggal'     => Carbon::parse($i->tanggal)->format('d/m/Y'),
                        'jumlah'      => $i->jumlah,
                        'lokasi'      => $i->lokasi->nama ?? '-',
                        'dipakai_oleh'=> $i->dipakai_oleh,
                        'tujuan'      => $i->tujuan,
                        'keterangan'  => $i->keterangan ?? '-',
                        'petugas'     => $i->user->name ?? 'Sistem',
                    ];
                })->values(),
            ];
        })->values();

        $totalItemLimbah = $grouped->count();
        $totalVolumeLimbah = $grouped->sum('total_limbah');

        return response()->json([
            'summary' => [
                'total_item' => $totalItemLimbah,
                'total_volume' => $totalVolumeLimbah,
            ],
            'data' => $grouped,
        ]);
    }
}
