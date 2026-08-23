<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Dashboard;
use App\Livewire\Anak\Index as AnakIndex;
use App\Livewire\Anak\Form as AnakForm;
use App\Livewire\Pengukuran\Form as PengukuranForm;
use App\Livewire\Pengukuran\Chart as PengukuranChart;
use App\Livewire\Laporan\Index as LaporanIndex;

use App\Http\Controllers\CetakController;

use App\Livewire\Auth\Login;
use App\Livewire\Pengaturan\TemplateRekomendasiIndex;
use App\Livewire\Referensi\GrafikIdeal;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Authentication Routes
Route::get('/login', Login::class)->name('login');
Route::post('/logout', function () {
    Auth::logout();
    session()->invalidate();
    session()->regenerateToken();
    return redirect('/login');
})->name('logout');

// Protected Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/anak', AnakIndex::class)->name('anak.index');
    Route::get('/anak/create', AnakForm::class)->name('anak.create');
    Route::get('/anak/{anak}/edit', AnakForm::class)->name('anak.edit');
    Route::get('/anak/{anak}/ukur', PengukuranForm::class)->name('pengukuran.create');
    Route::get('/pengukuran/{pengukuran}/edit', PengukuranForm::class)->name('pengukuran.edit');
    Route::get('/anak/{anak}/grafik', PengukuranChart::class)->name('pengukuran.chart');
    Route::get('/laporan', LaporanIndex::class)->name('laporan.index');
    Route::get('/pengaturan/template', TemplateRekomendasiIndex::class)->name('pengaturan.template');
    Route::get('/referensi/grafik', GrafikIdeal::class)->name('referensi.grafik');

    Route::get('/pengukuran/{id}/cetak/orangtua', [CetakController::class, 'versiOrangTua'])->name('cetak.orangtua');
    Route::get('/pengukuran/{id}/cetak/medis', [CetakController::class, 'versiMedis'])->name('cetak.medis');
});
