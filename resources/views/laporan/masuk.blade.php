<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Barang Masuk</title>
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
        .total-row { font-weight: bold; }
        .total-row td { border-top: 2px solid #059669; }

        /* Signature */
        .signature-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .signature-table td {
            width: 33.33%;
            text-align: center;
            vertical-align: top;
            padding: 6px 12px;
        }
        .sig-label { font-size: 9px; font-weight: bold; color: #1a1a2e; }
        .sig-role { font-size: 7.5px; color: #6b7280; font-style: italic; }
        .sig-space { height: 45px; }
        .sig-line { border-top: 1px solid #1a1a2e; padding-top: 4px; font-size: 8px; font-weight: bold; color: #1a1a2e; }
    </style>
</head>
<body>
    @include('laporan.partials.kop-surat')
    <div class="report-title">LAPORAN BARANG MASUK</div>

    <div class="info-box">
        <table>
            <tr>
                <td class="label">Tanggal Cetak</td>
                <td class="sep">:</td>
                <td class="value">{{ $tanggal }} — {{ $waktu }} WIB</td>
                <td class="label">Filter Lokasi</td>
                <td class="sep">:</td>
                <td class="value">{{ $filter['lokasi'] }}</td>
            </tr>
            <tr>
                <td class="label">Periode</td>
                <td class="sep">:</td>
                <td class="value">{{ $filter['start_date'] }} s/d {{ $filter['end_date'] }}</td>
                <td class="label">Total Transaksi</td>
                <td class="sep">:</td>
                <td class="value">{{ count($transaksis) }} transaksi</td>
            </tr>
        </table>
    </div>

    <table class="data">
        <thead>
            <tr>
                <th class="center" style="width: 4%;">No</th>
                <th style="width: 12%;">ID Transaksi</th>
                <th style="width: 10%;">Tanggal</th>
                <th style="width: 24%;">Barang</th>
                <th style="width: 14%;">Lokasi Masuk</th>
                <th class="center" style="width: 12%;">Jumlah</th>
                <th style="width: 24%;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transaksis as $i => $t)
            <tr>
                <td class="center">{{ $i + 1 }}</td>
                <td style="font-family: monospace; font-weight: bold; color: #059669;">{{ $t['id'] }}</td>
                <td>{{ $t['tanggal'] }}</td>
                <td>
                    <strong>{{ $t['barang'] }}</strong><br>
                    <span style="font-size: 7.5px; color: #9ca3af;">{{ $t['kode'] }}</span>
                </td>
                <td>{{ $t['lokasi'] }}</td>
                <td class="center" style="font-weight: bold; color: #059669;">+{{ $t['jumlah'] }} {{ $t['satuan'] }}</td>
                <td style="font-size: 8px; color: #6b7280;">{{ $t['keterangan'] }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; padding: 20px; color: #9ca3af;">Tidak ada transaksi.</td>
            </tr>
            @endforelse
            @if(count($transaksis) > 0)
            <tr class="total-row">
                <td colspan="5" style="text-align: right;">TOTAL QTY MASUK</td>
                <td class="center" style="color: #059669;">+{{ $totalQty }}</td>
                <td></td>
            </tr>
            @endif
        </tbody>
    </table>

    <table class="signature-table">
        <tr>
            <td>
                <div class="sig-label">Pemohon,</div>
                <div class="sig-role">User</div>
                <div class="sig-space"></div>
                <div class="sig-line">(..............................)</div>
            </td>
            <td>
                <div class="sig-label">Menyetujui,</div>
                <div class="sig-role">Operational Manager</div>
                <div class="sig-space"></div>
                <div class="sig-line">(..............................)</div>
            </td>
            <td>
                <div class="sig-label">Setup Oleh,</div>
                <div class="sig-role">Team DCO DASEN</div>
                <div class="sig-space"></div>
                <div class="sig-line">(..............................)</div>
            </td>
        </tr>
    </table>

    @include('laporan.partials.footer')
</body>
</html>
