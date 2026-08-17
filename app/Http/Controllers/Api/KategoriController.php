<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function index()
    {
        $kategoris = Kategori::withCount('barangs')->get()->map(function ($k) {
            $k->jumlah = $k->barangs_count;
            return $k;
        });
        return response()->json($kategoris);
    }

    public function store(Request $request)
    {
        if ($request->user() && $request->user()->role === 'user') {
            return response()->json(['message' => 'Aksi ditolak. Staff hanya memiliki akses lihat/view.'], 403);
        }

        $request->validate([
            'nama' => 'required|string|unique:kategoris,nama',
            'deskripsi' => 'nullable|string',
        ]);

        $kategori = Kategori::create([
            'nama' => $request->nama,
            'deskripsi' => $request->deskripsi,
        ]);

        $kategori->jumlah = 0;
        return response()->json($kategori, 201);
    }

    public function show(Kategori $kategori)
    {
        $kategori->jumlah = $kategori->barangs()->count();
        return response()->json($kategori);
    }

    public function update(Request $request, $id)
    {
        if ($request->user() && $request->user()->role === 'user') {
            return response()->json(['message' => 'Aksi ditolak. Staff hanya memiliki akses lihat/view.'], 403);
        }

        $kategori = Kategori::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|unique:kategoris,nama,' . $id,
            'deskripsi' => 'nullable|string',
        ]);

        $kategori->update([
            'nama' => $request->nama,
            'deskripsi' => $request->deskripsi,
        ]);

        $kategori->jumlah = $kategori->barangs()->count();
        return response()->json($kategori);
    }

    public function destroy(Request $request, $id)
    {
        if ($request->user() && $request->user()->role === 'user') {
            return response()->json(['message' => 'Aksi ditolak. Staff hanya memiliki akses lihat/view.'], 403);
        }

        $kategori = Kategori::findOrFail($id);

        if ($kategori->barangs()->exists()) {
            return response()->json(['message' => 'Tidak bisa menghapus kategori ini karena sedang digunakan oleh barang.'], 400);
        }

        $kategori->delete();

        return response()->json(['message' => 'Kategori berhasil dihapus']);
    }
}
