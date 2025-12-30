<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>{{ $title ?? 'Laporan Monitoring & Evaluasi Triwulanan' }}</title>

    <style>
        /* Global */
        @page {
            margin: 24px 28px;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #000;
            background: #fff;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-justify {
            text-align: justify;
        }

        /* Header */
        .header-wrap {
            margin-top: 10px;
        }

        .header-title {
            font-weight: 700;
            font-size: 16px;
            letter-spacing: .3px;
            margin: 0;
            text-transform: uppercase;
        }

        .header-subtitle {
            font-weight: 700;
            font-size: 13px;
            margin: 4px 0 0 0;
            text-transform: uppercase;
        }

        .periode {
            margin-top: 14px;
            margin-bottom: 10px;
            font-size: 12px;
        }

        /* Table */
        table.report {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        table.report th,
        table.report td {
            border: 1px solid #000;
            padding: 8px 10px;
            vertical-align: top;
            word-wrap: break-word;
        }

        table.report thead th {
            font-weight: 700;
            text-transform: uppercase;
            text-align: center;
        }

        /* Column widths (sesuaikan bila perlu) */
        th.col-kegiatan,
        td.col-kegiatan {
            width: 32%;
        }

        th.col-tindak,
        td.col-tindak {
            width: 34%;
        }

        th.col-kendala,
        td.col-kendala {
            width: 17%;
        }

        th.col-ket,
        td.col-ket {
            width: 17%;
        }

        /* Section row (Bidang) */
        tr.section-row td {
            font-weight: 700;
            text-transform: uppercase;
            text-align: left;
            padding: 6px 10px;
            background-color: rgba(128, 128, 128, 0.5);
        }

        /* Ensure justify for content cells */
        td {
            text-align: justify;
        }
    </style>
</head>

<body>

    <div class="header-wrap text-center">
        <p class="header-title">{{ strtoupper($title ?? 'WEEKLY SUMMARY') }}</p>
        <p class="header-subtitle">PUSAT DATA DAN INFORMASI</p>
        <p class="header-subtitle">KEMENTERIAN LUAR NEGERI</p>
    </div>

    <div class="periode text-right">
        <strong>Periode :</strong> {{ $periode ?? '-' }}
    </div>

    <table class="report">
        <thead>
            <tr>
                <th class="col-kegiatan">Kegiatan</th>
                <th class="col-tindak">Tindak Lanjut</th>
                <th class="col-kendala">Kendala</th>
                <th class="col-ket">Percepatan</th>
            </tr>
        </thead>

        <tbody>
            @forelse(($groups ?? []) as $bidangName => $items)
                <tr class="section-row">
                    <td colspan="4">{{ $bidangName }}</td>
                </tr>

                @foreach ($items as $act)
                    <tr>
                        <td class="col-kegiatan">{{ $act->kegiatan ?? '-' }}</td>
                        <td class="col-tindak">{{ $act->tindak_lanjut ?? '-' }}</td>
                        <td class="col-kendala">{{ $act->kendala ?? '-' }}</td>
                        <td class="col-ket">{{ $act->percepatan ?? '-' }}</td>
                    </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="4" class="text-center">Tidak ada data.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>

</html>
