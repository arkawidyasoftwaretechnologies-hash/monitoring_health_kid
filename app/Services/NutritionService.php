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
     * Referensi umum: < 11.5 cm = SAM, 11.5 - 12.5 cm = MAM, > 12.5 cm = Normal
     */
    public function determineStatusLiLA(?float $lila): ?string
    {
        if ($lila === null) return null;
        if ($lila < 11.5) return 'Gizi Buruk Akut (SAM)';
        if ($lila >= 11.5 && $lila < 12.5) return 'Gizi Kurang Akut (MAM)';
        return 'Normal';
    }

    /**
     * Mengecek apakah ada red flag
     */
    public function checkRedFlag(float $waz, float $haz, float $whz, ?float $hcfa = null, ?float $lila = null): array
    {
        $flags = [];
        if ($haz < -3) $flags[] = 'HAZ < -3SD (Severely Stunted)';
        if ($waz < -3) $flags[] = 'WAZ < -3SD (Severely Underweight)';
        if ($whz < -3) $flags[] = 'WHZ/BMIZ < -3SD (Severely Wasted)';
        
        if ($hcfa !== null) {
            if ($hcfa < -2) $flags[] = 'Lingkar Kepala < -2SD (Mikrosefali)';
            if ($hcfa > 2) $flags[] = 'Lingkar Kepala > +2SD (Makrosefali)';
        }

        if ($lila !== null && $lila < 11.5) {
            $flags[] = 'LiLA < 11.5cm (SAM - Severe Acute Malnutrition)';
        }

        if (empty($flags)) {
            return ['is_red_flag' => false, 'catatan' => null];
        }
        return ['is_red_flag' => true, 'catatan' => implode(', ', $flags) . ' - Segera rujuk ke faskes/dokter spesialis.'];
    }
}
