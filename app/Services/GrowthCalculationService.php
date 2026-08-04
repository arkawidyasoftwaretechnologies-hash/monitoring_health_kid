<?php

namespace App\Services;

use Carbon\Carbon;

class GrowthCalculationService
{
    /**
     * Hitung umur dalam bulan (pembulatan ke bawah atau desimal).
     */
    public function hitungUsiaBulan(string $tanggalLahir, string $tanggalUkur = null): int
    {
        $lahir = Carbon::parse($tanggalLahir);
        $ukur = $tanggalUkur ? Carbon::parse($tanggalUkur) : Carbon::now();
        
        return $lahir->diffInMonths($ukur);
    }

    /**
     * Hitung Indeks Massa Tubuh (IMT)
     * IMT = BB (kg) / (TB (m))^2
     */
    public function hitungIMT(float $beratBadan, float $tinggiBadanCm): float
    {
        if ($tinggiBadanCm <= 0) return 0;
        $tinggiMeter = $tinggiBadanCm / 100;
        return round($beratBadan / ($tinggiMeter * $tinggiMeter), 2);
    }
}
