<?php

namespace App\Livewire\Anak;

use Livewire\Component;
use App\Models\Anak;

class Form extends Component
{
    public $nama, $nik, $tanggal_lahir, $jenis_kelamin = 'L', $nama_ortu, $alamat;

    protected $rules = [
        'nama' => 'required|string',
        'nik' => 'nullable|string|unique:anaks,nik',
        'tanggal_lahir' => 'required|date',
        'jenis_kelamin' => 'required|in:L,P',
    ];

    public function submit()
    {
        $this->validate();

        Anak::create([
            'nama' => $this->nama,
            'nik' => $this->nik,
            'tanggal_lahir' => $this->tanggal_lahir,
            'jenis_kelamin' => $this->jenis_kelamin,
            'nama_ortu' => $this->nama_ortu,
            'alamat' => $this->alamat,
        ]);

        return redirect()->route('anak.index');
    }

    public function render()
    {
        return view('livewire.anak.form');
    }
}
