<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengukuran extends Model
{
    protected $guarded = [];

    public function anak()
    {
        return $this->belongsTo(Anak::class);
    }

    public function hasilStatusGizi()
    {
        return $this->hasOne(HasilStatusGizi::class);
    }

    public function redFlagLogs()
    {
        return $this->hasMany(RedFlagLog::class);
    }

    public function assessmentPlan()
    {
        return $this->hasOne(AssessmentPlan::class);
    }

    public function jadwalKontrols()
    {
        return $this->hasMany(JadwalKontrol::class);
    }
}
