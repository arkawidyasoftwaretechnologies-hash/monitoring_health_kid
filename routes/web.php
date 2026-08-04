<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Dashboard;
use App\Livewire\Anak\Index as AnakIndex;
use App\Livewire\Anak\Form as AnakForm;
use App\Livewire\Pengukuran\Form as PengukuranForm;
use App\Livewire\Pengukuran\Chart as PengukuranChart;
use App\Livewire\Laporan\Index as LaporanIndex;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', Dashboard::class)->name('dashboard');
Route::get('/anak', AnakIndex::class)->name('anak.index');
Route::get('/anak/create', AnakForm::class)->name('anak.create');
Route::get('/anak/{anak}/ukur', PengukuranForm::class)->name('pengukuran.create');
Route::get('/anak/{anak}/grafik', PengukuranChart::class)->name('pengukuran.chart');
Route::get('/laporan', LaporanIndex::class)->name('laporan.index');
