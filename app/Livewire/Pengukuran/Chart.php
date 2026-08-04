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

    public function mount(Anak $anak)
    {
        $this->anak = $anak->load(['pengukurans.hasilStatusGizi' => function($q) {
            $q->orderBy('created_at', 'asc'); // Ensure chronological order
        }]);

        foreach ($this->anak->pengukurans as $pengukuran) {
            $this->labels[] = 'Bulan ' . $pengukuran->usia_bulan;
            $this->wazData[] = $pengukuran->hasilStatusGizi->waz ?? 0;
            $this->hazData[] = $pengukuran->hasilStatusGizi->haz ?? 0;
        }
    }

    public function render()
    {
        return view('livewire.pengukuran.chart');
    }
}
