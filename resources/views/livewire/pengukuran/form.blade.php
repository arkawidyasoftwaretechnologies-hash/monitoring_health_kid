<div class="animate-fade-in" style="display: flex; flex-direction: column; gap: 1.5rem;">
    
    <div class="page-header" style="align-items: center; margin-bottom: 0;">
        <div>
            <h1 class="page-title">
                <span class="page-title-icon">📏</span> 
                {{ $pengukuran_id ? 'Edit Pengukuran' : 'Input Pengukuran' }}
            </h1>
            <p class="page-subtitle">Subjek Pemantauan: <strong>{{ $anak->nama }}</strong></p>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <a href="{{ route('anak.index') }}" class="btn btn-outline" style="padding: 0.4rem 1rem; text-decoration: none; color: #475569; border: 1px solid #cbd5e1; border-radius: 0.5rem; background: white; font-size: 0.85rem;">Kembali</a>
            @if(!Auth::user()->isDokter())
            <button type="submit" form="form-pengukuran" class="btn btn-primary" style="padding: 0.4rem 1rem; font-weight: bold; background: #2563eb; border: none; border-radius: 0.5rem; color: white; cursor: pointer; font-size: 0.85rem;">
                {{ $pengukuran_id ? '🔄 Perbarui' : '🧮 Hitung' }}
            </button>
            @endif
        </div>
    </div>

    <!-- Bagian Atas: Form Input Pengukuran -->
    <div class="card" style="margin-bottom: 0;">
        <form id="form-pengukuran" wire:submit.prevent="submit">
            <h4 style="color: var(--text-main); font-weight: 700; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; font-size: 1rem; border-bottom: 1px solid var(--border); padding-bottom: 0.8rem;">
                <span style="background: #e0f2fe; color: #0284c7; padding: 0.2rem 0.5rem; border-radius: 0.25rem; font-size: 0.7rem; text-transform: uppercase;">Langkah 1</span>
                Input Data Antropometri
            </h4>
            
            @if (session()->has('message'))
                <div style="padding: 0.75rem; background: #d1fae5; color: #065f46; border-radius: 0.5rem; margin-bottom: 1rem; font-size: 0.9rem;">
                    {{ session('message') }}
                </div>
            @endif

            <!-- Form Grouping -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1rem;">
                
                <!-- Group 1: Waktu & Identitas -->
                <div class="form-section">
                    <h4 class="form-section-title"><span>🗓️</span> Waktu & Standar Pengukuran</h4>
                    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                        <div class="form-group" style="margin-bottom: 0; flex: 1;">
                            <label>Tanggal Ukur</label>
                            <input type="date" wire:model="tanggal_ukur" required {{ Auth::user()->isDokter() ? 'disabled' : '' }}>
                        </div>
                    </div>
                </div>

                <!-- Group 2: Berat Badan -->
                <div class="form-section">
                    <h4 class="form-section-title"><span>⚖️</span> Data Berat Badan</h4>
                    <div style="display: flex; gap: 1rem;">
                        <div class="form-group" style="margin-bottom: 0; flex: 3;">
                            <label>Alat Ukur Berat</label>
                            <select wire:model="alat_ukur_bb" {{ Auth::user()->isDokter() ? 'disabled' : '' }}>
                                <option value="Timbangan Digital">Digital</option>
                                <option value="Timbangan Dacin">Dacin</option>
                                <option value="Timbangan Injak">Injak</option>
                                <option value="Timbangan Gantung">Gantung</option>
                            </select>
                        </div>
                        <div class="form-group" style="margin-bottom: 0; flex: 2;">
                            <label>Nilai BB (kg)</label>
                            <input type="number" step="0.01" wire:model="berat_badan" required {{ Auth::user()->isDokter() ? 'disabled' : '' }} style="font-weight: bold; font-size: 1.1rem; text-align: center;">
                        </div>
                    </div>
                </div>

                <!-- Group 3: Panjang/Tinggi Badan -->
                <div class="form-section" style="grid-column: 1 / -1;">
                    <h4 class="form-section-title"><span>📏</span> Data Panjang / Tinggi Badan</h4>
                    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                        <div class="form-group" style="margin-bottom: 0; flex: 2; min-width: 150px;">
                            <label>Alat Ukur Tinggi</label>
                            <select wire:model="alat_ukur_tb" {{ Auth::user()->isDokter() ? 'disabled' : '' }}>
                                <option value="Microtoise">Microtoise</option>
                                <option value="Infantometer">Infantometer</option>
                                <option value="Pita Meteran">Pita Meteran</option>
                            </select>
                        </div>
                        <div class="form-group" style="margin-bottom: 0; flex: 2; min-width: 150px;">
                            <label>Posisi Ukur</label>
                            <select wire:model="cara_ukur" {{ Auth::user()->isDokter() ? 'disabled' : '' }}>
                                <option value="berdiri">Berdiri (Tinggi Badan)</option>
                                <option value="telentang">Telentang (Panjang Badan)</option>
                            </select>
                        </div>
                        <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 120px;">
                            <label>Nilai TB/PB (cm)</label>
                            <input type="number" step="0.1" wire:model="tinggi_badan" required {{ Auth::user()->isDokter() ? 'disabled' : '' }} style="font-weight: bold; font-size: 1.1rem; text-align: center;">
                        </div>
                    </div>
                </div>

                <!-- Group 4: Opsional -->
                <div class="form-section" style="grid-column: 1 / -1;">
                    <h4 class="form-section-title"><span>🧩</span> Pengukuran Tambahan (Opsional)</h4>
                    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                        <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 150px;">
                            <label>Lingkar Kepala (cm)</label>
                            <input type="number" step="0.1" wire:model="lingkar_kepala" {{ Auth::user()->isDokter() ? 'disabled' : '' }}>
                        </div>
                        <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 150px;">
                            <label>Lingkar Lengan Atas / LiLA (cm)</label>
                            <input type="number" step="0.1" wire:model="lila" {{ Auth::user()->isDokter() ? 'disabled' : '' }}>
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
    <div class="card" style="background: var(--surface); box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1); border: 1px solid var(--border); padding: 1.5rem; border-radius: 0.5rem; margin-top: 2rem;">
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
            
            <div class="result-card">
                <h3 class="result-title">Berat / Umur (WAZ)</h3>
                <div style="display: flex; flex-direction: column; gap: 0.3rem;">
                    <span class="result-value">{{ $hasil->waz ?? 'N/A' }}</span>
                    <span class="badge {{ str_contains(strtolower($hasil->status_bb_u), 'kurang') ? 'badge-warning' : 'badge-normal' }}" style="align-self: flex-start; font-size: 0.7rem;">{{ $hasil->status_bb_u ?? '-' }}</span>
                </div>
            </div>

            <div class="result-card">
                <h3 class="result-title">Tinggi / Umur (HAZ)</h3>
                <div style="display: flex; flex-direction: column; gap: 0.3rem;">
                    <span class="result-value">{{ $hasil->haz ?? 'N/A' }}</span>
                    <span class="badge {{ str_contains(strtolower($hasil->status_tb_u), 'stunted') || str_contains(strtolower($hasil->status_tb_u), 'pendek') ? 'badge-danger' : 'badge-normal' }}" style="align-self: flex-start; font-size: 0.7rem;">{{ $hasil->status_tb_u ?? '-' }}</span>
                </div>
            </div>

            <div class="result-card">
                <h3 class="result-title">IMT / Umur (BMIZ)</h3>
                <div style="display: flex; flex-direction: column; gap: 0.3rem;">
                    <span class="result-value">{{ $hasil->bmiz ?? 'N/A' }}</span>
                    <span class="badge {{ str_contains(strtolower($hasil->status_imt_u), 'kurang') ? 'badge-warning' : (str_contains(strtolower($hasil->status_imt_u), 'lebih') || str_contains(strtolower($hasil->status_imt_u), 'obesitas') ? 'badge-danger' : 'badge-normal') }}" style="align-self: flex-start; font-size: 0.7rem;">{{ $hasil->status_imt_u ?? '-' }}</span>
                </div>
            </div>
            
            <div class="result-card">
                <h3 class="result-title">Berat / Tinggi (WHZ)</h3>
                <div style="display: flex; flex-direction: column; gap: 0.3rem;">
                    <span class="result-value">{{ $hasil->whz ?? 'N/A' }}</span>
                    <span class="badge {{ str_contains(strtolower($hasil->status_bb_tb), 'kurang') || str_contains(strtolower($hasil->status_bb_tb), 'buruk') ? 'badge-warning' : (str_contains(strtolower($hasil->status_bb_tb), 'lebih') || str_contains(strtolower($hasil->status_bb_tb), 'obesitas') ? 'badge-danger' : 'badge-normal') }}" style="align-self: flex-start; font-size: 0.7rem;">{{ $hasil->status_bb_tb ?? '-' }}</span>
                </div>
            </div>
            
            @if($hasil->hcfa !== null)
            <div class="result-card">
                <h3 class="result-title">LK / Umur (HCFA)</h3>
                <div style="display: flex; flex-direction: column; gap: 0.3rem;">
                    <span class="result-value">{{ $hasil->hcfa }}</span>
                    <span class="badge {{ str_contains(strtolower($hasil->status_lk_u), 'normal') ? 'badge-normal' : 'badge-danger' }}" style="align-self: flex-start; font-size: 0.7rem;">{{ $hasil->status_lk_u }}</span>
                </div>
            </div>
            @endif

            @if($hasil->status_lila !== null)
            <div class="result-card">
                <h3 class="result-title">Status LiLA</h3>
                <div style="display: flex; flex-direction: column; gap: 0.3rem;">
                    <span class="result-value">-</span>
                    <span class="badge {{ str_contains(strtolower($hasil->status_lila), 'buruk') ? 'badge-danger' : (str_contains(strtolower($hasil->status_lila), 'kurang') ? 'badge-warning' : 'badge-normal') }}" style="align-self: flex-start; font-size: 0.7rem;">{{ $hasil->status_lila }}</span>
                </div>
            </div>
            @endif
        </div>

        <!-- Notifikasi (Interpretasi & Red Flag) berdampingan -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
            @php
                $hasil->standar = 'WHO'; // Force WHO for main result
                $resumeWho = app(\App\Services\NutritionService::class)->getEquivalentAgeResume($hasil->pengukuran, $hasil);
                $resumeCdc = $hasil_cdc ? app(\App\Services\NutritionService::class)->getEquivalentAgeResume($hasil->pengukuran, clone $hasil_cdc) : null;
            @endphp
            
            @if($resumeWho)
                <div class="alert-info" style="background: rgba(41, 128, 185, 0.08); border-left: 4px solid #2980b9;">
                    <h4 class="alert-info-title" style="color: #2980b9;">
                        📊 Kesimpulan Rumus WHO 2006
                    </h4>
                    <div class="alert-info-text" style="font-size: 0.85rem; line-height: 1.6;">
                        <p style="margin: 0 0 0.5rem 0; font-weight: bold; font-size: 0.95rem;">
                            Status: 
                            @if($resumeWho['is_stunting'])
                                <span style="color: #e74c3c;">Stanting / Pendek</span>
                            @else
                                <span style="color: #2ecc71;">Non Stanting (Normal)</span>
                            @endif
                        </p>
                        <ul style="margin: 0; padding-left: 1.2rem; color: var(--text-secondary);">
                            <li>Angka BB ({{ $resumeWho['bb'] }} kg) - setara dengan anak umur {{ $resumeWho['wa'] ?? '...' }} bulan</li>
                            <li>Angka TB ({{ $resumeWho['tb'] }} cm) - setara dengan anak umur {{ $resumeWho['ha'] ?? '...' }} bulan</li>
                        </ul>
                    </div>
                </div>
            @endif

            @if($resumeCdc)
                <div class="alert-info" style="background: rgba(155, 89, 182, 0.08); border-left: 4px solid #9b59b6;">
                    <h4 class="alert-info-title" style="color: #9b59b6;">
                        📊 Kesimpulan Rumus CDC 2000
                    </h4>
                    <div class="alert-info-text" style="font-size: 0.85rem; line-height: 1.6;">
                        <p style="margin: 0 0 0.5rem 0; font-weight: bold; font-size: 0.95rem;">
                            Status: 
                            @if($resumeCdc['is_stunting'])
                                <span style="color: #e74c3c;">Short Stature / Underweight</span>
                            @else
                                <span style="color: #2ecc71;">Normal</span>
                            @endif
                        </p>
                        <ul style="margin: 0; padding-left: 1.2rem; color: var(--text-secondary);">
                            <li>Angka BB ({{ $resumeCdc['bb'] }} kg) - setara dengan anak umur {{ $resumeCdc['wa'] ?? '...' }} bulan</li>
                            <li>Angka TB ({{ $resumeCdc['tb'] }} cm) - setara dengan anak umur {{ $resumeCdc['ha'] ?? '...' }} bulan</li>
                        </ul>
                    </div>
                </div>
            @endif

            @if($hasil->narasi_interpretasi)
                <div class="alert-info">
                    <h4 class="alert-info-title">
                        🩺 Evaluasi Klinis Z-Score
                    </h4>
                    <div class="alert-info-text" style="font-size: 0.85rem; line-height: 1.6;">
                        {!! \Illuminate\Support\Str::markdown($hasil->narasi_interpretasi ?? '') !!}
                    </div>
                </div>
            @endif

            {{-- 
            @php
                $rdaText = app(\App\Services\NutritionService::class)->generateRDAText($hasil, $hasil->pengukuran);
            @endphp
            @if($rdaText)
                <div class="alert-success" style="background: rgba(16, 185, 129, 0.1); border-left: 4px solid #10b981; padding: 1rem; border-radius: 0 0.5rem 0.5rem 0;">
                    <h4 style="color: #10b981; display: flex; align-items: center; gap: 0.5rem; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.9rem;">
                        🍽️ Rekomendasi Gizi (RDA)
                    </h4>
                    <div class="alert-info-text" style="font-size: 0.85rem; line-height: 1.6; color: var(--text-main);">
                        {!! \Illuminate\Support\Str::markdown($rdaText) !!}
                    </div>
                </div>
            @endif
            --}}

            @if($hasil->red_flag)
                <div class="alert-danger">
                    <h4 class="alert-danger-title">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"></polygon><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                        Peringatan (Red Flag)
                    </h4>
                    <p class="alert-danger-text">{{ $hasil->catatan_red_flag }}</p>
                </div>
            @endif
        </div>
        
        <!-- Assessment & Plan (Langkah 2) -->
        @if($show_assessment_form && !Auth::user()->isOperator())
            <div class="assessment-box">
                <h4 style="color: var(--text-main); font-weight: 700; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; font-size: 1rem;">
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
                                <label style="display:block; margin-bottom: 0.5rem; color: var(--text-main); font-weight: 600;">📝 Assessment Klinis</label>
                                <textarea x-model="draftAssessment" style="background: rgba(255,255,255,0.05); border: 1px solid var(--border); color: var(--text-main); border-radius: 8px; padding: 12px; flex: 1; width: 100%; min-height: 120px; resize: vertical; font-family: inherit; font-size: 0.95rem; box-shadow: inset 0 1px 2px 0 rgba(0,0,0,0.05);"></textarea>
                            </div>
                            <!-- Kolom Template Cek Box -->
                            <div class="template-box">
                                <span class="template-box-title">Template Assessment:</span>
                                <div style="display: flex; flex-direction: column; gap: 0.6rem;">
                                    @foreach($templates as $template)
                                        @if($template->template_assessment)
                                        <label class="template-item">
                                            <input type="checkbox" @change="toggleText('assessment', '{{ addslashes($template->template_assessment) }}', $event.target.checked)">
                                            <span>{{ $template->nama_template }}</span>
                                        </label>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        
                        <hr style="border-top: 1px dashed var(--border); margin: 0;">

                        <!-- Blok Plan -->
                        <div style="display: grid; grid-template-columns: 1fr auto; gap: 1.5rem; align-items: stretch;">
                            <!-- Kolom Textarea (Caption) -->
                            <div class="form-group" style="margin-bottom: 0; display: flex; flex-direction: column;">
                                <label style="display:block; margin-bottom: 0.5rem; color: var(--text-main); font-weight: 600;">🎯 Plan / Rencana Tindak Lanjut</label>
                                <textarea x-model="draftPlan" style="background: rgba(255,255,255,0.05); border: 1px solid var(--border); color: var(--text-main); border-radius: 8px; padding: 12px; flex: 1; width: 100%; min-height: 120px; resize: vertical; font-family: inherit; font-size: 0.95rem; box-shadow: inset 0 1px 2px 0 rgba(0,0,0,0.05);"></textarea>
                            </div>
                            <!-- Kolom Template Cek Box -->
                            <div class="template-box">
                                <span class="template-box-title">Template Plan:</span>
                                <div style="display: flex; flex-direction: column; gap: 0.6rem;">
                                    @foreach($templates as $template)
                                        @if($template->template_plan)
                                        <label class="template-item">
                                            <input type="checkbox" @change="toggleText('plan', '{{ addslashes($template->template_plan) }}', $event.target.checked)">
                                            <span>{{ $template->nama_template }}</span>
                                        </label>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--border); padding-top: 1rem; flex-wrap: wrap; gap: 1rem;">
                        <span style="font-size: 0.75rem; color: var(--text-muted); flex: 1; min-width: 200px;">
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
