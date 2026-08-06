<?php

namespace App\Livewire\Pengukuran;

use Livewire\Component;
use App\Models\Anak;

class Chart extends Component
{
    public $anak;
    public $labels = [];
    public $wazData = [];
    public $hazData = [];
    public $bmizData = [];
    public $whzData = [];
    public $hcfaData = [];
    public $lilaData = [];
    public $beratAktualData = [];
    public $tinggiAktualData = [];
    public $imtAktualData = [];
    public $lkAktualData = [];

    public function mount(Anak $anak)
    {
        $this->anak = $anak->load(['pengukurans' => function($q) {
            $q->orderBy('tanggal_ukur', 'asc');
        }, 'pengukurans.hasilStatusGizi', 'pengukurans.redFlagLogs']);

        foreach ($this->anak->pengukurans as $pengukuran) {
            $this->labels[] = 'Bulan ' . $pengukuran->usia_bulan;
            $this->wazData[] = isset($pengukuran->hasilStatusGizi->waz) ? floatval($pengukuran->hasilStatusGizi->waz) : null;
            $this->hazData[] = isset($pengukuran->hasilStatusGizi->haz) ? floatval($pengukuran->hasilStatusGizi->haz) : null;
            $this->bmizData[] = isset($pengukuran->hasilStatusGizi->bmiz) ? floatval($pengukuran->hasilStatusGizi->bmiz) : null;
            $this->whzData[] = isset($pengukuran->hasilStatusGizi->whz) ? floatval($pengukuran->hasilStatusGizi->whz) : null;
            $this->hcfaData[] = isset($pengukuran->hasilStatusGizi->hcfa) ? floatval($pengukuran->hasilStatusGizi->hcfa) : null;
            $this->lilaData[] = isset($pengukuran->lila) ? floatval($pengukuran->lila) : null;
            $this->beratAktualData[] = isset($pengukuran->berat_badan) ? floatval($pengukuran->berat_badan) : null;
            $this->tinggiAktualData[] = isset($pengukuran->tinggi_badan) ? floatval($pengukuran->tinggi_badan) : null;
            
            $bmi = null;
            if (isset($pengukuran->berat_badan) && isset($pengukuran->tinggi_badan) && $pengukuran->tinggi_badan > 0) {
                $tinggi_m = $pengukuran->tinggi_badan / 100;
                $bmi = round($pengukuran->berat_badan / ($tinggi_m * $tinggi_m), 2);
            }
            $this->imtAktualData[] = $bmi;
            $this->lkAktualData[] = isset($pengukuran->lingkar_kepala) ? floatval($pengukuran->lingkar_kepala) : null;
        }
    }

    public function render()
    {
        return view('livewire.pengukuran.chart');
    }

    public function deletePengukuran($id)
    {
        $pengukuran = \App\Models\Pengukuran::find($id);
        if ($pengukuran) {
            $pengukuran->hasilStatusGizi()->delete();
            $pengukuran->delete();
            
            // Reload page to refresh charts and table
            return redirect()->route('pengukuran.chart', $this->anak->id);
        }
    }
}
