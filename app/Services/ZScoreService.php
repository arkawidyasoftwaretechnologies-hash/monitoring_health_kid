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
}
