<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring Stunting Kid Health</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @livewireStyles
</head>
<style>
    .app-layout { display: flex; flex-direction: row; min-height: 100vh; margin: 0; padding: 0; }
    .app-sidebar {
        width: 260px; flex-shrink: 0; background: var(--surface-glass); backdrop-filter: blur(16px); 
        -webkit-backdrop-filter: blur(16px); border-right: 1px solid rgba(255, 255, 255, 0.4); 
        padding: 2rem 1.5rem; display: flex; flex-direction: column; position: sticky; top: 0; 
        height: 100vh; z-index: 50; box-shadow: var(--shadow-sm);
    }
    .app-main {
        flex: 1; padding: 2rem; overflow-x: hidden; overflow-y: auto; width: calc(100% - 260px);
    }
    .app-container {
        margin: 0 auto; padding: 0; max-width: 100%; /* Full width */
    }
    @media (max-width: 992px) {
        .app-layout { flex-direction: column; }
        .app-sidebar { width: 100%; height: auto; position: relative; padding: 1rem; border-right: none; border-bottom: 1px solid rgba(255,255,255,0.2); }
        .app-main { width: 100%; padding: 1rem; }
    }
</style>
<body class="app-layout">
    
    <!-- Sidebar Kiri -->
    <aside class="app-sidebar">
        <!-- Logo -->
        <div class="logo" style="margin-bottom: 2rem; display: flex; align-items: center; gap: 0.75rem; font-size: 1.3rem; font-weight: 800; background: linear-gradient(135deg, var(--primary), var(--secondary)); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
            <div style="background: linear-gradient(135deg, var(--primary), var(--secondary)); border-radius: 8px; padding: 6px; display: flex; align-items: center; justify-content: center;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            </div>
            Kid Health
        </div>

        <!-- Menu Navigasi -->
        <nav style="flex: 1;">
            <ul style="list-style: none; display: flex; flex-direction: column; gap: 0.5rem; padding: 0;">
                <li>
                    <a href="{{ route('dashboard') }}" style="display: flex; align-items: center; gap: 1rem; padding: 0.8rem 1rem; border-radius: 0.5rem; text-decoration: none; color: {{ request()->routeIs('dashboard') ? 'var(--primary)' : 'var(--text-main)' }}; background: {{ request()->routeIs('dashboard') ? 'rgba(79, 70, 229, 0.1)' : 'transparent' }}; font-weight: {{ request()->routeIs('dashboard') ? '700' : '500' }}; transition: all 0.2s;">
                        <span style="font-size: 1.2rem;">📊</span> Dashboard
                    </a>
                </li>
                <li x-data="{ open: {{ (request()->routeIs('anak.*') || request()->routeIs('pengukuran.*')) ? 'true' : 'false' }} }">
                    <div style="display: flex; flex-direction: column;">
                        <div style="display: flex; align-items: center; justify-content: space-between; border-radius: 0.5rem; background: {{ (request()->routeIs('anak.*') || request()->routeIs('pengukuran.*')) ? 'rgba(79, 70, 229, 0.1)' : 'transparent' }}; transition: all 0.2s;">
                            <a href="{{ route('anak.index') }}" style="flex: 1; display: flex; align-items: center; gap: 1rem; padding: 0.8rem 1rem; text-decoration: none; color: {{ (request()->routeIs('anak.*') || request()->routeIs('pengukuran.*')) ? 'var(--primary)' : 'var(--text-main)' }}; font-weight: {{ (request()->routeIs('anak.*') || request()->routeIs('pengukuran.*')) ? '700' : '500' }};">
                                <span style="font-size: 1.2rem;">👶</span> Data Anak
                            </a>
                            <button @click.prevent="open = !open" style="background: transparent; border: none; padding: 0.8rem; cursor: pointer; color: var(--text-muted); display: flex; align-items: center; justify-content: center;">
                                <svg x-show="!open" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                <svg x-show="open" style="display: none;" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15"></polyline></svg>
                            </button>
                        </div>
                        
                        <ul x-show="open" style="display: none; list-style: none; padding-left: 2.8rem; margin-top: 0.5rem; flex-direction: column; gap: 0.3rem;" x-transition>
                            <li>
                                <a href="{{ route('anak.index') }}" style="text-decoration: none; font-size: 0.9rem; color: {{ request()->routeIs('anak.index') ? 'var(--primary)' : 'var(--text-muted)' }}; font-weight: {{ request()->routeIs('anak.index') ? '700' : '500' }}; display: block; padding: 0.4rem 0; transition: color 0.2s;">📋 Daftar Anak</a>
                            </li>
                            @if(!Auth::user()->isDokter())
                            <li>
                                <a href="{{ route('anak.create') }}" style="text-decoration: none; font-size: 0.9rem; color: {{ request()->routeIs('anak.create') ? 'var(--primary)' : 'var(--text-muted)' }}; font-weight: {{ request()->routeIs('anak.create') ? '700' : '500' }}; display: block; padding: 0.4rem 0; transition: color 0.2s;">➕ Tambah Anak Baru</a>
                            </li>
                            @endif
                            
                            @php
                                $ctxAnak = null;
                                $ctxPengukuran = null;
                                if(request()->route('anak')) {
                                    $id = is_object(request()->route('anak')) ? request()->route('anak')->id : request()->route('anak');
                                    $ctxAnak = \App\Models\Anak::find($id);
                                } elseif(request()->route('pengukuran')) {
                                    $pId = is_object(request()->route('pengukuran')) ? request()->route('pengukuran')->id : request()->route('pengukuran');
                                    $p = \App\Models\Pengukuran::find($pId);
                                    $ctxPengukuran = $p;
                                    $ctxAnak = $p ? $p->anak : null;
                                }
                                
                                if ($ctxAnak && !$ctxPengukuran) {
                                    $ctxPengukuran = $ctxAnak->pengukurans()->latest('tanggal_ukur')->first();
                                }
                            @endphp
                            
                            @if($ctxAnak)
                                <li style="margin-top: 0.5rem; border-top: 1px dashed var(--border); padding-top: 0.5rem;">
                                    <span style="font-size: 0.7rem; color: var(--text-muted); text-transform: uppercase; font-weight: 800; letter-spacing: 0.05em;">Aksi Khusus:</span>
                                    <div style="font-size: 0.75rem; color: var(--text-main); font-weight: 600; margin-bottom: 0.3rem; margin-top: 0.2rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $ctxAnak->nama }}</div>
                                </li>
                                @if(!Auth::user()->isDokter())
                                <li>
                                    <a href="{{ route('anak.edit', $ctxAnak->id) }}" style="text-decoration: none; font-size: 0.9rem; color: {{ request()->routeIs('anak.edit') ? 'var(--primary)' : 'var(--text-muted)' }}; font-weight: {{ request()->routeIs('anak.edit') ? '700' : '500' }}; display: block; padding: 0.4rem 0; transition: color 0.2s;">✏️ Edit Profil</a>
                                </li>
                                <li>
                                    <a href="{{ route('pengukuran.create', $ctxAnak->id) }}" style="text-decoration: none; font-size: 0.9rem; color: {{ request()->routeIs('pengukuran.create') && !request()->route('pengukuran') ? 'var(--primary)' : 'var(--text-muted)' }}; font-weight: {{ request()->routeIs('pengukuran.create') && !request()->route('pengukuran') ? '700' : '500' }}; display: block; padding: 0.4rem 0; transition: color 0.2s;">📏 Ukur Baru</a>
                                </li>
                                @endif
                                <li>
                                    <a href="{{ route('pengukuran.chart', $ctxAnak->id) }}" style="text-decoration: none; font-size: 0.9rem; color: {{ request()->routeIs('pengukuran.chart') ? 'var(--primary)' : 'var(--text-muted)' }}; font-weight: {{ request()->routeIs('pengukuran.chart') ? '700' : '500' }}; display: block; padding: 0.4rem 0; transition: color 0.2s;">📈 Grafik Tumbuh</a>
                                </li>
                                
                                @if($ctxPengukuran)
                                <li>
                                    <a href="{{ route('pengukuran.edit', $ctxPengukuran->id) }}" style="text-decoration: none; font-size: 0.9rem; color: {{ request()->routeIs('pengukuran.edit') ? 'var(--primary)' : 'var(--text-muted)' }}; font-weight: {{ request()->routeIs('pengukuran.edit') ? '700' : '500' }}; display: block; padding: 0.4rem 0; transition: color 0.2s;">
                                        {{ Auth::user()->isDokter() ? '🩺 Analisis Medis' : '✍️ Edit Ukur' }}
                                    </a>
                                </li>
                                
                                    @if($ctxPengukuran->assessmentPlan)
                                    <li>
                                        <a href="{{ route('cetak.medis', $ctxPengukuran->id) }}" target="_blank" style="text-decoration: none; font-size: 0.9rem; color: var(--text-muted); font-weight: 500; display: block; padding: 0.4rem 0; transition: color 0.2s;">🖨️ Cetak Medis</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('cetak.orangtua', $ctxPengukuran->id) }}" target="_blank" style="text-decoration: none; font-size: 0.9rem; color: var(--text-muted); font-weight: 500; display: block; padding: 0.4rem 0; transition: color 0.2s;">🖨️ Cetak Ortu</a>
                                    </li>
                                    @endif
                                @endif
                            @endif
                        </ul>
                    </div>
                </li>
                <li>
                    <a href="{{ route('laporan.index') }}" style="display: flex; align-items: center; gap: 1rem; padding: 0.8rem 1rem; border-radius: 0.5rem; text-decoration: none; color: {{ request()->routeIs('laporan.*') ? 'var(--primary)' : 'var(--text-main)' }}; background: {{ request()->routeIs('laporan.*') ? 'rgba(79, 70, 229, 0.1)' : 'transparent' }}; font-weight: {{ request()->routeIs('laporan.*') ? '700' : '500' }}; transition: all 0.2s;">
                        <span style="font-size: 1.2rem;">📄</span> Laporan
                    </a>
                </li>
                
                @if(Auth::check() && (Auth::user()->isAdmin() || Auth::user()->isDokter()))
                <li style="margin-top: 1rem;">
                    <span style="font-size: 0.7rem; text-transform: uppercase; color: var(--text-muted); font-weight: 800; padding-left: 1rem; letter-spacing: 1px;">Kustomisasi Dokter</span>
                </li>
                <li>
                    <a href="{{ route('pengaturan.template') }}" style="display: flex; align-items: center; gap: 1rem; padding: 0.8rem 1rem; border-radius: 0.5rem; text-decoration: none; color: {{ request()->routeIs('pengaturan.template') ? 'var(--primary)' : 'var(--text-main)' }}; background: {{ request()->routeIs('pengaturan.template') ? 'rgba(79, 70, 229, 0.1)' : 'transparent' }}; font-weight: {{ request()->routeIs('pengaturan.template') ? '700' : '500' }}; transition: all 0.2s;">
                        <span style="font-size: 1.2rem;">⚙️</span> Template Medis
                    </a>
                </li>
                @endif
            </ul>
        </nav>

        <!-- Toggle Mode Gelap -->
        <div style="border-top: 1px solid rgba(255,255,255,0.2); padding-top: 1.5rem; margin-top: 1.5rem; display: flex; align-items: center; justify-content: space-between;">
            <span style="font-size: 0.9rem; font-weight: 500; color: var(--text-muted);">Mode Tampilan</span>
            <button id="theme-toggle" class="btn" style="background: rgba(255,255,255,0.1); color: var(--text-main); border: 1px solid var(--border); border-radius: 50%; width: 40px; height: 40px; padding: 0; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s;" aria-label="Toggle Dark Mode">
                <svg id="theme-icon-moon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: block;"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
                <svg id="theme-icon-sun" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>
            </button>
        </div>

        @auth
        <!-- Profil & Logout -->
        <div style="border-top: 1px solid rgba(255,255,255,0.2); padding-top: 1.5rem; margin-top: 1rem; display: flex; flex-direction: column; gap: 0.8rem;">
            <div style="display: flex; align-items: center; gap: 0.8rem;">
                <div style="width: 36px; height: 36px; border-radius: 50%; background: var(--primary); color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 1rem;">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div style="display: flex; flex-direction: column; overflow: hidden;">
                    <span style="font-size: 0.9rem; font-weight: 700; color: var(--text-main); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ Auth::user()->name }}</span>
                    <span style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase;">{{ Auth::user()->role ? Auth::user()->role->name : 'Staff' }}</span>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                @csrf
                <button type="submit" style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 0.5rem; background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2); padding: 0.6rem; border-radius: 0.5rem; font-weight: 600; font-size: 0.85rem; cursor: pointer; transition: all 0.2s;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                    Keluar Sistem
                </button>
            </form>
        </div>
        @endauth
    </aside>

    <script>
        const themeToggleBtn = document.getElementById('theme-toggle');
        const themeIconMoon = document.getElementById('theme-icon-moon');
        const themeIconSun = document.getElementById('theme-icon-sun');
        const rootElement = document.documentElement;

        const currentTheme = localStorage.getItem('theme') || 
            (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
        
        if (currentTheme === 'dark') {
            rootElement.setAttribute('data-theme', 'dark');
            themeIconMoon.style.display = 'none';
            themeIconSun.style.display = 'block';
        }

        themeToggleBtn.addEventListener('click', () => {
            let theme = rootElement.getAttribute('data-theme');
            if (theme === 'dark') {
                rootElement.removeAttribute('data-theme');
                localStorage.setItem('theme', 'light');
                themeIconMoon.style.display = 'block';
                themeIconSun.style.display = 'none';
            } else {
                rootElement.setAttribute('data-theme', 'dark');
                localStorage.setItem('theme', 'dark');
                themeIconMoon.style.display = 'none';
                themeIconSun.style.display = 'block';
            }
        });
    </script>

    <!-- Konten Utama -->
    <main class="app-main">
        <div class="app-container">
            {{ $slot }}
        </div>
    </main>

    @livewireScripts
</body>
</html>
