<div class="animate-fade-in">
    <div class="page-header">
        <div>
            <h1 class="page-title">
                <span class="page-title-icon">👶</span> 
                {{ $anak_id ? 'Edit Data Anak' : 'Tambah Data Anak' }}
            </h1>
            <p class="page-subtitle">Lengkapi formulir identitas anak di bawah ini</p>
        </div>
    </div>

    <div class="glass-panel" style="max-width: 800px; margin: 0; padding: 2rem;">
    <form wire:submit.prevent="submit">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
            
            <div class="form-group" style="grid-column: 1 / -1;">
                <label style="color: var(--text-main); font-weight: 600;">Nama Lengkap <span style="color: #e74c3c;">*</span></label>
                <input type="text" wire:model="nama" placeholder="Masukkan nama anak" required 
                       style="background: rgba(255,255,255,0.05); border: 1px solid var(--border); color: var(--text-main); border-radius: 8px; padding: 12px; width: 100%;">
                @error('nama') <span style="color: #e74c3c; font-size: 0.8rem; margin-top: 0.3rem; display: block;">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label style="color: var(--text-main); font-weight: 600;">NIK</label>
                <input type="text" wire:model="nik" placeholder="Masukkan NIK (opsional)" 
                       style="background: rgba(255,255,255,0.05); border: 1px solid var(--border); color: var(--text-main); border-radius: 8px; padding: 12px; width: 100%;">
                @error('nik') <span style="color: #e74c3c; font-size: 0.8rem; margin-top: 0.3rem; display: block;">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label style="color: var(--text-main); font-weight: 600;">Tanggal Lahir <span style="color: #e74c3c;">*</span></label>
                <input type="date" wire:model="tanggal_lahir" required 
                       style="background: rgba(255,255,255,0.05); border: 1px solid var(--border); color: var(--text-main); border-radius: 8px; padding: 12px; width: 100%;">
                @error('tanggal_lahir') <span style="color: #e74c3c; font-size: 0.8rem; margin-top: 0.3rem; display: block;">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label style="color: var(--text-main); font-weight: 600;">Jenis Kelamin <span style="color: #e74c3c;">*</span></label>
                <select wire:model="jenis_kelamin" required 
                        style="background: rgba(255,255,255,0.05); border: 1px solid var(--border); color: var(--text-main); border-radius: 8px; padding: 12px; width: 100%; cursor: pointer;">
                    <option value="L">Laki-laki</option>
                    <option value="P">Perempuan</option>
                </select>
            </div>

            <div class="form-group">
                <label style="color: var(--text-main); font-weight: 600;">Nama Orang Tua</label>
                <input type="text" wire:model="nama_ortu" placeholder="Nama ayah/ibu" 
                       style="background: rgba(255,255,255,0.05); border: 1px solid var(--border); color: var(--text-main); border-radius: 8px; padding: 12px; width: 100%;">
            </div>

            <div class="form-group" style="grid-column: 1 / -1;">
                <label style="color: var(--text-main); font-weight: 600;">Alamat</label>
                <textarea wire:model="alamat" rows="3" placeholder="Alamat domisili anak" 
                          style="background: rgba(255,255,255,0.05); border: 1px solid var(--border); color: var(--text-main); border-radius: 8px; padding: 12px; width: 100%;"></textarea>
            </div>
        </div>

        <div style="margin-top: 2rem; display: flex; gap: 1rem; justify-content: flex-end; border-top: 1px solid rgba(52,152,219,0.3); padding-top: 1.5rem;">
            <a href="{{ route('anak.index') }}" style="padding: 12px 24px; border-radius: 8px; background: rgba(149,165,166,0.15); color: var(--text-muted); text-decoration: none; font-weight: 600; border: 1px solid rgba(149,165,166,0.3);">
                Batal
            </a>
            <button type="submit" style="padding: 12px 24px; border-radius: 8px; background: linear-gradient(135deg, #3498db, #2980b9); color: white; border: none; font-weight: 600; cursor: pointer; box-shadow: 0 4px 6px rgba(52,152,219,0.3);">
                Simpan Data
            </button>
        </div>
    </form>
</div>
