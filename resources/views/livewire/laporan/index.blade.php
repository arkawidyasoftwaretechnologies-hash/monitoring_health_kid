<div class="card">
    <div class="card-header">
        <h2 class="card-title">Laporan Ringkasan Gizi Anak</h2>
    </div>

    <div style="margin-top: 1.5rem;">
        <p style="color: var(--text-muted); margin-bottom: 2rem;">Berikut adalah rekapitulasi data berdasarkan seluruh riwayat pengukuran anak-anak yang terdaftar dalam sistem pemantauan.</p>
        
        <div class="dashboard-grid">
            <div class="card stat-card" style="background: rgba(79, 70, 229, 0.05); border-color: rgba(79, 70, 229, 0.2);">
                <div class="stat-icon primary">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <div class="stat-details">
                    <h3>Total Anak Terdaftar</h3>
                    <p>{{ $totalAnak }}</p>
                </div>
            </div>
            
            <div class="card stat-card" style="background: rgba(239, 68, 68, 0.05); border-color: rgba(239, 68, 68, 0.2);">
                <div class="stat-icon danger">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                </div>
                <div class="stat-details">
                    <h3>Indikasi Stunting (HAZ < -2 SD)</h3>
                    <p>{{ $stunted }}</p>
                </div>
            </div>
            
            <div class="card stat-card" style="background: rgba(245, 158, 11, 0.05); border-color: rgba(245, 158, 11, 0.2);">
                <div class="stat-icon warning">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.24 12.24a6 6 0 0 0-8.49-8.49L5 10.5V19h8.5z"/><line x1="16" y1="8" x2="2" y2="22"/><line x1="17.5" y1="15" x2="9" y2="6.5"/></svg>
                </div>
                <div class="stat-details">
                    <h3>Gizi Kurang (WAZ < -2 SD)</h3>
                    <p>{{ $underweight }}</p>
                </div>
            </div>
        </div>

        <div style="margin-top: 3rem;">
            <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                <h2 class="card-title">Filter & Export Data Pengukuran</h2>
                <div style="display: flex; gap: 0.5rem;">
                    <button wire:click="exportExcel" class="btn btn-success" style="padding: 0.5rem 1rem; font-size: 0.8rem;">Export XLS</button>
                    <button wire:click="exportPdf" class="btn btn-danger" style="background: #ef4444; color: white; padding: 0.5rem 1rem; font-size: 0.8rem; border: none; border-radius: 0.5rem; cursor: pointer;">Export PDF</button>
                </div>
            </div>
            
            <div style="display: flex; gap: 1rem; margin-top: 1.5rem; flex-wrap: wrap;">
                <div class="form-group" style="flex: 1; min-width: 200px;">
                    <label>Pilih Anak</label>
                    <select wire:model.live="selectedAnak">
                        <option value="">-- Semua Anak --</option>
                        @foreach($anaksList as $anak)
                            <option value="{{ $anak->id }}">{{ $anak->nama }} ({{ $anak->nik ?? '-' }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="flex: 1; min-width: 150px;">
                    <label>Tanggal Awal</label>
                    <input type="date" wire:model.live="startDate">
                </div>
                <div class="form-group" style="flex: 1; min-width: 150px;">
                    <label>Tanggal Akhir</label>
                    <input type="date" wire:model.live="endDate">
                </div>
                <div class="form-group" style="flex: 1; min-width: 150px;">
                    <label>Status TB/U (Stunting)</label>
                    <select wire:model.live="statusTBU">
                        <option value="">-- Semua Status --</option>
                        <option value="Normal">Normal</option>
                        <option value="Pendek (Stunted)">Pendek (Stunted)</option>
                        <option value="Sangat Pendek (Severely Stunted)">Sangat Pendek</option>
                        <option value="Tinggi">Tinggi</option>
                    </select>
                </div>
                <div class="form-group" style="flex: 1; min-width: 150px;">
                    <label>Peringatan Medis (Red Flag)</label>
                    <select wire:model.live="redFlag">
                        <option value="">-- Semua --</option>
                        <option value="1">Ya (Ada Red Flag)</option>
                        <option value="0">Tidak (Aman)</option>
                    </select>
                </div>
            </div>

            <div class="table-wrapper" style="margin-top: 1.5rem;">
                <table>
                    <thead>
                        <tr>
                            <th>Tgl Ukur</th>
                            <th>Nama Anak</th>
                            <th>Usia</th>
                            <th>BB</th>
                            <th>TB</th>
                            <th>LK</th>
                            <th>LiLA</th>
                            <th>IMT/U (BMIZ)</th>
                            <th>WAZ</th>
                            <th>HAZ</th>
                            <th>Red Flag</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pengukurans as $row)
                        <tr>
                            <td>{{ $row->tanggal_ukur }}</td>
                            <td style="font-weight: 500;">{{ $row->anak->nama ?? '-' }}</td>
                            <td>{{ $row->usia_bulan }} bln</td>
                            <td>{{ $row->berat_badan }} kg</td>
                            <td>{{ $row->tinggi_badan }} cm</td>
                            <td>{{ $row->lingkar_kepala ? $row->lingkar_kepala . ' cm' : '-' }}</td>
                            <td>{{ $row->lila ? $row->lila . ' cm' : '-' }}</td>
                            <td>
                                <strong style="color: var(--primary);">{{ $row->hasilStatusGizi->bmiz ?? '-' }}</strong><br>
                                <span class="badge {{ str_contains(strtolower($row->hasilStatusGizi->status_imt_u ?? ''), 'kurang') ? 'badge-warning' : (str_contains(strtolower($row->hasilStatusGizi->status_imt_u ?? ''), 'lebih') || str_contains(strtolower($row->hasilStatusGizi->status_imt_u ?? ''), 'obesitas') ? 'badge-danger' : 'badge-normal') }}" style="font-size: 0.6rem;">
                                    {{ $row->hasilStatusGizi->status_imt_u ?? '-' }}
                                </span>
                            </td>
                            <td>
                                <strong style="color: var(--primary);">{{ $row->hasilStatusGizi->waz ?? '-' }}</strong><br>
                                <span class="badge {{ str_contains(strtolower($row->hasilStatusGizi->status_bb_u ?? ''), 'kurang') ? 'badge-warning' : 'badge-normal' }}" style="font-size: 0.6rem;">
                                    {{ $row->hasilStatusGizi->status_bb_u ?? '-' }}
                                </span>
                            </td>
                            <td>
                                <strong style="color: var(--primary);">{{ $row->hasilStatusGizi->haz ?? '-' }}</strong><br>
                                <span class="badge {{ str_contains(strtolower($row->hasilStatusGizi->status_tb_u ?? ''), 'stunted') || str_contains(strtolower($row->hasilStatusGizi->status_tb_u ?? ''), 'pendek') ? 'badge-danger' : 'badge-normal' }}" style="font-size: 0.6rem;">
                                    {{ $row->hasilStatusGizi->status_tb_u ?? '-' }}
                                </span>
                            </td>
                            <td>
                                @if(optional($row->hasilStatusGizi)->red_flag)
                                    <span style="color: red; font-weight: bold;">Ya</span>
                                @else
                                    <span style="color: gray;">Tidak</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" style="text-align: center; color: var(--text-muted); padding: 2rem;">Tidak ada data pengukuran yang cocok dengan filter.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
