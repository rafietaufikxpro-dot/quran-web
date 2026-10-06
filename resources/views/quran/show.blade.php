@extends('layouts.app')

@section('judul', data_get($surat, 'namaLatin') . ' — ' . config('app.name'))

@section('konten')
    {{-- Informasi surat --}}
    <div class="card kartu-layanan mb-4">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h1 class="h4 fw-bold judul-hijau mb-1">
                        {{ data_get($surat, 'nomor') }}. {{ data_get($surat, 'namaLatin') }}
                    </h1>
                    <p class="text-body-secondary mb-0">
                        {{ data_get($surat, 'arti') }} &middot; {{ data_get($surat, 'jumlahAyat') }} ayat
                        &middot; {{ data_get($surat, 'tempatTurun') }}
                    </p>
                </div>
                <p class="teks-arab mb-0" dir="rtl" lang="ar">{{ data_get($surat, 'nama') }}</p>
            </div>
            <p class="small text-body-secondary mt-2 mb-0">{{ strip_tags((string) data_get($surat, 'deskripsi')) }}</p>
        </div>
    </div>

    {{-- Pilihan qari dan audio surat penuh --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-lg-5">
            <div class="card kartu-layanan h-100">
                <div class="card-body">
                    <h2 class="h6 fw-bold judul-hijau">Qari</h2>
                    <form method="GET" action="{{ route('quran.show', data_get($surat, 'nomor')) }}" class="d-flex gap-2">
                        <select name="qari" class="form-select" aria-label="Pilih qari">
                            @foreach (config('equran.qari') as $kunci => $namaQari)
                                <option value="{{ $kunci }}" @selected($kunci === $qari)>{{ $namaQari }}</option>
                            @endforeach
                        </select>
                        <button class="btn btn-hijau" type="submit">Ganti</button>
                    </form>
                    <p class="small text-body-secondary mt-2 mb-0">Qari aktif: {{ config('equran.qari.' . $qari) }}</p>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-7">
            <div class="card kartu-layanan h-100">
                <div class="card-body">
                    <h2 class="h6 fw-bold judul-hijau">Audio Surat Penuh</h2>
                    <x-pemutar-audio :sumber="data_get($surat, 'audioFull.' . $qari)" />
                </div>
            </div>
        </div>
    </div>

    {{-- Daftar ayat halaman ini --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <h2 class="h5 fw-bold judul-hijau mb-0">Ayat</h2>
        <span class="small text-body-secondary">
            Menampilkan ayat {{ ($halaman - 1) * config('equran.per_halaman') + 1 }}–{{ ($halaman - 1) * config('equran.per_halaman') + count($ayat) }}
            dari {{ data_get($surat, 'jumlahAyat') }}
        </span>
    </div>

    @foreach ($ayat as $itemAyat)
        <div class="card kartu-layanan mb-3">
            <div class="card-body">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                    <span class="badge rounded-pill text-bg-success">{{ data_get($itemAyat, 'nomorAyat') }}</span>
                    <x-pemutar-audio :sumber="data_get($itemAyat, 'audio.' . $qari)" ringkas />
                </div>
                <p class="teks-arab mb-3" dir="rtl" lang="ar">{{ data_get($itemAyat, 'teksArab') }}</p>
                <p class="fst-italic small text-body-secondary mb-2">{{ data_get($itemAyat, 'teksLatin') }}</p>
                <p class="mb-0">{{ data_get($itemAyat, 'teksIndonesia') }}</p>
            </div>
        </div>
    @endforeach

    {{-- Navigasi halaman ayat; pilihan qari ikut terbawa --}}
    <nav class="d-flex justify-content-between align-items-center mb-4" aria-label="Navigasi halaman ayat">
        @if ($halaman > 1)
            <a class="btn btn-outline-hijau" href="{{ route('quran.show', ['nomor' => data_get($surat, 'nomor'), 'qari' => $qari, 'halaman' => $halaman - 1]) }}">&laquo; Sebelumnya</a>
        @else
            <span></span>
        @endif
        <span class="small text-body-secondary">Halaman {{ $halaman }} dari {{ $totalHalaman }}</span>
        @if ($halaman < $totalHalaman)
            <a class="btn btn-outline-hijau" href="{{ route('quran.show', ['nomor' => data_get($surat, 'nomor'), 'qari' => $qari, 'halaman' => $halaman + 1]) }}">Berikutnya &raquo;</a>
        @else
            <span></span>
        @endif
    </nav>

    {{-- Navigasi surat sebelum/sesudah; datanya sudah disediakan API --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
        @php($suratSebelumnya = data_get($surat, 'suratSebelumnya'))
        @if ($suratSebelumnya)
            <a class="btn btn-outline-hijau" href="{{ route('quran.show', data_get($suratSebelumnya, 'nomor')) }}">
                &laquo; {{ data_get($suratSebelumnya, 'namaLatin') }}
            </a>
        @else
            <span></span>
        @endif

        <a class="btn btn-hijau" href="{{ route('quran.tafsir', data_get($surat, 'nomor')) }}">Lihat Tafsir</a>

        @php($suratSelanjutnya = data_get($surat, 'suratSelanjutnya'))
        @if ($suratSelanjutnya)
            <a class="btn btn-outline-hijau" href="{{ route('quran.show', data_get($suratSelanjutnya, 'nomor')) }}">
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
