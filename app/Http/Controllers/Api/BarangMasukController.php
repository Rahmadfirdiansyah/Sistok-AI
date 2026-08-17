<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\Lokasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BarangMasukController extends Controller
{
    public function index()
    {
        $transaksis = BarangMasuk::with(['barang.satuan', 'lokasi', 'user'])
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($t) {
                return $this->mapSingleTransaksi($t);
            });

        return response()->json($transaksis);
    }

    public function store(Request $request)
    {
        if ($request->user() && $request->user()->role === 'user') {
            return response()->json(['message' => 'Aksi ditolak. Staff hanya memiliki akses lihat/view.'], 403);
        }

        $request->validate([
            'barang_id' => 'required|exists:barangs,id',
            'lokasi' => 'required|string',
            'qty' => 'required|integer|min:1',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($request) {
            $barang = Barang::findOrFail($request->barang_id);
            $lokasi = Lokasi::firstOrCreate(['nama' => $request->lokasi]);

            $transaksi = BarangMasuk::create([
                'tanggal' => $request->tanggal,
                'barang_id' => $barang->id,
                'lokasi_id' => $lokasi->id,
                'jumlah' => $request->qty,
                'keterangan' => $request->keterangan,
                'user_id' => auth()->id(),
            ]);

            // Adjust stock
            $barang->increment('stok', $request->qty);

            $loaded = BarangMasuk::with(['barang.satuan', 'lokasi', 'user'])->find($transaksi->id);
            return response()->json($this->mapSingleTransaksi($loaded), 201);
        });
    }

    public function show(BarangMasuk $barangMasuk)
    {
        $barangMasuk->load(['barang.satuan', 'lokasi', 'user']);
        return response()->json($this->mapSingleTransaksi($barangMasuk));
    }

    public function update(Request $request, $id)
    {
        if ($request->user() && $request->user()->role === 'user') {
            return response()->json(['message' => 'Aksi ditolak. Staff hanya memiliki akses lihat/view.'], 403);
        }

        $transaksi = BarangMasuk::with('barang')->findOrFail($id);

        $request->validate([
            'barang_id' => 'required|exists:barangs,id',
            'lokasi' => 'required|string',
            'qty' => 'required|integer|min:1',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($request, $transaksi) {
            $oldQty = $transaksi->jumlah;
            $oldBarang = $transaksi->barang;

            $barang = Barang::findOrFail($request->barang_id);
            $lokasi = Lokasi::firstOrCreate(['nama' => $request->lokasi]);

            $transaksi->update([
                'tanggal' => $request->tanggal,
                'barang_id' => $barang->id,
                'lokasi_id' => $lokasi->id,
                'jumlah' => $request->qty,
                'keterangan' => $request->keterangan,
            ]);

            // Adjust stock
            if ($oldBarang->id === $barang->id) {
                $diff = $request->qty - $oldQty;
                if ($diff > 0) {
                    $barang->increment('stok', $diff);
                } elseif ($diff < 0) {
                    $barang->decrement('stok', abs($diff));
                }
            } else {
                // Decrement old barang stock, increment new barang stock
                $oldBarang->decrement('stok', $oldQty);
                $barang->increment('stok', $request->qty);
            }

            $loaded = BarangMasuk::with(['barang.satuan', 'lokasi', 'user'])->find($transaksi->id);
            return response()->json($this->mapSingleTransaksi($loaded));
        });
    }

    public function destroy(Request $request, $id)
    {
        if ($request->user() && $request->user()->role === 'user') {
            return response()->json(['message' => 'Aksi ditolak. Staff hanya memiliki akses lihat/view.'], 403);
        }

        $transaksi = BarangMasuk::with('barang')->findOrFail($id);

        DB::transaction(function () use ($transaksi) {
            $barang = $transaksi->barang;
            if ($barang) {
                $barang->decrement('stok', $transaksi->jumlah);
            }
            $transaksi->delete();
        });

        return response()->json(['message' => 'Transaksi barang masuk berhasil dihapus']);
    }

    private function mapSingleTransaksi(BarangMasuk $t)
    {
        return [
            'id' => $t->id,
            'barang_id' => $t->barang_id,
            'barang' => $t->barang->nama ?? '',
            'kodeBarang' => $t->barang->kode ?? '',
            'lokasi' => $t->lokasi->nama ?? '',
            'qty' => $t->jumlah,
            'satuan' => $t->barang->satuan->nama ?? 'pcs',
            'tanggal' => $t->tanggal,
            'keterangan' => $t->keterangan ?? '',
            'petugas' => $t->user->name ?? 'Sistem',
        ];
    }
}
