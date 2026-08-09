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

        // Rincian Perhitungan Matematis Z-Score
        $rincian = [];
        $rincian[] = "🧮 **Rincian Perhitungan Z-Score (Standar WHO):**";
        
        if ($currentHasil->waz !== null && $idealBBRef) {
            $L = round($idealBBRef->L, 4);
            $M = round($idealBBRef->M, 4);
            $S = round($idealBBRef->S, 4);
            $X = $currentPengukuran->berat_badan;
            if ($L == 0) {
                $hitung = "ln({$X} / {$M}) / {$S}";
            } else {
                $hitung = "((({$X} / {$M}) ^ {$L}) - 1) / ({$L} × {$S})";
            }
            $rincian[] = "- **Berat Badan (WAZ):**\n  Z = {$hitung} = **{$currentHasil->waz}**";
        }

        if ($currentHasil->haz !== null && $idealTBRef) {
            $L = round($idealTBRef->L, 4);
            $M = round($idealTBRef->M, 4);
            $S = round($idealTBRef->S, 4);
            $X = $currentPengukuran->tinggi_badan;
            if ($L == 0) {
                $hitung = "ln({$X} / {$M}) / {$S}";
            } else {
                $hitung = "((({$X} / {$M}) ^ {$L}) - 1) / ({$L} × {$S})";
            }
            $rincian[] = "- **Tinggi Badan (HAZ):**\n  Z = {$hitung} = **{$currentHasil->haz}**";
        }

        // Red flag injection
        if ($currentHasil->red_flag) {
            $narasi[] = "\n🚨 PERINGATAN MEDIS: " . $currentHasil->catatan_red_flag;
        }

        return implode("\n\n", $narasi);
    }

    public function calculateRDAValues(int $usiaBulan, string $jk, ?float $waz, ?float $haz, ?float $whz, float $bbAktual): array
    {
        $idealBBRef = \App\Models\WhoGrowthReference::where('indeks', 'waz')
                        ->where('jenis_kelamin', $jk)->where('usia_bulan', $usiaBulan)->first();
        
        $bbIdeal = $idealBBRef ? round($idealBBRef->M, 2) : $bbAktual;
        $rdaKaloriPerKg = $usiaBulan < 12 ? 110 : ($usiaBulan < 36 ? 100 : 90);
        
        // Logika Target Kalori berdasarkan status gizi
        if (($waz !== null && $waz < -2) || ($haz !== null && $haz < -2) || ($whz !== null && $whz < -2)) {
            // Kurang Gizi / Stunting -> Kejar Tumbuh (Gunakan BB Ideal)
            $targetKalori = round($rdaKaloriPerKg * $bbIdeal);
        } elseif (($waz !== null && $waz > 2) || ($whz !== null && $whz > 2)) {
            // Gizi Lebih / Obesitas -> Weight Management (Gunakan BB Ideal agar tidak overfeeding)
            $targetKalori = round($rdaKaloriPerKg * $bbIdeal);
        } else {
            // Gizi Normal -> Maintenance (Gunakan BB Aktual)
            $targetKalori = round($rdaKaloriPerKg * $bbAktual);
        }

        return [
            'bb_ideal' => $bbIdeal,
            'kkal_kebutuhan' => $targetKalori,
        ];
    }

    /**
     * Generate rekomendasi gizi RDA secara terpisah untuk semua kondisi gizi.
     */
    public function generateRDAText($currentHasil, $currentPengukuran): ?string
    {
        if (!$currentHasil || !$currentPengukuran || !$currentHasil->kkal_kebutuhan) return null;

        $usia = $currentPengukuran->usia_bulan ?? 0;
        $bbAktual = $currentPengukuran->berat_badan;
        $bbIdeal = $currentHasil->bb_ideal;
        $targetKalori = $currentHasil->kkal_kebutuhan;
        
        $rdaKaloriPerKg = $usia < 12 ? 110 : ($usia < 36 ? 100 : 90);
        $rdaProteinPerKg = $usia < 12 ? 2.0 : ($usia < 36 ? 1.5 : 1.2);

        $waz = $currentHasil->waz;
        $haz = $currentHasil->haz;
        $whz = $currentHasil->z_whz ?? $currentHasil->whz;

        $isKurang = (($waz !== null && $waz < -2) || ($haz !== null && $haz < -2) || ($whz !== null && $whz < -2));
        $isLebih = (($waz !== null && $waz > 2) || ($whz !== null && $whz > 2));

        if ($isKurang) {
            $targetProtein = round($rdaProteinPerKg * $bbIdeal, 1);
            $teksKondisi = "Karena indikator gizi anak kurang, perhitungan target kalori menggunakan **Berat Badan Ideal ({$bbIdeal} kg)** (Median WHO), bukan berat aktual untuk mengejar ketertinggalan pertumbuhan.";
            $rumusKalori = "{$rdaKaloriPerKg} Kkal/kg × {$bbIdeal} kg (BB Ideal)";
            $rumusProtein = "{$rdaProteinPerKg} g/kg × {$bbIdeal} kg (BB Ideal)";
            $saran = "Tingkatkan asupan kalori padat gizi secara bertahap dan gunakan protein hewani ganda.";
        } elseif ($isLebih) {
            $targetProtein = round($rdaProteinPerKg * $bbIdeal, 1);
            $teksKondisi = "Karena indikator gizi anak lebih (overweight), perhitungan target kalori dibatasi menggunakan **Berat Badan Ideal ({$bbIdeal} kg)** agar tidak terjadi kelebihan asupan kalori lebih lanjut.";
            $rumusKalori = "{$rdaKaloriPerKg} Kkal/kg × {$bbIdeal} kg (BB Ideal)";
            $rumusProtein = "{$rdaProteinPerKg} g/kg × {$bbIdeal} kg (BB Ideal)";
            $saran = "Fokus pada pengurangan makanan manis/lemak jenuh, perbanyak serat, dan aktivitas fisik.";
        } else {
            $targetProtein = round($rdaProteinPerKg * $bbAktual, 1);
            $teksKondisi = "Status gizi anak tergolong normal. Kebutuhan kalori dihitung berdasarkan **Berat Badan Aktual saat ini ({$bbAktual} kg)** untuk pemeliharaan (*maintenance*).";
            $rumusKalori = "{$rdaKaloriPerKg} Kkal/kg × {$bbAktual} kg (BB Aktual)";
            $rumusProtein = "{$rdaProteinPerKg} g/kg × {$bbAktual} kg (BB Aktual)";
            $saran = "Pertahankan asupan gizi seimbang harian dan rutinitas aktivitas fisik.";
        }

        return "{$teksKondisi}\n\n"
             . "- **Standar Referensi Gizi (Berdasarkan Usia {$usia} Bulan):**\n"
             . "  • RDA Kalori: **{$rdaKaloriPerKg} Kkal / kg BB**\n"
             . "  • Kebutuhan Protein: **{$rdaProteinPerKg} gram / kg BB**\n\n"
             . "- **Target Energi (Kalori):**\n"
             . "  *Rumus: Standar RDA Kalori × BB Acuan*\n"
             . "  Hasil: {$rumusKalori} = **{$targetKalori} Kkal/hari**\n\n"
             . "- **Target Protein:**\n"
             . "  *Rumus: Standar Kebutuhan Protein × BB Acuan*\n"
             . "  Hasil: {$rumusProtein} = **{$targetProtein} gram/hari**\n\n"
             . "*(Rekomendasi Klinis: {$saran})*";
    }
}
