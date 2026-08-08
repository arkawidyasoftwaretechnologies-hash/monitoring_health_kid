<div style="display: flex; flex-direction: column; gap: 1.5rem;">
    <!-- Bagian Atas: Form Input Pengukuran -->
    <div class="card" style="margin-bottom: 0;">
        <div class="card-header" style="padding-bottom: 0.5rem; border-bottom: 1px solid var(--border); margin-bottom: 1rem;">
            <h2 class="card-title" style="font-size: 1.1rem;">
                <span style="background: #e0f2fe; color: #0284c7; padding: 0.2rem 0.6rem; border-radius: 4px; font-size: 0.8rem; margin-right: 0.5rem;">Langkah 1</span> 
                {{ $pengukuran_id ? 'Edit Pengukuran' : 'Input Pengukuran' }}: <strong>{{ $anak->nama }}</strong>
            </h2>
        </div>
        
        @if (session()->has('message'))
            <div style="padding: 0.75rem; background: #d1fae5; color: #065f46; border-radius: 0.5rem; margin-bottom: 1rem; font-size: 0.9rem;">
                {{ session('message') }}
            </div>
        @endif

            <!-- Form Grouping -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1rem;">
                
                <!-- Group 1: Waktu & Identitas -->
                <div style="padding: 1.25rem; border: 1px solid #cbd5e1; border-radius: 0.5rem; background: #f8fafc; box-shadow: inset 0 2px 4px 0 rgb(0 0 0 / 0.02);">
                    <h4 style="font-size: 0.8rem; color: #334155; margin-bottom: 1rem; text-transform: uppercase; font-weight: 800; letter-spacing: 0.05em; border-bottom: 2px solid #e2e8f0; padding-bottom: 0.5rem;">🗓️ Waktu Pengukuran</h4>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label style="font-size: 0.85rem; font-weight: 700; color: #1e293b; margin-bottom: 0.4rem; display: block;">Tanggal Ukur</label>
                        <input type="date" wire:model="tanggal_ukur" required {{ Auth::user()->isDokter() ? 'disabled' : '' }} style="padding: 0.6rem; width: 100%; border: 1px solid #94a3b8; border-radius: 0.375rem; background: {{ Auth::user()->isDokter() ? '#f1f5f9' : '#ffffff' }}; color: #0f172a; font-size: 0.95rem; box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);">
                    </div>
                </div>

                <!-- Group 2: Berat Badan -->
                <div style="padding: 1.25rem; border: 1px solid #86efac; border-radius: 0.5rem; background: #f0fdf4; box-shadow: inset 0 2px 4px 0 rgb(0 0 0 / 0.02);">
                    <h4 style="font-size: 0.8rem; color: #166534; margin-bottom: 1rem; text-transform: uppercase; font-weight: 800; letter-spacing: 0.05em; border-bottom: 2px solid #bbf7d0; padding-bottom: 0.5rem;">⚖️ Data Berat Badan</h4>
                    <div style="display: flex; gap: 1rem;">
                        <div class="form-group" style="margin-bottom: 0; flex: 3;">
                            <label style="font-size: 0.85rem; font-weight: 700; color: #14532d; margin-bottom: 0.4rem; display: block;">Alat Ukur Berat</label>
                            <select wire:model="alat_ukur_bb" {{ Auth::user()->isDokter() ? 'disabled' : '' }} style="padding: 0.6rem; width: 100%; border: 1px solid #4ade80; border-radius: 0.375rem; background: {{ Auth::user()->isDokter() ? '#f1f5f9' : '#ffffff' }}; color: #064e3b; font-size: 0.95rem; box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);">
                                <option value="Timbangan Digital">Digital</option>
                                <option value="Timbangan Dacin">Dacin</option>
                                <option value="Timbangan Injak">Injak</option>
                                <option value="Timbangan Gantung">Gantung</option>
                            </select>
                        </div>
                        <div class="form-group" style="margin-bottom: 0; flex: 2;">
                            <label style="font-size: 0.85rem; font-weight: 700; color: #14532d; margin-bottom: 0.4rem; display: block;">Nilai BB (kg)</label>
                            <input type="number" step="0.01" wire:model="berat_badan" required {{ Auth::user()->isDokter() ? 'disabled' : '' }} style="padding: 0.6rem; width: 100%; border: 2px solid #22c55e; border-radius: 0.375rem; background: {{ Auth::user()->isDokter() ? '#f1f5f9' : '#ffffff' }}; font-weight: bold; color: #14532d; font-size: 1.1rem; text-align: center; box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);">
                        </div>
                    </div>
                </div>

                <!-- Group 3: Panjang/Tinggi Badan -->
                <div style="padding: 1.25rem; border: 1px solid #93c5fd; border-radius: 0.5rem; background: #eff6ff; grid-column: 1 / -1; box-shadow: inset 0 2px 4px 0 rgb(0 0 0 / 0.02);">
                    <h4 style="font-size: 0.8rem; color: #1e40af; margin-bottom: 1rem; text-transform: uppercase; font-weight: 800; letter-spacing: 0.05em; border-bottom: 2px solid #bfdbfe; padding-bottom: 0.5rem;">📏 Data Panjang / Tinggi Badan</h4>
                    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                        <div class="form-group" style="margin-bottom: 0; flex: 2; min-width: 150px;">
                            <label style="font-size: 0.85rem; font-weight: 700; color: #1e3a8a; margin-bottom: 0.4rem; display: block;">Alat Ukur Tinggi</label>
                            <select wire:model="alat_ukur_tb" {{ Auth::user()->isDokter() ? 'disabled' : '' }} style="padding: 0.6rem; width: 100%; border: 1px solid #60a5fa; border-radius: 0.375rem; background: {{ Auth::user()->isDokter() ? '#f1f5f9' : '#ffffff' }}; color: #1e3a8a; font-size: 0.95rem; box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);">
                                <option value="Microtoise">Microtoise</option>
                                <option value="Infantometer">Infantometer</option>
                                <option value="Pita Meteran">Pita Meteran</option>
                            </select>
                        </div>
                        <div class="form-group" style="margin-bottom: 0; flex: 2; min-width: 150px;">
                            <label style="font-size: 0.85rem; font-weight: 700; color: #1e3a8a; margin-bottom: 0.4rem; display: block;">Posisi Ukur</label>
                            <select wire:model="cara_ukur" {{ Auth::user()->isDokter() ? 'disabled' : '' }} style="padding: 0.6rem; width: 100%; border: 1px solid #60a5fa; border-radius: 0.375rem; background: {{ Auth::user()->isDokter() ? '#f1f5f9' : '#ffffff' }}; color: #1e3a8a; font-size: 0.95rem; box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);">
                                <option value="berdiri">Berdiri (Tinggi Badan)</option>
                                <option value="telentang">Telentang (Panjang Badan)</option>
                            </select>
                        </div>
                        <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 120px;">
                            <label style="font-size: 0.85rem; font-weight: 700; color: #1e3a8a; margin-bottom: 0.4rem; display: block;">Nilai TB/PB (cm)</label>
                            <input type="number" step="0.1" wire:model="tinggi_badan" required {{ Auth::user()->isDokter() ? 'disabled' : '' }} style="padding: 0.6rem; width: 100%; border: 2px solid #3b82f6; border-radius: 0.375rem; background: {{ Auth::user()->isDokter() ? '#f1f5f9' : '#ffffff' }}; font-weight: bold; color: #1e3a8a; font-size: 1.1rem; text-align: center; box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);">
                        </div>
                    </div>
                </div>

                <!-- Group 4: Opsional -->
                <div style="padding: 1.25rem; border: 1px solid #d8b4fe; border-radius: 0.5rem; background: #faf5ff; grid-column: 1 / -1; box-shadow: inset 0 2px 4px 0 rgb(0 0 0 / 0.02);">
                    <h4 style="font-size: 0.8rem; color: #6b21a8; margin-bottom: 1rem; text-transform: uppercase; font-weight: 800; letter-spacing: 0.05em; border-bottom: 2px solid #e9d5ff; padding-bottom: 0.5rem;">🧩 Pengukuran Tambahan (Opsional)</h4>
                    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                        <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 150px;">
                            <label style="font-size: 0.85rem; font-weight: 700; color: #581c87; margin-bottom: 0.4rem; display: block;">Lingkar Kepala (cm)</label>
                            <input type="number" step="0.1" wire:model="lingkar_kepala" {{ Auth::user()->isDokter() ? 'disabled' : '' }} style="padding: 0.6rem; width: 100%; border: 1px solid #c084fc; border-radius: 0.375rem; background: {{ Auth::user()->isDokter() ? '#f1f5f9' : '#ffffff' }}; color: #581c87; font-size: 0.95rem; box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);">
                        </div>
                        <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 150px;">
                            <label style="font-size: 0.85rem; font-weight: 700; color: #581c87; margin-bottom: 0.4rem; display: block;">Lingkar Lengan Atas / LiLA (cm)</label>
                            <input type="number" step="0.1" wire:model="lila" {{ Auth::user()->isDokter() ? 'disabled' : '' }} style="padding: 0.6rem; width: 100%; border: 1px solid #c084fc; border-radius: 0.375rem; background: {{ Auth::user()->isDokter() ? '#f1f5f9' : '#ffffff' }}; color: #581c87; font-size: 0.95rem; box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);">
                        </div>
                    </div>
                </div>

            </div>

            <!-- Tombol Aksi Form Input -->
            <div style="display: flex; justify-content: flex-end; gap: 1rem; margin-top: 1rem; border-top: 1px solid var(--border); padding-top: 1rem;">
                <a href="{{ route('anak.index') }}" class="btn btn-outline" style="padding: 0.5rem 1.5rem; text-decoration: none; color: #475569; border: 1px solid #cbd5e1; border-radius: 0.5rem; background: white;">Kembali</a>
                
                @if(!Auth::user()->isDokter())
                <button type="submit" class="btn btn-primary" style="padding: 0.5rem 2rem; font-weight: bold; background: #2563eb; border: none; border-radius: 0.5rem; color: white; cursor: pointer;">
                    {{ $pengukuran_id ? '🔄 Perbarui Data Pengukuran' : '🧮 Hitung & Simpan Data' }}
                </button>
                @endif
            </div>
        </form>
    </div>

    <!-- Bagian Bawah: Hasil Kalkulasi & Assessment (Tampil jika sudah dihitung) -->
    @if($hasil)
    <div class="card" style="background: rgba(255,255,255,0.95); box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);">
        <div class="card-header" style="padding-bottom: 0.5rem; border-bottom: 1px solid var(--border); margin-bottom: 1rem;">
            <h2 class="card-title" style="font-size: 1.1rem; display: flex; justify-content: space-between; align-items: center;">
                <span>
                    <span style="background: #dcfce7; color: #166534; padding: 0.2rem 0.6rem; border-radius: 4px; font-size: 0.8rem; margin-right: 0.5rem;">Hasil Kalkulasi</span> 
                    Z-Score WHO
                </span>
                <span style="font-size: 0.8rem; color: var(--text-muted); font-weight: normal;">Usia saat diukur: <strong>{{ $hasil->pengukuran->usia_bulan ?? '-' }} bulan</strong></span>
            </h2>
        </div>
        
        <!-- Grid Hasil Z-Score (4 Kolom) -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
            
            <div style="padding: 1rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 0.5rem;">
                <h3 style="font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); margin-bottom: 0.5rem;">Berat / Umur (WAZ)</h3>
                <div style="display: flex; flex-direction: column; gap: 0.3rem;">
                    <span style="font-size: 1.5rem; font-weight: 800; color: var(--primary);">{{ $hasil->waz ?? 'N/A' }}</span>
                    <span class="badge {{ str_contains(strtolower($hasil->status_bb_u), 'kurang') ? 'badge-warning' : 'badge-normal' }}" style="align-self: flex-start; font-size: 0.7rem;">{{ $hasil->status_bb_u ?? '-' }}</span>
                </div>
            </div>

            <div style="padding: 1rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 0.5rem;">
                <h3 style="font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); margin-bottom: 0.5rem;">Tinggi / Umur (HAZ)</h3>
                <div style="display: flex; flex-direction: column; gap: 0.3rem;">
                    <span style="font-size: 1.5rem; font-weight: 800; color: var(--primary);">{{ $hasil->haz ?? 'N/A' }}</span>
                    <span class="badge {{ str_contains(strtolower($hasil->status_tb_u), 'stunted') || str_contains(strtolower($hasil->status_tb_u), 'pendek') ? 'badge-danger' : 'badge-normal' }}" style="align-self: flex-start; font-size: 0.7rem;">{{ $hasil->status_tb_u ?? '-' }}</span>
                </div>
            </div>

            <div style="padding: 1rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 0.5rem;">
                <h3 style="font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); margin-bottom: 0.5rem;">IMT / Umur (BMIZ)</h3>
                <div style="display: flex; flex-direction: column; gap: 0.3rem;">
                    <span style="font-size: 1.5rem; font-weight: 800; color: var(--primary);">{{ $hasil->bmiz ?? 'N/A' }}</span>
                    <span class="badge {{ str_contains(strtolower($hasil->status_imt_u), 'kurang') ? 'badge-warning' : (str_contains(strtolower($hasil->status_imt_u), 'lebih') || str_contains(strtolower($hasil->status_imt_u), 'obesitas') ? 'badge-danger' : 'badge-normal') }}" style="align-self: flex-start; font-size: 0.7rem;">{{ $hasil->status_imt_u ?? '-' }}</span>
                </div>
            </div>
            
            <div style="padding: 1rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 0.5rem;">
                <h3 style="font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); margin-bottom: 0.5rem;">Berat / Tinggi (WHZ)</h3>
                <div style="display: flex; flex-direction: column; gap: 0.3rem;">
                    <span style="font-size: 1.5rem; font-weight: 800; color: var(--primary);">{{ $hasil->whz ?? 'N/A' }}</span>
                    <span class="badge {{ str_contains(strtolower($hasil->status_bb_tb), 'kurang') || str_contains(strtolower($hasil->status_bb_tb), 'buruk') ? 'badge-warning' : (str_contains(strtolower($hasil->status_bb_tb), 'lebih') || str_contains(strtolower($hasil->status_bb_tb), 'obesitas') ? 'badge-danger' : 'badge-normal') }}" style="align-self: flex-start; font-size: 0.7rem;">{{ $hasil->status_bb_tb ?? '-' }}</span>
                </div>
            </div>
            
            @if($hasil->hcfa !== null)
            <div style="padding: 1rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 0.5rem;">
                <h3 style="font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); margin-bottom: 0.5rem;">LK / Umur (HCFA)</h3>
                <div style="display: flex; flex-direction: column; gap: 0.3rem;">
                    <span style="font-size: 1.5rem; font-weight: 800; color: var(--primary);">{{ $hasil->hcfa }}</span>
                    <span class="badge {{ str_contains(strtolower($hasil->status_lk_u), 'normal') ? 'badge-normal' : 'badge-danger' }}" style="align-self: flex-start; font-size: 0.7rem;">{{ $hasil->status_lk_u }}</span>
                </div>
            </div>
            @endif

            @if($hasil->status_lila !== null)
            <div style="padding: 1rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 0.5rem;">
                <h3 style="font-size: 0.75rem; text-transform: uppercase; color: var(--text-muted); margin-bottom: 0.5rem;">Status LiLA</h3>
                <div style="display: flex; flex-direction: column; gap: 0.3rem;">
                    <span style="font-size: 1.5rem; font-weight: 800; color: var(--text-muted);">-</span>
                    <span class="badge {{ str_contains(strtolower($hasil->status_lila), 'buruk') ? 'badge-danger' : (str_contains(strtolower($hasil->status_lila), 'kurang') ? 'badge-warning' : 'badge-normal') }}" style="align-self: flex-start; font-size: 0.7rem;">{{ $hasil->status_lila }}</span>
                </div>
            </div>
            @endif
        </div>

        <!-- Notifikasi (Interpretasi & Red Flag) berdampingan -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
            @if($hasil->narasi_interpretasi)
                <div style="background: rgba(52,152,219,0.05); border-left: 4px solid #3498db; padding: 1rem; border-radius: 0 0.5rem 0.5rem 0;">
                    <h4 style="color: #2980b9; display: flex; align-items: center; gap: 0.5rem; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.9rem;">
                        💡 Kesimpulan Sistem
                    </h4>
                    <p style="color: #2c3e50; font-size: 0.85rem; line-height: 1.4; margin: 0;">{{ $hasil->narasi_interpretasi }}</p>
                </div>
            @endif

            @if($hasil->red_flag)
                <div style="background: #fef2f2; border-left: 4px solid #ef4444; padding: 1rem; border-radius: 0 0.5rem 0.5rem 0;">
                    <h4 style="color: #b91c1c; display: flex; align-items: center; gap: 0.5rem; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.9rem;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"></polygon><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                        Peringatan (Red Flag)
                    </h4>
                    <p style="color: #7f1d1d; font-size: 0.85rem; margin: 0; line-height: 1.4;">{{ $hasil->catatan_red_flag }}</p>
                </div>
            @endif
        </div>
        
        <!-- Assessment & Plan (Langkah 2) -->
        @if($show_assessment_form && !Auth::user()->isOperator())
            <div style="background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 0.5rem; padding: 1.5rem;">
                <h4 style="color: #0f172a; font-weight: 700; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; font-size: 1rem;">
                    <span style="background: #2563eb; color: white; padding: 0.2rem 0.5rem; border-radius: 0.25rem; font-size: 0.7rem; text-transform: uppercase;">Langkah 2</span>
                    ✍️ Draft Assessment & Plan Klinis (Rekam Medis)
                </h4>
                
                <form wire:submit.prevent="simpanAssessment" x-data="{
                    draftAssessment: @entangle('draft_assessment'),
                    draftPlan: @entangle('draft_plan'),
                    toggleText(type, text, isChecked) {
                        let currentText = (type === 'assessment' ? this.draftAssessment : this.draftPlan) || '';
                        let bullet = '- ' + text;
                        if (isChecked) {
                            currentText = currentText ? currentText + '\n' + bullet : bullet;
                        } else {
                            currentText = currentText.replace('\n' + bullet, '').replace(bullet + '\n', '').replace(bullet, '').trim();
                        }
                        if (type === 'assessment') this.draftAssessment = currentText;
                        else this.draftPlan = currentText;
                    }
                }">
                    <div style="display: flex; flex-direction: column; gap: 1.5rem; margin-bottom: 1.5rem;">
                        
                        <!-- Blok Assessment -->
                        <div style="display: grid; grid-template-columns: 1fr auto; gap: 1.5rem; align-items: stretch;">
                            <!-- Kolom Textarea (Caption) -->
                            <div class="form-group" style="margin-bottom: 0; display: flex; flex-direction: column;">
                                <label style="display:block; margin-bottom: 0.4rem; color: #475569; font-weight: 700; font-size: 0.85rem;">📝 Assessment Klinis</label>
                                <textarea x-model="draftAssessment" class="form-control" style="flex: 1; width: 100%; min-height: 120px; padding: 0.6rem; border-radius: 0.375rem; border: 1px solid #94a3b8; font-size: 0.9rem; line-height: 1.4; box-shadow: inset 0 1px 2px 0 rgb(0 0 0 / 0.05);"></textarea>
                            </div>
                            <!-- Kolom Template Cek Box -->
                            <div style="background: #f1f5f9; padding: 1rem; border-radius: 0.375rem; border: 1px solid #e2e8f0; min-width: 260px; max-width: 350px; align-self: flex-start;">
                                <span style="font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 0.8rem; display: block; text-transform: uppercase; text-align: left;">Template Assessment:</span>
                                <div style="display: flex; flex-direction: column; gap: 0.6rem;">
                                    @foreach($templates as $template)
                                        @if($template->template_assessment)
                                        <label style="font-size: 0.8rem; color: #334155; display: flex; align-items: flex-start; justify-content: flex-start; gap: 0.5rem; cursor: pointer; margin: 0; width: 100%;">
                                            <input type="checkbox" @change="toggleText('assessment', '{{ addslashes($template->template_assessment) }}', $event.target.checked)" style="width: auto !important; margin: 0.15rem 0 0 0 !important; flex-shrink: 0;">
                                            <span style="text-align: left; line-height: 1.3;">{{ $template->nama_template }}</span>
                                        </label>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        
                        <hr style="border-top: 1px dashed #cbd5e1; margin: 0;">

                        <!-- Blok Plan -->
                        <div style="display: grid; grid-template-columns: 1fr auto; gap: 1.5rem; align-items: stretch;">
                            <!-- Kolom Textarea (Caption) -->
                            <div class="form-group" style="margin-bottom: 0; display: flex; flex-direction: column;">
                                <label style="display:block; margin-bottom: 0.4rem; color: #475569; font-weight: 700; font-size: 0.85rem;">🎯 Plan / Rencana Tindak Lanjut</label>
                                <textarea x-model="draftPlan" class="form-control" style="flex: 1; width: 100%; min-height: 120px; padding: 0.6rem; border-radius: 0.375rem; border: 1px solid #94a3b8; font-size: 0.9rem; line-height: 1.4; box-shadow: inset 0 1px 2px 0 rgb(0 0 0 / 0.05);"></textarea>
                            </div>
                            <!-- Kolom Template Cek Box -->
                            <div style="background: #f1f5f9; padding: 1rem; border-radius: 0.375rem; border: 1px solid #e2e8f0; min-width: 260px; max-width: 350px; align-self: flex-start;">
                                <span style="font-size: 0.75rem; font-weight: 700; color: #64748b; margin-bottom: 0.8rem; display: block; text-transform: uppercase; text-align: left;">Template Plan:</span>
                                <div style="display: flex; flex-direction: column; gap: 0.6rem;">
                                    @foreach($templates as $template)
                                        @if($template->template_plan)
                                        <label style="font-size: 0.8rem; color: #334155; display: flex; align-items: flex-start; justify-content: flex-start; gap: 0.5rem; cursor: pointer; margin: 0; width: 100%;">
                                            <input type="checkbox" @change="toggleText('plan', '{{ addslashes($template->template_plan) }}', $event.target.checked)" style="width: auto !important; margin: 0.15rem 0 0 0 !important; flex-shrink: 0;">
                                            <span style="text-align: left; line-height: 1.3;">{{ $template->nama_template }}</span>
                                        </label>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #e2e8f0; padding-top: 1rem; flex-wrap: wrap; gap: 1rem;">
                        <span style="font-size: 0.75rem; color: #64748b; flex: 1; min-width: 200px;">
                            <strong>Penting:</strong> Dengan menekan setuju, Anda menyetujui rekomendasi ini untuk disimpan sebagai bagian dari rekam medis resmi.
                        </span>
                        
                        <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                            @if($pengukuran_id && \App\Models\AssessmentPlan::where('pengukuran_id', $pengukuran_id)->exists())
                                <a href="{{ route('cetak.medis', $pengukuran_id) }}" target="_blank" class="btn btn-secondary" style="padding: 0.5rem 1rem; text-decoration: none; color: white; border-radius: 0.375rem; background: #475569; font-size: 0.85rem;">🖨️ Cetak Medis</a>
                                <a href="{{ route('cetak.orangtua', $pengukuran_id) }}" target="_blank" class="btn btn-info" style="padding: 0.5rem 1rem; text-decoration: none; color: white; border-radius: 0.375rem; background: #0891b2; font-size: 0.85rem;">🖨️ Cetak Ortu</a>
                            @endif

                            <button type="submit" class="btn btn-success" style="padding: 0.5rem 1.5rem; font-weight: bold; background: #16a34a; color: white; border: none; border-radius: 0.375rem; cursor: pointer; box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);">
                                ✅ Setuju & Simpan ke Rekam Medis
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        @endif
    </div>
    @endif
</div>
