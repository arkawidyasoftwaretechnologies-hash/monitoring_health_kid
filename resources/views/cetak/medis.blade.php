<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Medis Tumbuh Kembang</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            font-size: 14px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .title {
            font-size: 20px;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
        }
        .subtitle {
            font-size: 14px;
            color: #555;
            margin: 5px 0 0 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
            width: 30%;
        }
        .section-title {
            background-color: #e2e8f0;
            padding: 10px;
            font-weight: bold;
            margin-bottom: 10px;
            border-left: 5px solid #475569;
        }
        .assessment-box {
            border: 1px solid #cbd5e1;
            padding: 15px;
            background-color: #f8fafc;
            margin-bottom: 20px;
        }
        .footer {
            margin-top: 40px;
            text-align: right;
        }
        .signature-line {
            width: 200px;
            border-bottom: 1px solid #333;
            display: inline-block;
            margin-top: 50px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 class="title">REKAM MEDIS TUMBUH KEMBANG ANAK</h1>
            <p class="subtitle">Dokumen Medis Rahasia</p>
        </div>

        <div class="section-title">Data Pasien & Pengukuran</div>
        <table>
            <tr>
                <th>Nama Pasien</th>
                <td>{{ $anak->nama }}</td>
            </tr>
            <tr>
                <th>Tanggal Lahir / Jenis Kelamin</th>
                <td>{{ $anak->tanggal_lahir }} / {{ $anak->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
            </tr>
            <tr>
                <th>Tanggal Pengukuran / Usia</th>
                <td>{{ $pengukuran->tanggal_ukur }} / {{ $pengukuran->usia_bulan }} bulan</td>
            </tr>
            <tr>
                <th>Berat / Tinggi Badan</th>
                <td>{{ $pengukuran->berat_badan }} kg / {{ $pengukuran->tinggi_badan }} cm ({{ $pengukuran->cara_ukur }})</td>
            </tr>
            @if($pengukuran->lingkar_kepala || $pengukuran->lila)
            <tr>
                <th>Lingkar Kepala / LiLA</th>
                <td>{{ $pengukuran->lingkar_kepala ?? '-' }} cm / {{ $pengukuran->lila ?? '-' }} cm</td>
            </tr>
            @endif
        </table>

        @if($hasil)
        <div class="section-title">Hasil Kalkulasi Z-Score WHO</div>
        <table>
            <tr>
                <th>Indikator</th>
                <th>Z-Score</th>
                <th>Status</th>
            </tr>
            <tr>
                <td>Berat Badan / Umur (WAZ)</td>
                <td>{{ $hasil->waz ?? 'N/A' }} SD</td>
                <td>{{ $hasil->status_bb_u ?? '-' }}</td>
            </tr>
            <tr>
                <td>Tinggi Badan / Umur (HAZ)</td>
                <td>{{ $hasil->haz ?? 'N/A' }} SD</td>
                <td>{{ $hasil->status_tb_u ?? '-' }}</td>
            </tr>
            <tr>
                <td>Berat Badan / Tinggi Badan (WHZ)</td>
                <td>{{ $hasil->whz ?? 'N/A' }} SD</td>
                <td>{{ $hasil->status_bb_tb ?? '-' }}</td>
            </tr>
            <tr>
                <td>IMT / Umur (BMIZ)</td>
                <td>{{ $hasil->bmiz ?? 'N/A' }} SD</td>
                <td>{{ $hasil->status_imt_u ?? '-' }}</td>
            </tr>
            @if($hasil->hcfa !== null)
            <tr>
                <td>Lingkar Kepala / Umur (HCFA)</td>
                <td>{{ $hasil->hcfa ?? 'N/A' }} SD</td>
                <td>{{ $hasil->status_lk_u ?? '-' }}</td>
            </tr>
            @endif
        </table>
        @endif

        @if($pengukuran->redFlagLogs->isNotEmpty())
        <div class="section-title" style="border-left-color: #ef4444; background-color: #fef2f2;">Peringatan Medis (Red Flags)</div>
        <ul>
            @foreach($pengukuran->redFlagLogs as $flag)
                <li>
                    <strong>[{{ strtoupper($flag->severity) }}]</strong> {{ $flag->kategori_flag }}: {{ $flag->rekomendasi_rujukan }}
                </li>
            @endforeach
        </ul>
        @endif

        <div class="section-title">Assessment & Plan (Klinis)</div>
        <div class="assessment-box">
            @if($assessment)
                <h4 style="margin-top:0;">Assessment:</h4>
                <div style="white-space: pre-wrap; margin-bottom: 15px;">{{ $assessment->assessment_final }}</div>
                
                <h4>Plan:</h4>
                <div style="white-space: pre-wrap;">{{ $assessment->plan_final }}</div>
            @else
                <p><i>Assessment belum disetujui/disimpan ke rekam medis.</i></p>
            @endif
        </div>

        <div class="footer">
            <p>Dicetak pada: {{ date('d M Y H:i') }}</p>
            <p>Dokter Pemeriksa,</p>
            <div class="signature-line"></div>
            <p>{{ $assessment?->dokter?->name ?? '....................................' }}</p>
        </div>
    </div>
</body>
</html>
