<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Satuan;
use Illuminate\Http\Request;

class SatuanController extends Controller
{
    public function index()
    {
        $satuans = Satuan::withCount('barangs')->get()->map(function ($s) {
            $s->digunakan = $s->barangs_count;
            return $s;
        });
        return response()->json($satuans);
    }

    public function store(Request $request)
    {
        if ($request->user() && $request->user()->role === 'user') {
            return response()->json(['message' => 'Aksi ditolak. Staff hanya memiliki akses lihat/view.'], 403);
        }

        $request->validate([
            'nama' => 'required|string|unique:satuans,nama',
            'keterangan' => 'nullable|string',
        ]);

        $satuan = Satuan::create([
            'nama' => $request->nama,
            'keterangan' => $request->keterangan,
        ]);

        $satuan->digunakan = 0;
        return response()->json($satuan, 201);
    }

    public function show(Satuan $satuan)
    {
        $satuan->digunakan = $satuan->barangs()->count();
        return response()->json($satuan);
    }

    public function update(Request $request, $id)
    {
        if ($request->user() && $request->user()->role === 'user') {
            return response()->json(['message' => 'Aksi ditolak. Staff hanya memiliki akses lihat/view.'], 403);
        }

        $satuan = Satuan::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|unique:satuans,nama,' . $id,
            'keterangan' => 'nullable|string',
        ]);

        $satuan->update([
            'nama' => $request->nama,
            'keterangan' => $request->keterangan,
        ]);

        $satuan->digunakan = $satuan->barangs()->count();
        return response()->json($satuan);
    }

    public function destroy(Request $request, $id)
    {
        if ($request->user() && $request->user()->role === 'user') {
            return response()->json(['message' => 'Aksi ditolak. Staff hanya memiliki akses lihat/view.'], 403);
        }

        $satuan = Satuan::findOrFail($id);

        if ($satuan->barangs()->exists()) {
            return response()->json(['message' => 'Tidak bisa menghapus satuan ini karena sedang digunakan oleh barang.'], 400);
        }

        $satuan->delete();

        return response()->json(['message' => 'Satuan berhasil dihapus']);
    }
}
