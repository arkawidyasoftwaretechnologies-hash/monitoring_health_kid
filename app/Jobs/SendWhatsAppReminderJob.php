<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\JadwalKontrol;
use App\Services\WhatsAppReminderService;

class SendWhatsAppReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $jadwal;

    public function __construct(JadwalKontrol $jadwal)
    {
        $this->jadwal = $jadwal;
    }

    public function handle(WhatsAppReminderService $service): void
    {
        if ($this->jadwal->status_reminder === 'terkirim') {
            return;
        }

        $berhasil = $service->kirimReminder($this->jadwal);

        if (!$berhasil) {
            $this->release(600); // retry after 10 minutes if failed
        }
    }
}
