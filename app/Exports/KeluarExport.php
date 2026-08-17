<?php

namespace App\Exports;

use App\Models\BarangKeluar;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KeluarExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = BarangKeluar::with(['barang.satuan', 'lokasi']);

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

    public function map($k): array
    {
        return [
            'TRX-OUT-' . str_pad($k->id, 3, '0', STR_PAD_LEFT),
            Carbon::parse($k->tanggal)->format('d/m/Y'),
            $k->barang->kode ?? '-',
            $k->barang->nama ?? '-',
            $k->dipakai_oleh,
            $k->tujuan,
            $k->lokasi->nama ?? '-',
            $k->jumlah,
            $k->barang->satuan->nama ?? 'pcs',
            $k->keterangan ?? '-'
        ];
    }

    public function headings(): array
    {
        return [
            'ID Transaksi',
            'Tanggal',
            'Kode Barang',
            'Nama Barang',
            'Dipakai Oleh',
            'Tujuan Penggunaan',
            'Lokasi Keluar',
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
