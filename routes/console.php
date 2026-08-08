<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\JadwalKontrol;
use App\Jobs\SendWhatsAppReminderJob;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// DITUNDA SEMENTARA: Fitur Reminder WhatsApp
// Schedule::call(function () {
//     $besok = now()->addDay()->toDateString();
//     $jadwalHariIni = JadwalKontrol::where('tanggal_kontrol_rencana', $besok)
//         ->where('status_reminder', 'belum_terkirim')
//         ->whereHas('anak', function ($q) {
//             $q->where('consent_wa_reminder', true);
//         })
//         ->get();
//
//     foreach ($jadwalHariIni as $jadwal) {
//         SendWhatsAppReminderJob::dispatch($jadwal);
//     }
// })->dailyAt('09:00');
