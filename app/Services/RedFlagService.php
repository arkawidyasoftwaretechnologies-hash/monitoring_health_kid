<?php

namespace App\Services;

use App\Models\Pengukuran;
use App\Models\HasilStatusGizi;
use Illuminate\Support\Facades\Log;

class RedFlagService
{
    public function evaluasi(Pengukuran $p, HasilStatusGizi $hasil): array
    {
        $flags = [];

        // --- Gizi akut (WHZ) ---
        if ($hasil->whz !== null) {
            if ($hasil->whz < -3) {
                $flags[] = $this->buatFlag('gizi_buruk_akut', 'merah', $hasil->whz,
                    'Rujuk segera ke Puskesmas/RS untuk tata laksana gizi buruk akut (SOP tiered referral tier-3)');
            } elseif ($hasil->whz < -2) {
                $flags[] = $this->buatFlag('gizi_kurang_akut', 'kuning', $hasil->whz,
                    'Pantauan intensif kader + konsultasi gizi Puskesmas (tier-2)');
            } elseif ($hasil->whz > 3) {
                $flags[] = $this->buatFlag('obesitas', 'merah', $hasil->whz,
                    'Rujuk konsultasi gizi/dokter anak (tier-2/3)');
            } elseif ($hasil->whz > 2) {
                $flags[] = $this->buatFlag('overweight', 'kuning', $hasil->whz,
                    'Edukasi pola makan, pantau tren (tier-1)');
            }
        }

        // --- Stunting (HAZ) ---
        if ($hasil->haz !== null) {
            if ($hasil->haz < -3) {
                $flags[] = $this->buatFlag('stunting_berat', 'merah', $hasil->haz,
                    'Rujuk Puskesmas untuk evaluasi stunting berat (tier-2/3)');
            } elseif ($hasil->haz < -2) {
                $flags[] = $this->buatFlag('stunting', 'kuning', $hasil->haz,
                    'Pantauan rutin + intervensi gizi lokal (tier-1)');
            }
        }

        // --- Underweight (WAZ) ---
        if ($hasil->waz !== null) {
            if ($hasil->waz < -3) {
                $flags[] = $this->buatFlag('underweight_berat', 'merah', $hasil->waz,
                    'Rujuk Puskesmas (tier-2)');
            } elseif ($hasil->waz < -2) {
                $flags[] = $this->buatFlag('underweight', 'kuning', $hasil->waz,
                    'Pantauan rutin (tier-1)');
            }
        }

        // --- LiLA ---
        if ($p->lila !== null && $p->usia_bulan >= 6 && $p->usia_bulan <= 59) {
            if ($p->lila < 11.5) {
                $flags[] = $this->buatFlag('lila_gizi_buruk', 'merah', $p->lila,
                    'Rujuk segera (tier-3)');
            } elseif ($p->lila < 12.5) {
                $flags[] = $this->buatFlag('lila_gizi_kurang', 'kuning', $p->lila,
                    'Pantauan intensif (tier-2)');
            }
        }

        // --- Lingkar kepala ---
        if ($hasil->hcfa !== null) {
            if ($hasil->hcfa < -2) {
                $flags[] = $this->buatFlag('mikrosefali', 'merah', $hasil->hcfa,
                    'Rujuk dokter anak/neurologi (tier-3)');
            } elseif ($hasil->hcfa > 2) {
                $flags[] = $this->buatFlag('makrosefali', 'merah', $hasil->hcfa,
                    'Rujuk dokter anak/neurologi (tier-3)');
            }
        }

        // --- Growth faltering (velocity, butuh data pengukuran sebelumnya) ---
        $faltering = $this->cekGrowthFaltering($p);
        if ($faltering) {
            $flags[] = $faltering;
        }

        return $flags;
    }

    private function cekGrowthFaltering(Pengukuran $p): ?array
    {
        $riwayat = Pengukuran::where('anak_id', $p->anak_id)
            ->where('tanggal_ukur', '<', $p->tanggal_ukur)
            ->orderByDesc('tanggal_ukur')
            ->limit(2)
            ->get();

        if ($riwayat->count() < 2) return null;

        $kenaikan = [];
        $bandingkan = [$p, ...$riwayat->take(1)]; // pengukuran ini vs 1 sebelumnya, dst.
        foreach ([$p, $riwayat[0], $riwayat[1]] as $i => $row) {
            if ($i === 0) continue;
            $sebelumnya = $i === 1 ? $p : $riwayat[0];
            $kenaikan[] = $sebelumnya->berat_badan - $row->berat_badan;
        }

        $usiaHari = $p->usia_bulan * 30; // estimasi kasar
        $ambangMinimal = $this->ambangKenaikanBB($usiaHari); // g/bulan, dari tabel WHO velocity
        $keduaKurang = collect($kenaikan)->every(fn($k) => ($k * 1000) < ($ambangMinimal * 0.5));

        if ($keduaKurang) {
            return $this->buatFlag('growth_faltering', 'kuning', end($kenaikan),
                'Kenaikan BB di bawah 50% standar WHO 2 bulan berturut — evaluasi asupan & infeksi (tier-1/2)');
        }
        return null;
    }

    private function ambangKenaikanBB(int $usiaHari): float
    {
        $bulan = intdiv($usiaHari, 30);
        return match(true) {
            $bulan < 3  => 800,
            $bulan < 6  => 550,
            $bulan < 9  => 450,
            $bulan < 12 => 350,
            default     => 250,
        };
    }

    private function buatFlag(string $kategori, string $severity, float $nilai, string $rekomendasi): array
    {
        return compact('kategori', 'severity', 'nilai', 'rekomendasi');
    }
}
