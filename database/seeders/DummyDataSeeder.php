<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Anak;
use App\Models\Pengukuran;
use App\Models\HasilStatusGizi;
use App\Models\RedFlagLog;
use App\Models\User;
use Carbon\Carbon;
use App\Services\GrowthCalculationService;
use App\Services\ZScoreService;
use App\Services\NutritionService;
use App\Services\RedFlagService;

class DummyDataSeeder extends Seeder
{
    public function run(
        GrowthCalculationService $growthService,
        ZScoreService $zScoreService,
        NutritionService $nutritionService,
        RedFlagService $redFlagService
    ): void {
        // Hapus data lama (disable foreign key check sementara agar bisa truncate)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        RedFlagLog::truncate();
        HasilStatusGizi::truncate();
        Pengukuran::truncate();
        Anak::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Ambil ID User pertama (sebagai petugas)
        $petugas = User::first();
        $petugasId = $petugas ? $petugas->id : 1;
        
        $anaksData = [
            // Normal (12 Bulan)
            ['nama' => 'Budi Santoso', 'jk' => 'L', 'usia_bulan' => 12, 'bb' => 9.5, 'tb' => 75.0, 'lila' => 13.5, 'lk' => 46.0],
            // Stunting (24 Bulan)
            ['nama' => 'Ani Pertiwi', 'jk' => 'P', 'usia_bulan' => 24, 'bb' => 10.0, 'tb' => 80.0, 'lila' => 14.0, 'lk' => 48.0],
            // Obesitas (36 Bulan)
            ['nama' => 'Candra Wijaya', 'jk' => 'L', 'usia_bulan' => 36, 'bb' => 20.0, 'tb' => 95.0, 'lila' => 16.0, 'lk' => 50.0],
            // Wasted / Gizi Kurang Akut (9 Bulan)
            ['nama' => 'Dinda Lestari', 'jk' => 'P', 'usia_bulan' => 9, 'bb' => 6.5, 'tb' => 70.0, 'lila' => 12.0, 'lk' => 44.0],
            // Normal (6 Bulan)
            ['nama' => 'Eko Prasetyo', 'jk' => 'L', 'usia_bulan' => 6, 'bb' => 7.8, 'tb' => 67.0, 'lila' => 14.5, 'lk' => 43.0],
            // Severely Wasted (18 Bulan)
            ['nama' => 'Fani Amelia', 'jk' => 'P', 'usia_bulan' => 18, 'bb' => 7.0, 'tb' => 78.0, 'lila' => 11.0, 'lk' => 45.0],
            // Tinggi & Normal (48 Bulan)
            ['nama' => 'Gilang Ramadhan', 'jk' => 'L', 'usia_bulan' => 48, 'bb' => 16.5, 'tb' => 105.0, 'lila' => 15.5, 'lk' => 51.0],
            // Mikrosefali (15 Bulan)
            ['nama' => 'Hana Putri', 'jk' => 'P', 'usia_bulan' => 15, 'bb' => 9.2, 'tb' => 77.0, 'lila' => 14.2, 'lk' => 40.0],
            // Growth Faltering (Anak ini dikasih 2 data ukuran berurutan dengan BB stagnan)
            ['nama' => 'Irfan Hakim', 'jk' => 'L', 'usia_bulan' => 10, 'bb' => 8.2, 'tb' => 73.0, 'lila' => 13.8, 'lk' => 45.0, 'faltering' => true],
            // Normal Bayi Muda (3 Bulan)
            ['nama' => 'Juwita Ningsih', 'jk' => 'P', 'usia_bulan' => 3, 'bb' => 5.8, 'tb' => 60.0, 'lila' => 12.5, 'lk' => 40.0],
        ];

        foreach ($anaksData as $index => $data) {
            $tglLahir = Carbon::now()->subMonths($data['usia_bulan'])->format('Y-m-d');
            
            $anak = Anak::create([
                'nik' => '327' . str_pad($index + 1, 13, '0', STR_PAD_LEFT),
                'nama' => $data['nama'],
                'tanggal_lahir' => $tglLahir,
                'jenis_kelamin' => $data['jk'],
                'nama_ortu' => 'Orang Tua ' . $data['nama'],
                'alamat' => 'Jl. Contoh Dummy No. ' . ($index + 1),
            ]);

            // Tentukan jumlah pengukuran, maksimal 10 atau sebatas usianya
            $jumlahUkur = min(10, $data['usia_bulan'] + 1);

            for ($i = $jumlahUkur; $i >= 1; $i--) {
                $bulanMundur = $i - 1; // Jika 10 records, berjalan dari 9, 8, ..., 0
                $usiaSaatIni = $data['usia_bulan'] - $bulanMundur;
                $tglUkur = Carbon::now()->subMonths($bulanMundur)->format('Y-m-d');
                
                // Simulasi regresi linier mundur
                if (isset($data['faltering']) && $data['faltering'] && $bulanMundur <= 1) {
                    // Kasus faltering: 2 bulan terakhir nyaris tidak naik
                    $bbSaatIni = $data['bb'] - ($bulanMundur * 0.05);
                    $tbSaatIni = $data['tb'] - ($bulanMundur * 0.5);
                    $lkSaatIni = $data['lk'] - ($bulanMundur * 0.1);
                    $lilaSaatIni = $data['lila'] - ($bulanMundur * 0.05);
                } else {
                    // Normal mundur
                    $bbSaatIni = $data['bb'] - ($bulanMundur * 0.3); // rata-rata naik 300g per bulan
                    $tbSaatIni = $data['tb'] - ($bulanMundur * 0.8); // rata-rata naik 0.8cm per bulan
                    $lkSaatIni = $data['lk'] - ($bulanMundur * 0.2);
                    $lilaSaatIni = $data['lila'] - ($bulanMundur * 0.1);
                    
                    // Sedikit random noise agar kurva terlihat organik
                    $bbSaatIni += (rand(-5, 5) / 100);
                    $tbSaatIni += (rand(-5, 5) / 100);
                }
                
                // Mencegah nilai turun di bawah ambang biologis
                $bbSaatIni = round(max(2.0, $bbSaatIni), 2);
                $tbSaatIni = round(max(40.0, $tbSaatIni), 2);
                $lkSaatIni = round(max(30.0, $lkSaatIni), 2);
                $lilaSaatIni = round(max(8.0, $lilaSaatIni), 2);

                $caraUkur = $usiaSaatIni < 24 ? 'telentang' : 'berdiri';

                $pengukuran = Pengukuran::create([
                    'anak_id' => $anak->id,
                    'tanggal_ukur' => $tglUkur,
                    'usia_bulan' => $usiaSaatIni,
                    'berat_badan' => $bbSaatIni,
                    'tinggi_badan' => $tbSaatIni,
                    'cara_ukur' => $caraUkur,
                    'lingkar_kepala' => $lkSaatIni,
                    'lila' => $lilaSaatIni,
                    'alat_ukur_bb' => 'Timbangan Digital (Dinkes)',
                    'alat_ukur_tb' => 'Infantometer',
                    'petugas_id' => $petugasId,
                ]);

                // Hitung Indikator Gizi
                $imt = $growthService->hitungIMT($bbSaatIni, $tbSaatIni);
                $waz = $zScoreService->getZScore('waz', $anak->jenis_kelamin, $usiaSaatIni, $bbSaatIni);
                $haz = $zScoreService->getZScore('haz', $anak->jenis_kelamin, $usiaSaatIni, $tbSaatIni);
                $bmiz = $zScoreService->getZScore('bmiz', $anak->jenis_kelamin, $usiaSaatIni, $imt);
                $whz = $zScoreService->getWHZ($anak->jenis_kelamin, $tbSaatIni, $bbSaatIni, $usiaSaatIni, $caraUkur);
                $hcfa = $zScoreService->getZScore('hcfa', $anak->jenis_kelamin, $usiaSaatIni, $lkSaatIni);

                // Ambil data bulan sebelumnya untuk evaluasi narasi dan red flag
                $previousPengukuran = Pengukuran::where('anak_id', $anak->id)
                    ->where('tanggal_ukur', '<', $tglUkur)
                    ->orderBy('tanggal_ukur', 'desc')
                    ->first();
                $previousHasil = $previousPengukuran ? $previousPengukuran->hasilStatusGizi : null;

                $hasil = HasilStatusGizi::create([
                    'pengukuran_id' => $pengukuran->id,
                    'waz' => $waz,
                    'haz' => $haz,
                    'bmiz' => $bmiz,
                    'hcfa' => $hcfa,
                    'whz' => $whz,
                    'status_bb_u' => $waz !== null ? $nutritionService->determineStatusBBU($waz) : null,
                    'status_tb_u' => $haz !== null ? $nutritionService->determineStatusTBU($haz) : null,
                    'status_imt_u' => $bmiz !== null ? $nutritionService->determineStatusIMTU($bmiz) : null,
                    'status_lk_u' => $hcfa !== null ? $nutritionService->determineStatusLKU($hcfa) : null,
                    'status_bb_tb' => $whz !== null ? $nutritionService->determineStatusBBTB($whz) : null,
                    'status_lila' => $nutritionService->determineStatusLiLA($lilaSaatIni, $usiaSaatIni),
                    'red_flag' => false,
                ]);

                $flags = $redFlagService->evaluasi($pengukuran, $hasil);
                if (count($flags) > 0) {
                    $hasil->update(['red_flag' => true]);
                    foreach ($flags as $flag) {
                        RedFlagLog::create([
                            'pengukuran_id' => $pengukuran->id,
                            'anak_id' => $anak->id,
                            'kategori_flag' => $flag['kategori'],
                            'severity' => $flag['severity'],
                            'nilai_pemicu' => $flag['nilai'],
                            'rekomendasi_rujukan' => $flag['rekomendasi'],
                            'status' => 'baru'
                        ]);
                    }
                }

                $narasi = $nutritionService->generateNarrative($hasil, $previousHasil, $pengukuran, $previousPengukuran);
                $hasil->update(['narasi_interpretasi' => $narasi]);
            }
        }
    }
}
