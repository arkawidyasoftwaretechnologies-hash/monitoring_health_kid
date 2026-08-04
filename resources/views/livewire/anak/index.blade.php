<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h2 class="card-title">Data Anak</h2>
        <a href="{{ route('anak.create') }}" class="btn btn-primary">+ Tambah Anak</a>
    </div>

    <div class="table-wrapper" style="margin-top: 1rem;">
        <table>
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>NIK</th>
                    <th>L/P</th>
                    <th>Usia (Bln)</th>
                    <th>Tgl Ukur (Terakhir)</th>
                    <th>BB/TB</th>
                    <th>LK/LiLA</th>
                    <th>Status IMT/U & BB/U</th>
                    <th>Status TB/U & LK/U</th>
                    <th>Red Flag</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($anaks as $anak)
                    @php
                        $latest = $anak->pengukurans->first();
                        $hasil = $latest ? $latest->hasilStatusGizi : null;
                    @endphp
                    <tr>
                        <td style="font-weight: 500;">{{ $anak->nama }}</td>
                        <td>{{ $anak->nik ?? '-' }}</td>
                        <td>{{ $anak->jenis_kelamin }}</td>
                        <td>{{ floor(\Carbon\Carbon::parse($anak->tanggal_lahir)->diffInMonths(now())) }} bln</td>
                        <td>{{ $latest ? $latest->tanggal_ukur : 'Belum diukur' }}</td>
                        
                        <td>
                            @if($latest)
                                {{ $latest->berat_badan }} kg / {{ $latest->tinggi_badan }} cm
                            @else
                                -
                            @endif
                        </td>

                        <td>
                            @if($latest)
                                {{ $latest->lingkar_kepala ? $latest->lingkar_kepala . ' cm' : '-' }} / {{ $latest->lila ? $latest->lila . ' cm' : '-' }}
                            @else
                                -
                            @endif
                        </td>

                        <td>
                            @if($hasil)
                                <span class="badge {{ str_contains(strtolower($hasil->status_imt_u ?? ''), 'kurang') ? 'badge-warning' : (str_contains(strtolower($hasil->status_imt_u ?? ''), 'lebih') || str_contains(strtolower($hasil->status_imt_u ?? ''), 'obesitas') ? 'badge-danger' : 'badge-normal') }}" style="font-size: 0.65rem; margin-bottom: 2px;">
                                    IMT: {{ $hasil->status_imt_u ?? '-' }}
                                </span><br>
                                <span class="badge {{ str_contains(strtolower($hasil->status_bb_u ?? ''), 'kurang') ? 'badge-warning' : 'badge-normal' }}" style="font-size: 0.65rem;">
                                    BB: {{ $hasil->status_bb_u ?? '-' }}
                                </span>
                            @else
                                -
                            @endif
                        </td>

                        <td>
                            @if($hasil)
                                <span class="badge {{ str_contains(strtolower($hasil->status_tb_u ?? ''), 'stunted') || str_contains(strtolower($hasil->status_tb_u ?? ''), 'pendek') ? 'badge-danger' : 'badge-normal' }}" style="font-size: 0.65rem; margin-bottom: 2px;">
                                    TB: {{ $hasil->status_tb_u ?? '-' }}
                                </span><br>
                                <span class="badge {{ str_contains(strtolower($hasil->status_lk_u ?? ''), 'normal') ? 'badge-normal' : 'badge-danger' }}" style="font-size: 0.65rem;">
                                    LK: {{ $hasil->status_lk_u ?? '-' }}
                                </span>
                            @else
                                -
                            @endif
                        </td>

                        <td>
                            @if($hasil && $hasil->red_flag)
                                <span style="color: red; font-weight: bold; cursor: help;" title="{{ $hasil->catatan_red_flag }}">Ya</span>
                            @else
                                <span style="color: gray;">Tidak</span>
                            @endif
                        </td>
                        
                        <td style="min-width: 170px;">
                            <a href="{{ route('pengukuran.create', $anak->id) }}" class="btn btn-success" style="padding: 0.25rem 0.5rem; font-size: 0.7rem;">+ Ukur</a>
                            <a href="{{ route('pengukuran.chart', $anak->id) }}" class="btn btn-primary" style="padding: 0.25rem 0.5rem; font-size: 0.7rem; background: #6b7280;">Grafik & Detail</a>
                            <button wire:click="deleteAnak({{ $anak->id }})" wire:confirm="Yakin ingin menghapus data anak ini? Riwayat pengukurannya juga akan terhapus secara permanen." class="btn btn-danger" style="padding: 0.25rem 0.5rem; font-size: 0.7rem; background: #ef4444; color: white; border: none; border-radius: 0.375rem; cursor: pointer;">Hapus</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--text-muted);">Belum ada data anak.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
