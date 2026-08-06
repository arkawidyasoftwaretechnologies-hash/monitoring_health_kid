<?php

namespace App\Livewire\Anak;

use Livewire\Component;
use App\Models\Anak;

class Form extends Component
{
    public $anak_id, $nama, $nik, $tanggal_lahir, $jenis_kelamin = 'L', $nama_ortu, $alamat;

    protected function rules()
    {
        return [
            'nama' => 'required|string',
            'nik' => 'nullable|string|unique:anaks,nik,' . $this->anak_id,
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
        ];
    }

    public function mount(Anak $anak = null)
    {
        if ($anak && $anak->exists) {
            $this->anak_id = $anak->id;
            $this->nama = $anak->nama;
            $this->nik = $anak->nik;
            $this->tanggal_lahir = $anak->tanggal_lahir;
            $this->jenis_kelamin = $anak->jenis_kelamin;
            $this->nama_ortu = $anak->nama_ortu;
            $this->alamat = $anak->alamat;
        }
    }

    public function submit()
    {
        $this->validate();

        if ($this->anak_id) {
            $anak = Anak::find($this->anak_id);
            $anak->update([
                'nama' => $this->nama,
                'nik' => $this->nik,
                'tanggal_lahir' => $this->tanggal_lahir,
                'jenis_kelamin' => $this->jenis_kelamin,
                'nama_ortu' => $this->nama_ortu,
                'alamat' => $this->alamat,
            ]);
        } else {
            Anak::create([
                'nama' => $this->nama,
                'nik' => $this->nik,
                'tanggal_lahir' => $this->tanggal_lahir,
                'jenis_kelamin' => $this->jenis_kelamin,
                'nama_ortu' => $this->nama_ortu,
                'alamat' => $this->alamat,
            ]);
        }

        return redirect()->route('anak.index');
    }

    public function render()
    {
        return view('livewire.anak.form');
    }
}
