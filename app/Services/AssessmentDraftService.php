<?php

namespace App\Services;

use App\Models\Pengukuran;
use App\Models\TemplateRekomendasi;

class AssessmentDraftService
{
    public function generate(Pengukuran $p): array
    {
        $flags = $p->redFlagLogs;

        $kondisiTerpicu = $flags->isEmpty()
            ? ['normal']
            : $flags->sortByDesc('severity')->pluck('kategori_flag')->unique()->toArray();

        $draftAssessment = [];
        $draftPlan = [];

        foreach ($kondisiTerpicu as $kondisi) {
            $template = TemplateRekomendasi::where('kondisi_pemicu', $kondisi)
                ->where('aktif', true)
                ->orderBy('urutan_prioritas')
                ->first();

            if (!$template) continue;

            $draftAssessment[] = $this->isiVariabel($template->template_assessment, $p);
            $draftPlan[] = $this->isiVariabel($template->template_plan, $p);
        }

        // Variabel RDA tetap disediakan jika template database ingin menggunakannya
        $gizi = $p->hasilStatusGizi;
        if ($gizi && ($gizi->z_waz < -2 || $gizi->z_haz < -2 || $gizi->z_whz < -2)) {
            // Kita tidak lagi memasukkan teks secara paksa di sini, 
            // karena sudah dipindahkan ke Kesimpulan Sistem.
        }

        return [
            'assessment' => implode("\n\n", $draftAssessment),
            'plan' => implode("\n\n", $draftPlan),
        ];
    }

    private function isiVariabel(string $template, Pengukuran $p): string
    {
        $anak = $p->anak;
        $whz = $p->hasilStatusGizi?->z_whz ?? 0;
        $haz = $p->hasilStatusGizi?->z_haz ?? 0;
        $waz = $p->hasilStatusGizi?->z_waz ?? 0;

        $jk = $p->anak->jenis_kelamin;
        $usia = $p->usia_bulan;
        $ref = \App\Models\WhoGrowthReference::where('indeks', 'waz')->where('jenis_kelamin', $jk)->where('usia_bulan', $usia)->first();
        $bbIdeal = $ref ? round($ref->M, 2) : 0;
        
        $rdaKaloriPerKg = $usia < 12 ? 110 : ($usia < 36 ? 100 : 90);
        $targetKalori = round($rdaKaloriPerKg * $bbIdeal);

        return strtr($template, [
            '[NAMA_ANAK]' => $anak->nama,
            '[USIA]'      => $this->formatUsia($p->usia_bulan),
            '[WHZ]'       => number_format($whz, 2),
            '[HAZ]'       => number_format($haz, 2),
            '[WAZ]'       => number_format($waz, 2),
            '[BB_IDEAL]'  => $bbIdeal,
            '[TARGET_KALORI]' => $targetKalori,
        ]);
    }

    private function formatUsia(int $usiaBulan): string
    {
        return "{$usiaBulan} bulan";
    }
}
