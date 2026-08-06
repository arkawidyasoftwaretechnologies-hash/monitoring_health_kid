<div>
    <!-- Stat Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
        @php
            $cardData = [
                ['label' => 'Total Aktif', 'val' => $stats['total'], 'color' => '#3498db', 'bg' => 'rgba(52,152,219,0.15)', 'fv' => 'Semua'],
                ['label' => 'Normal', 'val' => $stats['normal'], 'color' => '#2ecc71', 'bg' => 'rgba(46,204,113,0.15)', 'fv' => 'Normal'],
                ['label' => 'Stunting', 'val' => $stats['stunting'], 'color' => '#e74c3c', 'bg' => 'rgba(231,76,60,0.15)', 'fv' => 'Stunted'],
                ['label' => 'Red Flag ⚠', 'val' => $stats['red_flag'], 'color' => '#e67e22', 'bg' => 'rgba(230,126,34,0.15)', 'fv' => 'Red Flag'],
                ['label' => 'Belum Diukur', 'val' => $stats['belum_diukur'], 'color' => '#95a5a6', 'bg' => 'rgba(149,165,166,0.15)', 'fv' => 'Belum Diukur'],
                ['label' => 'Ditampilkan', 'val' => count($anaks), 'color' => '#f39c12', 'bg' => 'rgba(243,156,18,0.15)', 'fv' => null],
            ];
        @endphp
        
        @foreach($cardData as $s)
            <div wire:click="{{ $s['fv'] !== null ? "setFilterStatus('".$s['fv']."')" : '' }}" 
                 class="glass-panel" 
                 style="padding: 1rem; text-align: center; border: 1px solid {{ $s['color'] }}30; cursor: {{ $s['fv'] !== null ? 'pointer' : 'default' }};
                        box-shadow: {{ $filterStatus === $s['fv'] ? '0 0 0 2px '.$s['color'] : 'none' }}; transition: all 0.2s;">
                <div style="font-size: 1.8rem; font-weight: bold; color: {{ $s['color'] }}">{{ $s['val'] }}</div>
                <div style="font-size: 0.72rem; color: {{ $s['color'] }}; opacity: 0.85; margin-top: 0.2rem;">{{ $s['label'] }}</div>
            </div>
        @endforeach
    </div>

    <!-- Filter Bar -->
    <div class="glass-panel" style="padding: 1.5rem; margin-bottom: 1.5rem;">
        <div style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center; justify-content: space-between;">
            <div style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center; flex: 1;">
                <div style="position: relative; flex: 1; min-width: 200px;">
                    <span style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--text-muted);">🔍</span>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama atau NIK..." 
                           style="width: 100%; padding: 10px 12px 10px 36px; border-radius: 8px; border: 1px solid var(--border); background: rgba(255,255,255,0.05); color: var(--text-main);">
                </div>
                <select wire:model.live="filterStatus" style="width: auto; min-width: 160px; padding: 10px 12px; border-radius: 8px; border: 1px solid var(--border); background: rgba(255,255,255,0.05); color: var(--text-main); cursor: pointer;">
                    <option value="Semua">🩺 Semua Status</option>
                    <option value="Normal">✅ Normal</option>
                    <option value="Stunted">⚠️ Stunting</option>
                    <option value="Red Flag">🚨 Red Flag</option>
                    <option value="Belum Diukur">❓ Belum Diukur</option>
                </select>
                
                @if($search || $filterStatus !== 'Semua')
                    <button wire:click="$set('search', ''); $set('filterStatus', 'Semua')" 
                            style="padding: 10px 14px; border-radius: 8px; background: rgba(231,76,60,0.15); border: 1px solid rgba(231,76,60,0.5); color: #e74c3c; cursor: pointer;">
                        ✕ Reset
                    </button>
                @endif
            </div>
            
            <a href="{{ route('anak.create') }}" style="padding: 10px 16px; border-radius: 8px; background: rgba(52,152,219,0.2); border: 1px solid #3498db; color: #3498db; text-decoration: none; font-weight: 600;">
                + Tambah Anak
            </a>
        </div>
    </div>

    <!-- Table -->
    <div class="glass-panel animate-fade-in" style="padding: 1.5rem;">
        <div class="table-wrapper" style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.82rem;">
                <thead>
                    <!-- Baris 1: Group header -->
                    <tr style="border-bottom: 1px solid rgba(52,152,219,0.15);">
                        <th colspan="4" style="padding: 0.5rem 0.5rem 0.3rem; color: #3498db; font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; background: transparent;">
                            📋 Identitas Anak
                        </th>
                        <th colspan="11" style="padding: 0.5rem 0.5rem 0.3rem; color: #1abc9c; font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; border-left: 2px solid rgba(26,188,156,0.3); background: transparent;">
                            📏 Pengukuran Terakhir (WHO Z-Score)
                        </th>
                        <th style="padding: 0.5rem 0.5rem 0.3rem; color: var(--text-muted); font-size: 0.68rem; background: transparent;"></th>
                    </tr>
                    <!-- Baris 2: Column headers -->
                    <tr style="border-bottom: 2px solid rgba(52,152,219,0.3); background: rgba(0,0,0,0.05);">
                        @foreach(['NIK', 'Nama', 'L/P', 'Tgl Ukur'] as $h)
                            <th style="padding: 0.6rem 0.5rem; color: var(--text-muted); font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.03em; white-space: nowrap; background: transparent;">{{ $h }}</th>
                        @endforeach
                        
                        @foreach(['Usia (Bln)', 'BB (kg)', 'TB (cm)', 'LK (cm)', 'LiLA (cm)', 'HAZ', 'WAZ', 'WHZ', 'Status Gizi', 'IMT/U & BB/U', 'TB/U & LK/U'] as $h)
                            <th style="padding: 0.6rem 0.5rem; color: #1abc9c; font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.03em; white-space: nowrap; border-left: {{ $h === 'Usia (Bln)' ? '2px solid rgba(26,188,156,0.3)' : 'none' }}; text-align: center; background: transparent;">{{ $h }}</th>
                        @endforeach
                        
                        <th style="padding: 0.6rem 0.5rem; color: var(--text-muted); font-size: 0.7rem; font-weight: 600; text-transform: uppercase; background: transparent;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        if (!function_exists('getZColor')) {
                            function getZColor($z) {
                                if ($z === null || $z === '') return ['bg' => 'transparent', 'color' => 'var(--text-muted)'];
                                if ($z <= -3) return ['bg' => 'rgba(192,57,43,0.25)', 'color' => '#e74c3c'];
                                if ($z <= -2) return ['bg' => 'rgba(243,156,18,0.2)', 'color' => '#f39c12'];
                                if ($z >= 3) return ['bg' => 'rgba(192,57,43,0.25)', 'color' => '#e74c3c'];
                                if ($z >= 2) return ['bg' => 'rgba(52,152,219,0.2)', 'color' => '#3498db'];
                                return ['bg' => 'rgba(39,174,96,0.15)', 'color' => '#2ecc71'];
                            }
                        }
                        
                        if (!function_exists('getStatusStyle')) {
                            function getStatusStyle($s) {
                                if (!$s) return ['bg' => 'rgba(149,165,166,0.2)', 'color' => '#95a5a6'];
                                $lower = strtolower($s);
                                if (str_contains($lower, 'severely') || str_contains($lower, 'sangat') || str_contains($lower, 'obesitas')) return ['bg' => 'rgba(192,57,43,0.25)', 'color' => '#e74c3c'];
                                if (str_contains($lower, 'stunted') || str_contains($lower, 'wasted') || str_contains($lower, 'underweight') || str_contains($lower, 'kurang') || str_contains($lower, 'pendek')) return ['bg' => 'rgba(243,156,18,0.2)', 'color' => '#f39c12'];
                                if (str_contains($lower, 'normal')) return ['bg' => 'rgba(39,174,96,0.15)', 'color' => '#2ecc71'];
                                return ['bg' => 'rgba(52,152,219,0.15)', 'color' => '#3498db'];
                            }
                        }
                    @endphp
                    @forelse($anaks as $anak)
                        @php
                            $latest = $anak->pengukurans->first();
                            $hasil = $latest ? $latest->hasilStatusGizi : null;
                            $sc = $latest ? getStatusStyle($latest->status_stunting ?? 'Belum Kalkulasi') : ['bg' => 'rgba(149,165,166,0.2)', 'color' => '#95a5a6'];
                        @endphp
                        
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.05); transition: background .15s;" 
                            onmouseenter="this.style.background='rgba(52,152,219,0.06)'" 
                            onmouseleave="this.style.background='transparent'">
                            
                            <td style="padding: 0.6rem 0.5rem; font-family: monospace; font-size: 0.75rem; color: var(--text-secondary);">{{ $anak->nik ?? '—' }}</td>
                            <td style="padding: 0.6rem 0.5rem; font-weight: 600; max-width: 130px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $anak->nama }}</td>
                            <td style="padding: 0.6rem 0.5rem; text-align: center;">
                                <span style="padding: 1px 6px; border-radius: 4px; font-size: 0.75rem; background: {{ $anak->jenis_kelamin==='L' ? 'rgba(52,152,219,0.2)' : 'rgba(231,76,60,0.2)' }}; color: {{ $anak->jenis_kelamin==='L' ? '#3498db' : '#e74c3c' }};">
                                    {{ $anak->jenis_kelamin }}
                                </span>
                            </td>
                            <td style="padding: 0.6rem 0.5rem; color: #f39c12; font-size: 0.78rem; max-width: 110px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                {{ $latest ? $latest->tanggal_ukur : '—' }}
                            </td>
                            
                            <!-- Pengukuran Terakhir -->
                            <td style="padding: 0.6rem 0.5rem; text-align: center; color: var(--text-secondary); border-left: 2px solid rgba(26,188,156,0.3);">
                                {{ $latest ? floor(\Carbon\Carbon::parse($anak->tanggal_lahir)->diffInMonths(\Carbon\Carbon::parse($latest->tanggal_ukur))) : '—' }}
                            </td>
                            <td style="padding: 0.6rem 0.5rem; text-align: center; color: var(--text-secondary);">{{ $latest->berat_badan ?? '—' }}</td>
                            <td style="padding: 0.6rem 0.5rem; text-align: center; color: var(--text-secondary);">{{ $latest->tinggi_badan ?? '—' }}</td>
                            <td style="padding: 0.6rem 0.5rem; text-align: center; color: var(--text-secondary);">{{ $latest->lingkar_kepala ?? '—' }}</td>
                            <td style="padding: 0.6rem 0.5rem; text-align: center; color: var(--text-secondary);">{{ $latest->lila ?? '—' }}</td>
                            
                            @foreach([$hasil->haz ?? null, $hasil->waz ?? null, $hasil->bmiz ?? null] as $z)
                                @php $zc = getZColor($z); @endphp
                                <td style="padding: 0.6rem 0.5rem; text-align: center;">
                                    <span style="padding: 1px 5px; border-radius: 4px; font-weight: 700; font-family: monospace; font-size: 0.75rem; background: {{ $zc['bg'] }}; color: {{ $zc['color'] }};">
                                        {{ $z !== null ? number_format((float)$z, 2) : '—' }}
                                    </span>
                                </td>
                            @endforeach
                            
                            <td style="padding: 0.6rem 0.5rem;">
                                @if($hasil)
                                    <span style="padding: 2px 7px; border-radius: 20px; font-size: 0.68rem; font-weight: 700; background: {{ getStatusStyle($hasil->status_tb_u)['bg'] }}; color: {{ getStatusStyle($hasil->status_tb_u)['color'] }}; white-space: nowrap;">
                                        {{ $hasil->status_tb_u ?? 'Belum Kalkulasi' }}
                                        @if($hasil->red_flag) ⚠ @endif
                                    </span>
                                @else
                                    <span style="padding: 2px 7px; border-radius: 20px; font-size: 0.68rem; font-weight: 700; background: rgba(149,165,166,0.2); color: #95a5a6; white-space: nowrap;">
                                        Belum Diukur
                                    </span>
                                @endif
                            </td>
                            
                            <td style="padding: 0.6rem 0.5rem;">
                                @if($hasil)
                                    <div style="display: flex; flex-direction: column; gap: 0.2rem;">
                                        <span style="font-size: 0.65rem; color: var(--text-secondary);">IMT/U: <strong style="color: {{ getStatusStyle($hasil->status_imt_u)['color'] }}">{{ $hasil->status_imt_u ?? '—' }}</strong></span>
                                        <span style="font-size: 0.65rem; color: var(--text-secondary);">BB/U: <strong style="color: {{ getStatusStyle($hasil->status_bb_u)['color'] }}">{{ $hasil->status_bb_u ?? '—' }}</strong></span>
                                    </div>
                                @else
                                    <span style="color: var(--text-muted);">—</span>
                                @endif
                            </td>
                            
                            <td style="padding: 0.6rem 0.5rem;">
                                @if($hasil)
                                    <div style="display: flex; flex-direction: column; gap: 0.2rem;">
                                        <span style="font-size: 0.65rem; color: var(--text-secondary);">TB/U: <strong style="color: {{ getStatusStyle($hasil->status_tb_u)['color'] }}">{{ $hasil->status_tb_u ?? '—' }}</strong></span>
                                        <span style="font-size: 0.65rem; color: var(--text-secondary);">LK/U: <strong style="color: {{ getStatusStyle($hasil->status_lk_u)['color'] }}">{{ $hasil->status_lk_u ?? '—' }}</strong></span>
                                    </div>
                                @else
                                    <span style="color: var(--text-muted);">—</span>
                                @endif
                            </td>
                            
                            <td style="padding: 0.6rem 0.5rem;">
                                <div style="display: flex; gap: 0.3rem;">
                                    <a href="{{ route('anak.edit', $anak->id) }}" title="Edit" style="padding: 4px 8px; background: rgba(52,152,219,0.15); border: 1px solid #3498db; color: #3498db; border-radius: 5px; cursor: pointer; font-size: 0.72rem; text-decoration: none;">✏️</a>
                                    <a href="{{ route('pengukuran.create', $anak->id) }}" style="padding: 4px 8px; background: rgba(39,174,96,0.2); border: 1px solid #2ecc71; color: #2ecc71; border-radius: 5px; cursor: pointer; font-size: 0.72rem; text-decoration: none;">+ Ukur</a>
                                    <a href="{{ route('pengukuran.chart', $anak->id) }}" style="padding: 4px 8px; background: rgba(155,89,182,0.2); border: 1px solid #9b59b6; color: #9b59b6; border-radius: 5px; cursor: pointer; font-size: 0.72rem; text-decoration: none; white-space: nowrap;">📄 Laporan</a>
                                    <button wire:click="deleteAnak({{ $anak->id }})" wire:confirm="Yakin ingin menghapus data anak ini beserta seluruh riwayat pengukurannya?" style="padding: 4px 8px; background: rgba(231,76,60,0.15); border: 1px solid #e74c3c; color: #e74c3c; border-radius: 5px; cursor: pointer; font-size: 0.72rem;" title="Hapus">🗑</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="16" style="padding: 3rem; text-align: center; color: var(--text-muted);">
                                <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">👶</div>
                                Tidak ada anak yang sesuai filter.<br>
                                <a href="{{ route('anak.create') }}" style="display: inline-block; margin-top: 1rem; padding: 8px 16px; border-radius: 8px; background: rgba(52,152,219,0.2); border: 1px solid #3498db; color: #3498db; cursor: pointer; text-decoration: none;">
                                    + Tambah Data Anak
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
