<?php

namespace App\Livewire\Pengukuran;

use Livewire\Component;
use App\Models\Anak;
use App\Models\Pengukuran;
use App\Models\HasilStatusGizi;
use App\Services\GrowthCalculationService;
use App\Services\ZScoreService;
use App\Services\NutritionService;
use App\Models\TemplateRekomendasi;
use Carbon\Carbon;

class Form extends Component
{
    public $anak;
    public $tanggal_ukur;
    public $berat_badan;
    public $tinggi_badan;
    public $cara_ukur = 'berdiri';
    public $lingkar_kepala;
    public $lila;
    
    public $hasil = null; // Store result to show immediately
    public $show_assessment_form = false;
    public $draft_assessment = '';
    public $draft_plan = '';

    public $pengukuran_id;

    public $alat_ukur_bb = 'Timbangan Digital';
    public $alat_ukur_tb = 'Microtoise';

    protected $rules = [
        'tanggal_ukur' => 'required|date',
        'berat_badan' => 'required|numeric',
        'tinggi_badan' => 'required|numeric',
        'cara_ukur' => 'required|in:berdiri,telentang',
        'lingkar_kepala' => 'nullable|numeric',
        'lila' => 'nullable|numeric',
        'alat_ukur_bb' => 'nullable|string',
        'alat_ukur_tb' => 'nullable|string',
    ];

    public function mount(Anak $anak = null, Pengukuran $pengukuran = null)
    {
        if ($pengukuran && $pengukuran->exists) {
            $this->pengukuran_id = $pengukuran->id;
            $this->anak = $pengukuran->anak;
            $this->tanggal_ukur = $pengukuran->tanggal_ukur;
            $this->berat_badan = $pengukuran->berat_badan;
            $this->tinggi_badan = $pengukuran->tinggi_badan;
            $this->cara_ukur = $pengukuran->cara_ukur;
            $this->lingkar_kepala = $pengukuran->lingkar_kepala;
            $this->lila = $pengukuran->lila;
            $this->alat_ukur_bb = $pengukuran->alat_ukur_bb ?? 'Timbangan Digital';
            $this->alat_ukur_tb = $pengukuran->alat_ukur_tb ?? 'Microtoise';
            
            $this->hasil = $pengukuran->hasilStatusGizi;
            
            if ($pengukuran->assessmentPlan) {
                $this->draft_assessment = $pengukuran->assessmentPlan->assessment_final;
                $this->draft_plan = $pengukuran->assessmentPlan->plan_final;
                $this->show_assessment_form = true;
            } elseif ($this->hasil) {
                // Generate draft if assessment doesn't exist yet (e.g. inputted by operator who cannot save assessment)
                $draft = app(\App\Services\AssessmentDraftService::class)->generate($pengukuran);
                $this->draft_assessment = $draft['assessment'];
                $this->draft_plan = $draft['plan'];
                $this->show_assessment_form = true;
            }
        } else {
            $this->anak = $anak;
            $this->tanggal_ukur = date('Y-m-d');
        }
    }

    public function submit(GrowthCalculationService $growthService, ZScoreService $zScoreService, NutritionService $nutritionService, \App\Services\RedFlagService $redFlagService)
    {
        $this->validate();

        $usiaBulan = $growthService->hitungUsiaBulan($this->anak->tanggal_lahir, $this->tanggal_ukur);
        $imt = $growthService->hitungIMT($this->berat_badan, $this->tinggi_badan);
        
        $petugasId = auth()->id(); // Nullable if not logged in, prevents foreign key error

        if ($this->pengukuran_id) {
            $pengukuran = Pengukuran::find($this->pengukuran_id);
            $pengukuran->update([
                'tanggal_ukur' => $this->tanggal_ukur,
                'usia_bulan' => $usiaBulan,
                'berat_badan' => $this->berat_badan,
                'tinggi_badan' => $this->tinggi_badan,
                'cara_ukur' => $this->cara_ukur,
                'lingkar_kepala' => $this->lingkar_kepala ?: null,
                'lila' => $this->lila ?: null,
                'alat_ukur_bb' => $this->alat_ukur_bb,
                'alat_ukur_tb' => $this->alat_ukur_tb,
                'petugas_id' => $petugasId,
            ]);
            // Delete old hasilStatusGizi to replace it
            $pengukuran->hasilStatusGizi()->delete();
            \App\Models\RedFlagLog::where('pengukuran_id', $this->pengukuran_id)->delete();
        } else {
            $pengukuran = Pengukuran::create([
                'anak_id' => $this->anak->id,
                'tanggal_ukur' => $this->tanggal_ukur,
                'usia_bulan' => $usiaBulan,
                'berat_badan' => $this->berat_badan,
                'tinggi_badan' => $this->tinggi_badan,
                'cara_ukur' => $this->cara_ukur,
                'lingkar_kepala' => $this->lingkar_kepala ?: null,
                'lila' => $this->lila ?: null,
                'alat_ukur_bb' => $this->alat_ukur_bb,
                'alat_ukur_tb' => $this->alat_ukur_tb,
                'petugas_id' => $petugasId,
            ]);
        }

        $waz = $zScoreService->getZScore('waz', $this->anak->jenis_kelamin, $usiaBulan, $this->berat_badan);
        $haz = $zScoreService->getZScore('haz', $this->anak->jenis_kelamin, $usiaBulan, $this->tinggi_badan);
        $bmiz = $zScoreService->getZScore('bmiz', $this->anak->jenis_kelamin, $usiaBulan, $imt);
        
        // Calculate WHZ using new signature (gender, ht, wt, age, cara_ukur)
        $whz = $zScoreService->getWHZ($this->anak->jenis_kelamin, (float)$this->tinggi_badan, (float)$this->berat_badan, $usiaBulan, $this->cara_ukur);
        
        $hcfa = null;
        if (!empty($this->lingkar_kepala)) {
            $hcfa = $zScoreService->getZScore('hcfa', $this->anak->jenis_kelamin, $usiaBulan, $this->lingkar_kepala);
        }

        // Fetch previous measurement to generate narrative
        $previousPengukuran = Pengukuran::where('anak_id', $this->anak->id)
            ->where('tanggal_ukur', '<', $this->tanggal_ukur)
            ->orderBy('tanggal_ukur', 'desc')
            ->first();
            
        $previousHasil = $previousPengukuran ? $previousPengukuran->hasilStatusGizi : null;

        $this->hasil = HasilStatusGizi::create([
            'pengukuran_id' => $pengukuran->id,
            'waz' => $waz,
            'haz' => $haz,
            'bmiz' => $bmiz,
            'hcfa' => $hcfa,
            'whz' => $whz,
            'status_bb_u' => $waz !== null ? $nutritionService->determineStatusBBU($waz) : null,
            'status_tb_u' => $haz !== null ? $nutritionService->determineStatusTBU($haz) : null,
            'status_imt_u' => $bmiz !== null ? $nutritionService->determineStatusIMTU($bmiz) : null,
            'status_lk_u' => $hcfa !== null ? $nutritionService->determineStatusLKU($hcfa) : null,
            'status_bb_tb' => $whz !== null ? $nutritionService->determineStatusBBTB($whz) : null,
            'status_lila' => $nutritionService->determineStatusLiLA($this->lila ? (float)$this->lila : null, $usiaBulan),
            'red_flag' => false, // Will update below
            'catatan_red_flag' => null,
        ]);

        // Evaluate Red Flags
        $flags = $redFlagService->evaluasi($pengukuran, $this->hasil);
        
        if (count($flags) > 0) {
            $this->hasil->update(['red_flag' => true]); // maintain legacy column
            foreach ($flags as $flag) {
                \App\Models\RedFlagLog::create([
                    'pengukuran_id' => $pengukuran->id,
                    'anak_id' => $this->anak->id,
                    'kategori_flag' => $flag['kategori'],
                    'severity' => $flag['severity'],
                    'nilai_pemicu' => $flag['nilai'],
                    'rekomendasi_rujukan' => $flag['rekomendasi'],
                    'status' => 'baru'
                ]);
            }
        }
        
        // Generate narrative after creating the new record, passing the current record to generateNarrative
        $narasi = $nutritionService->generateNarrative($this->hasil, $previousHasil, $pengukuran, $previousPengukuran);
        $this->hasil->update(['narasi_interpretasi' => $narasi]);
        
        $this->pengukuran_id = $pengukuran->id;

        // Generate Assessment Draft
        $draft = app(\App\Services\AssessmentDraftService::class)->generate($pengukuran);
        $this->draft_assessment = $draft['assessment'];
        $this->draft_plan = $draft['plan'];
        $this->show_assessment_form = true;

        // Show success logic or reset
        if (auth()->user()->isOperator()) {
            session()->flash('message', 'Data pengukuran berhasil disimpan. Proses selesai.');
        } else {
            session()->flash('message', 'Data pengukuran berhasil disimpan. Silakan tinjau Assessment & Plan klinis.');
        }
    }

    public function simpanAssessment()
    {
        $this->validate([
            'draft_assessment' => 'required',
            'draft_plan' => 'required',
        ]);

        $pengukuran = Pengukuran::findOrFail($this->pengukuran_id);
        
        $draftAwal = app(\App\Services\AssessmentDraftService::class)->generate($pengukuran);
        $isModified = ($this->draft_assessment !== $draftAwal['assessment'] || $this->draft_plan !== $draftAwal['plan']);

        $pengukuran->assessmentPlan()->updateOrCreate(
            ['pengukuran_id' => $pengukuran->id],
            [
                'draft_otomatis' => "Assessment:\n" . $draftAwal['assessment'] . "\nPlan:\n" . $draftAwal['plan'],
                'assessment_final' => $this->draft_assessment,
                'plan_final' => $this->draft_plan,
                'disetujui_oleh' => auth()->id() ?? 1,
                'disetujui_at' => now(),
                'dimodifikasi_dari_draft' => $isModified,
            ]
        );

        session()->flash('message', 'Assessment & Plan berhasil disimpan ke Rekam Medis.');
        return redirect()->route('anak.index');
    }

    public function render()
    {
        $templates = TemplateRekomendasi::where('aktif', true)->orderBy('urutan_prioritas')->get();
        return view('livewire.pengukuran.form', compact('templates'));
    }
}
