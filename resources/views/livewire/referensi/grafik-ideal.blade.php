<div>
    <div class="page-header" style="margin-bottom: 2rem;">
        <h1 style="font-size: 1.8rem; color: var(--text-main); margin-bottom: 0.5rem; font-weight: 700;">
            📈 Grafik Standar Ideal (0 - 60 Bulan)
        </h1>
        <p style="color: var(--text-muted); font-size: 0.95rem;">
            Kurva Median (P50) dari referensi pertumbuhan WHO 2006 (BB, TB, LK, LiLA) dan CDC 2000 (BB, TB).
        </p>
    </div>

    <!-- Filter Control -->
    <div class="glass-panel" style="padding: 1.5rem; margin-bottom: 2rem; display: flex; gap: 1.5rem; flex-wrap: wrap; align-items: center;">
        <div style="flex: 1; min-width: 200px; max-width: 300px;">
            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--text-muted); margin-bottom: 0.5rem;">Jenis Kelamin</label>
            <select wire:model.live="jenis_kelamin" class="form-input" style="width: 100%;">
                <option value="L">Laki-laki</option>
                <option value="P">Perempuan</option>
            </select>
        </div>
    </div>

    <!-- Alpine/JS Script & Container -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <div wire:key="charts-{{ $jenis_kelamin }}" x-data="growthCharts(
        {{ json_encode($labels) }},
        {{ json_encode($whoData) }},
        {{ json_encode($cdcData) }}
    )" x-init="initCharts()">
        
        <!-- WHO Section -->
        <h2 style="font-size: 1.4rem; color: #2980b9; margin-bottom: 1rem; border-bottom: 2px solid rgba(41, 128, 185, 0.2); padding-bottom: 0.5rem;">
            📊 Standar WHO 2006
        </h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 1.5rem; margin-bottom: 3rem;">
            <!-- WHO WAZ -->
            <div class="glass-panel chart-card" style="padding: 1.5rem;">
                <h3 style="color: var(--text-main); font-size: 1.05rem; margin-bottom: 1rem;">Berat Badan (BB/U)</h3>
                <div style="position: relative; height: 350px;">
                    <canvas id="chartWhoWaz"></canvas>
                </div>
            </div>
            <!-- WHO HAZ -->
            <div class="glass-panel chart-card" style="padding: 1.5rem;">
                <h3 style="color: var(--text-main); font-size: 1.05rem; margin-bottom: 1rem;">Tinggi Badan (TB/U)</h3>
                <div style="position: relative; height: 350px;">
                    <canvas id="chartWhoHaz"></canvas>
                </div>
            </div>
            <!-- WHO HCFA -->
            <div class="glass-panel chart-card" style="padding: 1.5rem;">
                <h3 style="color: var(--text-main); font-size: 1.05rem; margin-bottom: 1rem;">Lingkar Kepala (LK/U)</h3>
                <div style="position: relative; height: 350px;">
                    <canvas id="chartWhoHcfa"></canvas>
                </div>
            </div>
            <!-- WHO LILA -->
            <div class="glass-panel chart-card" style="padding: 1.5rem;">
                <h3 style="color: var(--text-main); font-size: 1.05rem; margin-bottom: 1rem;">Lingkar Lengan Atas (LiLA)</h3>
                <div style="position: relative; height: 350px;">
                    <canvas id="chartWhoLila"></canvas>
                </div>
            </div>
        </div>

        <!-- CDC Section -->
        <h2 style="font-size: 1.4rem; color: #9b59b6; margin-bottom: 1rem; border-bottom: 2px solid rgba(155, 89, 182, 0.2); padding-bottom: 0.5rem;">
            📊 Standar CDC 2000
        </h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 1.5rem;">
            <!-- CDC WAZ -->
            <div class="glass-panel chart-card" style="padding: 1.5rem;">
                <h3 style="color: var(--text-main); font-size: 1.05rem; margin-bottom: 1rem;">Berat Badan (BB/U)</h3>
                <div style="position: relative; height: 350px;">
                    <canvas id="chartCdcWaz"></canvas>
                </div>
            </div>
            <!-- CDC HAZ -->
            <div class="glass-panel chart-card" style="padding: 1.5rem;">
                <h3 style="color: var(--text-main); font-size: 1.05rem; margin-bottom: 1rem;">Tinggi Badan (TB/U)</h3>
                <div style="position: relative; height: 350px;">
                    <canvas id="chartCdcHaz"></canvas>
                </div>
            </div>
        </div>

    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('growthCharts', (labels, whoData, cdcData) => ({
                labels: labels,
                whoData: whoData,
                cdcData: cdcData,
                charts: {}, // Simpan instance chart

                initCharts() {
                    this.renderChart('chartWhoWaz', this.whoData.waz, 'Berat Badan (kg)', '#2980b9', 'WHO');
                    this.renderChart('chartWhoHaz', this.whoData.haz, 'Tinggi Badan (cm)', '#2980b9', 'WHO');
                    this.renderChart('chartWhoHcfa', this.whoData.hcfa, 'Lingkar Kepala (cm)', '#2980b9', 'WHO');
                    this.renderChart('chartWhoLila', this.whoData.lila, 'LiLA (cm)', '#27ae60', 'WHO'); // Hijau statis
                    
                    this.renderChart('chartCdcWaz', this.cdcData.waz, 'Berat Badan (kg)', '#9b59b6', 'CDC');
                    this.renderChart('chartCdcHaz', this.cdcData.haz, 'Tinggi Badan (cm)', '#9b59b6', 'CDC');
                },
                
                renderChart(elementId, data, yLabel, color, standard) {
                    const ctx = document.getElementById(elementId);
                    if (!ctx) return;
                    
                    if (this.charts[elementId]) this.charts[elementId].destroy();
                    
                    this.charts[elementId] = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: this.labels,
                            datasets: [{
                                label: 'Median ' + standard + ' (' + yLabel + ')',
                                data: data,
                                borderColor: color,
                                backgroundColor: 'transparent',
                                borderWidth: 3,
                                pointRadius: 0,
                                pointHoverRadius: 5,
                                fill: false,
                                tension: 0.4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { display: false } },
                            scales: {
                                x: { title: { display: true, text: 'Usia (Bulan)' }, grid: { display: false } },
                                y: { title: { display: true, text: yLabel } }
                            }
                        }
                    });
                }
            }));
        });
    </script>
</div>
