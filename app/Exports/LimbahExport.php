<?php

namespace App\Exports;

use App\Models\BarangKeluar;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LimbahExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = BarangKeluar::with(['barang.kategori', 'barang.satuan'])
            ->where('jenis_keluar', 'limbah');

        if ($this->request->filled('kategori')) {
            $query->whereHas('barang.kategori', fn($q) => $q->where('nama', $this->request->kategori));
        }

        $allLimbah = $query->orderBy('tanggal', 'desc')->get();

        return $allLimbah->groupBy('barang_id')->map(function ($items) {
            $first = $items->first();
            $barang = $first->barang;

            return (object) [
                'kode'            => $barang->kode ?? '-',
                'nama'            => $barang->nama ?? '-',
                'kategori'        => $barang->kategori->nama ?? '-',
                'total_limbah'    => $items->sum('jumlah'),
                'satuan'          => $barang->satuan->nama ?? 'unit',
                'frekuensi'       => $items->count(),
                'terakhir_dibuang' => $items->max('tanggal'),
            ];
        })->values();
    }

    public function map($row): array
    {
        return [
            $row->kode,
            $row->nama,
            $row->kategori,
            $row->total_limbah,
            $row->satuan,
            $row->frekuensi . ' kali',
            $row->terakhir_dibuang,
        ];
    }

    public function headings(): array
    {
        return [
            'Kode Barang',
            'Nama Barang',
            'Kategori',
            'Total Limbah Terakumulasi',
            'Satuan',
            'Frekuensi Pengeluaran',
            'Terakhir Dibuang',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
