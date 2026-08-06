<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RedFlagLog extends Model
{
    protected $fillable = [
        'pengukuran_id',
        'anak_id',
        'kategori_flag',
        'severity',
        'nilai_pemicu',
        'rekomendasi_rujukan',
        'status',
    ];

    public function pengukuran()
    {
        return $this->belongsTo(Pengukuran::class);
    }

    public function anak()
    {
        return $this->belongsTo(Anak::class);
    }
}
