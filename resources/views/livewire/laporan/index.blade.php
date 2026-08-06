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

            @if($selectedAnak && count($chartLabels) > 0)
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(450px, 1fr)); gap: 2rem; margin-bottom: 2rem; margin-top: 1.5rem;">
                <!-- Chart 1: IMT/U & BB/U -->
                <div class="glass-panel animate-fade-in" style="padding: 1.5rem;">
                    <h3 style="color: var(--text-main); font-size: 1.1rem; margin-bottom: 1rem; border-bottom: 1px solid rgba(52,152,219,0.2); padding-bottom: 0.8rem;">
                        Tren Status Gizi Berat Badan (IMT/U & BB/U)
                    </h3>
                    <div class="chart-container" style="height: 350px; position: relative;">
                        <canvas id="laporanChartBB"></canvas>
                    </div>
                </div>

                <!-- Chart 2: TB/U & LK/U -->
                <div class="glass-panel animate-fade-in" style="padding: 1.5rem;">
                    <h3 style="color: var(--text-main); font-size: 1.1rem; margin-bottom: 1rem; border-bottom: 1px solid rgba(52,152,219,0.2); padding-bottom: 0.8rem;">
                        Tren Pertumbuhan Linier (TB/U & LK/U)
                    </h3>
                    <div class="chart-container" style="height: 350px; position: relative;">
                        <canvas id="laporanChartTB"></canvas>
                    </div>
                </div>
                
                <!-- Chart 3: LiLA -->
                <div class="glass-panel animate-fade-in" style="padding: 1.5rem;">
                    <h3 style="color: var(--text-main); font-size: 1.1rem; margin-bottom: 1rem; border-bottom: 1px solid rgba(52,152,219,0.2); padding-bottom: 0.8rem;">
                        Tren Lingkar Lengan Atas (LiLA)
                    </h3>
                    <div class="chart-container" style="height: 350px; position: relative;">
                        <canvas id="laporanChartLila"></canvas>
                    </div>
                </div>

                <!-- Chart 4: Aktual BB & TB -->
                <div class="glass-panel animate-fade-in" style="padding: 1.5rem;">
                    <h3 style="color: var(--text-main); font-size: 1.1rem; margin-bottom: 1rem; border-bottom: 1px solid rgba(52,152,219,0.2); padding-bottom: 0.8rem;">
                        Kurva Kecepatan Tumbuh (BB & TB Aktual)
                    </h3>
                    <div class="chart-container" style="height: 350px; position: relative;">
                        <canvas id="laporanChartAktual"></canvas>
                    </div>
                </div>

                <!-- Chart 5: IMT Aktual -->
                <div class="glass-panel animate-fade-in" style="padding: 1.5rem;">
                    <h3 style="color: var(--text-main); font-size: 1.1rem; margin-bottom: 1rem; border-bottom: 1px solid rgba(52,152,219,0.2); padding-bottom: 0.8rem;">
                        Indeks Massa Tubuh Aktual (IMT / BMI)
                    </h3>
                    <div class="chart-container" style="height: 350px; position: relative;">
                        <canvas id="laporanChartIMT"></canvas>
                    </div>
                </div>

                <!-- Chart 6: LK Aktual -->
                <div class="glass-panel animate-fade-in" style="padding: 1.5rem;">
                    <h3 style="color: var(--text-main); font-size: 1.1rem; margin-bottom: 1rem; border-bottom: 1px solid rgba(52,152,219,0.2); padding-bottom: 0.8rem;">
                        Tren Lingkar Kepala Aktual (cm)
                    </h3>
                    <div class="chart-container" style="height: 350px; position: relative;">
                        <canvas id="laporanChartLK"></canvas>
                    </div>
                </div>
            </div>

            <script>
                document.addEventListener('livewire:initialized', () => {
                    let chartBB = null;
                    let chartTB = null;
                    let chartLila = null;
                    let chartAktual = null;
                    let chartIMT = null;
                    let chartLK = null;
                    
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
                                    color: (context) => context.tick.value === 0 ? 'rgba(52,152,219,0.3)' : 'rgba(255,255,255,0.05)',
                                    lineWidth: (context) => context.tick.value === 0 ? 2 : 1
                                },
                                ticks: { color: '#9ca3af' }
                            }
                        }
                    };

                    const lilaOptions = {
                        ...commonOptions,
                        scales: {
                            x: commonOptions.scales.x,
                            y: {
                                min: 8,
                                max: 20,
                                title: { display: true, text: 'LiLA (cm)', color: '#9ca3af' },
                                grid: { color: 'rgba(255,255,255,0.05)' },
                                ticks: { color: '#9ca3af' }
                            }
                        }
                    };

                    const lkOptions = {
                        ...commonOptions,
                        scales: {
                            x: commonOptions.scales.x,
                            y: {
                                min: 30,
                                max: 60,
                                title: { display: true, text: 'Lingkar Kepala (cm)', color: '#9ca3af' },
                                grid: { color: 'rgba(255,255,255,0.05)' },
                                ticks: { color: '#9ca3af' }
                            }
                        }
                    };

                    const imtOptions = {
                        ...commonOptions,
                        scales: {
                            x: commonOptions.scales.x,
                            y: {
                                min: 10,
                                max: 25,
                                title: { display: true, text: 'IMT (kg/m²)', color: '#9ca3af' },
                                grid: { color: 'rgba(255,255,255,0.05)' },
                                ticks: { color: '#9ca3af' }
                            }
                        }
                    };

                    const aktualOptions = {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: commonOptions.plugins,
                        scales: {
                            x: commonOptions.scales.x,
                            y: {
                                type: 'linear',
                                display: true,
                                position: 'left',
                                title: { display: true, text: 'Berat Badan (kg)', color: '#10B981' },
                                grid: { color: 'rgba(255,255,255,0.05)' },
                                ticks: { color: '#10B981' }
                            },
                            y1: {
                                type: 'linear',
                                display: true,
                                position: 'right',
                                title: { display: true, text: 'Tinggi Badan (cm)', color: '#4F46E5' },
                                grid: { drawOnChartArea: false }, // only draw grid lines for one axis
                                ticks: { color: '#4F46E5' }
                            }
                        }
                    };
                    
                    const renderCharts = (data) => {
                        const ctxBB = document.getElementById('laporanChartBB');
                        const ctxTB = document.getElementById('laporanChartTB');
                        const ctxLila = document.getElementById('laporanChartLila');
                        const ctxAktual = document.getElementById('laporanChartAktual');
                        const ctxIMT = document.getElementById('laporanChartIMT');
                        const ctxLK = document.getElementById('laporanChartLK');
                        
                        if(chartBB) chartBB.destroy();
                        if(chartTB) chartTB.destroy();
                        if(chartLila) chartLila.destroy();
                        if(chartAktual) chartAktual.destroy();
                        if(chartIMT) chartIMT.destroy();
                        if(chartLK) chartLK.destroy();
                        
                        if(ctxBB) {
                            chartBB = new Chart(ctxBB, {
                                type: 'line',
                                data: {
                                    labels: data.labels,
                                    datasets: [
                                        {
                                            label: 'IMT/U (BMIZ)',
                                            data: data.bmiz,
                                            borderColor: '#8b5cf6',
                                            backgroundColor: 'rgba(139, 92, 246, 0.1)',
                                            borderWidth: 3,
                                            fill: true,
                                            tension: 0.4,
                                            pointBackgroundColor: '#8b5cf6'
                                        },
                                        {
                                            label: 'BB/U (WAZ)',
                                            data: data.waz,
                                            borderColor: '#10B981',
                                            backgroundColor: 'transparent',
                                            borderWidth: 3,
                                            borderDash: [5, 5],
                                            tension: 0.4,
                                            pointBackgroundColor: '#10B981'
                                        },
                                        { label: 'Batas Bawah Normal (-2 SD)', data: Array(data.labels.length).fill(-2), borderColor: 'rgba(245, 158, 11, 0.6)', borderWidth: 2, borderDash: [10, 5], pointRadius: 0, fill: false },
                                        { label: 'Batas Gizi Buruk (-3 SD)', data: Array(data.labels.length).fill(-3), borderColor: 'rgba(239, 68, 68, 0.6)', borderWidth: 2, borderDash: [10, 5], pointRadius: 0, fill: false }
                                    ]
                                },
                                options: commonOptions
                            });
                        }

                        if(ctxTB) {
                            chartTB = new Chart(ctxTB, {
                                type: 'line',
                                data: {
                                    labels: data.labels,
                                    datasets: [
                                        {
                                            label: 'TB/U (HAZ)',
                                            data: data.haz,
                                            borderColor: '#4F46E5',
                                            backgroundColor: 'rgba(79, 70, 229, 0.1)',
                                            borderWidth: 3,
                                            fill: true,
                                            tension: 0.4,
                                            pointBackgroundColor: '#4F46E5'
                                        },
                                        {
                                            label: 'LK/U (HCFA)',
                                            data: data.hcfa,
                                            borderColor: '#0ea5e9',
                                            backgroundColor: 'transparent',
                                            borderWidth: 3,
                                            borderDash: [5, 5],
                                            tension: 0.4,
                                            pointBackgroundColor: '#0ea5e9'
                                        },
                                        { label: 'Batas Bawah Normal (-2 SD)', data: Array(data.labels.length).fill(-2), borderColor: 'rgba(245, 158, 11, 0.6)', borderWidth: 2, borderDash: [10, 5], pointRadius: 0, fill: false },
                                        { label: 'Batas Sangat Pendek (-3 SD)', data: Array(data.labels.length).fill(-3), borderColor: 'rgba(239, 68, 68, 0.6)', borderWidth: 2, borderDash: [10, 5], pointRadius: 0, fill: false }
                                    ]
                                },
                                options: commonOptions
                            });
                        }

                        if(ctxLila) {
                            chartLila = new Chart(ctxLila, {
                                type: 'line',
                                data: {
                                    labels: data.labels,
                                    datasets: [
                                        {
                                            label: 'LiLA Terukur (cm)',
                                            data: data.lila,
                                            borderColor: '#f43f5e',
                                            backgroundColor: 'rgba(244, 63, 94, 0.1)',
                                            borderWidth: 3,
                                            fill: true,
                                            tension: 0.4,
                                            pointBackgroundColor: '#f43f5e'
                                        },
                                        { label: 'Batas Gizi Kurang (12.5 cm)', data: Array(data.labels.length).fill(12.5), borderColor: 'rgba(245, 158, 11, 0.6)', borderWidth: 2, borderDash: [10, 5], pointRadius: 0, fill: false },
                                        { label: 'Batas Gizi Buruk (11.5 cm)', data: Array(data.labels.length).fill(11.5), borderColor: 'rgba(239, 68, 68, 0.6)', borderWidth: 2, borderDash: [10, 5], pointRadius: 0, fill: false }
                                    ]
                                },
                                options: lilaOptions
                            });
                        }

                        if(ctxAktual) {
                            chartAktual = new Chart(ctxAktual, {
                                type: 'line',
                                data: {
                                    labels: data.labels,
                                    datasets: [
                                        {
                                            label: 'Berat Badan Aktual (kg)',
                                            data: data.beratAktual,
                                            borderColor: '#10B981',
                                            backgroundColor: 'transparent',
                                            borderWidth: 3,
                                            yAxisID: 'y',
                                            fill: false,
                                            tension: 0.4,
                                            pointBackgroundColor: '#10B981'
                                        },
                                        {
                                            label: 'Tinggi Badan Aktual (cm)',
                                            data: data.tinggiAktual,
                                            borderColor: '#4F46E5',
                                            backgroundColor: 'transparent',
                                            borderWidth: 3,
                                            yAxisID: 'y1',
                                            fill: false,
                                            tension: 0.4,
                                            pointBackgroundColor: '#4F46E5'
                                        }
                                    ]
                                },
                                options: aktualOptions
                            });
                        }

                        if(ctxIMT) {
                            chartIMT = new Chart(ctxIMT, {
                                type: 'line',
                                data: {
                                    labels: data.labels,
                                    datasets: [
                                        {
                                            label: 'IMT / BMI (kg/m²)',
                                            data: data.imtAktual,
                                            borderColor: '#f59e0b',
                                            backgroundColor: 'rgba(245, 158, 11, 0.1)',
                                            borderWidth: 3,
                                            fill: true,
                                            tension: 0.4,
                                            pointBackgroundColor: '#f59e0b'
                                        }
                                    ]
                                },
                                options: imtOptions
                            });
                        }

                        if(ctxLK) {
                            chartLK = new Chart(ctxLK, {
                                type: 'line',
                                data: {
                                    labels: data.labels,
                                    datasets: [
                                        {
                                            label: 'Lingkar Kepala Terukur (cm)',
                                            data: data.lkAktual,
                                            borderColor: '#06b6d4',
                                            backgroundColor: 'rgba(6, 182, 212, 0.1)',
                                            borderWidth: 3,
                                            fill: true,
                                            tension: 0.4,
                                            pointBackgroundColor: '#06b6d4'
                                        }
                                    ]
                                },
                                options: lkOptions
                            });
                        }
                    };

                    // Initial render
                    renderCharts({
                        labels: {!! json_encode($chartLabels) !!},
                        haz: {!! json_encode($chartHaz) !!},
                        waz: {!! json_encode($chartWaz) !!},
                        bmiz: {!! json_encode($chartBmiz) !!},
                        hcfa: {!! json_encode($chartHcfa) !!},
                        lila: {!! json_encode($chartLila) !!},
                        beratAktual: {!! json_encode($chartBeratAktual) !!},
                        tinggiAktual: {!! json_encode($chartTinggiAktual) !!},
                        imtAktual: {!! json_encode($chartImtAktual) !!},
                        lkAktual: {!! json_encode($chartLkAktual) !!}
                    });

                    // Update when livewire changes
                    Livewire.on('chart-updated', (data) => {
                        renderCharts(data[0]);
                    });
                });
            </script>
            @endif

            <div class="glass-panel animate-fade-in" style="margin-top: 1.5rem; padding: 1.5rem;">
                <div class="table-wrapper" style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.82rem;">
                        <thead>
                            <tr style="border-bottom: 1px solid rgba(52,152,219,0.15);">
                                <th colspan="3" style="padding: 0.5rem 0.5rem 0.3rem; color: #3498db; font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; background: transparent;">
                                    📋 Detail Anak
                                </th>
                                <th colspan="8" style="padding: 0.5rem 0.5rem 0.3rem; color: #1abc9c; font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; border-left: 2px solid rgba(26,188,156,0.3); background: transparent;">
                                    📏 Hasil Pengukuran (Z-Score)
                                </th>
                            </tr>
                            <tr style="border-bottom: 2px solid rgba(52,152,219,0.3); background: rgba(0,0,0,0.05);">
                                <th style="padding: 0.6rem 0.5rem; color: var(--text-muted); font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.03em; white-space: nowrap; background: transparent; cursor: pointer;" wire:click="sortBy('tanggal_ukur')">
                                    Tgl Ukur
                                    @if($sortField === 'tanggal_ukur')
                                        <span style="font-size: 0.8rem; margin-left: 2px;">{!! $sortDirection === 'asc' ? '↑' : '↓' !!}</span>
                                    @endif
                                </th>
                                <th style="padding: 0.6rem 0.5rem; color: var(--text-muted); font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.03em; white-space: nowrap; background: transparent; cursor: pointer;" wire:click="sortBy('anak.nama')">
                                    Nama Anak
                                    @if($sortField === 'anak.nama')
                                        <span style="font-size: 0.8rem; margin-left: 2px;">{!! $sortDirection === 'asc' ? '↑' : '↓' !!}</span>
                                    @endif
                                </th>
                                <th style="padding: 0.6rem 0.5rem; color: var(--text-muted); font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.03em; white-space: nowrap; background: transparent;">Usia</th>
                                
                                @foreach(['BB', 'TB', 'LK', 'LiLA', 'IMT/U (BMIZ)', 'WAZ', 'HAZ', 'Red Flag'] as $h)
                                    <th style="padding: 0.6rem 0.5rem; color: #1abc9c; font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.03em; white-space: nowrap; border-left: {{ $h === 'BB' ? '2px solid rgba(26,188,156,0.3)' : 'none' }}; text-align: center; background: transparent;">{{ $h }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                if (!function_exists('getReportZColor')) {
                                    function getReportZColor($z) {
                                        if ($z === null || $z === '') return ['bg' => 'transparent', 'color' => 'var(--text-muted)'];
                                        if ($z <= -3) return ['bg' => 'rgba(192,57,43,0.25)', 'color' => '#e74c3c'];
                                        if ($z <= -2) return ['bg' => 'rgba(243,156,18,0.2)', 'color' => '#f39c12'];
                                        if ($z >= 3) return ['bg' => 'rgba(192,57,43,0.25)', 'color' => '#e74c3c'];
                                        if ($z >= 2) return ['bg' => 'rgba(52,152,219,0.2)', 'color' => '#3498db'];
                                        return ['bg' => 'rgba(39,174,96,0.15)', 'color' => '#2ecc71'];
                                    }
                                }
                                
                                if (!function_exists('getReportStatusStyle')) {
                                    function getReportStatusStyle($s) {
                                        if (!$s) return ['bg' => 'rgba(149,165,166,0.2)', 'color' => '#95a5a6'];
                                        $lower = strtolower($s);
                                        if (str_contains($lower, 'severely') || str_contains($lower, 'sangat') || str_contains($lower, 'obesitas')) return ['bg' => 'rgba(192,57,43,0.25)', 'color' => '#e74c3c'];
                                        if (str_contains($lower, 'stunted') || str_contains($lower, 'wasted') || str_contains($lower, 'underweight') || str_contains($lower, 'kurang') || str_contains($lower, 'pendek')) return ['bg' => 'rgba(243,156,18,0.2)', 'color' => '#f39c12'];
                                        if (str_contains($lower, 'normal')) return ['bg' => 'rgba(39,174,96,0.15)', 'color' => '#2ecc71'];
                                        return ['bg' => 'rgba(52,152,219,0.15)', 'color' => '#3498db'];
                                    }
                                }
                            @endphp
                            @forelse($pengukurans as $row)
                            @php
                                $hasil = $row->hasilStatusGizi;
                            @endphp
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.05); transition: background .15s;" 
                                onmouseenter="this.style.background='rgba(52,152,219,0.06)'" 
                                onmouseleave="this.style.background='transparent'">
                                <td style="padding: 0.6rem 0.5rem; font-family: monospace; font-size: 0.75rem; color: var(--text-secondary);">{{ $row->tanggal_ukur }}</td>
                                <td style="padding: 0.6rem 0.5rem; font-weight: 600; max-width: 130px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $row->anak->nama ?? '-' }}</td>
                                <td style="padding: 0.6rem 0.5rem;">{{ $row->usia_bulan }} bln</td>
                                
                                <!-- Pengukuran -->
                                <td style="padding: 0.6rem 0.5rem; text-align: center; border-left: 2px solid rgba(26,188,156,0.3);">{{ $row->berat_badan }} kg</td>
                                <td style="padding: 0.6rem 0.5rem; text-align: center;">{{ $row->tinggi_badan }} cm</td>
                                <td style="padding: 0.6rem 0.5rem; text-align: center;">{{ $row->lingkar_kepala ? $row->lingkar_kepala . ' cm' : '-' }}</td>
                                <td style="padding: 0.6rem 0.5rem; text-align: center;">{{ $row->lila ? $row->lila . ' cm' : '-' }}</td>
                                
                                <!-- Z-Scores and Statuses -->
                                @php $z1 = getReportZColor($hasil->bmiz ?? null); @endphp
                                <td style="padding: 0.6rem 0.5rem; text-align: center;">
                                    <span style="padding: 1px 5px; border-radius: 4px; font-weight: 700; font-family: monospace; font-size: 0.75rem; background: {{ $z1['bg'] }}; color: {{ $z1['color'] }}; display: inline-block; margin-bottom: 3px;">
                                        {{ $hasil->bmiz ?? '—' }}
                                    </span><br>
                                    <span style="font-size: 0.6rem; color: {{ getReportStatusStyle($hasil->status_imt_u ?? '')['color'] }}; font-weight: 600;">
                                        {{ $hasil->status_imt_u ?? '-' }}
                                    </span>
                                </td>
                                
                                @php $z2 = getReportZColor($hasil->waz ?? null); @endphp
                                <td style="padding: 0.6rem 0.5rem; text-align: center;">
                                    <span style="padding: 1px 5px; border-radius: 4px; font-weight: 700; font-family: monospace; font-size: 0.75rem; background: {{ $z2['bg'] }}; color: {{ $z2['color'] }}; display: inline-block; margin-bottom: 3px;">
                                        {{ $hasil->waz ?? '—' }}
                                    </span><br>
                                    <span style="font-size: 0.6rem; color: {{ getReportStatusStyle($hasil->status_bb_u ?? '')['color'] }}; font-weight: 600;">
                                        {{ $hasil->status_bb_u ?? '-' }}
                                    </span>
                                </td>
                                
                                @php $z3 = getReportZColor($hasil->haz ?? null); @endphp
                                <td style="padding: 0.6rem 0.5rem; text-align: center;">
                                    <span style="padding: 1px 5px; border-radius: 4px; font-weight: 700; font-family: monospace; font-size: 0.75rem; background: {{ $z3['bg'] }}; color: {{ $z3['color'] }}; display: inline-block; margin-bottom: 3px;">
                                        {{ $hasil->haz ?? '—' }}
                                    </span><br>
                                    <span style="font-size: 0.6rem; color: {{ getReportStatusStyle($hasil->status_tb_u ?? '')['color'] }}; font-weight: 600;">
                                        {{ $hasil->status_tb_u ?? '-' }}
                                    </span>
                                </td>
                                
                                <td style="padding: 0.6rem 0.5rem; text-align: center;">
                                    @if(optional($hasil)->red_flag)
                                        <span style="padding: 2px 6px; border-radius: 4px; font-size: 0.65rem; background: rgba(231,76,60,0.15); color: #e74c3c; font-weight: bold;">⚠ Ya</span>
                                    @else
                                        <span style="color: var(--text-muted); font-size: 0.8rem;">Tidak</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="11" style="text-align: center; color: var(--text-muted); padding: 3rem;">
                                    <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">📄</div>
                                    Tidak ada data pengukuran yang cocok dengan filter.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
