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
    <div class="page-header" style="align-items: center;">
        <div>
            <h1 class="page-title">
                <span class="page-title-icon">📈</span> 
                Grafik Pertumbuhan
            </h1>
            <p class="page-subtitle">Riwayat & Tren Antropometri: <strong>{{ $anak->nama }}</strong></p>
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

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
        {{-- 
        @if($latestHasil && $latestHasil->narasi_interpretasi)
            <div class="alert-info animate-fade-in">
                <h3 class="alert-info-title" style="font-size: 0.95rem; margin-bottom: 0.5rem;">
                    💡 Kesimpulan & Evaluasi Klinis Terkini
                </h3>
                <div class="alert-info-text" style="font-size: 0.85rem; line-height: 1.6;">
                    {!! \Illuminate\Support\Str::markdown($latestHasil->narasi_interpretasi) !!}
                </div>
            </div>
        @endif

        @php
            $rdaText = null;
            if ($latestHasil && $latestHasil->pengukuran) {
                $rdaText = app(\App\Services\NutritionService::class)->generateRDAText($latestHasil, $latestHasil->pengukuran);
            }
        @endphp
        
        @if($rdaText)
            <div class="alert-success animate-fade-in" style="background: rgba(16, 185, 129, 0.1); border-left: 4px solid #10b981; padding: 1rem; border-radius: 0 0.5rem 0.5rem 0;">
                <h3 style="color: #10b981; display: flex; align-items: center; gap: 0.5rem; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.95rem;">
                    🍽️ Rekomendasi Gizi Terkini (RDA)
                </h3>
                <div class="alert-info-text" style="font-size: 0.85rem; line-height: 1.6; color: var(--text-main);">
                    {!! \Illuminate\Support\Str::markdown($rdaText) !!}
                </div>
            </div>
        @endif
        --}}
        
        @if($latestHasil && $latestHasil->red_flag)
            <div class="alert-danger animate-fade-in" style="background: rgba(239, 68, 68, 0.1); border-left: 4px solid var(--danger); padding: 1rem; border-radius: 0 0.5rem 0.5rem 0;">
                <h3 style="color: var(--danger); display: flex; align-items: center; gap: 0.5rem; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.95rem;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"></polygon><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                    Peringatan Medis (Red Flag)
                </h3>
                <p class="alert-info-text" style="font-size: 0.85rem; line-height: 1.6; color: var(--danger);">
                    {{ $latestHasil->catatan_red_flag }}
                </p>
            </div>
        @endif
    </div>

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

            <!-- Chart 4: Overlay Berat Badan -->
            <div class="glass-panel chart-card" style="padding: 1.5rem; grid-column: 1 / -1;">
                <h3 style="color: var(--text-main); font-size: 1.1rem; margin-bottom: 1rem; border-bottom: 1px solid rgba(52,152,219,0.2); padding-bottom: 0.8rem;">
                    Overlay Berat Badan (Anak vs WHO/CDC)
                </h3>
                <div class="chart-container" style="height: 400px; position: relative;">
                    <canvas id="chartOverlayBB"></canvas>
                </div>
            </div>

            <!-- Chart 5: Overlay Tinggi Badan -->
            <div class="glass-panel chart-card" style="padding: 1.5rem; grid-column: 1 / -1;">
                <h3 style="color: var(--text-main); font-size: 1.1rem; margin-bottom: 1rem; border-bottom: 1px solid rgba(52,152,219,0.2); padding-bottom: 0.8rem;">
                    Overlay Tinggi Badan (Anak vs WHO/CDC)
                </h3>
                <div class="chart-container" style="height: 400px; position: relative;">
                    <canvas id="chartOverlayTB"></canvas>
                </div>
            </div>

            <!-- Chart 6: Overlay Lingkar Kepala -->
            <div class="glass-panel chart-card" style="padding: 1.5rem; grid-column: 1 / -1;">
                <h3 style="color: var(--text-main); font-size: 1.1rem; margin-bottom: 1rem; border-bottom: 1px solid rgba(52,152,219,0.2); padding-bottom: 0.8rem;">
                    Overlay Lingkar Kepala (Anak vs WHO)
                </h3>
                <div class="chart-container" style="height: 400px; position: relative;">
                    <canvas id="chartOverlayLK"></canvas>
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

            // Overlay Options
            const overlayOptions = (yLabel) => ({
                responsive: true,
                maintainAspectRatio: false,
                plugins: commonOptions.plugins,
                scales: {
                    x: { type: 'linear', title: { display: true, text: 'Usia (Bulan)', color: '#9ca3af' }, grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#9ca3af' } },
                    y: { title: { display: true, text: yLabel, color: '#9ca3af' }, grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#9ca3af' } }
                }
            });

            const labelsAxis = {!! json_encode($labelsAxis) !!};
            const whoData = {!! json_encode($whoData) !!};
            const cdcData = {!! json_encode($cdcData) !!};
            const anakCoords = {!! json_encode($anakCoords) !!};

            // Chart 4: Overlay BB
            initChart('chartOverlayBB', {
                type: 'line',
                data: {
                    labels: labelsAxis,
                    datasets: [
                        { label: 'Anak (BB Aktual)', data: anakCoords.bb, borderColor: '#ef4444', backgroundColor: '#ef4444', borderWidth: 3, pointRadius: 6, pointHoverRadius: 8, fill: false, tension: 0.2 },
                        { label: 'WHO Median (BB)', data: whoData.waz, borderColor: '#2980b9', backgroundColor: 'transparent', borderWidth: 2, pointRadius: 0, fill: false, tension: 0.4 },
                        { label: 'CDC Median (BB)', data: cdcData.waz, borderColor: '#9b59b6', backgroundColor: 'transparent', borderWidth: 2, pointRadius: 0, fill: false, tension: 0.4 }
                    ]
                },
                options: overlayOptions('Berat Badan (kg)')
            });

            // Chart 5: Overlay TB
            initChart('chartOverlayTB', {
                type: 'line',
                data: {
                    labels: labelsAxis,
                    datasets: [
                        { label: 'Anak (TB Aktual)', data: anakCoords.tb, borderColor: '#ef4444', backgroundColor: '#ef4444', borderWidth: 3, pointRadius: 6, pointHoverRadius: 8, fill: false, tension: 0.2 },
                        { label: 'WHO Median (TB)', data: whoData.haz, borderColor: '#2980b9', backgroundColor: 'transparent', borderWidth: 2, pointRadius: 0, fill: false, tension: 0.4 },
                        { label: 'CDC Median (TB)', data: cdcData.haz, borderColor: '#9b59b6', backgroundColor: 'transparent', borderWidth: 2, pointRadius: 0, fill: false, tension: 0.4 }
                    ]
                },
                options: overlayOptions('Tinggi Badan (cm)')
            });

            // Chart 6: Overlay LK
            initChart('chartOverlayLK', {
                type: 'line',
                data: {
                    labels: labelsAxis,
                    datasets: [
                        { label: 'Anak (LK Aktual)', data: anakCoords.lk, borderColor: '#ef4444', backgroundColor: '#ef4444', borderWidth: 3, pointRadius: 6, pointHoverRadius: 8, fill: false, tension: 0.2 },
                        { label: 'WHO Median (LK)', data: whoData.hcfa, borderColor: '#2980b9', backgroundColor: 'transparent', borderWidth: 2, pointRadius: 0, fill: false, tension: 0.4 }
                    ]
                },
                options: overlayOptions('Lingkar Kepala (cm)')
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
                                <!-- Native Dialog Modal -->
                                <button onclick="document.getElementById('modal-{{ $row->id }}').showModal()" title="Lihat Rekomendasi Gizi & Klinis" style="color: #10b981; background: rgba(16,185,129,0.15); border: 1px solid #10b981; border-radius: 5px; cursor: pointer; padding: 4px 8px; margin-right: 0.5rem; font-size: 0.72rem; font-weight: 600;">💡 Hasil</button>
                                
                                <dialog id="modal-{{ $row->id }}" style="padding: 2rem; max-width: 700px; width: 90%; max-height: 85vh; border-radius: 12px; border: none; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5); background: var(--surface); position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); margin: 0; overflow-y: auto;">
                                    <div style="position: relative; text-align: left;">
                                        <button onclick="document.getElementById('modal-{{ $row->id }}').close()" style="position: absolute; right: -1rem; top: -1rem; background: rgba(239,68,68,0.1); border: none; font-size: 1.2rem; cursor: pointer; color: #ef4444; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; transition: all 0.2s;" onmouseover="this.style.background='rgba(239,68,68,0.2)';" onmouseout="this.style.background='rgba(239,68,68,0.1)';">✕</button>
                                        
                                        <h2 style="font-size: 1.3rem; color: var(--text-main); margin-bottom: 0.5rem; border-bottom: 1px solid rgba(52,152,219,0.2); padding-bottom: 0.8rem; display: flex; align-items: center; gap: 0.5rem; margin-top: 0;">
                                            <span>💡</span> Hasil Rekomendasi Tumbuh Kembang
                                        </h2>
                                        <p style="color: var(--text-muted); font-size: 0.85rem; margin-bottom: 1.5rem;">Pengukuran Tanggal: <strong>{{ $row->tanggal_ukur }}</strong> (Usia: {{ $row->usia_bulan }} bulan)</p>
                                        
                                        <div style="font-size: 0.9rem; line-height: 1.6; white-space: normal;">
                                            @php
                                                $zScoreService = app(\App\Services\ZScoreService::class);
                                                $nutritionService = app(\App\Services\NutritionService::class);
                                                
                                                // WHO Resume
                                                $hasil->standar = 'WHO';
                                                $resumeWho = $nutritionService->getEquivalentAgeResume($row, $hasil);
                                                
                                                // CDC Resume (On-the-fly)
                                                $imt = $row->tinggi_badan > 0 ? $row->berat_badan / pow($row->tinggi_badan / 100, 2) : 0;
                                                $hasilCdc = (object)[
                                                    'waz' => $zScoreService->getZScore('waz', $anak->jenis_kelamin, $row->usia_bulan, $row->berat_badan, 'CDC'),
                                                    'haz' => $zScoreService->getZScore('haz', $anak->jenis_kelamin, $row->usia_bulan, $row->tinggi_badan, 'CDC'),
                                                    'bmiz' => $zScoreService->getZScore('bmiz', $anak->jenis_kelamin, $row->usia_bulan, $imt, 'CDC'),
                                                    'standar' => 'CDC'
                                                ];
                                                $hasilCdc->status_tb_u = $hasilCdc->haz !== null ? $nutritionService->determineStatusTBU($hasilCdc->haz, 'CDC') : null;
                                                $resumeCdc = $nutritionService->getEquivalentAgeResume($row, $hasilCdc);
                                            @endphp
                                            
                                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                                                @if($resumeWho)
                                                    <div style="padding: 1rem; background: rgba(41, 128, 185, 0.08); border-left: 4px solid #2980b9; border-radius: 0 4px 4px 0; color: var(--text-main);">
                                                        <strong style="color: #2980b9; display: block; margin-bottom: 0.5rem; font-size: 1.05rem;">📊 WHO 2006</strong>
                                                        <p style="margin: 0 0 0.5rem 0; font-weight: bold; font-size: 1rem;">
                                                            Status: 
                                                            @if($resumeWho['is_stunting'])
                                                                <span style="color: #e74c3c;">Stanting / Pendek</span>
                                                            @else
                                                                <span style="color: #2ecc71;">Non Stanting</span>
                                                            @endif
                                                        </p>
                                                        <div style="font-size: 0.85rem; line-height: 1.5; color: var(--text-secondary);">
                                                            <ul style="margin: 0; padding-left: 1.2rem;">
                                                                <li>BB setara umur {{ $resumeWho['wa'] ?? '...' }} bln</li>
                                                                <li>TB setara umur {{ $resumeWho['ha'] ?? '...' }} bln</li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                @endif

                                                @if($resumeCdc)
                                                    <div style="padding: 1rem; background: rgba(155, 89, 182, 0.08); border-left: 4px solid #9b59b6; border-radius: 0 4px 4px 0; color: var(--text-main);">
                                                        <strong style="color: #9b59b6; display: block; margin-bottom: 0.5rem; font-size: 1.05rem;">📊 CDC 2000</strong>
                                                        <p style="margin: 0 0 0.5rem 0; font-weight: bold; font-size: 1rem;">
                                                            Status: 
                                                            @if($resumeCdc['is_stunting'])
                                                                <span style="color: #e74c3c;">Short Stature / UW</span>
                                                            @else
                                                                <span style="color: #2ecc71;">Normal</span>
                                                            @endif
                                                        </p>
                                                        <div style="font-size: 0.85rem; line-height: 1.5; color: var(--text-secondary);">
                                                            <ul style="margin: 0; padding-left: 1.2rem;">
                                                                <li>BB setara umur {{ $resumeCdc['wa'] ?? '...' }} bln</li>
                                                                <li>TB setara umur {{ $resumeCdc['ha'] ?? '...' }} bln</li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>

                                            @if($hasil)
                                                @if($hasil->red_flag)
                                                    <div style="margin-bottom: 1.5rem; padding: 1rem; background: rgba(239,68,68,0.1); border-left: 4px solid #ef4444; border-radius: 0 4px 4px 0;">
                                                        <strong style="color: #ef4444; display: block; margin-bottom: 0.5rem;">🚨 Peringatan Medis (Red Flag)</strong>
                                                        <span style="color: #ef4444;">{{ $hasil->catatan_red_flag }}</span>
                                                    </div>
                                                @endif
                                                
                                                @if($hasil->narasi_interpretasi)
                                                    <div style="margin-bottom: 1.5rem; padding: 1rem; background: rgba(52,152,219,0.08); border-left: 4px solid #3498db; border-radius: 0 4px 4px 0; color: var(--text-main);">
                                                        <strong style="color: #3498db; display: block; margin-bottom: 0.5rem;">🩺 Evaluasi Klinis Z-Score</strong>
                                                        {!! \Illuminate\Support\Str::markdown($hasil->narasi_interpretasi) !!}
                                                    </div>
                                                @endif
                                                
                                                @php
                                                    $rdaRowText = app(\App\Services\NutritionService::class)->generateRDAText($hasil, $row);
                                                @endphp
                                                
                                                @if($rdaRowText)
                                                    <div style="margin-bottom: 1rem; padding: 1rem; background: rgba(16,185,129,0.08); border-left: 4px solid #10b981; border-radius: 0 4px 4px 0; color: var(--text-main);">
                                                        <strong style="color: #10b981; display: block; margin-bottom: 0.5rem;">🍽️ Rekomendasi Gizi (RDA)</strong>
                                                        {!! \Illuminate\Support\Str::markdown($rdaRowText) !!}
                                                    </div>
                                                @endif
                                            @else
                                                <div style="padding: 2rem; text-align: center; color: var(--text-muted); background: rgba(255,255,255,0.02); border-radius: 8px;">
                                                    <span style="font-size: 2rem; display: block; margin-bottom: 0.5rem; opacity: 0.5;">❓</span>
                                                    Belum ada hasil kalkulasi untuk pengukuran ini.
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </dialog>
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
