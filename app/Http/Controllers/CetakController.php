<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengukuran;
use Barryvdh\DomPDF\Facade\Pdf;

class CetakController extends Controller
{
    public function versiOrangTua($id)
    {
        $pengukuran = Pengukuran::with(['anak', 'redFlagLogs', 'assessmentPlan'])->findOrFail($id);

        $data = [
            'anak' => $pengukuran->anak,
            'pengukuran' => $pengukuran,
            'statusRingkas' => $this->terjemahkanKeAwam($pengukuran),
            'saranDokter' => $pengukuran->assessmentPlan?->plan_final ?? 'Lanjutkan pola makan saat ini. Kontrol rutin sesuai jadwal berikutnya.',
        ];

        $pdf = Pdf::loadView('cetak.orangtua', $data);
        return $pdf->stream('hasil_pemeriksaan_tumbuh_kembang.pdf');
    }

    private function terjemahkanKeAwam(Pengukuran $p): string
    {
        if ($p->redFlagLogs->isEmpty()) {
            return 'Baik - Pertumbuhan sesuai standar';
        }

        $severityTertinggi = $p->redFlagLogs->max('severity');
        
        return match($severityTertinggi) {
            'merah' => 'Perlu evaluasi lebih lanjut oleh dokter',
            'kuning' => 'Perlu diperhatikan pola makan/asuh',
            default => 'Baik - Pertumbuhan sesuai standar',
        };
    }

    public function versiMedis($id)
    {
        $pengukuran = Pengukuran::with(['anak', 'redFlagLogs', 'assessmentPlan', 'hasilStatusGizi'])->findOrFail($id);

        $data = [
            'anak' => $pengukuran->anak,
            'pengukuran' => $pengukuran,
            'hasil' => $pengukuran->hasilStatusGizi,
            'assessment' => $pengukuran->assessmentPlan,
        ];

        $pdf = Pdf::loadView('cetak.medis', $data);
        return $pdf->stream('laporan_medis_tumbuh_kembang.pdf');
    }
}
