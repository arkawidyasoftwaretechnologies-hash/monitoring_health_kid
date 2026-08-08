<div style="min-height: 100vh; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #f8fafc, #e2e8f0);">
    <div style="background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); padding: 3rem 2rem; border-radius: 1rem; box-shadow: 0 10px 25px rgba(0,0,0,0.05); width: 100%; max-width: 400px; border: 1px solid rgba(255,255,255,1);">
        
        <div style="text-align: center; margin-bottom: 2rem;">
            <div style="display: inline-flex; align-items: center; justify-content: center; background: linear-gradient(135deg, var(--primary), var(--secondary)); border-radius: 12px; padding: 12px; margin-bottom: 1rem; box-shadow: 0 4px 6px rgba(79, 70, 229, 0.2);">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            </div>
            <h2 style="font-size: 1.5rem; font-weight: 800; color: #1e293b; margin: 0;">Kid Health</h2>
            <p style="color: #64748b; font-size: 0.9rem; margin-top: 0.5rem;">Silakan masuk ke akun Anda</p>
        </div>

        <form wire:submit="login" style="display: flex; flex-direction: column; gap: 1.5rem;">
            <div>
                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #475569; margin-bottom: 0.5rem;">Email Address</label>
                <input type="email" wire:model="email" class="form-control" style="width: 100%; padding: 0.75rem 1rem; border-radius: 0.5rem; border: 1px solid #cbd5e1; font-size: 0.95rem; background: #fff;" required autofocus>
                @error('email') <span style="color: #ef4444; font-size: 0.8rem; margin-top: 0.25rem; display: block;">{{ $message }}</span> @enderror
            </div>

            <div>
                <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #475569; margin-bottom: 0.5rem;">Password</label>
                <input type="password" wire:model="password" class="form-control" style="width: 100%; padding: 0.75rem 1rem; border-radius: 0.5rem; border: 1px solid #cbd5e1; font-size: 0.95rem; background: #fff;" required>
                @error('password') <span style="color: #ef4444; font-size: 0.8rem; margin-top: 0.25rem; display: block;">{{ $message }}</span> @enderror
            </div>

            <div style="display: flex; align-items: center; justify-content: space-between;">
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                    <input type="checkbox" wire:model="remember" style="width: auto !important; margin: 0;">
                    <span style="font-size: 0.85rem; color: #64748b;">Ingat saya</span>
                </label>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.8rem; border-radius: 0.5rem; font-weight: 600; font-size: 1rem; margin-top: 0.5rem;">
                <span wire:loading.remove wire:target="login">Masuk</span>
                <span wire:loading wire:target="login">Memproses...</span>
            </button>
            
            <div style="margin-top: 1rem; text-align: center; font-size: 0.8rem; color: #94a3b8;">
                Akun Demo:<br>
                admin@klinik.com | bidan@klinik.com | dokter@klinik.com<br>
                (password: password)
            </div>
        </form>
    </div>
</div>
