<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header">
        <h2 class="card-title">Tambah Data Anak</h2>
    </div>

    <form wire:submit.prevent="submit" style="margin-top: 1rem;">
        <div class="form-group">
            <label>Nama Lengkap</label>
            <input type="text" wire:model="nama" placeholder="Masukkan nama anak" required>
            @error('nama') <span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label>NIK</label>
            <input type="text" wire:model="nik" placeholder="Masukkan NIK">
            @error('nik') <span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label>Tanggal Lahir</label>
            <input type="date" wire:model="tanggal_lahir" required>
            @error('tanggal_lahir') <span style="color: var(--danger); font-size: 0.8rem;">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label>Jenis Kelamin</label>
            <select wire:model="jenis_kelamin" required>
                <option value="L">Laki-laki</option>
                <option value="P">Perempuan</option>
            </select>
        </div>

        <div class="form-group">
            <label>Nama Orang Tua</label>
            <input type="text" wire:model="nama_ortu">
        </div>

        <div class="form-group">
            <label>Alamat</label>
            <textarea wire:model="alamat" rows="3"></textarea>
        </div>

        <div style="margin-top: 2rem; display: flex; gap: 1rem;">
            <button type="submit" class="btn btn-primary">Simpan Data</button>
            <a href="{{ route('anak.index') }}" class="btn" style="background: #e5e7eb;">Batal</a>
        </div>
    </form>
</div>
