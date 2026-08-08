<?php

namespace App\Services;

class NutritionService
{
    /**
     * Menentukan status gizi berdasarkan WAZ (BB/U)
     */
    public function determineStatusBBU(float $waz): string
    {
        if ($waz < -3) return 'Sangat kurang (severely underweight)';
        if ($waz >= -3 && $waz < -2) return 'Kurang (underweight)';
        if ($waz >= -2 && $waz <= 1) return 'Berat badan normal';
        return 'Risiko berat badan lebih'; // > 1
    }

    /**
     * Menentukan status gizi berdasarkan HAZ (TB/U atau PB/U)
     */
    public function determineStatusTBU(float $haz): string
    {
        if ($haz < -3) return 'Sangat pendek (severely stunted)';
        if ($haz >= -3 && $haz < -2) return 'Pendek (stunted)';
        if ($haz >= -2 && $haz <= 3) return 'Normal';
        return 'Tinggi'; // > 3
    }

    /**
     * Menentukan status gizi berdasarkan WHZ (BB/TB)
     */
    public function determineStatusBBTB(float $whz): string
    {
        if ($whz < -3) return 'Gizi buruk (severely wasted)';
        if ($whz >= -3 && $whz < -2) return 'Gizi kurang (wasted)';
        if ($whz >= -2 && $whz <= 1) return 'Gizi baik (normal)';
        if ($whz > 1 && $whz <= 2) return 'Berisiko gizi lebih (possible risk of overweight)';
        if ($whz > 2 && $whz <= 3) return 'Gizi lebih (overweight)';
        return 'Obesitas (obese)'; // > 3
    }

    /**
     * Menentukan status gizi berdasarkan IMT/U (BMIZ)
     */
    public function determineStatusIMTU(float $bmiz): string
    {
        if ($bmiz < -3) return 'Gizi buruk (severely wasted)';
        if ($bmiz >= -3 && $bmiz < -2) return 'Gizi kurang (wasted)';
        if ($bmiz >= -2 && $bmiz <= 1) return 'Gizi baik (normal)';
        if ($bmiz > 1 && $bmiz <= 2) return 'Berisiko gizi lebih (possible risk of overweight)';
        if ($bmiz > 2 && $bmiz <= 3) return 'Gizi lebih (overweight)';
        return 'Obesitas (obese)'; // > 3
    }

    /**
     * Menentukan status berdasarkan Lingkar Kepala (HCFA)
     */
    public function determineStatusLKU(float $hcfa): string
    {
        if ($hcfa < -2) return 'Mikrosefali (Microcephaly)';
        if ($hcfa >= -2 && $hcfa <= 2) return 'Normal';
        return 'Makrosefali (Macrocephaly)'; // > 2
    }

    /**
     * Menentukan status berdasarkan Lingkar Lengan Atas (LiLA)
     * Valid secara medis hanya untuk anak usia 6 - 59 bulan.
     */
    public function determineStatusLiLA(?float $lila, int $usiaBulan): ?string
    {
        if ($lila === null) return null;
        if ($usiaBulan < 6 || $usiaBulan > 59) return null; // LiLA tidak standar untuk < 6 bulan

        if ($lila < 11.5) return 'Gizi Buruk Akut (SAM)';
        if ($lila >= 11.5 && $lila < 12.5) return 'Gizi Kurang Akut (MAM)';
        return 'Normal';
    }

    /**
     * Mengecek apakah ada red flag
     * Menambahkan pengecekan null safety dan batasan usia.
     */
    public function checkRedFlag(?float $waz, ?float $haz, ?float $whz, ?float $hcfa, ?float $lila, int $usiaBulan): array
    {
        $flags = [];
        if ($haz !== null && $haz < -3) $flags[] = 'HAZ < -3SD (Severely Stunted)';
        if ($waz !== null && $waz < -3) $flags[] = 'WAZ < -3SD (Severely Underweight)';
        if ($whz !== null && $whz < -3) $flags[] = 'WHZ/BMIZ < -3SD (Severely Wasted)';
        
        if ($hcfa !== null) {
            if ($hcfa < -2) $flags[] = 'Lingkar Kepala < -2SD (Mikrosefali)';
            if ($hcfa > 2) $flags[] = 'Lingkar Kepala > +2SD (Makrosefali)';
        }

        // LiLA red flag valid hanya untuk 6-59 bln
        if ($lila !== null && $usiaBulan >= 6 && $usiaBulan <= 59 && $lila < 11.5) {
            $flags[] = 'LiLA < 11.5cm (SAM - Severe Acute Malnutrition)';
        }

        if (empty($flags)) {
            return ['is_red_flag' => false, 'catatan' => null];
        }
        return ['is_red_flag' => true, 'catatan' => implode(', ', $flags) . ' - Segera rujuk ke faskes/dokter spesialis.'];
    }

    /**
     * Generate narasi otomatis (interpretasi kunjungan) untuk memandu kader.
     */
    public function generateNarrative($currentHasil, $previousHasil, $currentPengukuran, $previousPengukuran): string
    {
        if (!$currentHasil || !$currentPengukuran) return "Data tidak lengkap untuk diinterpretasikan.";

        $narasi = [];
        
        // Fetch Ideal References
        $jk = $currentPengukuran->anak->jenis_kelamin ?? 'L';
        $usia = $currentPengukuran->usia_bulan ?? 0;
        
        $idealBBRef = \App\Models\WhoGrowthReference::where('indeks', 'waz')
                        ->where('jenis_kelamin', $jk)->where('usia_bulan', $usia)->first();
                        
        $idealTBRef = \App\Models\WhoGrowthReference::where('indeks', 'haz')
                        ->where('jenis_kelamin', $jk)->where('usia_bulan', $usia)->first();

        // 1. Kesimpulan Berat Badan
        if ($currentHasil->waz !== null) {
            $idealBBText = $idealBBRef ? "ideal menurut standar WHO adalah sekitar **" . round($idealBBRef->M, 1) . " kg**" : "ideal belum ditemukan";
            $narasi[] = "📊 **Pemantauan Berat Badan:**\nSaat ini berat badan anak terukur **{$currentPengukuran->berat_badan} kg**. Untuk anak seumurannya ({$usia} bulan), berat {$idealBBText}. Berdasarkan hal ini, status berat badan anak tergolong **{$currentHasil->status_bb_u}**.";
        }

        // 2. Kesimpulan Tinggi Badan
        if ($currentHasil->haz !== null) {
            $idealTBText = $idealTBRef ? "idealnya berada di kisaran **" . round($idealTBRef->M, 1) . " cm**" : "ideal belum ditemukan";
            
            // Tambahkan sedikit empati jika stunting
            $isStunted = str_contains(strtolower($currentHasil->status_tb_u), 'pendek') || str_contains(strtolower($currentHasil->status_tb_u), 'stunted');
            $tambahan = $isStunted ? " Kondisi ini membutuhkan perhatian khusus untuk mengejar ketertinggalan pertumbuhannya." : "";
            
            $narasi[] = "📏 **Pemantauan Tinggi/Panjang Badan:**\nTinggi/panjang badan anak tercatat **{$currentPengukuran->tinggi_badan} cm**, sementara {$idealTBText}. Pertumbuhan tinggi anak saat ini masuk dalam kategori **{$currentHasil->status_tb_u}**.{$tambahan}";
        }

        // Bandingkan dengan bulan sebelumnya
        if ($previousPengukuran && $previousHasil) {
            $bbDiff = $currentPengukuran->berat_badan - $previousPengukuran->berat_badan;
            $tbDiff = $currentPengukuran->tinggi_badan - $previousPengukuran->tinggi_badan;
            
            if ($bbDiff < 0) {
                $narasi[] = "📉 **Evaluasi Tumbuh Kembang:**\nPerhatian, berat badan anak **turun " . abs(round($bbDiff, 2)) . " kg** dibandingkan bulan sebelumnya. Mohon agar asupan nutrisi dan pola makan di bulan ini dievaluasi kembali bersama tenaga kesehatan.";
            } elseif ($bbDiff == 0) {
                $narasi[] = "⚖️ **Evaluasi Tumbuh Kembang:**\nBerat badan anak **tidak mengalami kenaikan** dibandingkan bulan sebelumnya (stagnan). Perlu ditinjau kembali asupan kalori harian anak.";
            } else {
                $narasi[] = "📈 **Evaluasi Tumbuh Kembang:**\nKabar baik! Terdapat tren kenaikan berat badan sebesar **" . round($bbDiff, 2) . " kg** dari pengukuran terakhir. Terus pertahankan pola asuh dan gizi seimbangnya!";
            }
        } else {
            $narasi[] = "📌 *Ini adalah catatan pengukuran pertama anak di dalam sistem.*";
        }

        // Red flag injection
        if ($currentHasil->red_flag) {
            $narasi[] = "\n🚨 PERINGATAN MEDIS: " . $currentHasil->catatan_red_flag;
        }

        return implode("\n\n", $narasi);
    }
}
