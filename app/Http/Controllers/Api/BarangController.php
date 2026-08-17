<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Lokasi;
use App\Models\Satuan;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    public function nextKode()
    {
        $nextId = 1;
        try {
            // Bypass cache stats MySQL 8.0 agar selalu real-time
            try {
                \Illuminate\Support\Facades\DB::statement("SET SESSION information_schema_stats_expiry = 0");
            } catch (\Exception $e) {}

            $status = \Illuminate\Support\Facades\DB::select("SHOW TABLE STATUS LIKE 'barangs'");
            if (!empty($status)) {
                $nextId = $status[0]->Auto_increment;
            }
        } catch (\Exception $e) {
            $maxId = \App\Models\Barang::max('id');
            $nextId = $maxId ? $maxId + 1 : 1;
        }
        $kode = 'BRG-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);
        return response()->json(['kode' => $kode]);
    }

    public function index()
    {
        $barangs = Barang::with(['kategori', 'lokasi', 'satuan', 'user'])
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($b) {
                return $this->mapSingleBarang($b);
            });
        return response()->json($barangs);
    }

    public function store(Request $request)
    {
        if ($request->user() && $request->user()->role === 'user') {
            return response()->json(['message' => 'Aksi ditolak. Staff hanya memiliki akses lihat/view.'], 403);
        }

        $request->validate([
            'kode' => 'required|string|unique:barangs,kode',
            'nama' => 'required|string|unique:barangs,nama',
            'kategori' => 'required|string',
            'lokasi' => 'required|string',
            'satuan' => 'required|string',
            'stok' => 'nullable|integer',
            'minStok' => 'required|integer|min:1',
        ]);

        $ids = $this->getOrCreateRelatedIds($request);

        $barang = Barang::create([
            'kode' => $request->kode,
            'nama' => $request->nama,
            'kategori_id' => $ids['kategori_id'],
            'lokasi_id' => $ids['lokasi_id'],
            'satuan_id' => $ids['satuan_id'],
            'stok' => $request->stok ?? 0,
            'min_stok' => $request->minStok ?? 0,
            'user_id' => auth()->id(),
        ]);

        if ($request->hasFile('gambar')) {
            $path = $request->file('gambar')->store('barangs', 'public');
            $barang->gambar = $path;
            $barang->save();
        }

        $loaded = Barang::with(['kategori', 'lokasi', 'satuan', 'user'])->find($barang->id);
        return response()->json($this->mapSingleBarang($loaded), 201);
    }

    public function show(Barang $barang)
    {
        $barang->load(['kategori', 'lokasi', 'satuan', 'user']);
        return response()->json($this->mapSingleBarang($barang));
    }

    public function update(Request $request, $id)
    {
        if ($request->user() && $request->user()->role === 'user') {
            return response()->json(['message' => 'Aksi ditolak. Staff hanya memiliki akses lihat/view.'], 403);
        }

        $barang = Barang::findOrFail($id);

        $request->validate([
            'kode' => 'required|string|unique:barangs,kode,' . $id,
            'nama' => 'required|string|unique:barangs,nama,' . $id,
            'kategori' => 'required|string',
            'lokasi' => 'required|string',
            'satuan' => 'required|string',
            'stok' => 'nullable|integer',
            'minStok' => 'required|integer|min:1',
        ]);

        $ids = $this->getOrCreateRelatedIds($request);

        $barang->update([
            'kode' => $request->kode,
            'nama' => $request->nama,
            'kategori_id' => $ids['kategori_id'],
            'lokasi_id' => $ids['lokasi_id'],
            'satuan_id' => $ids['satuan_id'],
            'stok' => $request->stok ?? 0,
            'min_stok' => $request->minStok ?? 0,
        ]);

        if ($request->hasFile('gambar')) {
            if ($barang->gambar) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($barang->gambar);
            }
            $path = $request->file('gambar')->store('barangs', 'public');
            $barang->gambar = $path;
            $barang->save();
        }

        $loaded = Barang::with(['kategori', 'lokasi', 'satuan', 'user'])->find($barang->id);
        return response()->json($this->mapSingleBarang($loaded));
    }

    public function destroy(Request $request, $id)
    {
        if ($request->user() && $request->user()->role === 'user') {
            return response()->json(['message' => 'Aksi ditolak. Staff hanya memiliki akses lihat/view.'], 403);
        }

        $barang = Barang::findOrFail($id);


        $hasMasuk = \App\Models\BarangMasuk::where('barang_id', $id)->exists();
        $hasKeluar = \App\Models\BarangKeluar::where('barang_id', $id)->exists();

        if ($hasMasuk || $hasKeluar) {
            return response()->json(['message' => 'Tidak bisa menghapus barang ini karena sudah memiliki riwayat transaksi.'], 400);
        }

        if ($barang->gambar) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($barang->gambar);
        }

        $barang->delete();

        return response()->json(['message' => 'Barang berhasil dihapus']);
    }

    private function getOrCreateRelatedIds(Request $request)
    {
        $kategori = Kategori::firstOrCreate(['nama' => $request->kategori]);
        $lokasi = Lokasi::firstOrCreate(['nama' => $request->lokasi]);
        $satuan = Satuan::firstOrCreate(['nama' => $request->satuan]);

        return [
            'kategori_id' => $kategori->id,
            'lokasi_id' => $lokasi->id,
            'satuan_id' => $satuan->id,
        ];
    }

    private function mapSingleBarang(Barang $b)
    {
        return [
            'id' => $b->id,
            'kode' => $b->kode,
            'nama' => $b->nama,
            'kategori_id' => $b->kategori_id,
            'kategori' => $b->kategori->nama ?? '',
            'lokasi_id' => $b->lokasi_id,
            'lokasi' => $b->lokasi->nama ?? '',
            'satuan_id' => $b->satuan_id,
            'satuan' => $b->satuan->nama ?? '',
            'stok' => $b->stok,
            'minStok' => $b->min_stok,
            'gambar' => $b->gambar ? url('storage/' . $b->gambar) : null,
            'petugas' => $b->user->name ?? 'Sistem',
        ];
    }
}
