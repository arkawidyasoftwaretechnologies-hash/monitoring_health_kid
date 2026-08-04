<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Anak;
use App\Models\Pengukuran;
use App\Models\HasilStatusGizi;
use App\Services\GrowthCalculationService;
use App\Services\ZScoreService;
use App\Services\NutritionService;

class PengukuranController extends Controller
{
    public function store(Request $request, $anak_id, GrowthCalculationService $growthService, ZScoreService $zScoreService, NutritionService $nutritionService)
    {
        $validated = $request->validate([
            'tanggal_ukur' => 'required|date',
            'berat_badan' => 'required|numeric',
            'tinggi_badan' => 'required|numeric',
            'cara_ukur' => 'required|in:berdiri,telentang',
            'lingkar_kepala' => 'nullable|numeric',
            'lila' => 'nullable|numeric',
        ]);

        $anak = Anak::findOrFail($anak_id);
        
        $usiaBulan = $growthService->hitungUsiaBulan($anak->tanggal_lahir, $validated['tanggal_ukur']);
        $imt = $growthService->hitungIMT($validated['berat_badan'], $validated['tinggi_badan']);

        $pengukuran = Pengukuran::create([
            'anak_id' => $anak->id,
            'tanggal_ukur' => $validated['tanggal_ukur'],
            'usia_bulan' => $usiaBulan,
            'berat_badan' => $validated['berat_badan'],
            'tinggi_badan' => $validated['tinggi_badan'],
            'cara_ukur' => $validated['cara_ukur'],
            'lingkar_kepala' => $validated['lingkar_kepala'] ?? null,
            'lila' => $validated['lila'] ?? null,
            'petugas_id' => auth()->id(),
        ]);

        $waz = $zScoreService->getZScore('waz', $anak->jenis_kelamin, $usiaBulan, $validated['berat_badan']);
        $haz = $zScoreService->getZScore('haz', $anak->jenis_kelamin, $usiaBulan, $validated['tinggi_badan']);
        $whz = null; // Butuh interpolasi TB untuk WHZ dari tabel LMS
        $bmiz = $zScoreService->getZScore('bmiz', $anak->jenis_kelamin, $usiaBulan, $imt);
        
        $hcfa = null;
        if (!empty($validated['lingkar_kepala'])) {
            $hcfa = $zScoreService->getZScore('hcfa', $anak->jenis_kelamin, $usiaBulan, $validated['lingkar_kepala']);
        }

        $redFlagCheck = $nutritionService->checkRedFlag(
            $waz ?? 0, 
            $haz ?? 0, 
            $bmiz ?? 0, // Using BMIZ instead of WHZ for SAM check if WHZ is null
            $hcfa, 
            $validated['lila'] ?? null
        );

        $hasil = HasilStatusGizi::create([
            'pengukuran_id' => $pengukuran->id,
            'waz' => $waz,
            'haz' => $haz,
            'whz' => $whz,
            'bmiz' => $bmiz,
            'hcfa' => $hcfa,
            'status_bb_u' => $waz !== null ? $nutritionService->determineStatusBBU($waz) : null,
            'status_tb_u' => $haz !== null ? $nutritionService->determineStatusTBU($haz) : null,
            'status_imt_u' => $bmiz !== null ? $nutritionService->determineStatusIMTU($bmiz) : null,
            'status_lk_u' => $hcfa !== null ? $nutritionService->determineStatusLKU($hcfa) : null,
            'status_lila' => $nutritionService->determineStatusLiLA($validated['lila'] ?? null),
            'red_flag' => $redFlagCheck['is_red_flag'],
            'catatan_red_flag' => $redFlagCheck['catatan'],
        ]);

        return response()->json([
            'pengukuran' => $pengukuran,
            'hasil' => $hasil
        ], 201);
    }
}
