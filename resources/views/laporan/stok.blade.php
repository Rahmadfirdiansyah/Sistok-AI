<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Stok Barang</title>
    <style>
        @page { margin: 20px 42px; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 10px; color: #1f2937; }

        /* Title */
        .report-title {
            text-align: center;
            font-size: 13px;
            font-weight: bold;
            color: #1a1a2e;
            letter-spacing: 1.5px;
            padding: 4px 0;
            margin-bottom: 6px;
        }

        /* Info Section */
        .info-box {
            border: 1px solid #d1d5db;
            margin-bottom: 8px;
            padding: 5px 10px;
        }
        .info-box table { width: 100%; border-collapse: collapse; }
        .info-box td { padding: 1.5px 4px; font-size: 9px; }
        .info-box .label { color: #6b7280; width: 100px; }
        .info-box .sep { width: 8px; }
        .info-box .value { font-weight: bold; color: #1f2937; }

        /* Data Table */
        table.data { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        table.data th {
            background: #f9fafb;
            color: #1a1a2e;
            padding: 4px 5px;
            text-align: left;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            border: 1px solid #d1d5db;
        }
        table.data th.right { text-align: right; }
        table.data th.center { text-align: center; }
        table.data td {
            padding: 3px 5px;
            border: 1px solid #d1d5db;
            font-size: 8.5px;
            vertical-align: top;
        }
        table.data td.right { text-align: right; }
        table.data td.center { text-align: center; }

        .status-aman { color: #059669; font-weight: bold; }
        .status-rendah { color: #d97706; font-weight: bold; }
        .status-kritis { color: #dc2626; font-weight: bold; }
    </style>
</head>
<body>
    @include('laporan.partials.kop-surat')
    <div class="report-title">LAPORAN STOK BARANG</div>

    <div class="info-box">
        <table>
            <tr>
                <td class="label">Tanggal Cetak</td>
                <td class="sep">:</td>
                <td class="value">{{ $tanggal }} — {{ $waktu }} WIB</td>
                <td class="label">Filter Kategori</td>
                <td class="sep">:</td>
                <td class="value">{{ $filter['kategori'] }}</td>
            </tr>
            <tr>
                <td class="label">Total Data</td>
                <td class="sep">:</td>
                <td class="value">{{ count($barangs) }} barang</td>
                <td class="label">Filter Status</td>
                <td class="sep">:</td>
                <td class="value">{{ $filter['status'] }}</td>
            </tr>
        </table>
    </div>

    <table class="data">
        <thead>
            <tr>
                <th class="center" style="width: 4%;">No</th>
                <th style="width: 10%;">Kode</th>
                <th style="width: 22%;">Nama Barang</th>
                <th style="width: 14%;">Kategori</th>
                <th style="width: 14%;">Lokasi</th>
                <th class="center" style="width: 10%;">Min. Stok</th>
                <th class="center" style="width: 10%;">Stok</th>
                <th class="center" style="width: 10%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($barangs as $i => $b)
            <tr>
                <td class="center">{{ $i + 1 }}</td>
                <td style="font-family: monospace; font-weight: bold; color: #4f46e5;">{{ $b['kode'] }}</td>
                <td>{{ $b['nama'] }}</td>
                <td>{{ $b['kategori'] }}</td>
                <td>{{ $b['lokasi'] }}</td>
                <td class="center">{{ $b['min_stok'] }} {{ $b['satuan'] }}</td>
                <td class="center" style="font-weight: bold;">{{ $b['stok'] }} {{ $b['satuan'] }}</td>
                <td class="center status-{{ strtolower($b['status']) }}">{{ $b['status'] }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align: center; padding: 20px; color: #9ca3af;">Tidak ada data.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @include('laporan.partials.footer')
</body>
</html>
