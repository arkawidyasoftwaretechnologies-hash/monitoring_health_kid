<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pengukuran;
use App\Models\HasilStatusGizi;
use App\Models\RedFlagLog;
use App\Services\ZScoreService;
use App\Services\NutritionService;
use App\Services\RedFlagService;
use App\Services\GrowthCalculationService;
use App\Services\AssessmentDraftService;

class RecalculateZScore extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:recalculate-zscore';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kalkulasi ulang nilai Z-Score dan status medis seluruh pengukuran berdasarkan data WHO terbaru.';

    /**
     * Execute the console command.
     */
    public function handle(
        ZScoreService $zScoreService, 
        NutritionService $nutritionService, 
        RedFlagService $redFlagService,
        GrowthCalculationService $growthService,
        AssessmentDraftService $assessmentDraftService
    )
    {
        $pengukurans = Pengukuran::with('anak', 'hasilStatusGizi', 'assessmentPlan')->get();
        $total = $pengukurans->count();

        $this->info("Memulai kalibrasi ulang untuk $total data pengukuran...");
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        foreach ($pengukurans as $pengukuran) {
            $anak = $pengukuran->anak;
            $usiaBulan = $pengukuran->usia_bulan;
            $imt = $growthService->hitungIMT($pengukuran->berat_badan, $pengukuran->tinggi_badan);

            $waz = $zScoreService->getZScore('waz', $anak->jenis_kelamin, $usiaBulan, $pengukuran->berat_badan);
            $haz = $zScoreService->getZScore('haz', $anak->jenis_kelamin, $usiaBulan, $pengukuran->tinggi_badan);
            $bmiz = $zScoreService->getZScore('bmiz', $anak->jenis_kelamin, $usiaBulan, $imt);
            $whz = $zScoreService->getWHZ($anak->jenis_kelamin, (float)$pengukuran->tinggi_badan, (float)$pengukuran->berat_badan, $usiaBulan, $pengukuran->cara_ukur);
            
            $hcfa = null;
            if (!empty($pengukuran->lingkar_kepala)) {
                $hcfa = $zScoreService->getZScore('hcfa', $anak->jenis_kelamin, $usiaBulan, $pengukuran->lingkar_kepala);
            }

            // Hapus Hasil Status Gizi Lama
            if ($pengukuran->hasilStatusGizi) {
                $pengukuran->hasilStatusGizi->delete();
            }

            // Hapus Red Flag Log Lama
            RedFlagLog::where('pengukuran_id', $pengukuran->id)->delete();

            // Buat Hasil Baru
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
                'status_lila' => $nutritionService->determineStatusLiLA($pengukuran->lila ? (float)$pengukuran->lila : null, $usiaBulan),
                'red_flag' => false,
                'catatan_red_flag' => null,
            ]);

            // Evaluasi Red Flags
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

            // Regenerate Narrative
            $previousPengukuran = Pengukuran::where('anak_id', $anak->id)
                ->where('tanggal_ukur', '<', $pengukuran->tanggal_ukur)
                ->orderBy('tanggal_ukur', 'desc')
                ->first();
                
            $previousHasil = $previousPengukuran ? $previousPengukuran->hasilStatusGizi : null;
            $narasi = $nutritionService->generateNarrative($hasil, $previousHasil, $pengukuran, $previousPengukuran);
            $hasil->update(['narasi_interpretasi' => $narasi]);

            // Regenerate Assessment Draft if untouched
            if ($pengukuran->assessmentPlan && !$pengukuran->assessmentPlan->dimodifikasi_dari_draft) {
                // Refresh the relation so it gets the new red flags
                $pengukuran->load('redFlagLogs');
                
                $draft = $assessmentDraftService->generate($pengukuran);
                $pengukuran->assessmentPlan->update([
                    'draft_otomatis' => "Assessment:\n" . $draft['assessment'] . "\nPlan:\n" . $draft['plan'],
                    'assessment_final' => $draft['assessment'],
                    'plan_final' => $draft['plan'],
                ]);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Kalibrasi selesai dengan sukses! Seluruh data telah diperbarui.");
    }
}
