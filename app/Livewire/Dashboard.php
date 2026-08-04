<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Anak;
use App\Models\Pengukuran;

class Dashboard extends Component
{
    public $totalAnak;
    public $totalPengukuran;
    public $stuntingAnak;

    public function mount()
    {
        $this->totalAnak = Anak::count();
        $this->totalPengukuran = Pengukuran::count();
        $this->stuntingAnak = \App\Models\HasilStatusGizi::where('status_tb_u', 'like', '%stunted%')->count();
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}
