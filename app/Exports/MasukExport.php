<?php

namespace App\Exports;

use App\Models\BarangMasuk;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MasukExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = BarangMasuk::with(['barang.satuan', 'lokasi']);

        if ($this->request->filled('start_date')) {
            $query->whereDate('tanggal', '>=', $this->request->start_date);
        }
        if ($this->request->filled('end_date')) {
            $query->whereDate('tanggal', '<=', $this->request->end_date);
        }
        if ($this->request->filled('lokasi')) {
            $query->whereHas('lokasi', fn($q) => $q->where('nama', $this->request->lokasi));
        }

        return $query->orderBy('tanggal', 'desc')->get();
    }

    public function map($m): array
    {
        return [
            'TRX-IN-' . str_pad($m->id, 3, '0', STR_PAD_LEFT),
            Carbon::parse($m->tanggal)->format('d/m/Y'),
            $m->barang->kode ?? '-',
            $m->barang->nama ?? '-',
            $m->lokasi->nama ?? '-',
            $m->jumlah,
            $m->barang->satuan->nama ?? 'pcs',
            $m->keterangan ?? '-'
        ];
    }

    public function headings(): array
    {
        return [
            'ID Transaksi',
            'Tanggal',
            'Kode Barang',
            'Nama Barang',
            'Lokasi Masuk',
            'Jumlah',
            'Satuan',
            'Keterangan'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
