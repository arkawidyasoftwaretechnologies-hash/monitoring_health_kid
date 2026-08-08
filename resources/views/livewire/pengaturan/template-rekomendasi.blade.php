<div style="padding: 1rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div>
            <h2 style="font-size: 1.5rem; font-weight: 800; color: #1e293b; margin: 0;">Pengaturan Template Rekam Medis</h2>
            <p style="color: #64748b; margin: 0.2rem 0 0 0; font-size: 0.9rem;">Kelola opsi checkbox Assessment & Plan untuk form dokter.</p>
        </div>
        <button wire:click="create()" class="btn btn-primary" style="display: flex; align-items: center; gap: 0.5rem;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Tambah Template
        </button>
    </div>

    @if (session()->has('message'))
        <div style="background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem;">
            {{ session('message') }}
        </div>
    @endif

    <!-- Tabel Data -->
    <div style="background: white; border-radius: 0.75rem; box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1); border: 1px solid #e2e8f0; overflow: hidden;">
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                        <th style="padding: 1rem; font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; width: 50px;">No</th>
                        <th style="padding: 1rem; font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Nama Template</th>
                        <th style="padding: 1rem; font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Kode (Pemicu)</th>
                        <th style="padding: 1rem; font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Teks Assessment</th>
                        <th style="padding: 1rem; font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Teks Plan</th>
                        <th style="padding: 1rem; font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; text-align: center;">Status</th>
                        <th style="padding: 1rem; font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; text-align: right; width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($templates as $index => $template)
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 1rem; color: #64748b; font-size: 0.9rem;">{{ $index + 1 }}</td>
                            <td style="padding: 1rem; color: #334155; font-weight: 600; font-size: 0.9rem;">{{ $template->nama_template }}</td>
                            <td style="padding: 1rem; color: #64748b; font-size: 0.8rem;"><code>{{ $template->kondisi_pemicu }}</code></td>
                            <td style="padding: 1rem; color: #475569; font-size: 0.85rem;">{{ Str::limit($template->template_assessment, 50) }}</td>
                            <td style="padding: 1rem; color: #475569; font-size: 0.85rem;">{{ Str::limit($template->template_plan, 50) }}</td>
                            <td style="padding: 1rem; text-align: center;">
                                @if($template->aktif)
                                    <span style="background: #ecfdf5; color: #059669; padding: 0.2rem 0.5rem; border-radius: 9999px; font-size: 0.7rem; font-weight: 600;">Aktif</span>
                                @else
                                    <span style="background: #f1f5f9; color: #64748b; padding: 0.2rem 0.5rem; border-radius: 9999px; font-size: 0.7rem; font-weight: 600;">Nonaktif</span>
                                @endif
                            </td>
                            <td style="padding: 1rem; text-align: right; display: flex; justify-content: flex-end; gap: 0.5rem;">
                                <button wire:click="edit({{ $template->id }})" class="btn btn-secondary" style="padding: 0.4rem 0.6rem; font-size: 0.8rem;">Edit</button>
                                <button wire:click="delete({{ $template->id }})" wire:confirm="Yakin ingin menghapus template ini?" class="btn" style="padding: 0.4rem 0.6rem; font-size: 0.8rem; background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5;">Hapus</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding: 2rem; text-align: center; color: #64748b;">Belum ada data template.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($templates->hasPages())
            <div style="padding: 1rem; border-top: 1px solid #e2e8f0;">
                {{ $templates->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Form -->
    @if($isModalOpen)
        <div style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 100;">
            <div style="background: white; border-radius: 0.75rem; width: 100%; max-width: 600px; padding: 2rem; box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1); max-height: 90vh; overflow-y: auto;">
                <h3 style="font-size: 1.25rem; font-weight: 700; color: #1e293b; margin-top: 0; margin-bottom: 1.5rem;">{{ $template_id ? 'Edit Template' : 'Tambah Template' }}</h3>
                
                <form wire:submit.prevent="store">
                    <div class="form-group" style="margin-bottom: 1rem;">
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #475569; margin-bottom: 0.3rem;">Nama Template (Tampil di Layar Dokter)</label>
                        <input type="text" wire:model="nama_template" class="form-control" style="width: 100%;" placeholder="Contoh: Pertumbuhan Normal" required>
                        @error('nama_template') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group" style="margin-bottom: 1rem;">
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #475569; margin-bottom: 0.3rem;">Kode Kondisi Pemicu Sistem</label>
                        <input type="text" wire:model="kondisi_pemicu" class="form-control" style="width: 100%; font-family: monospace; font-size: 0.8rem;" placeholder="Contoh: stunting_berat" required>
                        <small style="color: #64748b; font-size: 0.7rem;">Kode yang dihasilkan oleh Red Flag Log otomatis. (contoh: normal, underweight, stunting, obesitas)</small>
                        @error('kondisi_pemicu') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group" style="margin-bottom: 1rem;">
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #475569; margin-bottom: 0.3rem;">Teks Assessment (Analisis)</label>
                        <textarea wire:model="template_assessment" class="form-control" style="width: 100%; min-height: 80px;" placeholder="Contoh: Pertumbuhan sesuai kurva WHO..." required></textarea>
                        @error('template_assessment') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group" style="margin-bottom: 1rem;">
                        <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #475569; margin-bottom: 0.3rem;">Teks Plan (Tindak Lanjut)</label>
                        <textarea wire:model="template_plan" class="form-control" style="width: 100%; min-height: 80px;" placeholder="Contoh: Lanjutkan stimulasi perkembangan..." required></textarea>
                        @error('template_plan') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group" style="margin-bottom: 1.5rem;">
                            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #475569; margin-bottom: 0.3rem;">Urutan Prioritas (Sortir)</label>
                            <input type="number" wire:model="urutan_prioritas" class="form-control" style="width: 100%;">
                        </div>

                        <div class="form-group" style="margin-bottom: 1.5rem; display: flex; align-items: center; margin-top: 1.5rem;">
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <input type="checkbox" wire:model="aktif">
                                <span style="font-size: 0.85rem; font-weight: 600; color: #475569;">Template Aktif</span>
                            </label>
                        </div>
                    </div>

                    <div style="display: flex; justify-content: flex-end; gap: 1rem; border-top: 1px solid #e2e8f0; padding-top: 1.5rem;">
                        <button type="button" wire:click="closeModal()" class="btn btn-secondary">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
