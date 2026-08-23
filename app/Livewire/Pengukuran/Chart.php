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
        // Siapkan batas usia (maksimal 60 bulan, atau umur pengukuran terakhir anak jika > 60)
        $maxUsiaAnak = 0;
        foreach ($this->anak->pengukurans as $pengukuran) {
            if ($pengukuran->usia_bulan > $maxUsiaAnak) {
                $maxUsiaAnak = $pengukuran->usia_bulan;
            }
        }
        $maxUsiaAxis = max(60, min($maxUsiaAnak + 2, 228)); // Kasih jarak 2 bulan, mentok 228

        $labelsAxis = range(0, $maxUsiaAxis);
        $jenisKelamin = $this->anak->jenis_kelamin;

        // Fetch WHO Data (waz, haz, hcfa)
        $whoRefs = \App\Models\WhoGrowthReference::whereIn('indeks', ['waz', 'haz', 'hcfa'])
            ->where('jenis_kelamin', $jenisKelamin)
            ->whereIn('usia_bulan', $labelsAxis)
            ->orderBy('usia_bulan')
            ->get()
            ->groupBy('indeks');

        $whoData = [
            'waz' => [],
            'haz' => [],
            'hcfa' => [],
        ];

        foreach (['waz', 'haz', 'hcfa'] as $indeks) {
            $dataByIndex = isset($whoRefs[$indeks]) ? $whoRefs[$indeks]->keyBy('usia_bulan') : collect();
            foreach ($labelsAxis as $bulan) {
                $whoData[$indeks][] = isset($dataByIndex[$bulan]) ? (float) $dataByIndex[$bulan]->M : null;
            }
        }

        // Fetch CDC Data (waz, haz)
        $cdcRefs = \App\Models\CdcGrowthReference::whereIn('indeks', ['waz', 'haz'])
            ->where('jenis_kelamin', $jenisKelamin)
            ->where('usia_bulan', '<=', $maxUsiaAxis + 0.5)
            ->orderBy('usia_bulan')
            ->get()
            ->groupBy('indeks');

        $cdcData = [
            'waz' => [],
            'haz' => []
        ];

        foreach (['waz', 'haz'] as $indeks) {
            $dataByIndex = isset($cdcRefs[$indeks]) ? $cdcRefs[$indeks] : collect();
            foreach ($labelsAxis as $bulan) {
                $closest = $dataByIndex->sortBy(function($item) use ($bulan) {
                    return abs($item->usia_bulan - $bulan);
                })->first();
                
                if ($closest && abs($closest->usia_bulan - $bulan) <= 1.5) {
                    $cdcData[$indeks][] = (float) $closest->M;
                } else {
                    $cdcData[$indeks][] = null;
                }
            }
        }

        // Format Aktual Anak ke koordinat (x, y)
        $anakCoords = [
            'bb' => [],
            'tb' => [],
            'lk' => []
        ];
        
        foreach ($this->anak->pengukurans as $pengukuran) {
            if (isset($pengukuran->berat_badan)) {
                $anakCoords['bb'][] = ['x' => $pengukuran->usia_bulan, 'y' => floatval($pengukuran->berat_badan)];
            }
            if (isset($pengukuran->tinggi_badan)) {
                $anakCoords['tb'][] = ['x' => $pengukuran->usia_bulan, 'y' => floatval($pengukuran->tinggi_badan)];
            }
            if (isset($pengukuran->lingkar_kepala)) {
                $anakCoords['lk'][] = ['x' => $pengukuran->usia_bulan, 'y' => floatval($pengukuran->lingkar_kepala)];
            }
        }

        return view('livewire.pengukuran.chart', [
            'labelsAxis' => $labelsAxis,
            'whoData' => $whoData,
            'cdcData' => $cdcData,
            'anakCoords' => $anakCoords
        ]);
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
