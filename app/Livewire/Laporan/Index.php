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

    public function getFilteredPengukurans()
    {
        return Pengukuran::with(['anak', 'hasilStatusGizi'])
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
            })
            ->orderBy('tanggal_ukur', 'desc')
            ->get();
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

        return view('livewire.laporan.index', [
            'totalAnak' => $totalAnak,
            'stunted' => $stunted,
            'underweight' => $underweight,
            'anaksList' => Anak::orderBy('nama')->get(),
            'pengukurans' => $this->getFilteredPengukurans()
        ]);
    }
}
