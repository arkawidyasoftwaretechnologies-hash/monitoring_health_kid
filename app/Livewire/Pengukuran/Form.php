<?php

namespace App\Livewire\Pengukuran;

use Livewire\Component;
use App\Models\Anak;
use App\Models\Pengukuran;
use App\Models\HasilStatusGizi;
use App\Services\GrowthCalculationService;
use App\Services\ZScoreService;
use App\Services\NutritionService;
use Carbon\Carbon;

class Form extends Component
{
    public $anak;
    public $tanggal_ukur;
    public $berat_badan;
    public $tinggi_badan;
    public $cara_ukur = 'berdiri';
    public $lingkar_kepala;
    public $lila;
    
    public $hasil = null; // Store result to show immediately

    protected $rules = [
        'tanggal_ukur' => 'required|date',
        'berat_badan' => 'required|numeric',
        'tinggi_badan' => 'required|numeric',
        'cara_ukur' => 'required|in:berdiri,telentang',
        'lingkar_kepala' => 'nullable|numeric',
        'lila' => 'nullable|numeric',
    ];

    public function mount(Anak $anak)
    {
        $this->anak = $anak;
        $this->tanggal_ukur = date('Y-m-d');
    }

    public function submit(GrowthCalculationService $growthService, ZScoreService $zScoreService, NutritionService $nutritionService)
    {
        $this->validate();

        $usiaBulan = $growthService->hitungUsiaBulan($this->anak->tanggal_lahir, $this->tanggal_ukur);
        $imt = $growthService->hitungIMT($this->berat_badan, $this->tinggi_badan);

        $pengukuran = Pengukuran::create([
            'anak_id' => $this->anak->id,
            'tanggal_ukur' => $this->tanggal_ukur,
            'usia_bulan' => $usiaBulan,
            'berat_badan' => $this->berat_badan,
            'tinggi_badan' => $this->tinggi_badan,
            'cara_ukur' => $this->cara_ukur,
            'lingkar_kepala' => $this->lingkar_kepala ?: null,
            'lila' => $this->lila ?: null,
        ]);

        $waz = $zScoreService->getZScore('waz', $this->anak->jenis_kelamin, $usiaBulan, $this->berat_badan);
        $haz = $zScoreService->getZScore('haz', $this->anak->jenis_kelamin, $usiaBulan, $this->tinggi_badan);
        $bmiz = $zScoreService->getZScore('bmiz', $this->anak->jenis_kelamin, $usiaBulan, $imt);
        
        $hcfa = null;
        if (!empty($this->lingkar_kepala)) {
            $hcfa = $zScoreService->getZScore('hcfa', $this->anak->jenis_kelamin, $usiaBulan, $this->lingkar_kepala);
        }
        
        $redFlagCheck = $nutritionService->checkRedFlag(
            $waz ?? 0, 
            $haz ?? 0, 
            $bmiz ?? 0,
            $hcfa,
            $this->lila ? (float)$this->lila : null
        );

        $this->hasil = HasilStatusGizi::create([
            'pengukuran_id' => $pengukuran->id,
            'waz' => $waz,
            'haz' => $haz,
            'bmiz' => $bmiz,
            'hcfa' => $hcfa,
            'status_bb_u' => $waz !== null ? $nutritionService->determineStatusBBU($waz) : null,
            'status_tb_u' => $haz !== null ? $nutritionService->determineStatusTBU($haz) : null,
            'status_imt_u' => $bmiz !== null ? $nutritionService->determineStatusIMTU($bmiz) : null,
            'status_lk_u' => $hcfa !== null ? $nutritionService->determineStatusLKU($hcfa) : null,
            'status_lila' => $nutritionService->determineStatusLiLA($this->lila ? (float)$this->lila : null),
            'red_flag' => $redFlagCheck['is_red_flag'],
            'catatan_red_flag' => $redFlagCheck['catatan'],
        ]);
        
        // Show success logic or reset
        session()->flash('message', 'Data pengukuran berhasil disimpan dan dikalkulasi.');
    }

    public function render()
    {
        return view('livewire.pengukuran.form');
    }
}
