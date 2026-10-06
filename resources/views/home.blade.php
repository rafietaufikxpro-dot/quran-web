@extends('layouts.app')

@section('judul', 'Beranda — ' . config('app.name'))

@section('konten')
    {{-- Pembuka: bismillah, nama situs, dan widget jam --}}
    <section class="pola-islami rounded-3xl bg-hijau-tua px-6 py-12 text-center sm:px-10">
        <p class="font-arab text-3xl leading-relaxed text-emas/90" dir="rtl" lang="ar">بِسْمِ اللّٰهِ الرَّحْمٰنِ الرَّحِيْمِ</p>
        <h1 class="mt-5 text-3xl font-extrabold tracking-tight text-white sm:text-4xl">{{ config('app.name') }}</h1>
        <p class="mx-auto mt-3 max-w-xl text-sm leading-relaxed text-krem/80 sm:text-base">
            Pendamping ibadah harian Anda: baca dan dengarkan Al-Qur'an, temukan doa harian,
            dan lihat jadwal sholat &mdash; semuanya dalam satu tempat.
        </p>

        {{-- Widget jam langsung + waktu sholat berikutnya --}}
        <div class="mt-8 flex justify-center">
            <x-jam-sholat class="jam-sholat-beranda" />
        </div>
    </section>

    {{-- Tiga kartu tautan layanan --}}
    <div class="mt-8 grid grid-cols-1 gap-5 md:grid-cols-3">
        <div class="kartu kartu-naik flex flex-col p-6 text-center">
            <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-hijau-lembut text-hijau-tua">
                <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"
                     stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
                    <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                </svg>
            </span>
            <h2 class="mt-4 text-lg font-bold judul-hijau">Al-Qur'an</h2>
            <p class="mt-2 grow text-sm leading-relaxed text-tinta-lembut">
                Baca 114 surat lengkap dengan teks Arab, transliterasi Latin,
                terjemahan, tafsir, dan audio dari 6 qari.
            </p>
            <a href="{{ route('quran.index') }}" class="tombol tombol-utama mt-5 w-full">Buka Al-Qur'an</a>
        </div>

        <div class="kartu kartu-naik flex flex-col p-6 text-center">
            <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-hijau-lembut text-hijau-tua">
                <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"
                     stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1-1.1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21l7.8-7.6 1-1a5.5 5.5 0 0 0 0-7.8z"/>
                </svg>
            </span>
            <h2 class="mt-4 text-lg font-bold judul-hijau">Doa Harian</h2>
            <p class="mt-2 grow text-sm leading-relaxed text-tinta-lembut">
                Temukan 227 doa dan dzikir sehari-hari: teks Arab berharakat,
                Latin, terjemahan, dan sumbernya. Bisa difilter per grup dan tag.
            </p>
            <a href="{{ route('doa.index') }}" class="tombol tombol-utama mt-5 w-full">Buka Doa Harian</a>
        </div>

        <div class="kartu kartu-naik flex flex-col p-6 text-center">
            <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-hijau-lembut text-hijau-tua">
                <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"
                     stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                </svg>
            </span>
            <h2 class="mt-4 text-lg font-bold judul-hijau">Jadwal Sholat</h2>
            <p class="mt-2 grow text-sm leading-relaxed text-tinta-lembut">
                Lihat jadwal imsak sampai isya untuk 34 provinsi dan
                517 kabupaten/kota di seluruh Indonesia.
            </p>
            <a href="{{ route('jadwal.index') }}" class="tombol tombol-utama mt-5 w-full">Buka Jadwal Sholat</a>
        </div>
    </div>
@endsection
