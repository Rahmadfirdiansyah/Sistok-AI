<?php

namespace App\Exports;

use App\Models\Barang;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StokExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = Barang::with(['kategori', 'lokasi', 'satuan']);

        if ($this->request->filled('kategori')) {
            $query->whereHas('kategori', fn($q) => $q->where('nama', $this->request->kategori));
        }

        if ($this->request->filled('status')) {
            $status = $this->request->status;
            if ($status === 'kritis') {
                $query->whereRaw('stok < min_stok / 2');
            } elseif ($status === 'rendah') {
                $query->whereRaw('stok >= min_stok / 2 AND stok < min_stok');
            } elseif ($status === 'aman') {
                $query->whereRaw('stok >= min_stok');
            }
        }

        return $query->orderBy('kode')->get();
    }

    public function map($barang): array
    {
        $statusKey = $barang->stok < $barang->min_stok / 2 ? 'Kritis' : ($barang->stok < $barang->min_stok ? 'Rendah' : 'Aman');

        return [
            $barang->kode,
            $barang->nama,
            $barang->kategori->nama ?? '-',
            $barang->lokasi->nama ?? '-',
            $barang->min_stok,
            $barang->stok,
            $barang->satuan->nama ?? '-',
            $statusKey
        ];
    }

    public function headings(): array
    {
        return [
            'Kode',
            'Nama Barang',
            'Kategori',
            'Lokasi',
            'Min. Stok',
            'Stok Saat Ini',
            'Satuan',
            'Status'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
