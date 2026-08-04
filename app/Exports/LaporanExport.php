<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class LaporanExport implements FromView
{
    public $pengukurans;

    public function __construct($pengukurans)
    {
        $this->pengukurans = $pengukurans;
    }

    public function view(): View
    {
        return view('exports.laporan', [
            'pengukurans' => $this->pengukurans
        ]);
    }
}
