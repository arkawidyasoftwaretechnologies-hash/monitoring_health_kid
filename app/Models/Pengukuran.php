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
}
