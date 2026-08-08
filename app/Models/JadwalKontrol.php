<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalKontrol extends Model
{
    protected $guarded = [];

    protected $casts = [
        'tanggal_kontrol_rencana' => 'date',
        'dikirim_at' => 'datetime',
    ];

    public function anak()
    {
        return $this->belongsTo(Anak::class);
    }

    public function pengukuran()
    {
        return $this->belongsTo(Pengukuran::class);
    }
}
