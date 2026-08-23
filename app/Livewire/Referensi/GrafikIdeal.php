<?php

namespace App\Livewire\Referensi;

use Livewire\Component;
use App\Models\WhoGrowthReference;
use App\Models\CdcGrowthReference;

class GrafikIdeal extends Component
{
    public $jenis_kelamin = 'L'; // Default Laki-laki

    public function render()
    {
        $labels = range(0, 60);

        // Fetch WHO Data
        $whoRefs = WhoGrowthReference::whereIn('indeks', ['waz', 'haz', 'hcfa'])
            ->where('jenis_kelamin', $this->jenis_kelamin)
            ->whereIn('usia_bulan', $labels)
            ->orderBy('usia_bulan')
            ->get()
            ->groupBy('indeks');

        $whoData = [
            'waz' => [],
            'haz' => [],
            'hcfa' => [],
            'lila' => array_fill(0, 61, 12.5) // Static line for LiLA
        ];

        foreach (['waz', 'haz', 'hcfa'] as $indeks) {
            $dataByIndex = isset($whoRefs[$indeks]) ? $whoRefs[$indeks]->keyBy('usia_bulan') : collect();
            foreach ($labels as $bulan) {
                $whoData[$indeks][] = isset($dataByIndex[$bulan]) ? (float) $dataByIndex[$bulan]->M : null;
            }
        }

        // Fetch CDC Data (hanya waz dan haz)
        $cdcRefs = CdcGrowthReference::whereIn('indeks', ['waz', 'haz'])
            ->where('jenis_kelamin', $this->jenis_kelamin)
            ->where('usia_bulan', '<=', 60.5)
            ->orderBy('usia_bulan')
            ->get()
            ->groupBy('indeks');

        $cdcData = [
            'waz' => [],
            'haz' => []
        ];

        foreach (['waz', 'haz'] as $indeks) {
            $dataByIndex = isset($cdcRefs[$indeks]) ? $cdcRefs[$indeks] : collect();
            foreach ($labels as $bulan) {
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

        return view('livewire.referensi.grafik-ideal', [
            'labels' => $labels,
            'whoData' => $whoData,
            'cdcData' => $cdcData,
        ])->layout('layouts.app');
    }
}
