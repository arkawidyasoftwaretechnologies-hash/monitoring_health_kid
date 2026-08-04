<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HasilStatusGizi extends Model
{
    protected $guarded = [];

    public function pengukuran()
    {
        return $this->belongsTo(Pengukuran::class);
    }
}
