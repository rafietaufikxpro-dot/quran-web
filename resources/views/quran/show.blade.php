@extends('layouts.app')

@section('judul', data_get($surat, 'namaLatin') . ' — ' . config('app.name'))

@section('konten')
    {{-- Informasi surat --}}
    <div class="kartu mb-4 p-5 sm:p-6">
        <div class="flex flex-wrap items-center justify-between gap-x-4 gap-y-3">
            <div>
                <h1 class="text-xl font-extrabold tracking-tight judul-hijau sm:text-2xl">
                    {{ data_get($surat, 'nomor') }}. {{ data_get($surat, 'namaLatin') }}
                </h1>
                <p class="mt-1 text-sm text-tinta-lembut">
                    {{ data_get($surat, 'arti') }} &middot; {{ data_get($surat, 'jumlahAyat') }} ayat
                    &middot; {{ data_get($surat, 'tempatTurun') }}
                </p>
            </div>
            <p class="teks-arab" dir="rtl" lang="ar">{{ data_get($surat, 'nama') }}</p>
        </div>
        <p class="mt-3 text-xs leading-relaxed text-tinta-lembut">{{ strip_tags((string) data_get($surat, 'deskripsi')) }}</p>
    </div>

    {{-- Pilihan qari dan audio surat penuh --}}
    <div class="mb-6 grid grid-cols-1 gap-4 lg:grid-cols-12">
        <div class="lg:col-span-5">
            <div class="kartu h-full p-5 sm:p-6">
                <h2 class="text-sm font-bold judul-hijau">Qari</h2>
                <form method="GET" action="{{ route('quran.show', data_get($surat, 'nomor')) }}" class="mt-3 flex gap-2">
                    <select name="qari" class="kolom" aria-label="Pilih qari">
                        @foreach (config('equran.qari') as $kunci => $namaQari)
                            <option value="{{ $kunci }}" @selected($kunci === $qari)>{{ $namaQari }}</option>
                        @endforeach
                    </select>
                    <button class="tombol tombol-utama" type="submit">Ganti</button>
                </form>
                <p class="mt-3 text-xs text-tinta-lembut">Qari aktif: {{ config('equran.qari.' . $qari) }}</p>
            </div>
        </div>
        <div class="lg:col-span-7">
            <div class="kartu h-full p-5 sm:p-6">
                <h2 class="text-sm font-bold judul-hijau">Audio Surat Penuh</h2>
                <div class="mt-3">
                    <x-pemutar-audio :sumber="data_get($surat, 'audioFull.' . $qari)" />
                </div>
            </div>
        </div>
    </div>

    {{-- Daftar ayat halaman ini --}}
    <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
        <h2 class="text-lg font-bold judul-hijau">Ayat</h2>
        <span class="text-sm text-tinta-lembut">
            Menampilkan ayat {{ ($halaman - 1) * config('equran.per_halaman') + 1 }}–{{ ($halaman - 1) * config('equran.per_halaman') + count($ayat) }}
            dari {{ data_get($surat, 'jumlahAyat') }}
        </span>
    </div>

    @foreach ($ayat as $itemAyat)
        <article class="kartu mb-3 p-5 sm:p-6">
            <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                <span class="lencana lencana-hijau">{{ data_get($itemAyat, 'nomorAyat') }}</span>
                <x-pemutar-audio :sumber="data_get($itemAyat, 'audio.' . $qari)" ringkas />
            </div>
            <p class="teks-arab mb-3" dir="rtl" lang="ar">{{ data_get($itemAyat, 'teksArab') }}</p>
            <p class="mb-2 text-sm italic text-tinta-lembut">{{ data_get($itemAyat, 'teksLatin') }}</p>
            <p class="mb-0 leading-relaxed">{{ data_get($itemAyat, 'teksIndonesia') }}</p>
        </article>
    @endforeach

    {{-- Navigasi halaman ayat; pilihan qari ikut terbawa --}}
    <nav class="mt-6 mb-6 flex items-center justify-between gap-3" aria-label="Navigasi halaman ayat">
        @if ($halaman > 1)
            <a class="tombol tombol-garis" href="{{ route('quran.show', ['nomor' => data_get($surat, 'nomor'), 'qari' => $qari, 'halaman' => $halaman - 1]) }}">&laquo; Sebelumnya</a>
        @else
            <span></span>
        @endif
        <span class="text-sm text-tinta-lembut">Halaman {{ $halaman }} dari {{ $totalHalaman }}</span>
        @if ($halaman < $totalHalaman)
            <a class="tombol tombol-garis" href="{{ route('quran.show', ['nomor' => data_get($surat, 'nomor'), 'qari' => $qari, 'halaman' => $halaman + 1]) }}">Berikutnya &raquo;</a>
        @else
            <span></span>
        @endif
    </nav>

    {{-- Navigasi surat sebelum/sesudah; datanya sudah disediakan API --}}
    <div class="flex flex-wrap items-center justify-between gap-2">
        @php($suratSebelumnya = data_get($surat, 'suratSebelumnya'))
        @if ($suratSebelumnya)
            <a class="tombol tombol-garis" href="{{ route('quran.show', data_get($suratSebelumnya, 'nomor')) }}">
                &laquo; {{ data_get($suratSebelumnya, 'namaLatin') }}
            </a>
        @else
            <span></span>
        @endif

        <a class="tombol tombol-utama" href="{{ route('quran.tafsir', data_get($surat, 'nomor')) }}">Lihat Tafsir</a>

        @php($suratSelanjutnya = data_get($surat, 'suratSelanjutnya'))
        @if ($suratSelanjutnya)
            <a class="tombol tombol-garis" href="{{ route('quran.show', data_get($suratSelanjutnya, 'nomor')) }}">
                {{ data_get($suratSelanjutnya, 'namaLatin') }} &raquo;
            </a>
        @else
            <span></span>
        @endif
    </div>
@endsection

@push('skrip')
    <script src="{{ asset('js/audio.js') }}"></script>
@endpush
