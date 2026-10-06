@extends('layouts.app')

@section('judul', "Al-Qur'an — " . config('app.name'))

@section('konten')
    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight judul-hijau sm:text-3xl">Al-Qur'an</h1>
            <p class="mt-1 text-sm text-tinta-lembut">114 surat lengkap dengan terjemahan, tafsir, dan audio.</p>
        </div>

        {{-- Pencarian nama/arti/nomor surat (disaring di controller) --}}
        <form method="GET" action="{{ route('quran.index') }}" class="flex w-full gap-2 sm:w-auto" role="search">
            <input type="search" name="q" value="{{ $kataKunci }}" class="kolom sm:w-72"
                   placeholder="Cari surat, mis. al fatihah / 36" aria-label="Cari surat">
            <button class="tombol tombol-utama" type="submit">Cari</button>
        </form>
    </div>

    @if ($kataKunci !== '')
        <p class="mb-4 text-sm text-tinta-lembut">
            Hasil pencarian "{{ $kataKunci }}": {{ $jumlahHasil }} surat.
            <a href="{{ route('quran.index') }}" class="tautan">Tampilkan semua</a>
        </p>
    @endif

    @if ($jumlahHasil === 0)
        {{-- Pesan ramah saat pencarian tidak menghasilkan apa pun --}}
        <div class="pesan-kotak pesan-peringatan">
            Tidak ada surat yang cocok dengan pencarian "{{ $kataKunci }}".
            Coba kata lain, misalnya <em>fatihah</em> atau <em>36</em>.
        </div>
    @else
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
            @foreach ($daftarSurat as $surat)
                <x-kartu-surat :surat="$surat" />
            @endforeach
        </div>

        {{-- Navigasi halaman daftar surat; kata kunci pencarian ikut terbawa --}}
        @if ($totalHalaman > 1)
            <nav class="mt-8 flex items-center justify-between gap-3" aria-label="Navigasi halaman daftar surat">
                @if ($halaman > 1)
                    <a class="tombol tombol-garis" href="{{ route('quran.index', array_filter(['q' => $kataKunci, 'halaman' => $halaman - 1])) }}">&laquo; Sebelumnya</a>
                @else
                    <span></span>
                @endif
                <span class="text-sm text-tinta-lembut">Halaman {{ $halaman }} dari {{ $totalHalaman }}</span>
                @if ($halaman < $totalHalaman)
                    <a class="tombol tombol-garis" href="{{ route('quran.index', array_filter(['q' => $kataKunci, 'halaman' => $halaman + 1])) }}">Berikutnya &raquo;</a>
                @else
                    <span></span>
                @endif
            </nav>
        @endif
    @endif
@endsection
