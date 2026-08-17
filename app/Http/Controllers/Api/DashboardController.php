<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today()->toDateString();
        $startOfMonth = Carbon::now()->startOfMonth()->toDateString();

        // 1. Calculations for stats
        $totalBarang = Barang::count();
        $barangMasukToday = BarangMasuk::whereDate('tanggal', $today)->sum('jumlah');
        $barangKeluarToday = BarangKeluar::whereDate('tanggal', $today)->sum('jumlah');
        $stokKritisCount = Barang::whereColumn('stok', '<', 'min_stok')->count();

        // Changes
        $newBarangsThisMonth = Barang::whereDate('created_at', '>=', $startOfMonth)->count();
        $masukThisMonth = BarangMasuk::whereDate('tanggal', '>=', $startOfMonth)->count();
        $keluarThisMonth = BarangKeluar::whereDate('tanggal', '>=', $startOfMonth)->count();

        // 2. Low stock list
        $lowStock = Barang::with(['kategori', 'lokasi', 'satuan'])
            ->whereColumn('stok', '<', 'min_stok')
            ->orderBy('stok', 'asc')
            ->limit(5)
            ->get()
            ->map(function ($b) {
                return [
                    'name' => $b->nama,
                    'cat' => $b->kategori->nama ?? '',
                    'lokasi' => $b->lokasi->nama ?? '',
                    'stok' => $b->stok,
                    'unit' => $b->satuan->nama ?? 'pcs'
                ];
            });

        // 3. Raw Data untuk Masuk & Keluar Terbaru
        $rawMasuk = BarangMasuk::with(['barang.satuan'])
            ->orderBy('id', 'desc')
            ->limit(5)
            ->get();

        $rawKeluar = BarangKeluar::with(['barang.satuan'])
            ->orderBy('id', 'desc')
            ->limit(5)
            ->get();

        // 4. Map untuk tampilan dashboard terbaru
        $masukTerbaru = $rawMasuk->map(function ($m) {
            $tanggal = Carbon::parse($m->tanggal);
            $dateStr = $tanggal->isToday() ? 'Hari ini' : ($tanggal->isYesterday() ? 'Kemarin' : $tanggal->translatedFormat('d M'));
            return [
                'id' => $m->id,
                'barang' => $m->barang->nama ?? '',
                'tanggal' => $dateStr,
                'qty' => $m->jumlah . ' ' . ($m->barang->satuan->nama ?? 'pcs')
            ];
        });

        $keluarTerbaru = $rawKeluar->map(function ($k) {
            $tanggal = Carbon::parse($k->tanggal);
            $dateStr = $tanggal->isToday() ? 'Hari ini' : ($tanggal->isYesterday() ? 'Kemarin' : $tanggal->translatedFormat('d M'));
            return [
                'id' => $k->id,
                'barang' => $k->barang->nama ?? '',
                'tujuan' => $k->tujuan,
                'tanggal' => $dateStr,
                'qty' => $k->jumlah . ' ' . ($k->barang->satuan->nama ?? 'pcs')
            ];
        });

        // 5. Activity Feed (Merged newest entries)
        $activity = collect();

        foreach ($rawMasuk as $m) {
            $activity->push([
                'id' => 'm_' . $m->id,
                'bg' => 'bg-green-50',
                'color' => 'text-green-505', // matches green theme color or text-green-500
                'text' => "{$m->jumlah} unit " . ($m->barang->nama ?? 'barang') . " masuk",
                'time' => $m->created_at ? $m->created_at->diffForHumans() : Carbon::parse($m->tanggal)->diffForHumans(),
                'timestamp' => $m->created_at ? $m->created_at->timestamp : Carbon::parse($m->tanggal)->timestamp,
                'path' => '<polyline points="20 6 9 17 4 12"/>'
            ]);
        }

        foreach ($rawKeluar as $k) {
            $activity->push([
                'id' => 'k_' . $k->id,
                'bg' => 'bg-orange-50',
                'color' => 'text-orange-500',
                'text' => "{$k->jumlah} unit " . ($k->barang->nama ?? 'barang') . " keluar ke " . ($k->tujuan ?? 'Tujuan'),
                'time' => $k->created_at ? $k->created_at->diffForHumans() : Carbon::parse($k->tanggal)->diffForHumans(),
                'timestamp' => $k->created_at ? $k->created_at->timestamp : Carbon::parse($k->tanggal)->timestamp,
                'path' => '<line x1="12" y1="19" x2="12" y2="5"/><polyline points="5 12 12 5 19 12"/>'
            ]);
        }

        // Sort merged activity by timestamp descending
        $activity = $activity->sortByDesc('timestamp')->values()->take(5);

        return response()->json([
            'totalBarang' => $totalBarang,
            'totalMasukToday' => $barangMasukToday,
            'totalKeluarToday' => $barangKeluarToday,
            'stokKritisCount' => $stokKritisCount,
            'newBarangsThisMonth' => $newBarangsThisMonth,
            'masukThisMonth' => $masukThisMonth,
            'keluarThisMonth' => $keluarThisMonth,
            'lowStock' => $lowStock,
            'masuk' => $masukTerbaru,
            'keluar' => $keluarTerbaru,
            'activity' => $activity
        ]);
    }
}
