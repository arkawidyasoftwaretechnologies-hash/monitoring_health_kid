<?php

namespace App\Livewire\Anak;

use Livewire\Component;
use App\Models\Anak;

class Index extends Component
{
    public $search = '';
    public $filterStatus = 'Semua';

    public function setFilterStatus($status)
    {
        $this->filterStatus = $this->filterStatus === $status ? 'Semua' : $status;
    }

    public function deleteAnak($id)
    {
        $anak = Anak::find($id);
        if ($anak) {
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
        $allAnaks = Anak::with(['puskesmas', 'pengukurans' => function($q) {
            $q->latest('tanggal_ukur');
        }, 'pengukurans.hasilStatusGizi', 'pengukurans.assessmentPlan'])->orderBy('created_at', 'desc')->get();

        // Compute Stats
        $stats = [
            'total' => $allAnaks->count(),
            'normal' => 0,
            'stunting' => 0,
            'red_flag' => 0,
            'belum_diukur' => 0,
        ];

        foreach ($allAnaks as $a) {
            $latest = $a->pengukurans->first();
            $hasil = $latest ? $latest->hasilStatusGizi : null;
            if (!$latest) {
                $stats['belum_diukur']++;
            } else {
                if ($hasil) {
                    $tb_u = strtolower($hasil->status_tb_u ?? '');
                    if (str_contains($tb_u, 'stunted') || str_contains($tb_u, 'pendek')) {
                        $stats['stunting']++;
                    } else if (str_contains($tb_u, 'normal')) {
                        // Rough logic for normal
                        $stats['normal']++;
                    }
                    if ($hasil->red_flag) {
                        $stats['red_flag']++;
                    }
                }
            }
        }

        // Apply Filters
        $filteredAnaks = $allAnaks->filter(function($b) {
            // Search filter
            if ($this->search) {
                $q = strtolower($this->search);
                if (!str_contains(strtolower($b->nama), $q) && !str_contains(strtolower($b->nik ?? ''), $q)) {
                    return false;
                }
            }

            // Status filter
            if ($this->filterStatus !== 'Semua') {
                $latest = $b->pengukurans->first();
                $hasil = $latest ? $latest->hasilStatusGizi : null;
                
                if ($this->filterStatus === 'Belum Diukur' && $latest) return false;
                if ($this->filterStatus !== 'Belum Diukur' && !$latest) return false;

                if ($this->filterStatus === 'Normal' && $hasil) {
                    $tb_u = strtolower($hasil->status_tb_u ?? '');
                    if (!str_contains($tb_u, 'normal')) return false;
                }
                if ($this->filterStatus === 'Stunted' && $hasil) {
                    $tb_u = strtolower($hasil->status_tb_u ?? '');
                    if (!str_contains($tb_u, 'stunted') && !str_contains($tb_u, 'pendek')) return false;
                }
                if ($this->filterStatus === 'Red Flag' && $hasil) {
                    if (!$hasil->red_flag) return false;
                }
            }

            return true;
        });

        return view('livewire.anak.index', [
            'anaks' => $filteredAnaks,
            'stats' => $stats
        ]);
    }
}
