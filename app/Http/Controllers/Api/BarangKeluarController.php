<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\BarangKeluar;
use App\Models\Lokasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BarangKeluarController extends Controller
{
    public function index()
    {
        $transaksis = BarangKeluar::with(['barang.satuan', 'lokasi', 'user'])
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
            'qty' => 'required|integer|min:1',
            'lokasi' => 'required|string',
            'dipakaiOleh' => 'required|string',
            'tujuan' => 'required|string',
            'jenis_keluar' => 'nullable|string|in:pemakaian,limbah',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($request) {
            $barang = Barang::findOrFail($request->barang_id);
            $lokasi = Lokasi::firstOrCreate(['nama' => $request->lokasi]);

            if ($barang->stok < $request->qty) {
                throw ValidationException::withMessages([
                    'qty' => ["Stok barang '{$barang->nama}' tidak mencukupi. Stok saat ini: {$barang->stok}"],
                ]);
            }

            $transaksi = BarangKeluar::create([
                'tanggal' => $request->tanggal,
                'barang_id' => $barang->id,
                'lokasi_id' => $lokasi->id,
                'jumlah' => $request->qty,
                'dipakai_oleh' => $request->dipakaiOleh,
                'tujuan' => $request->tujuan,
                'jenis_keluar' => $request->jenis_keluar ?? 'pemakaian',
                'keterangan' => $request->keterangan,
                'user_id' => auth()->id(),
            ]);

            // Adjust stock
            $barang->decrement('stok', $request->qty);

            $loaded = BarangKeluar::with(['barang.satuan', 'lokasi', 'user'])->find($transaksi->id);
            return response()->json($this->mapSingleTransaksi($loaded), 201);
        });
    }

    public function show(BarangKeluar $barangKeluar)
    {
        $barangKeluar->load(['barang.satuan', 'lokasi', 'user']);
        return response()->json($this->mapSingleTransaksi($barangKeluar));
    }

    public function update(Request $request, $id)
    {
        if ($request->user() && $request->user()->role === 'user') {
            return response()->json(['message' => 'Aksi ditolak. Staff hanya memiliki akses lihat/view.'], 403);
        }

        $transaksi = BarangKeluar::with('barang')->findOrFail($id);

        $request->validate([
            'barang_id' => 'required|exists:barangs,id',
            'qty' => 'required|integer|min:1',
            'lokasi' => 'required|string',
            'dipakaiOleh' => 'required|string',
            'tujuan' => 'required|string',
            'jenis_keluar' => 'nullable|string|in:pemakaian,limbah',
            'tanggal' => 'required|date',
            'keterangan' => 'nullable|string',
        ]);

        return DB::transaction(function () use ($request, $transaksi) {
            $oldQty = $transaksi->jumlah;
            $oldBarang = $transaksi->barang;

            $barang = Barang::findOrFail($request->barang_id);
            $lokasi = Lokasi::firstOrCreate(['nama' => $request->lokasi]);

            if ($oldBarang->id === $barang->id) {
                $availableStock = $barang->stok + $oldQty;
                if ($availableStock < $request->qty) {
                    throw ValidationException::withMessages([
                        'qty' => ["Stok barang '{$barang->nama}' tidak mencukupi. Stok saat ini (ditambah transaksi lama): {$availableStock}"],
                    ]);
                }

                $transaksi->update([
                    'tanggal' => $request->tanggal,
                    'barang_id' => $barang->id,
                    'lokasi_id' => $lokasi->id,
                    'jumlah' => $request->qty,
                    'dipakai_oleh' => $request->dipakaiOleh,
                    'tujuan' => $request->tujuan,
                    'jenis_keluar' => $request->jenis_keluar ?? 'pemakaian',
                    'keterangan' => $request->keterangan,
                ]);

                // Adjust stock
                $diff = $request->qty - $oldQty;
                if ($diff > 0) {
                    $barang->decrement('stok', $diff);
                } elseif ($diff < 0) {
                    $barang->increment('stok', abs($diff));
                }
            } else {
                if ($barang->stok < $request->qty) {
                    throw ValidationException::withMessages([
                        'qty' => ["Stok barang baru '{$barang->nama}' tidak mencukupi. Stok saat ini: {$barang->stok}"],
                    ]);
                }

                $transaksi->update([
                    'tanggal' => $request->tanggal,
                    'barang_id' => $barang->id,
                    'lokasi_id' => $lokasi->id,
                    'jumlah' => $request->qty,
                    'dipakai_oleh' => $request->dipakaiOleh,
                    'tujuan' => $request->tujuan,
                    'jenis_keluar' => $request->jenis_keluar ?? 'pemakaian',
                    'keterangan' => $request->keterangan,
                ]);

                // Restore stock of old item, deduct stock of new item
                $oldBarang->increment('stok', $oldQty);
                $barang->decrement('stok', $request->qty);
            }

            $loaded = BarangKeluar::with(['barang.satuan', 'lokasi', 'user'])->find($transaksi->id);
            return response()->json($this->mapSingleTransaksi($loaded));
        });
    }

    public function destroy(Request $request, $id)
    {
        if ($request->user() && $request->user()->role === 'user') {
            return response()->json(['message' => 'Aksi ditolak. Staff hanya memiliki akses lihat/view.'], 403);
        }

        $transaksi = BarangKeluar::with('barang')->findOrFail($id);

        DB::transaction(function () use ($transaksi) {
            $barang = $transaksi->barang;
            if ($barang) {
                $barang->increment('stok', $transaksi->jumlah);
            }
            $transaksi->delete();
        });

        return response()->json(['message' => 'Transaksi barang keluar berhasil dihapus']);
    }

    private function mapSingleTransaksi(BarangKeluar $t)
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
            'dipakaiOleh' => $t->dipakai_oleh ?? '',
            'tujuan' => $t->tujuan ?? '',
            'jenisKeluar' => $t->jenis_keluar ?? 'pemakaian',
            'keterangan' => $t->keterangan ?? '',
            'petugas' => $t->user->name ?? 'Sistem',
        ];
    }
}
