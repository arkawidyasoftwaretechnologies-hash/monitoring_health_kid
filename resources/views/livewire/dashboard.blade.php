<div>
    <div class="page-header animate-fade-in">
        <div>
            <h1 class="page-title">
                <span class="page-title-icon">🏠</span> 
                Dashboard Utama
            </h1>
            <p class="page-subtitle">Ringkasan data pemantauan pertumbuhan anak di posyandu</p>
        </div>
    </div>

    <div class="dashboard-grid animate-fade-in">
        <div class="card stat-card">
            <div class="stat-icon primary">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div class="stat-details">
                <h3>Total Anak</h3>
                <p>{{ $totalAnak }}</p>
            </div>
        </div>
        
        <div class="card stat-card">
            <div class="stat-icon success">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
            </div>
            <div class="stat-details">
                <h3>Total Pengukuran</h3>
                <p>{{ $totalPengukuran }}</p>
            </div>
        </div>

        <div class="card stat-card">
            <div class="stat-icon danger">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            </div>
            <div class="stat-details">
                <h3>Indikasi Stunting</h3>
                <p>{{ $stuntingAnak }}</p>
            </div>
        </div>
    </div>

    <div class="card animate-fade-in">
        <div class="card-header">
            <h2 class="card-title">Selamat Datang di Stunting Monitor</h2>
        </div>
        <p style="color: var(--text-muted); margin-top: 1rem;">Gunakan menu navigasi untuk mengelola data anak dan memasukkan hasil pengukuran bulanan secara presisi menggunakan standar WHO Child Growth.</p>
        <div style="margin-top: 1.5rem;">
            <a href="{{ route('anak.index') }}" class="btn btn-primary">Kelola Data Anak &rarr;</a>
        </div>
    </div>
</div>
