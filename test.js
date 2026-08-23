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
