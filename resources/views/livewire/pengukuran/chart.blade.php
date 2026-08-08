<style>
    @media print {
        @page { size: landscape; margin: 10mm; }
        body, html { width: 100% !important; height: auto !important; background: white !important; color: black !important; font-size: 8pt; overflow: visible !important; line-height: 1.3 !important; }
        .no-print, header, nav, aside, footer, .sidebar, .table-wrapper a, .table-wrapper button { display: none !important; }
        .main-content, .content-wrapper, main { margin: 0 !important; padding: 0 !important; width: 100% !important; max-width: 100% !important; overflow: visible !important; }
        
        /* Matikan CSS Grid/Flex yang bikin terpotong di Chrome */
        div[style*="display: grid"], div[style*="display: flex"] { display: block !important; width: 100% !important; }
        
        .glass-panel { 
            box-shadow: none !important; border: 1px solid #ddd !important; 
            break-inside: avoid; page-break-inside: avoid; 
            margin-bottom: 10px !important; padding: 10px !important; width: 100% !important; 
            box-sizing: border-box !important; display: block !important;
        }
        
        /* 2 chart berdampingan khusus cetak */
        .glass-panel.chart-card {
            width: 48% !important;
            display: inline-block !important;
            vertical-align: top;
            margin-right: 1%;
            margin-bottom: 10px !important;
        }

        .chart-container { page-break-inside: avoid; break-inside: avoid; height: 220px !important; width: 100% !important; }
        h2 { font-size: 12pt !important; color: black !important; margin-bottom: 5px !important; }
        h3 { font-size: 9pt !important; color: black !important; border-bottom: 1px solid #ccc !important; padding-bottom: 2px !important; margin-bottom: 5px !important; }
        
        /* Tabel agar tidak terpotong */
        .table-wrapper { overflow: visible !important; width: 100% !important; }
        table { width: 100% !important; border-collapse: collapse !important; table-layout: auto !important; }
        tr { page-break-inside: avoid; break-inside: avoid; }
        th, td { border: 1px solid #ccc !important; padding: 3px 2px !important; font-size: 6.5pt !important; white-space: normal !important; word-wrap: break-word !important; }
        
        /* Paksa element block setelah chart */
        .table-panel { width: 100% !important; display: block !important; clear: both !important; margin-top: 10px !important; }
    }
</style>
<div class="animate-fade-in">
    <div class="glass-panel" style="padding: 1.5rem; display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <div style="background: linear-gradient(135deg, #9b59b6, #8e44ad); width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem;">📈</div>
            <div>
                <h2 style="font-size: 1.4rem; color: var(--text-main); font-weight: 700;">Grafik Pertumbuhan</h2>
                <div style="color: var(--text-muted); font-size: 0.85rem; margin-top: 0.2rem;">Riwayat & Tren Antropometri: <strong>{{ $anak->nama }}</strong></div>
            </div>
        </div>
        <div style="display: flex; gap: 0.5rem;" class="no-print">
            <button onclick="window.print()" style="padding: 8px 16px; border-radius: 8px; background: #ef4444; color: white; border: none; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 0.5rem; transition: all 0.2s;">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                Cetak PDF
            </button>
            <a href="{{ route('anak.index') }}" style="padding: 8px 16px; border-radius: 8px; background: rgba(149,165,166,0.15); color: var(--text-muted); text-decoration: none; font-weight: 600; border: 1px solid rgba(149,165,166,0.3); transition: all 0.2s; display: flex; align-items: center;">
                Kembali
            </a>
        </div>
    </div>

    @php
        $latestHasil = $anak->pengukurans->last()?->hasilStatusGizi;
    @endphp

    @if($latestHasil && $latestHasil->narasi_interpretasi)
        <div class="alert-info animate-fade-in" style="margin-bottom: 2rem;">
            <h3 class="alert-info-title">
                💡 Kesimpulan & Evaluasi Klinis Terkini
            </h3>
            <p class="alert-info-text">{!! str_replace("\n", '<br>', e($latestHasil->narasi_interpretasi)) !!}</p>
        </div>
    @endif

    @if(count($labels) > 0)
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(450px, 1fr)); gap: 2rem; margin-bottom: 2rem;">
            <!-- Chart 1: IMT/U & BB/U -->
            <div class="glass-panel chart-card" style="padding: 1.5rem;">
                <h3 style="color: var(--text-main); font-size: 1.1rem; margin-bottom: 1rem; border-bottom: 1px solid rgba(52,152,219,0.2); padding-bottom: 0.8rem;">
                    Tren Status Gizi Berat Badan (IMT/U, BB/U, WHZ)
                </h3>
                <div class="chart-container" style="height: 350px; position: relative;">
                    <canvas id="chartBB"></canvas>
                </div>
            </div>

            <!-- Chart 2: TB/U & LK/U -->
            <div class="glass-panel chart-card" style="padding: 1.5rem;">
                <h3 style="color: var(--text-main); font-size: 1.1rem; margin-bottom: 1rem; border-bottom: 1px solid rgba(52,152,219,0.2); padding-bottom: 0.8rem;">
                    Tren Pertumbuhan Linier (TB/U & LK/U)
                </h3>
                <div class="chart-container" style="height: 350px; position: relative;">
                    <canvas id="chartTB"></canvas>
                </div>
            </div>
            
            <!-- Chart 3: LiLA -->
            <div class="glass-panel chart-card" style="padding: 1.5rem;">
                <h3 style="color: var(--text-main); font-size: 1.1rem; margin-bottom: 1rem; border-bottom: 1px solid rgba(52,152,219,0.2); padding-bottom: 0.8rem;">
                    Tren Lingkar Lengan Atas (LiLA)
                </h3>
                <div class="chart-container" style="height: 350px; position: relative;">
                    <canvas id="chartLila"></canvas>
                </div>
            </div>

            <!-- Chart 4: Aktual BB & TB -->
            <div class="glass-panel chart-card" style="padding: 1.5rem;">
                <h3 style="color: var(--text-main); font-size: 1.1rem; margin-bottom: 1rem; border-bottom: 1px solid rgba(52,152,219,0.2); padding-bottom: 0.8rem;">
                    Kurva Kecepatan Tumbuh (BB & TB Aktual)
                </h3>
                <div class="chart-container" style="height: 350px; position: relative;">
                    <canvas id="chartAktual"></canvas>
                </div>
            </div>

            <!-- Chart 5: IMT Aktual -->
            <div class="glass-panel chart-card" style="padding: 1.5rem;">
                <h3 style="color: var(--text-main); font-size: 1.1rem; margin-bottom: 1rem; border-bottom: 1px solid rgba(52,152,219,0.2); padding-bottom: 0.8rem;">
                    Indeks Massa Tubuh Aktual (IMT / BMI)
                </h3>
                <div class="chart-container" style="height: 350px; position: relative;">
                    <canvas id="chartIMT"></canvas>
                </div>
            </div>

            <!-- Chart 6: LK Aktual -->
            <div class="glass-panel chart-card" style="padding: 1.5rem;">
                <h3 style="color: var(--text-main); font-size: 1.1rem; margin-bottom: 1rem; border-bottom: 1px solid rgba(52,152,219,0.2); padding-bottom: 0.8rem;">
                    Tren Lingkar Kepala Aktual (cm)
                </h3>
                <div class="chart-container" style="height: 350px; position: relative;">
                    <canvas id="chartLK"></canvas>
                </div>
            </div>
        </div>
    @else
        <div class="glass-panel" style="padding: 3rem; text-align: center; margin-bottom: 2rem;">
            <div style="font-size: 2.5rem; margin-bottom: 1rem; opacity: 0.5;">📊</div>
            <p style="color: var(--text-muted); font-size: 1.1rem;">Belum ada data pengukuran untuk ditampilkan di grafik.</p>
        </div>
    @endif

    <script>
        function renderKidCharts() {
            const commonOptions = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { color: '#9ca3af', usePointStyle: true, boxWidth: 8 } },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgba(31, 41, 55, 0.9)',
                        titleColor: '#fff',
                        bodyColor: '#cbd5e1',
                        borderColor: 'rgba(255,255,255,0.1)',
                        borderWidth: 1
                    }
                },
                scales: {
                    x: {
                        grid: { color: 'rgba(255,255,255,0.05)' },
                        ticks: { color: '#9ca3af' }
                    },
                    y: {
                        min: -6,
                        max: 6,
                        title: { display: true, text: 'Z-Score (SD)', color: '#9ca3af' },
                        grid: {
                            color: (context) => context.tick && context.tick.value === 0 ? 'rgba(52,152,219,0.3)' : 'rgba(255,255,255,0.05)',
                            lineWidth: (context) => context.tick && context.tick.value === 0 ? 2 : 1
                        },
                        ticks: { color: '#9ca3af' }
                    }
                }
            };

            const lilaOptions = { ...commonOptions, scales: { x: commonOptions.scales.x, y: { min: 8, max: 20, title: { display: true, text: 'LiLA (cm)', color: '#9ca3af' }, grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#9ca3af' } } } };
            const lkOptions = { ...commonOptions, scales: { x: commonOptions.scales.x, y: { min: 30, max: 60, title: { display: true, text: 'Lingkar Kepala (cm)', color: '#9ca3af' }, grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#9ca3af' } } } };
            const imtOptions = { ...commonOptions, scales: { x: commonOptions.scales.x, y: { min: 10, max: 25, title: { display: true, text: 'IMT (kg/m²)', color: '#9ca3af' }, grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#9ca3af' } } } };
            
            const aktualOptions = {
                responsive: true, maintainAspectRatio: false, plugins: commonOptions.plugins,
                scales: {
                    x: commonOptions.scales.x,
                    y: { type: 'linear', display: true, position: 'left', title: { display: true, text: 'Berat Badan (kg)', color: '#10B981' }, grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#10B981' } },
                    y1: { type: 'linear', display: true, position: 'right', title: { display: true, text: 'Tinggi Badan (cm)', color: '#4F46E5' }, grid: { drawOnChartArea: false }, ticks: { color: '#4F46E5' } }
                }
            };

            const labels = {!! json_encode($labels) !!};
            window.appCharts = window.appCharts || {};

            function initChart(id, config) {
                const ctx = document.getElementById(id);
                if(ctx) {
                    if(window.appCharts[id]) window.appCharts[id].destroy();
                    window.appCharts[id] = new Chart(ctx, config);
                }
            }

            // Chart 1: BB
            initChart('chartBB', {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        { label: 'IMT/U (BMIZ)', data: {!! json_encode($bmizData) !!}, borderColor: '#8b5cf6', backgroundColor: 'rgba(139, 92, 246, 0.1)', borderWidth: 3, fill: true, tension: 0.4, pointBackgroundColor: '#8b5cf6', pointRadius: 4 },
                        { label: 'BB/U (WAZ)', data: {!! json_encode($wazData) !!}, borderColor: '#10B981', backgroundColor: 'transparent', borderWidth: 3, borderDash: [5, 5], tension: 0.4, pointBackgroundColor: '#10B981', pointRadius: 4 },
                        { label: 'BB/TB (WHZ)', data: {!! json_encode($whzData) !!}, borderColor: '#f59e0b', backgroundColor: 'transparent', borderWidth: 3, borderDash: [2, 2], tension: 0.4, pointBackgroundColor: '#f59e0b', pointRadius: 4 },
                        { label: 'Batas Obesitas (+3 SD)', data: Array(labels.length).fill(3), borderColor: 'rgba(239, 68, 68, 0.6)', borderWidth: 2, borderDash: [10, 5], pointRadius: 0, fill: false },
                        { label: 'Batas Gizi Lebih (+2 SD)', data: Array(labels.length).fill(2), borderColor: 'rgba(245, 158, 11, 0.6)', borderWidth: 2, borderDash: [10, 5], pointRadius: 0, fill: false },
                        { label: 'Batas Bawah Normal (-2 SD)', data: Array(labels.length).fill(-2), borderColor: 'rgba(245, 158, 11, 0.6)', borderWidth: 2, borderDash: [10, 5], pointRadius: 0, fill: false },
                        { label: 'Batas Gizi Buruk (-3 SD)', data: Array(labels.length).fill(-3), borderColor: 'rgba(239, 68, 68, 0.6)', borderWidth: 2, borderDash: [10, 5], pointRadius: 0, fill: false }
                    ]
                },
                options: commonOptions
            });

            // Chart 2: TB
            initChart('chartTB', {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        { label: 'TB/U (HAZ)', data: {!! json_encode($hazData) !!}, borderColor: '#4F46E5', backgroundColor: 'rgba(79, 70, 229, 0.1)', borderWidth: 3, fill: true, tension: 0.4, pointBackgroundColor: '#4F46E5', pointRadius: 4 },
                        { label: 'LK/U (HCFA)', data: {!! json_encode($hcfaData) !!}, borderColor: '#0ea5e9', backgroundColor: 'transparent', borderWidth: 3, borderDash: [5, 5], tension: 0.4, pointBackgroundColor: '#0ea5e9', pointRadius: 4 },
                        { label: 'Batas Bawah Normal (-2 SD)', data: Array(labels.length).fill(-2), borderColor: 'rgba(245, 158, 11, 0.6)', borderWidth: 2, borderDash: [10, 5], pointRadius: 0, fill: false },
                        { label: 'Batas Sangat Pendek (-3 SD)', data: Array(labels.length).fill(-3), borderColor: 'rgba(239, 68, 68, 0.6)', borderWidth: 2, borderDash: [10, 5], pointRadius: 0, fill: false }
                    ]
                },
                options: commonOptions
            });

            // Chart 3: LiLA
            initChart('chartLila', {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        { label: 'LiLA Terukur (cm)', data: {!! json_encode($lilaData) !!}, borderColor: '#f43f5e', backgroundColor: 'rgba(244, 63, 94, 0.1)', borderWidth: 3, fill: true, tension: 0.4, pointBackgroundColor: '#f43f5e', pointRadius: 4 },
                        { label: 'Batas Gizi Kurang (12.5 cm)', data: Array(labels.length).fill(12.5), borderColor: 'rgba(245, 158, 11, 0.6)', borderWidth: 2, borderDash: [10, 5], pointRadius: 0, fill: false },
                        { label: 'Batas Gizi Buruk (11.5 cm)', data: Array(labels.length).fill(11.5), borderColor: 'rgba(239, 68, 68, 0.6)', borderWidth: 2, borderDash: [10, 5], pointRadius: 0, fill: false }
                    ]
                },
                options: lilaOptions
            });

            // Chart 4: Aktual BB & TB
            initChart('chartAktual', {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        { label: 'Berat Badan Aktual (kg)', data: {!! json_encode($beratAktualData) !!}, borderColor: '#10B981', backgroundColor: 'transparent', borderWidth: 3, yAxisID: 'y', fill: false, tension: 0.4, pointBackgroundColor: '#10B981', pointRadius: 4 },
                        { label: 'Tinggi Badan Aktual (cm)', data: {!! json_encode($tinggiAktualData) !!}, borderColor: '#4F46E5', backgroundColor: 'transparent', borderWidth: 3, yAxisID: 'y1', fill: false, tension: 0.4, pointBackgroundColor: '#4F46E5', pointRadius: 4 }
                    ]
                },
                options: aktualOptions
            });

            // Chart 5: IMT Aktual
            initChart('chartIMT', {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{ label: 'IMT / BMI (kg/m²)', data: {!! json_encode($imtAktualData) !!}, borderColor: '#f59e0b', backgroundColor: 'rgba(245, 158, 11, 0.1)', borderWidth: 3, fill: true, tension: 0.4, pointBackgroundColor: '#f59e0b', pointRadius: 4 }]
                },
                options: imtOptions
            });

            // Chart 6: LK Aktual
            initChart('chartLK', {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{ label: 'Lingkar Kepala Terukur (cm)', data: {!! json_encode($lkAktualData) !!}, borderColor: '#06b6d4', backgroundColor: 'rgba(6, 182, 212, 0.1)', borderWidth: 3, fill: true, tension: 0.4, pointBackgroundColor: '#06b6d4', pointRadius: 4 }]
                },
                options: lkOptions
            });
        }

        setTimeout(renderKidCharts, 100);
        document.addEventListener('livewire:navigated', renderKidCharts);
    </script>

    <!-- Tabel Riwayat Pengukuran -->
    <div class="glass-panel table-panel" style="padding: 1.5rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h3 style="color: var(--text-main); font-size: 1.1rem; border-left: 4px solid #1abc9c; padding-left: 0.8rem;">
                Riwayat Seluruh Pengukuran & Indikator Medis
            </h3>
        </div>
        
        <div class="table-wrapper" style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.82rem;">
                <thead>
                    <!-- Baris 1: Group header -->
                    <tr style="border-bottom: 1px solid rgba(52,152,219,0.15);">
                        <th colspan="2" style="padding: 0.5rem 0.5rem 0.3rem; color: #3498db; font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; background: transparent;">
                            🗓 Waktu Pengukuran
                        </th>
                        <th colspan="14" style="padding: 0.5rem 0.5rem 0.3rem; color: #1abc9c; font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; border-left: 2px solid rgba(26,188,156,0.3); background: transparent;">
                            📏 Hasil Pengukuran (Z-Score)
                        </th>
                    </tr>
                    <!-- Baris 2: Column headers -->
                    <tr style="border-bottom: 2px solid rgba(52,152,219,0.3); background: rgba(0,0,0,0.05);">
                        @foreach(['Tgl Ukur', 'Usia'] as $h)
                            <th style="padding: 0.6rem 0.5rem; color: var(--text-muted); font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.03em; white-space: nowrap; background: transparent;">{{ $h }}</th>
                        @endforeach
                        
                        @foreach(['BB (kg)', 'TB (cm)', 'LK (cm)', 'LiLA', 'WAZ (BB/U)', 'HAZ (TB/U)', 'WHZ (BB/TB)', 'BMIZ (IMT/U)', 'HCFA (LK/U)', 'Status Pertumbuhan (TB)', 'Status Gizi Tambahan', 'Petugas', 'Aksi'] as $h)
                            <th style="padding: 0.6rem 0.5rem; color: #1abc9c; font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.03em; white-space: nowrap; border-left: {{ $h === 'BB (kg)' ? '2px solid rgba(26,188,156,0.3)' : 'none' }}; text-align: center; background: transparent;">{{ $h }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @php
                        if (!function_exists('getChartZColor')) {
                            function getChartZColor($z) {
                                if ($z === null || $z === '') return ['bg' => 'transparent', 'color' => 'var(--text-muted)'];
                                if ($z <= -3) return ['bg' => 'rgba(192,57,43,0.25)', 'color' => '#e74c3c'];
                                if ($z <= -2) return ['bg' => 'rgba(243,156,18,0.2)', 'color' => '#f39c12'];
                                if ($z >= 3) return ['bg' => 'rgba(192,57,43,0.25)', 'color' => '#e74c3c'];
                                if ($z >= 2) return ['bg' => 'rgba(52,152,219,0.2)', 'color' => '#3498db'];
                                return ['bg' => 'rgba(39,174,96,0.15)', 'color' => '#2ecc71'];
                            }
                        }
                        
                        if (!function_exists('getChartStatusStyle')) {
                            function getChartStatusStyle($s) {
                                if (!$s) return ['bg' => 'rgba(149,165,166,0.2)', 'color' => '#95a5a6'];
                                $lower = strtolower($s);
                                if (str_contains($lower, 'severely') || str_contains($lower, 'sangat') || str_contains($lower, 'obesitas')) return ['bg' => 'rgba(192,57,43,0.25)', 'color' => '#e74c3c'];
                                if (str_contains($lower, 'stunted') || str_contains($lower, 'wasted') || str_contains($lower, 'underweight') || str_contains($lower, 'kurang') || str_contains($lower, 'pendek')) return ['bg' => 'rgba(243,156,18,0.2)', 'color' => '#f39c12'];
                                if (str_contains($lower, 'normal')) return ['bg' => 'rgba(39,174,96,0.15)', 'color' => '#2ecc71'];
                                return ['bg' => 'rgba(52,152,219,0.15)', 'color' => '#3498db'];
                            }
                        }
                    @endphp
                    @forelse($anak->pengukurans->sortByDesc('tanggal_ukur') as $row)
                        @php
                            $hasil = $row->hasilStatusGizi;
                        @endphp
                        
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.05); transition: background .15s;" 
                            onmouseenter="this.style.background='rgba(52,152,219,0.06)'" 
                            onmouseleave="this.style.background='transparent'">
                            
                            <td style="padding: 0.6rem 0.5rem; font-family: monospace; font-size: 0.75rem; color: var(--text-secondary);">{{ $row->tanggal_ukur }}</td>
                            <td style="padding: 0.6rem 0.5rem;">{{ $row->usia_bulan }} bln</td>
                            
                            <!-- Pengukuran Fisik -->
                            <td style="padding: 0.6rem 0.5rem; text-align: center; border-left: 2px solid rgba(26,188,156,0.3);">{{ $row->berat_badan }}</td>
                            <td style="padding: 0.6rem 0.5rem; text-align: center;">{{ $row->tinggi_badan }}</td>
                            <td style="padding: 0.6rem 0.5rem; text-align: center;">{{ $row->lingkar_kepala ?? '—' }}</td>
                            <td style="padding: 0.6rem 0.5rem; text-align: center;">{{ $row->lila ?? '—' }}</td>
                            
                            <!-- Z-Scores -->
                            @foreach([$hasil->waz ?? null, $hasil->haz ?? null, $hasil->whz ?? null, $hasil->bmiz ?? null, $hasil->hcfa ?? null] as $z)
                                @php $zc = getChartZColor($z); @endphp
                                <td style="padding: 0.6rem 0.5rem; text-align: center;">
                                    <span style="padding: 1px 5px; border-radius: 4px; font-weight: 700; font-family: monospace; font-size: 0.75rem; background: {{ $zc['bg'] }}; color: {{ $zc['color'] }};">
                                        {{ $z !== null ? number_format((float)$z, 2) : '—' }}
                                    </span>
                                </td>
                            @endforeach
                            
                            <!-- Status Pertumbuhan -->
                            <td style="padding: 0.6rem 0.5rem; text-align: center;">
                                @if($hasil)
                                    <span style="padding: 2px 7px; border-radius: 20px; font-size: 0.65rem; font-weight: 700; background: {{ getChartStatusStyle($hasil->status_tb_u)['bg'] }}; color: {{ getChartStatusStyle($hasil->status_tb_u)['color'] }}; white-space: nowrap;">
                                        {{ $hasil->status_tb_u ?? 'Belum Kalkulasi' }}
                                        @if($hasil->red_flag) ⚠ @endif
                                    </span>
                                @else
                                    <span style="padding: 2px 7px; border-radius: 20px; font-size: 0.65rem; font-weight: 700; background: rgba(149,165,166,0.2); color: #95a5a6; white-space: nowrap;">
                                        Belum Diukur
                                    </span>
                                @endif
                            </td>
                            
                            <!-- Status Gizi Tambahan -->
                            <td style="padding: 0.6rem 0.5rem; text-align: left;">
                                @if($hasil)
                                    <div style="display: flex; flex-direction: column; gap: 0.3rem;">
                                        <div style="font-size: 0.62rem; line-height: 1.1; color: var(--text-secondary); display: flex; justify-content: space-between; gap: 0.5rem;">
                                            <span>BB/TB:</span>
                                            <strong style="color: {{ getChartStatusStyle($hasil->status_bb_tb)['color'] }}; text-align: right;">{{ $hasil->status_bb_tb ?? '—' }}</strong>
                                        </div>
                                        <div style="font-size: 0.62rem; line-height: 1.1; color: var(--text-secondary); display: flex; justify-content: space-between; gap: 0.5rem;">
                                            <span>BB/U:</span>
                                            <strong style="color: {{ getChartStatusStyle($hasil->status_bb_u)['color'] }}; text-align: right;">{{ $hasil->status_bb_u ?? '—' }}</strong>
                                        </div>
                                        <div style="font-size: 0.62rem; line-height: 1.1; color: var(--text-secondary); display: flex; justify-content: space-between; gap: 0.5rem;">
                                            <span>LK/U:</span>
                                            <strong style="color: {{ getChartStatusStyle($hasil->status_lk_u)['color'] }}; text-align: right;">{{ $hasil->status_lk_u ?? '—' }}</strong>
                                        </div>
                                    </div>
                                @else
                                    <span style="color: var(--text-muted); text-align: center; display: block;">—</span>
                                @endif
                            </td>
                            
                            <td style="padding: 0.6rem 0.5rem; text-align: center;">
                                <span style="font-size: 0.65rem; color: var(--text-muted); display: block;">BB: {{ $row->alat_ukur_bb ?? '-' }}</span>
                                <span style="font-size: 0.65rem; color: var(--text-muted); display: block;">TB: {{ $row->alat_ukur_tb ?? '-' }}</span>
                                <span style="font-size: 0.65rem; color: #8e44ad; font-weight: 600; display: block; margin-top: 2px;">Oleh: Petugas #{{ $row->petugas_id ?? '-' }}</span>
                                @if(count($row->redFlagLogs) > 0)
                                    <div style="margin-top: 4px; display: flex; flex-direction: column; gap: 2px;">
                                        @php
                                            $merah = $row->redFlagLogs->where('severity', 'merah')->count();
                                            $kuning = $row->redFlagLogs->where('severity', 'kuning')->count();
                                        @endphp
                                        @if($merah > 0)
                                            <span style="padding: 2px 4px; background: rgba(231,76,60,0.1); border: 1px solid #e74c3c; border-radius: 4px; color: #c0392b; font-size: 0.6rem; font-weight: 600;" title="{{ $row->redFlagLogs->where('severity', 'merah')->pluck('kategori_flag')->implode(', ') }}">🚨 {{ $merah }} Merah</span>
                                        @endif
                                        @if($kuning > 0)
                                            <span style="padding: 2px 4px; background: rgba(241,196,15,0.1); border: 1px solid #f1c40f; border-radius: 4px; color: #d35400; font-size: 0.6rem; font-weight: 600;" title="{{ $row->redFlagLogs->where('severity', 'kuning')->pluck('kategori_flag')->implode(', ') }}">⚠️ {{ $kuning }} Kuning</span>
                                        @endif
                                    </div>
                                @endif
                            </td>
                            
                            <td style="padding: 0.6rem 0.5rem; text-align: center; white-space: nowrap;">
                                <a href="{{ route('pengukuran.edit', $row->id) }}" title="Edit" style="color: #3498db; margin-right: 0.5rem; text-decoration: none;">✏️</a>
                                <button type="button" wire:click="deletePengukuran({{ $row->id }})" wire:confirm="Yakin ingin menghapus data pengukuran ini?" title="Hapus" style="color: #e74c3c; background: none; border: none; cursor: pointer; padding: 0;">🗑️</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" style="text-align: center; color: var(--text-muted); padding: 3rem;">Belum ada data riwayat pengukuran.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
