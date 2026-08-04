<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
    <!-- Form Input -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Input Pengukuran: {{ $anak->nama }}</h2>
        </div>
        
        @if (session()->has('message'))
            <div style="padding: 1rem; background: #d1fae5; color: #065f46; border-radius: 0.5rem; margin-bottom: 1rem;">
                {{ session('message') }}
            </div>
        @endif

        <form wire:submit.prevent="submit" style="margin-top: 1rem;">
            <div class="form-group">
                <label>Tanggal Ukur</label>
                <input type="date" wire:model="tanggal_ukur" required>
            </div>
            
            <div style="display: flex; gap: 1rem;">
                <div class="form-group" style="flex: 1;">
                    <label>Berat Badan (kg)</label>
                    <input type="number" step="0.01" wire:model="berat_badan" required>
                </div>
                
                <div class="form-group" style="flex: 1;">
                    <label>Tinggi/Panjang (cm)</label>
                    <input type="number" step="0.1" wire:model="tinggi_badan" required>
                </div>
            </div>

            <div class="form-group">
                <label>Cara Ukur</label>
                <select wire:model="cara_ukur">
                    <option value="berdiri">Berdiri (Tinggi Badan)</option>
                    <option value="telentang">Telentang (Panjang Badan)</option>
                </select>
            </div>

            <div style="display: flex; gap: 1rem;">
                <div class="form-group" style="flex: 1;">
                    <label>Lingkar Kepala (cm) - Opsional</label>
                    <input type="number" step="0.1" wire:model="lingkar_kepala">
                </div>
                
                <div class="form-group" style="flex: 1;">
                    <label>LiLA (cm) - Opsional</label>
                    <input type="number" step="0.1" wire:model="lila">
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">Hitung & Simpan Data</button>
        </form>
    </div>

    <!-- Hasil Kalkulasi (Real-time) -->
    <div class="card" style="background: rgba(255,255,255,0.9);">
        <div class="card-header">
            <h2 class="card-title">Hasil Kalkulasi Z-Score WHO</h2>
        </div>
        
        @if($hasil)
            <div style="margin-top: 1rem;">
                <div style="margin-bottom: 1.5rem; padding-bottom: 1.5rem; border-bottom: 1px solid var(--border);">
                    <h3 style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.5rem;">Berat Badan Menurut Umur (WAZ)</h3>
                    <div style="display: flex; align-items: baseline; gap: 1rem;">
                        <span style="font-size: 2rem; font-weight: 700; color: var(--primary);">{{ $hasil->waz ?? 'N/A' }} SD</span>
                        <span class="badge {{ str_contains(strtolower($hasil->status_bb_u), 'kurang') ? 'badge-warning' : 'badge-normal' }}">{{ $hasil->status_bb_u ?? 'Data tidak tersedia' }}</span>
                    </div>
                </div>

                <div style="margin-bottom: 1.5rem; padding-bottom: 1.5rem; border-bottom: 1px solid var(--border);">
                    <h3 style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.5rem;">Tinggi Badan Menurut Umur (HAZ)</h3>
                    <div style="display: flex; align-items: baseline; gap: 1rem;">
                        <span style="font-size: 2rem; font-weight: 700; color: var(--primary);">{{ $hasil->haz ?? 'N/A' }} SD</span>
                        <span class="badge {{ str_contains(strtolower($hasil->status_tb_u), 'stunted') || str_contains(strtolower($hasil->status_tb_u), 'pendek') ? 'badge-danger' : 'badge-normal' }}">{{ $hasil->status_tb_u ?? 'Data tidak tersedia' }}</span>
                    </div>
                </div>

                <div style="margin-bottom: 1.5rem; padding-bottom: 1.5rem; border-bottom: 1px solid var(--border);">
                    <h3 style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.5rem;">Indeks Massa Tubuh (IMT/U / BMIZ)</h3>
                    <div style="display: flex; align-items: baseline; gap: 1rem;">
                        <span style="font-size: 2rem; font-weight: 700; color: var(--primary);">{{ $hasil->bmiz ?? 'N/A' }} SD</span>
                        <span class="badge {{ str_contains(strtolower($hasil->status_imt_u), 'kurang') ? 'badge-warning' : (str_contains(strtolower($hasil->status_imt_u), 'lebih') || str_contains(strtolower($hasil->status_imt_u), 'obesitas') ? 'badge-danger' : 'badge-normal') }}">{{ $hasil->status_imt_u ?? 'Data tidak tersedia' }}</span>
                    </div>
                </div>

                @if($hasil->hcfa !== null || $hasil->status_lila !== null)
                <div style="margin-bottom: 1.5rem; padding-bottom: 1.5rem; border-bottom: 1px solid var(--border); display: flex; gap: 2rem;">
                    @if($hasil->hcfa !== null)
                    <div>
                        <h3 style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.5rem;">Lingkar Kepala (LK/U)</h3>
                        <div style="display: flex; align-items: baseline; gap: 1rem;">
                            <span style="font-size: 1.5rem; font-weight: 700; color: var(--primary);">{{ $hasil->hcfa }} SD</span>
                            <span class="badge {{ str_contains(strtolower($hasil->status_lk_u), 'normal') ? 'badge-normal' : 'badge-danger' }}">{{ $hasil->status_lk_u }}</span>
                        </div>
                    </div>
                    @endif
                    @if($hasil->status_lila !== null)
                    <div>
                        <h3 style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.5rem;">Status LiLA</h3>
                        <div style="display: flex; align-items: baseline; gap: 1rem;">
                            <span class="badge {{ str_contains(strtolower($hasil->status_lila), 'buruk') ? 'badge-danger' : (str_contains(strtolower($hasil->status_lila), 'kurang') ? 'badge-warning' : 'badge-normal') }}">{{ $hasil->status_lila }}</span>
                        </div>
                    </div>
                    @endif
                </div>
                @endif

                @if($hasil->red_flag)
                    <div style="background: #fee2e2; border-left: 4px solid #ef4444; padding: 1rem; border-radius: 0.5rem;">
                        <h4 style="color: #991b1b; display: flex; align-items: center; gap: 0.5rem; font-weight: 600;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"></polygon><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                            Peringatan Medis (Red Flag)
                        </h4>
                        <p style="color: #7f1d1d; font-size: 0.875rem; margin-top: 0.5rem;">{{ $hasil->catatan_red_flag }}</p>
                    </div>
                @endif
                
                <div style="margin-top: 1.5rem;">
                    <a href="{{ route('anak.index') }}" class="btn btn-success">Kembali ke Daftar Anak</a>
                </div>
            </div>
        @else
            <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 300px; color: var(--text-muted); text-align: center;">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin-bottom: 1rem; opacity: 0.5;"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                <p>Silakan isi form di sebelah kiri untuk<br>menghitung status gizi secara real-time.</p>
            </div>
        @endif
    </div>
</div>
