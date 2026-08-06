<?php

namespace App\Services;

use App\Models\WhoGrowthReference;

class ZScoreService
{
    /**
     * Menghitung Z-score berdasarkan parameter LMS
     * Z = (((X / M) ^ L) - 1) / (L * S)
     */
    public function calculate(float $measurement, float $L, float $M, float $S): float
    {
        if ($L == 0) {
            $z = log($measurement / $M) / $S;
        } else {
            $z = (pow($measurement / $M, $L) - 1) / ($L * $S);
        }
        
        return round($z, 2);
    }

    /**
     * Mendapatkan Z-score spesifik untuk WAZ, HAZ, atau WHZ
     */
    public function getZScore(string $indeks, string $jenisKelamin, int $usiaBulan, float $measurement): ?float
    {
        $ref = WhoGrowthReference::where('indeks', $indeks)
            ->where('jenis_kelamin', $jenisKelamin)
            ->where('usia_bulan', $usiaBulan)
            ->first();

        if (!$ref) {
            return null; // Tidak ada data referensi
        }

        return $this->calculate($measurement, (float) $ref->L, (float) $ref->M, (float) $ref->S);
    }

    /**
     * Mendapatkan Z-score WHZ (BB/TB) berdasarkan tinggi badan dan cara ukur
     */
    public function getWHZ(string $jenisKelamin, float $tinggiBadan, float $beratBadan, int $usiaBulan, string $caraUkurAktual): ?float
    {
        // Aturan WHO: < 24 bulan diukur telentang (panjang). >= 24 bulan diukur berdiri (tinggi).
        $usiaHari = $usiaBulan * 30; // Estimasi hari
        $tipeUkurStandar = $usiaHari < 730 ? 'panjang' : 'tinggi';

        // Koreksi 0.7cm jika cara ukur aktual tidak sesuai standar usianya
        if ($usiaHari < 730 && $caraUkurAktual === 'berdiri') {
            $tinggiBadan += 0.7; // Harus dikonversi ke panjang
        } elseif ($usiaHari >= 730 && $caraUkurAktual === 'telentang') {
            $tinggiBadan -= 0.7; // Harus dikonversi ke tinggi
        }

        // Cari referensi dengan interpolasi
        $bawah = \Illuminate\Support\Facades\DB::table('who_whz_references')
            ->where('jenis_kelamin', $jenisKelamin)
            ->where('tipe_ukur', $tipeUkurStandar)
            ->where('panjang_tinggi', '<=', $tinggiBadan)
            ->orderByDesc('panjang_tinggi')
            ->first();

        $atas = \Illuminate\Support\Facades\DB::table('who_whz_references')
            ->where('jenis_kelamin', $jenisKelamin)
            ->where('tipe_ukur', $tipeUkurStandar)
            ->where('panjang_tinggi', '>=', $tinggiBadan)
            ->orderBy('panjang_tinggi')
            ->first();

        if (!$bawah || !$atas) return null;

        if ($bawah->panjang_tinggi == $atas->panjang_tinggi) {
            return $this->calculate($beratBadan, (float) $bawah->L, (float) $bawah->M, (float) $bawah->S);
        }

        // Interpolasi linear
        $rentang = $atas->panjang_tinggi - $bawah->panjang_tinggi;
        $porsi = ($tinggiBadan - $bawah->panjang_tinggi) / $rentang;

        $L = $bawah->L + $porsi * ($atas->L - $bawah->L);
        $M = $bawah->M + $porsi * ($atas->M - $bawah->M);
        $S = $bawah->S + $porsi * ($atas->S - $bawah->S);

        return $this->calculate($beratBadan, (float) $L, (float) $M, (float) $S);
    }
}
