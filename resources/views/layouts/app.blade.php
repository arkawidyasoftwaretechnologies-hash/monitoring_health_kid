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
<body>
    <header style="justify-content: flex-start; gap: 3rem;">
        <div class="logo">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
            Health Kid Monitoring
        </div>
        <nav style="display: flex; align-items: center; gap: 2rem;">
            <ul>
                <li><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li><a href="{{ route('anak.index') }}">Data Anak</a></li>
                <li><a href="{{ route('laporan.index') }}">Laporan</a></li>
            </ul>
            <button id="theme-toggle" class="btn" style="background: var(--surface); color: var(--text-main); border: 1px solid var(--border); border-radius: 50%; width: 40px; height: 40px; padding: 0; display: flex; align-items: center; justify-content: center;" aria-label="Toggle Dark Mode">
                <!-- Moon icon (default) -->
                <svg id="theme-icon-moon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: block;"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
                <!-- Sun icon (hidden by default) -->
                <svg id="theme-icon-sun" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>
            </button>
        </nav>
    </header>

    <script>
        const themeToggleBtn = document.getElementById('theme-toggle');
        const themeIconMoon = document.getElementById('theme-icon-moon');
        const themeIconSun = document.getElementById('theme-icon-sun');
        const rootElement = document.documentElement;

        // Check local storage or OS preference
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

    <main class="container">
        {{ $slot }}
    </main>

    @livewireScripts
</body>
</html>
