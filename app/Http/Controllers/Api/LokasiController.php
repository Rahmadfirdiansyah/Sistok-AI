<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lokasi;
use Illuminate\Http\Request;

class LokasiController extends Controller
{
    public function index()
    {
        $lokasis = Lokasi::withCount('barangs')->get()->map(function ($l) {
            $l->jumlah = $l->barangs_count;
            return $l;
        });
        return response()->json($lokasis);
    }

    public function store(Request $request)
    {
        if ($request->user() && $request->user()->role === 'user') {
            return response()->json(['message' => 'Aksi ditolak. Staff hanya memiliki akses lihat/view.'], 403);
        }

        $request->validate([
            'nama' => 'required|string|unique:lokasis,nama',
            'deskripsi' => 'nullable|string',
        ]);

        $lokasi = Lokasi::create([
            'nama' => $request->nama,
            'deskripsi' => $request->deskripsi,
        ]);

        $lokasi->jumlah = 0;
        return response()->json($lokasi, 201);
    }

    public function show(Lokasi $lokasi)
    {
        $lokasi->jumlah = $lokasi->barangs()->count();
        return response()->json($lokasi);
    }

    public function update(Request $request, $id)
    {
        if ($request->user() && $request->user()->role === 'user') {
            return response()->json(['message' => 'Aksi ditolak. Staff hanya memiliki akses lihat/view.'], 403);
        }

        $lokasi = Lokasi::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|unique:lokasis,nama,' . $id,
            'deskripsi' => 'nullable|string',
        ]);

        $lokasi->update([
            'nama' => $request->nama,
            'deskripsi' => $request->deskripsi,
        ]);

        $lokasi->jumlah = $lokasi->barangs()->count();
        return response()->json($lokasi);
    }

    public function destroy(Request $request, $id)
    {
        if ($request->user() && $request->user()->role === 'user') {
            return response()->json(['message' => 'Aksi ditolak. Staff hanya memiliki akses lihat/view.'], 403);
        }

        $lokasi = Lokasi::findOrFail($id);

        if ($lokasi->barangs()->exists()) {
            return response()->json(['message' => 'Tidak bisa menghapus lokasi ini karena sedang digunakan oleh barang.'], 400);
        }

        $lokasi->delete();

        return response()->json(['message' => 'Lokasi berhasil dihapus']);
    }
}
