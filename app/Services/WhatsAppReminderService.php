<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\JadwalKontrol;

class WhatsAppReminderService
{
    public function kirimReminder(JadwalKontrol $jadwal): bool
    {
        $nomorWa = $this->formatNomorWa($jadwal->nomor_wa_orangtua);

        // Default to empty strings if not configured to prevent crashes during dev
        $token = config('services.whatsapp.token', '');
        $endpoint = config('services.whatsapp.endpoint', '');

        if (empty($token) || empty($endpoint)) {
            // Mock response if API not configured
            $jadwal->update([
                'status_reminder' => 'terkirim',
                'dikirim_at' => now(),
            ]);
            return true;
        }

        $response = Http::withToken($token)
            ->post($endpoint, [
                'to' => $nomorWa,
                'type' => 'template',
                'template' => [
                    'name' => 'reminder_kontrol_anak',
                    'language' => ['code' => 'id'],
                    'components' => [[
                        'type' => 'body',
                        'parameters' => [
                            ['type' => 'text', 'text' => $jadwal->anak->nama],
                            ['type' => 'text', 'text' => $jadwal->tanggal_kontrol_rencana->format('d M Y')],
                        ]
                    ]]
                ]
            ]);

        $jadwal->update([
            'status_reminder' => $response->successful() ? 'terkirim' : 'gagal',
            'dikirim_at' => now(),
        ]);

        return $response->successful();
    }

    private function formatNomorWa(string $nomor): string
    {
        $nomor = preg_replace('/[^0-9]/', '', $nomor);

        if (str_starts_with($nomor, '0')) {
            $nomor = '62' . substr($nomor, 1);
        }

        return $nomor;
    }
}
