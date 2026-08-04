<?php

namespace App\Livewire\Anak;

use Livewire\Component;
use App\Models\Anak;

class Index extends Component
{
    public $search = '';

    public function deleteAnak($id)
    {
        $anak = Anak::find($id);
        if ($anak) {
            // Delete related measurements (if cascade is not set on DB level)
            foreach ($anak->pengukurans as $pengukuran) {
                if ($pengukuran->hasilStatusGizi) {
                    $pengukuran->hasilStatusGizi->delete();
                }
                $pengukuran->delete();
            }
            $anak->delete();
        }
    }

    public function render()
    {
        $anaks = Anak::with(['puskesmas', 'pengukurans' => function($q) {
            $q->latest('tanggal_ukur');
        }, 'pengukurans.hasilStatusGizi'])
        ->when($this->search, function($query) {
            $query->where('nama', 'like', '%' . $this->search . '%')
                  ->orWhere('nik', 'like', '%' . $this->search . '%');
        })
        ->orderBy('created_at', 'desc')->get();
        
        return view('livewire.anak.index', compact('anaks'));
    }
}
