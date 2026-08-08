<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hasil Pemeriksaan Tumbuh Kembang</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
        }
        .container {
            border: 2px solid #4ade80;
            border-radius: 10px;
            padding: 20px;
            max-width: 600px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            border-bottom: 2px dashed #ccc;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .title {
            font-size: 24px;
            font-weight: bold;
            color: #16a34a;
            margin: 0;
        }
        .subtitle {
            font-size: 18px;
            color: #555;
            margin: 5px 0 0 0;
        }
        .status-box {
            background-color: #f0fdf4;
            border-left: 5px solid #22c55e;
            padding: 15px;
            margin-bottom: 20px;
            font-size: 18px;
            font-weight: bold;
        }
        .status-kuning {
            background-color: #fefce8;
            border-left-color: #eab308;
        }
        .status-merah {
            background-color: #fef2f2;
            border-left-color: #ef4444;
        }
        .data-row {
            margin-bottom: 10px;
            font-size: 16px;
        }
        .data-label {
            font-weight: bold;
            display: inline-block;
            width: 150px;
        }
        .saran-box {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }
        .saran-title {
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 10px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 class="title">Hasil Pemeriksaan Tumbuh Kembang</h1>
            <p class="subtitle">{{ $anak->nama }} ({{ $pengukuran->usia_bulan }} Bulan)</p>
        </div>

        @php
            $statusClass = '';
            if (str_contains($statusRingkas, 'diperhatikan')) $statusClass = 'status-kuning';
            if (str_contains($statusRingkas, 'evaluasi')) $statusClass = 'status-merah';
        @endphp

        <div class="status-box {{ $statusClass }}">
            Status Tumbuh Kembang: {{ $statusRingkas }}
        </div>

        <div class="data-row">
            <span class="data-label">Berat Badan:</span>
            {{ $pengukuran->berat_badan }} kg
        </div>
        <div class="data-row">
            <span class="data-label">Panjang/Tinggi:</span>
            {{ $pengukuran->tinggi_badan }} cm
        </div>
        <div class="data-row">
            <span class="data-label">Lingkar Kepala:</span>
            {{ $pengukuran->lingkar_kepala }} cm
        </div>
        <div class="data-row">
            <span class="data-label">Tanggal Periksa:</span>
            {{ $pengukuran->tanggal_pengukuran }}
        </div>

        <div class="saran-box">
            <div class="saran-title">Saran & Tindak Lanjut dari Dokter:</div>
            <div style="white-space: pre-line;">{{ $saranDokter }}</div>
        </div>

        <div class="footer">
            Dicetak pada {{ date('d M Y H:i') }}<br>
            <i>Bawa dokumen ini pada kunjungan berikutnya.</i>
        </div>
    </div>
</body>
</html>
