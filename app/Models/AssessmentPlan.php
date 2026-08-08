<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentPlan extends Model
{
    protected $guarded = [];

    public function pengukuran()
    {
        return $this->belongsTo(Pengukuran::class);
    }

    public function dokter()
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }
}
