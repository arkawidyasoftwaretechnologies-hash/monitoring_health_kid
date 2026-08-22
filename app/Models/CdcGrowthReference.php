<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CdcGrowthReference extends Model
{
    use HasFactory;

    protected $fillable = [
        'indeks',
        'jenis_kelamin',
        'usia_bulan',
        'L',
        'M',
        'S',
    ];
}
