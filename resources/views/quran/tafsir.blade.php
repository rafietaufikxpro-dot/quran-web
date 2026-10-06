@extends('layouts.app')

@section('judul', 'Tafsir ' . data_get($surat, 'namaLatin') . ' — ' . config('app.name'))

@section('konten')
    {{-- Info singkat surat yang sedang dibuka tafsirnya --}}
    <div class="kartu mb-6 flex flex-wrap items-center justify-between gap-3 p-5">
        <div>
            <h1 class="text-xl font-extrabold tracking-tight judul-hijau">Tafsir {{ data_get($surat, 'namaLatin') }}</h1>
            <p class="mt-1 text-sm text-tinta-lembut">
                {{ data_get($surat, 'arti') }} &middot; {{ data_get($surat, 'jumlahAyat') }} ayat
            </p>
        </div>
        <p class="teks-arab mb-0" dir="rtl" lang="ar">{{ data_get($surat, 'nama') }}</p>
    </div>

    {{-- Daftar tafsir ayat pada halaman ini (20 tafsir per halaman) --}}
    @foreach ($tafsirAyat as $itemTafsir)
        <article class="kartu mb-3 p-5">
            <h2 class="mb-2 text-base font-bold judul-hijau">Tafsir Ayat {{ data_get($itemTafsir, 'ayat') }}</h2>
            <p class="leading-relaxed">{{ data_get($itemTafsir, 'teks') }}</p>
        </article>
    @endforeach

    {{-- Navigasi halaman tafsir --}}
    <nav class="mb-6 mt-8 flex items-center justify-between gap-3" aria-label="Navigasi halaman tafsir">
        @if ($halaman > 1)
            <a class="tombol tombol-garis" href="{{ route('quran.tafsir', ['nomor' => data_get($surat, 'nomor'), 'halaman' => $halaman - 1]) }}">&laquo; Sebelumnya</a>
        @else
            <span></span>
        @endif
        <span class="text-sm text-tinta-lembut">Halaman {{ $halaman }} dari {{ $totalHalaman }}</span>
        @if ($halaman < $totalHalaman)
            <a class="tombol tombol-garis" href="{{ route('quran.tafsir', ['nomor' => data_get($surat, 'nomor'), 'halaman' => $halaman + 1]) }}">Berikutnya &raquo;</a>
        @else
            <span></span>
        @endif
    </nav>

    <div class="text-center">
        <a class="tombol tombol-utama" href="{{ route('quran.show', data_get($surat, 'nomor')) }}">Kembali ke Ayat</a>
    </div>
@endsection
