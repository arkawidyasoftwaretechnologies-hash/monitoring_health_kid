<?php

namespace App\Livewire\Laporan;

use Livewire\Component;
use App\Models\Anak;
use App\Models\Pengukuran;
use App\Models\HasilStatusGizi;
use App\Exports\LaporanExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class Index extends Component
{
    public $selectedAnak = '';
    public $startDate = '';
    public $endDate = '';
    public $statusTBU = '';
    public $redFlag = '';
    
    public $sortField = 'tanggal_ukur';
    public $sortDirection = 'desc';

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function getFilteredPengukurans()
    {
        $query = Pengukuran::with(['anak', 'hasilStatusGizi'])
            ->when($this->selectedAnak, function ($query) {
                $query->where('anak_id', $this->selectedAnak);
            })
            ->when($this->startDate, function ($query) {
                $query->where('tanggal_ukur', '>=', $this->startDate);
            })
            ->when($this->endDate, function ($query) {
                $query->where('tanggal_ukur', '<=', $this->endDate);
            })
            ->when($this->statusTBU !== '', function ($query) {
                $query->whereHas('hasilStatusGizi', function ($q) {
                    $q->where('status_tb_u', $this->statusTBU);
                });
            })
            ->when($this->redFlag !== '', function ($query) {
                $query->whereHas('hasilStatusGizi', function ($q) {
                    $q->where('red_flag', $this->redFlag === '1');
                });
            });

        if ($this->sortField === 'anak.nama') {
            // Join anaks table to sort by anak nama
            $query->join('anaks', 'pengukurans.anak_id', '=', 'anaks.id')
                  ->select('pengukurans.*') // Make sure to only select pengukurans columns to avoid ID conflicts
                  ->orderBy('anaks.nama', $this->sortDirection);
        } else {
            $query->orderBy($this->sortField, $this->sortDirection);
        }

        return $query->get();
    }

    public function exportExcel()
    {
        $data = $this->getFilteredPengukurans();
        return Excel::download(new LaporanExport($data), 'laporan_stunting.xlsx');
    }

    public function exportPdf()
    {
        $data = $this->getFilteredPengukurans();
        $pdf = Pdf::loadView('exports.laporan', ['pengukurans' => $data])->setPaper('a4', 'landscape');
        // Livewire v3 allows returning standard Laravel responses
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, 'laporan_stunting.pdf');
    }

    public function render()
    {
        $totalAnak = Anak::count();
        $stunted = HasilStatusGizi::where('status_tb_u', 'like', '%stunted%')
                    ->orWhere('status_tb_u', 'like', '%pendek%')
                    ->count();
        $underweight = HasilStatusGizi::where('status_bb_u', 'like', '%underweight%')
                        ->orWhere('status_bb_u', 'like', '%kurang%')
                        ->count();

        $chartLabels = [];
        $chartHaz = [];
        $chartWaz = [];
        $chartBmiz = [];
        $chartHcfa = [];
        $chartLila = [];
        $chartBeratAktual = [];
        $chartTinggiAktual = [];
        $chartImtAktual = [];
        $chartLkAktual = [];
        
        if ($this->selectedAnak) {
            $anakSelected = Anak::with(['pengukurans' => function($q) {
                $q->orderBy('tanggal_ukur', 'asc');
            }, 'pengukurans.hasilStatusGizi'])->find($this->selectedAnak);

            if ($anakSelected) {
                foreach ($anakSelected->pengukurans as $row) {
                    $chartLabels[] = $row->usia_bulan . ' bln (' . \Carbon\Carbon::parse($row->tanggal_ukur)->format('M y') . ')';
                    $chartHaz[] = isset($row->hasilStatusGizi->haz) ? floatval($row->hasilStatusGizi->haz) : 0;
                    $chartWaz[] = isset($row->hasilStatusGizi->waz) ? floatval($row->hasilStatusGizi->waz) : 0;
                    $chartBmiz[] = isset($row->hasilStatusGizi->bmiz) ? floatval($row->hasilStatusGizi->bmiz) : 0;
                    $chartHcfa[] = isset($row->hasilStatusGizi->hcfa) ? floatval($row->hasilStatusGizi->hcfa) : 0;
                    $chartLila[] = isset($row->lila) ? floatval($row->lila) : null;
                    $chartBeratAktual[] = isset($row->berat_badan) ? floatval($row->berat_badan) : null;
                    $chartTinggiAktual[] = isset($row->tinggi_badan) ? floatval($row->tinggi_badan) : null;
                    
                    $bmi = null;
                    if (isset($row->berat_badan) && isset($row->tinggi_badan) && $row->tinggi_badan > 0) {
                        $tinggi_m = $row->tinggi_badan / 100;
                        $bmi = round($row->berat_badan / ($tinggi_m * $tinggi_m), 2);
                    }
                    $chartImtAktual[] = $bmi;
                    $chartLkAktual[] = isset($row->lingkar_kepala) ? floatval($row->lingkar_kepala) : null;
                }
            }
            
            // Dispatch browser event to re-render chart when data changes
            $this->dispatch('chart-updated', [
                'labels' => $chartLabels,
                'haz' => $chartHaz,
                'waz' => $chartWaz,
                'bmiz' => $chartBmiz,
                'hcfa' => $chartHcfa,
                'lila' => $chartLila,
                'beratAktual' => $chartBeratAktual,
                'tinggiAktual' => $chartTinggiAktual,
                'imtAktual' => $chartImtAktual,
                'lkAktual' => $chartLkAktual
            ]);
        }

        return view('livewire.laporan.index', [
            'totalAnak' => $totalAnak,
            'stunted' => $stunted,
            'underweight' => $underweight,
            'anaksList' => Anak::orderBy('nama')->get(),
            'pengukurans' => $this->getFilteredPengukurans(),
            'chartLabels' => $chartLabels,
            'chartHaz' => $chartHaz,
            'chartWaz' => $chartWaz,
            'chartBmiz' => $chartBmiz,
            'chartHcfa' => $chartHcfa,
            'chartLila' => $chartLila,
            'chartBeratAktual' => $chartBeratAktual,
            'chartTinggiAktual' => $chartTinggiAktual,
            'chartImtAktual' => $chartImtAktual,
            'chartLkAktual' => $chartLkAktual,
        ]);
    }
}
