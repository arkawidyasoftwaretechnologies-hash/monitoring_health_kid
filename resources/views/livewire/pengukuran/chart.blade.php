<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h2 class="card-title">Grafik Pertumbuhan: {{ $anak->nama }}</h2>
        <a href="{{ route('anak.index') }}" class="btn" style="background: #e5e7eb;">Kembali</a>
    </div>

    @if(count($labels) > 0)
        <div class="chart-container" style="margin-top: 2rem;">
            <canvas id="growthChart"></canvas>
        </div>
    @else
        <div style="padding: 3rem; text-align: center; color: var(--text-muted);">
            Belum ada data pengukuran untuk ditampilkan di grafik.
        </div>
    @endif

    <script>
        document.addEventListener('livewire:initialized', () => {
            const ctx = document.getElementById('growthChart');
            if(ctx) {
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($labels) !!},
                        datasets: [
                            {
                                label: 'Tinggi Badan / Umur (HAZ)',
                                data: {!! json_encode($hazData) !!},
                                borderColor: '#4F46E5',
                                backgroundColor: 'rgba(79, 70, 229, 0.1)',
                                borderWidth: 3,
                                fill: true,
                                tension: 0.4
                            },
                            {
                                label: 'Berat Badan / Umur (WAZ)',
                                data: {!! json_encode($wazData) !!},
                                borderColor: '#10B981',
                                backgroundColor: 'transparent',
                                borderWidth: 3,
                                borderDash: [5, 5],
                                tension: 0.4
                            },
                            {
                                label: 'Batas Bawah Normal (-2 SD)',
                                data: Array({!! count($labels) !!}).fill(-2),
                                borderColor: '#F59E0B',
                                borderWidth: 2,
                                borderDash: [10, 5],
                                pointRadius: 0,
                                fill: false
                            },
                            {
                                label: 'Batas Stunting/Gizi Buruk (-3 SD)',
                                data: Array({!! count($labels) !!}).fill(-3),
                                borderColor: '#EF4444',
                                borderWidth: 2,
                                borderDash: [10, 5],
                                pointRadius: 0,
                                fill: false
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom'
                            }
                        },
                        scales: {
                            y: {
                                min: -6,
                                max: 6,
                                title: {
                                    display: true,
                                    text: 'Z-Score (SD)'
                                },
                                grid: {
                                    color: (context) => {
                                        if (context.tick.value === 0) return '#000';
                                        return 'rgba(0,0,0,0.05)';
                                    },
                                    lineWidth: (context) => {
                                        if (context.tick.value === 0) return 2;
                                        return 1;
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>

    <!-- Tabel Riwayat Pengukuran -->
    <div style="margin-top: 3rem; border-top: 1px solid var(--border); padding-top: 2rem;">
        <h3 class="card-title" style="margin-bottom: 1rem;">Riwayat Seluruh Pengukuran & Indikator Medis</h3>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Tgl Ukur</th>
                        <th>Usia</th>
                        <th>BB</th>
                        <th>TB/PB</th>
                        <th>LK</th>
                        <th>LiLA</th>
                        <th>IMT/U (BMIZ)</th>
                        <th>WAZ</th>
                        <th>HAZ</th>
                        <th>LK/U (HCFA)</th>
                        <th>Red Flag</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($anak->pengukurans as $row)
                    <tr>
                        <td>{{ $row->tanggal_ukur }}</td>
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
                            <strong style="color: var(--primary);">{{ $row->hasilStatusGizi->hcfa ?? '-' }}</strong><br>
                            <span class="badge {{ str_contains(strtolower($row->hasilStatusGizi->status_lk_u ?? ''), 'normal') ? 'badge-normal' : 'badge-danger' }}" style="font-size: 0.6rem;">
                                {{ $row->hasilStatusGizi->status_lk_u ?? '-' }}
                            </span>
                        </td>
                        <td>
                            @if(optional($row->hasilStatusGizi)->red_flag)
                                <span style="color: red; font-weight: bold; cursor: help;" title="{{ $row->hasilStatusGizi->catatan_red_flag }}">Ya</span>
                            @else
                                <span style="color: gray;">Tidak</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" style="text-align: center; color: var(--text-muted); padding: 2rem;">Belum ada data riwayat pengukuran.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
