<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pengukuran Stunting</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Laporan Pengukuran Stunting</h2>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Anak</th>
                <th>NIK</th>
                <th>Jenis Kelamin</th>
                <th>Tanggal Ukur</th>
                <th>Usia (Bulan)</th>
                <th>Berat (kg)</th>
                <th>Tinggi (cm)</th>
                <th>LK (cm)</th>
                <th>LiLA (cm)</th>
                <th>IMT/U (BMIZ)</th>
                <th>Status IMT/U</th>
                <th>WAZ</th>
                <th>Status BB/U</th>
                <th>HAZ</th>
                <th>Status TB/U</th>
                <th>LK/U (HCFA)</th>
                <th>Status LK/U</th>
                <th>Status LiLA</th>
                <th>Red Flag</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pengukurans as $index => $row)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $row->anak->nama ?? '-' }}</td>
                <td>{{ $row->anak->nik ?? '-' }}</td>
                <td>{{ $row->anak->jenis_kelamin ?? '-' }}</td>
                <td>{{ $row->tanggal_ukur }}</td>
                <td>{{ $row->usia_bulan }}</td>
                <td>{{ $row->berat_badan }}</td>
                <td>{{ $row->tinggi_badan }}</td>
                <td>{{ $row->lingkar_kepala ?? '-' }}</td>
                <td>{{ $row->lila ?? '-' }}</td>
                <td>{{ $row->hasilStatusGizi->bmiz ?? '-' }}</td>
                <td>{{ $row->hasilStatusGizi->status_imt_u ?? '-' }}</td>
                <td>{{ $row->hasilStatusGizi->waz ?? '-' }}</td>
                <td>{{ $row->hasilStatusGizi->status_bb_u ?? '-' }}</td>
                <td>{{ $row->hasilStatusGizi->haz ?? '-' }}</td>
                <td>{{ $row->hasilStatusGizi->status_tb_u ?? '-' }}</td>
                <td>{{ $row->hasilStatusGizi->hcfa ?? '-' }}</td>
                <td>{{ $row->hasilStatusGizi->status_lk_u ?? '-' }}</td>
                <td>{{ $row->hasilStatusGizi->status_lila ?? '-' }}</td>
                <td>{{ optional($row->hasilStatusGizi)->red_flag ? 'Ya' : 'Tidak' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
