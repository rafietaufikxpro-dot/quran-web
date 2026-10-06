<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('judul', config('app.name'))</title>

    {{-- Font Latin Plus Jakarta Sans (Google Fonts) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Tailwind CSS hasil build statis (perbarui dengan: npm run build:css) --}}
    <link href="{{ asset('css/tailwind.css') }}" rel="stylesheet">
</head>
<body class="flex min-h-screen flex-col bg-krem font-sans text-tinta antialiased">

    {{-- Navbar: hijau tua dengan pola geometri samar, tampil di semua halaman --}}
    <nav class="pola-islami sticky top-0 z-40 bg-hijau-tua shadow-md shadow-hijau-tua/30">
        <div class="wadah flex flex-wrap items-center justify-between gap-2 py-3">
            <a class="flex items-center gap-2.5 text-base font-extrabold tracking-tight text-krem"
               href="{{ route('home') }}">
                {{-- Lambang bintang delapan (khatam) --}}
                <svg class="h-6 w-6 text-emas" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="1.6" aria-hidden="true">
                    <rect x="6.5" y="6.5" width="11" height="11" rx="1.5"/>
                    <rect x="6.5" y="6.5" width="11" height="11" rx="1.5" transform="rotate(45 12 12)"/>
                </svg>
                {{ config('app.name') }}
            </a>

            {{-- Tombol menu untuk layar kecil --}}
            <button id="tombolMenu" type="button"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-lg text-krem transition hover:bg-white/10 md:hidden"
                    aria-controls="menuUtama" aria-expanded="false" aria-label="Buka menu">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                     stroke-linecap="round" aria-hidden="true">
                    <path d="M4 7h16M4 12h16M4 17h16"/>
                </svg>
            </button>

            <ul id="menuUtama" class="hidden w-full flex-col gap-1 md:flex md:w-auto md:flex-row md:items-center">
                <li>
                    <a class="tautan-nav {{ request()->routeIs('home') ? 'tautan-nav-aktif' : '' }}"
                       href="{{ route('home') }}">Beranda</a>
                </li>
                <li>
                    <a class="tautan-nav {{ request()->is('quran*') ? 'tautan-nav-aktif' : '' }}"
                       href="{{ route('quran.index') }}">Al-Qur'an</a>
                </li>
                <li>
                    <a class="tautan-nav {{ request()->is('doa*') ? 'tautan-nav-aktif' : '' }}"
                       href="{{ route('doa.index') }}">Doa Harian</a>
                </li>
                <li>
                    <a class="tautan-nav {{ request()->is('jadwal-sholat*') ? 'tautan-nav-aktif' : '' }}"
                       href="{{ route('jadwal.index') }}">Jadwal Sholat</a>
                </li>
            </ul>
        </div>
    </nav>

    {{-- Isi halaman --}}
    <main class="flex-1 py-8">
        <div class="wadah">
            @yield('konten')
        </div>
    </main>

    {{-- Footer: sumber data dan nama situs --}}
    <footer class="pola-islami mt-auto bg-hijau-tua py-8 text-center text-xs text-krem/75">
        <div class="wadah space-y-1">
            <p>Sumber data: Kementerian Agama RI &amp; eQuran.id</p>
            <p>{{ config('app.name') }} &mdash; website pendamping ibadah harian.</p>
        </div>
    </footer>

    {{-- Buka/tutup menu navigasi layar kecil --}}
    <script>
        (function () {
            const tombol = document.getElementById('tombolMenu');
            const menu = document.getElementById('menuUtama');

            if (!tombol || !menu) {
                return;
            }

            tombol.addEventListener('click', function () {
                menu.classList.toggle('hidden');
                tombol.setAttribute('aria-expanded', menu.classList.contains('hidden') ? 'false' : 'true');
            });
        })();
    </script>

    @stack('skrip')
</body>
</html>
