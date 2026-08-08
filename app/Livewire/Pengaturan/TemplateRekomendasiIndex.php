<?php

namespace App\Livewire\Pengaturan;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\TemplateRekomendasi;

class TemplateRekomendasiIndex extends Component
{
    use WithPagination;

    public $nama_template = '';
    public $kondisi_pemicu = '';
    public $template_assessment = '';
    public $template_plan = '';
    public $urutan_prioritas = 0;
    public $aktif = true;
    public $template_id = null;

    public $isModalOpen = false;

    protected $rules = [
        'nama_template' => 'required|max:100',
        'kondisi_pemicu' => 'required|max:100',
        'template_assessment' => 'required',
        'template_plan' => 'required',
        'urutan_prioritas' => 'integer',
        'aktif' => 'boolean'
    ];

    public function render()
    {
        $templates = TemplateRekomendasi::orderBy('urutan_prioritas')->orderBy('id')->paginate(10);
        return view('livewire.pengaturan.template-rekomendasi', compact('templates'))
            ->layout('layouts.app');
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function openModal()
    {
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetValidation();
    }

    private function resetInputFields()
    {
        $this->nama_template = '';
        $this->kondisi_pemicu = '';
        $this->template_assessment = '';
        $this->template_plan = '';
        $this->urutan_prioritas = 0;
        $this->aktif = true;
        $this->template_id = null;
    }

    public function store()
    {
        $this->validate();

        TemplateRekomendasi::updateOrCreate(['id' => $this->template_id], [
            'nama_template' => $this->nama_template,
            'kondisi_pemicu' => $this->kondisi_pemicu,
            'template_assessment' => $this->template_assessment,
            'template_plan' => $this->template_plan,
            'urutan_prioritas' => $this->urutan_prioritas,
            'aktif' => $this->aktif,
        ]);

        session()->flash('message', $this->template_id ? 'Template berhasil diperbarui.' : 'Template berhasil ditambahkan.');

        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $template = TemplateRekomendasi::findOrFail($id);
        $this->template_id = $id;
        $this->nama_template = $template->nama_template;
        $this->kondisi_pemicu = $template->kondisi_pemicu;
        $this->template_assessment = $template->template_assessment;
        $this->template_plan = $template->template_plan;
        $this->urutan_prioritas = $template->urutan_prioritas;
        $this->aktif = $template->aktif;
        
        $this->openModal();
    }

    public function delete($id)
    {
        TemplateRekomendasi::find($id)->delete();
        session()->flash('message', 'Template berhasil dihapus.');
    }
}
