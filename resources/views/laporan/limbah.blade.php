<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Limbah & Barang Rusak</title>
    <style>
        @page { margin: 20px 42px; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 10px; color: #1f2937; }

        /* Title */
        .report-title {
            text-align: center;
            font-size: 13px;
            font-weight: bold;
            color: #991b1b;
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
        .info-box .label { color: #6b7280; width: 110px; }
        .info-box .sep { width: 8px; }
        .info-box .value { font-weight: bold; color: #1f2937; }

        /* Data Table */
        table.data { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        table.data th {
            background: #fef2f2;
            color: #991b1b;
            padding: 5px 6px;
            text-align: left;
            font-size: 8.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            border: 1px solid #fca5a5;
        }
        table.data th.right { text-align: right; }
        table.data th.center { text-align: center; }
        table.data td {
            padding: 4px 6px;
            border: 1px solid #e5e7eb;
            font-size: 8.5px;
            vertical-align: top;
        }
        table.data td.right { text-align: right; }
        table.data td.center { text-align: center; }

        .qty-limbah { color: #dc2626; font-weight: bold; }
    </style>
</head>
<body>
    @include('laporan.partials.kop-surat')
    <div class="report-title">LAPORAN AKUMULASI LIMBAH & BARANG RUSAK</div>

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
                <td class="label">Total Item Limbah</td>
                <td class="sep">:</td>
                <td class="value">{{ count($limbahs) }} jenis barang</td>
                <td class="label">Status Pengeluaran</td>
                <td class="sep">:</td>
                <td class="value">Limbah / Rusak / Afkir</td>
            </tr>
        </table>
    </div>

    <table class="data">
        <thead>
            <tr>
                <th class="center" style="width: 5%;">No</th>
                <th style="width: 12%;">Kode</th>
                <th style="width: 28%;">Nama Barang</th>
                <th style="width: 18%;">Kategori</th>
                <th class="right" style="width: 17%;">Total Akumulasi Limbah</th>
                <th class="center" style="width: 20%;">Terakhir Dibuang</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($limbahs as $i => $l)
            <tr>
                <td class="center">{{ $i + 1 }}</td>
                <td style="font-family: monospace; font-weight: bold; color: #dc2626;">{{ $l['kode'] }}</td>
                <td style="font-weight: bold; color: #111827;">{{ $l['nama'] }}</td>
                <td>{{ $l['kategori'] }}</td>
                <td class="right qty-limbah">{{ $l['total_limbah'] }} {{ $l['satuan'] }}</td>
                <td class="center">{{ $l['terakhir_dibuang'] }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center; padding: 20px; color: #9ca3af;">Tidak ada data limbah recorded.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @include('laporan.partials.footer')
</body>
</html>
