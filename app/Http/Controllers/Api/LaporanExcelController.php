<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StokExport;
use App\Exports\MasukExport;
use App\Exports\KeluarExport;
use App\Exports\RiwayatTransaksiExport;
use App\Exports\LimbahExport;

class LaporanExcelController extends Controller
{
    public function stok(Request $request)
    {
        return Excel::download(new StokExport($request), 'laporan-stok-' . date('Ymd') . '.xlsx');
    }

    public function masuk(Request $request)
    {
        return Excel::download(new MasukExport($request), 'laporan-masuk-' . date('Ymd') . '.xlsx');
    }

    public function keluar(Request $request)
    {
        return Excel::download(new KeluarExport($request), 'laporan-keluar-' . date('Ymd') . '.xlsx');
    }

    public function riwayatTransaksi(Request $request)
    {
        return Excel::download(new RiwayatTransaksiExport($request), 'laporan-riwayat-transaksi-' . date('Ymd') . '.xlsx');
    }

    public function limbah(Request $request)
    {
        return Excel::download(new LimbahExport($request), 'laporan-limbah-' . date('Ymd') . '.xlsx');
    }
}
