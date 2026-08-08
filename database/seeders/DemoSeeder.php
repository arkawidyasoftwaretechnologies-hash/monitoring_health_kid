<?php

namespace Database\Seeders;

use App\Models\Anak;
use App\Models\HasilStatusGizi;
use App\Models\Pengukuran;
use App\Models\WhoGrowthReference;
use App\Services\GrowthCalculationService;
use App\Services\NutritionService;
use App\Services\ZScoreService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(ZScoreService $zScoreService, NutritionService $nutritionService, GrowthCalculationService $growthService): void
    {
        // 1. Truncate tables to ensure a clean slate
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Anak::truncate();
        Pengukuran::truncate();
        HasilStatusGizi::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 2. Define 10 Demo Children
        $demoChildren = [
            // NORMAL (Mendekati Median WHO)
            ['nama' => 'Budi Santoso (Normal)', 'jenis_kelamin' => 'L', 'tipe' => 'normal'],
            ['nama' => 'Siti Aminah (Normal)', 'jenis_kelamin' => 'P', 'tipe' => 'normal'],
            ['nama' => 'Rizky Pratama (Normal)', 'jenis_kelamin' => 'L', 'tipe' => 'normal'],
            ['nama' => 'Ayu Lestari (Normal)', 'jenis_kelamin' => 'P', 'tipe' => 'normal'],

            // ANOMALI BAWAH (Stunting / Gizi Buruk)
            ['nama' => 'Dani Saputra (Stunting/Gizi Buruk)', 'jenis_kelamin' => 'L', 'tipe' => 'bawah'],
            ['nama' => 'Nisa Fitriani (Stunting/Gizi Buruk)', 'jenis_kelamin' => 'P', 'tipe' => 'bawah'],
            ['nama' => 'Galih Firmansyah (Stunting/Gizi Buruk)', 'jenis_kelamin' => 'L', 'tipe' => 'bawah'],

            // ANOMALI ATAS (Overweight / Makrosefali)
            ['nama' => 'Dinda Paramita (Overweight)', 'jenis_kelamin' => 'P', 'tipe' => 'atas'],
            ['nama' => 'Bima Sena (Overweight)', 'jenis_kelamin' => 'L', 'tipe' => 'atas'],
            ['nama' => 'Rina Melati (Overweight)', 'jenis_kelamin' => 'P', 'tipe' => 'atas'],
        ];

        foreach ($demoChildren as $index => $childData) {
            // Anak lahir 11 bulan yang lalu, sehingga sekarang usia 11 bulan
            $tanggalLahir = Carbon::now()->subMonths(11)->startOfMonth();
            
            $anak = Anak::create([
                'nik' => '35' . str_pad(rand(10000000000000, 99999999999999), 14, '0', STR_PAD_LEFT),
                'nama' => $childData['nama'],
                'jenis_kelamin' => $childData['jenis_kelamin'],
                'tanggal_lahir' => $tanggalLahir->format('Y-m-d'),
                'nama_ortu' => 'Bpk/Ibu ' . strtok($childData['nama'], " "),
                'alamat' => 'Jl. Mawar No. ' . rand(1, 100),
                'puskesmas_id' => null,
            ]);

            // Buat 6 pengukuran histori (dari usia 6 bulan hingga 11 bulan)
            for ($usiaBulan = 6; $usiaBulan <= 11; $usiaBulan++) {
                $tanggalUkur = $tanggalLahir->copy()->addMonths($usiaBulan);

                // Dapatkan Nilai Mentah berdasarkan Tipe Anomali
                $beratBadan = $this->generateRawValue('waz', $childData['jenis_kelamin'], $usiaBulan, $childData['tipe']);
                $tinggiBadan = $this->generateRawValue('haz', $childData['jenis_kelamin'], $usiaBulan, $childData['tipe']);
                $lingkarKepala = $this->generateRawValue('hcfa', $childData['jenis_kelamin'], $usiaBulan, $childData['tipe']);
                
                // Tambahkan sedikit noise agar grafik tidak terlalu mulus linear
                $beratBadan += (rand(-2, 2) / 10);
                $tinggiBadan += (rand(-5, 5) / 10);
                $lingkarKepala += (rand(-2, 2) / 10);
                
                // LiLA logic
                $lila = 14.0; // normal
                if ($childData['tipe'] == 'bawah') $lila = 11.2; // Gizi buruk
                if ($childData['tipe'] == 'atas') $lila = 17.5; // Overweight

                $pengukuran = Pengukuran::create([
                    'anak_id' => $anak->id,
                    'tanggal_ukur' => $tanggalUkur->format('Y-m-d'),
                    'usia_bulan' => $usiaBulan,
                    'berat_badan' => $beratBadan,
                    'tinggi_badan' => $tinggiBadan,
                    'lingkar_kepala' => $lingkarKepala,
                    'lila' => $lila,
                    'cara_ukur' => $usiaBulan < 24 ? 'telentang' : 'berdiri',
                    'petugas_id' => null,
                ]);

                // Hitung Z-Scores menggunakan Services
                $tinggiMeter = $tinggiBadan / 100;
                $imt = round($beratBadan / ($tinggiMeter * $tinggiMeter), 2);
                
                $waz = $zScoreService->getZScore('waz', $anak->jenis_kelamin, $usiaBulan, $beratBadan);
                $haz = $zScoreService->getZScore('haz', $anak->jenis_kelamin, $usiaBulan, $tinggiBadan);
                $bmiz = $zScoreService->getZScore('bmiz', $anak->jenis_kelamin, $usiaBulan, $imt);
                $whz = $zScoreService->getZScore('whz', $anak->jenis_kelamin, $usiaBulan, $beratBadan, $tinggiBadan);
                $hcfa = $zScoreService->getZScore('hcfa', $anak->jenis_kelamin, $usiaBulan, $lingkarKepala);

                // Status Gizi
                $status_bbu = $waz !== null ? $nutritionService->determineStatusBBU($waz) : null;
                $status_tbu = $haz !== null ? $nutritionService->determineStatusTBU($haz) : null;
                $status_imtu = $bmiz !== null ? $nutritionService->determineStatusIMTU($bmiz) : null;
                
                $redFlags = [];
                if ($waz !== null && $haz !== null && $whz !== null) {
                    $redFlags = $nutritionService->checkRedFlag($waz, $haz, $whz, $hcfa, $lila, $usiaBulan);
                }

                HasilStatusGizi::create([
                    'pengukuran_id' => $pengukuran->id,
                    'waz' => $waz,
                    'haz' => $haz,
                    'bmiz' => $bmiz,
                    'whz' => $whz,
                    'hcfa' => $hcfa,
                    'status_bb_u' => $status_bbu,
                    'status_tb_u' => $status_tbu,
                    'status_imt_u' => $status_imtu,
                    'red_flag' => count($redFlags) > 0,
                    'catatan_red_flag' => count($redFlags) > 0 ? implode(', ', $redFlags) : null,
                ]);
            }
        }
    }

    /**
     * Helper untuk mengkalkulasi nilai aktual (X) berdasarkan Z-score target
     * X = M * (1 + Z * L * S)^(1/L)
     */
    private function generateRawValue(string $indeks, string $jk, int $usia, string $tipe): float
    {
        $ref = WhoGrowthReference::where('indeks', $indeks)
            ->where('jenis_kelamin', $jk)
            ->where('usia_bulan', $usia)
            ->first();

        if (!$ref) return 10.0; // fallback

        $L = (float) $ref->L;
        $M = (float) $ref->M;
        $S = (float) $ref->S;

        $targetZ = 0; // Normal
        if ($tipe == 'atas') {
            $targetZ = 3.5; // Overweight / Very Tall
        } elseif ($tipe == 'bawah') {
            $targetZ = -3.5; // Severe Stunting / Wasting
        }

        if ($L == 0) {
            $X = $M * exp($targetZ * $S);
        } else {
            $base = 1 + ($targetZ * $L * $S);
            if ($base < 0) $base = 0.001; // prevent complex numbers
            $X = $M * pow($base, 1 / $L);
        }

        return round($X, 2);
    }
}
