<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\TemplateRekomendasi;

class TemplateRekomendasiSeeder extends Seeder
{
    public function run()
    {
        DB::table('template_rekomendasis')->truncate();
        
        $templates = [
            [
                'nama_template' => 'Pertumbuhan Normal',
                'kondisi_pemicu' => 'normal',
                'template_assessment' => 'Pertumbuhan [NAMA_ANAK] sesuai kurva WHO (Normal). Z-Score BB/U: [WAZ], TB/U: [HAZ], BB/TB: [WHZ].',
                'template_plan' => 'Edukasi pola asuh, nutrisi adekuat (ASI/MPASI), dan rutinitas jadwal makan yang baik.',
                'urutan_prioritas' => 1,
            ],
            [
                'nama_template' => 'Risiko Gagal Tumbuh',
                'kondisi_pemicu' => 'growth_faltering',
                'template_assessment' => 'Faltering Growth (Weight Faltering) / Kenaikan BB tidak adekuat.',
                'template_plan' => 'Edukasi praktik pemberian MPASI (Fokus Protein Hewani) dan densitas kalori.',
                'urutan_prioritas' => 2,
            ],
            [
                'nama_template' => 'Gizi Kurang',
                'kondisi_pemicu' => 'underweight',
                'template_assessment' => 'Gizi Kurang (Underweight) dengan risiko defisiensi nutrisi mikro.',
                'template_plan' => 'Berikan suplementasi Zat Besi (Fe) dan Multivitamin.',
                'urutan_prioritas' => 3,
            ],
            [
                'nama_template' => 'Gizi Sangat Kurang',
                'kondisi_pemicu' => 'underweight_berat',
                'template_assessment' => 'Gizi Sangat Kurang (Severely Underweight).',
                'template_plan' => 'Rujuk untuk penanganan gizi komprehensif dan cek penyulit medis.',
                'urutan_prioritas' => 4,
            ],
            [
                'nama_template' => 'Stunting',
                'kondisi_pemicu' => 'stunting',
                'template_assessment' => 'Perawakan Pendek (Stunted).',
                'template_plan' => 'Skrining TBC, ISK, dan rujuk untuk intervensi spesifik.',
                'urutan_prioritas' => 5,
            ],
            [
                'nama_template' => 'Stunting Berat',
                'kondisi_pemicu' => 'stunting_berat',
                'template_assessment' => 'Sangat Pendek (Severely Stunted) / Perawakan Pendek Patologis.',
                'template_plan' => 'Rujuk ke Dokter Spesialis Anak (Sp.A) sub Nutrisi / Endokrinologi.',
                'urutan_prioritas' => 6,
            ],
            [
                'nama_template' => 'Wasting',
                'kondisi_pemicu' => 'gizi_kurang_akut',
                'template_assessment' => 'Wasting / Gizi Kurang Akut.',
                'template_plan' => 'Pemberian Makanan Tambahan (PMT) Pemulihan dan observasi ketat.',
                'urutan_prioritas' => 7,
            ],
            [
                'nama_template' => 'Severe Wasting',
                'kondisi_pemicu' => 'gizi_buruk_akut',
                'template_assessment' => 'Severe Acute Malnutrition (SAM) / Gizi Buruk Akut.',
                'template_plan' => 'Rujuk UGD / Rawat Inap untuk tatalaksana F-75 & F-100.',
                'urutan_prioritas' => 8,
            ],
            [
                'nama_template' => 'Overweight',
                'kondisi_pemicu' => 'overweight',
                'template_assessment' => 'Overweight / Gizi Lebih.',
                'template_plan' => 'Edukasi pembatasan asupan gula, garam, lemak (GGL).',
                'urutan_prioritas' => 9,
            ],
            [
                'nama_template' => 'Obesitas',
                'kondisi_pemicu' => 'obesitas',
                'template_assessment' => 'Obesitas.',
                'template_plan' => 'Rujuk Sp.A untuk skrining metabolik (Gula Darah, Profil Lipid).',
                'urutan_prioritas' => 10,
            ],
            [
                'nama_template' => 'Mikrosefali',
                'kondisi_pemicu' => 'mikrosefali',
                'template_assessment' => 'Mikrosefali.',
                'template_plan' => 'Skrining TORCH dan rujuk Neurologi Anak.',
                'urutan_prioritas' => 11,
            ],
            [
                'nama_template' => 'Makrosefali',
                'kondisi_pemicu' => 'makrosefali',
                'template_assessment' => 'Makrosefali.',
                'template_plan' => 'Observasi tanda tekanan intrakranial dan rujuk.',
                'urutan_prioritas' => 12,
            ],
        ];

        foreach ($templates as $template) {
            TemplateRekomendasi::create($template);
        }
    }
}
