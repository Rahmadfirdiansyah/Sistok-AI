<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Riwayat Transaksi</title>
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
        .badge { padding: 1px 5px; border-radius: 3px; font-weight: bold; font-size: 7.5px; display: inline-block; text-align: center; }
        .badge-masuk { color: #059669; }
        .badge-keluar { color: #ea580c; }

        /* Notes */
        .notes {
            border: 1px solid #d1d5db;
            padding: 10px 15px;
            margin-bottom: 15px;
            font-size: 9px;
            color: #4b5563;
            line-height: 1.6;
        }
        .notes p { margin-bottom: 4px; }

        /* Signature */
        .signature-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .signature-table td {
            width: 33.33%;
            text-align: center;
            vertical-align: top;
            padding: 8px 15px;
        }
        .sig-label { font-size: 10px; font-weight: bold; color: #1a1a2e; }
        .sig-role { font-size: 8px; color: #6b7280; font-style: italic; }
        .sig-space { height: 50px; }
        .sig-line { border-top: 1px solid #1a1a2e; padding-top: 5px; font-size: 9px; font-weight: bold; color: #1a1a2e; }
    </style>
</head>
<body>
    @include('laporan.partials.kop-surat')
    <div class="report-title">LAPORAN RIWAYAT TRANSAKSI</div>

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
                <td class="label">Filter Kategori</td>
                <td class="sep">:</td>
                <td class="value">{{ $filter['kategori'] }}</td>
            </tr>
            <tr>
                <td class="label">Total Transaksi</td>
                <td class="sep">:</td>
                <td class="value">{{ count($transaksis) }} transaksi</td>
                <td class="label">Filter Jenis</td>
                <td class="sep">:</td>
                <td class="value">{{ $filter['jenis'] }}</td>
            </tr>
        </table>
    </div>

    <table class="data">
        <thead>
            <tr>
                <th class="center" style="width: 3%;">No</th>
                <th style="width: 12%;">ID Transaksi</th>
                <th class="center" style="width: 7%;">Jenis</th>
                <th style="width: 9%;">Tanggal</th>
                <th style="width: 20%;">Barang & Kategori</th>
                <th style="width: 10%;">Lokasi</th>
                <th class="center" style="width: 10%;">Jumlah</th>
                <th style="width: 22%;">Pemakai / Tujuan / Ket.</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($transaksis as $i => $t)
            <tr>
                <td class="center">{{ $i + 1 }}</td>
                <td style="font-family: monospace; font-weight: bold; color: {{ $t['jenis'] == 'MASUK' ? '#059669' : '#ea580c' }};">
                    {{ $t['id'] }}
                </td>
                <td class="center">
                    <span class="badge {{ $t['jenis'] == 'MASUK' ? 'badge-masuk' : 'badge-keluar' }}">{{ $t['jenis'] }}</span>
                </td>
                <td>{{ $t['tanggal'] }}</td>
                <td>
                    <strong>{{ $t['barang'] }}</strong><br>
                    <span style="font-size: 8px; color: #9ca3af;">{{ $t['kategori'] }}</span>
                </td>
                <td>{{ $t['lokasi'] }}</td>
                <td class="center" style="font-weight: bold; color: {{ $t['jenis'] == 'MASUK' ? '#059669' : '#ea580c' }};">
                    {{ $t['jenis'] == 'MASUK' ? '+' : '-' }}{{ $t['qty'] }} <span style="font-size: 8px; font-weight: normal;">{{ $t['satuan'] }}</span>
                </td>
                <td style="font-size: 8px;">
                    <strong>{{ $t['oleh_tujuan'] }}</strong>
                    @if($t['keterangan'] && $t['keterangan'] != '-')
                        <br><span style="color: #6b7280;">Ket: {{ $t['keterangan'] }}</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="center" style="padding: 20px; color: #9ca3af;">Tidak ada transaksi ditemukan untuk filter ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>


    @include('laporan.partials.footer')

</body>
</html>
