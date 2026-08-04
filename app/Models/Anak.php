<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Anak extends Model
{
    protected $guarded = [];

    public function puskesmas()
    {
        return $this->belongsTo(Puskesmas::class);
    }

    public function pengukurans()
    {
        return $this->hasMany(Pengukuran::class);
    }
}
