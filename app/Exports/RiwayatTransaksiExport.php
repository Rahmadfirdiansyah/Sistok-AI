<?php

namespace App\Exports;

use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class RiwayatTransaksiExport implements FromCollection, WithHeadings, WithMapping
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $startDate = $this->request->query('start_date');
        $endDate = $this->request->query('end_date');
        $kategori = $this->request->query('kategori');
        $lokasi = $this->request->query('lokasi');
        $jenis = $this->request->query('jenis');

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

        return $transaksi->sortByDesc(function ($item) {
            return $item['tanggal_raw'] . ' ' . ($item['created_at'] ? $item['created_at']->format('H:i:s') : '00:00:00');
        })->values();
    }

    public function headings(): array
    {
        return [
            'ID Transaksi',
            'Jenis',
            'Tanggal',
            'Nama Barang',
            'Kategori',
            'Lokasi',
            'Jumlah',
            'Satuan',
            'Keterangan',
            'Pemakai / Tujuan / Supplier'
        ];
    }

    public function map($row): array
    {
        return [
            $row['id'],
            $row['jenis'],
            $row['tanggal'],
            $row['barang'],
            $row['kategori'],
            $row['lokasi'],
            $row['jenis'] === 'MASUK' ? '+' . $row['qty'] : '-' . $row['qty'],
            $row['satuan'],
            $row['keterangan'],
            $row['oleh_tujuan'],
        ];
    }
}
