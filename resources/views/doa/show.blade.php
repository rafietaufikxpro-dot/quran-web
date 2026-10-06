@extends('layouts.app')

@section('judul', data_get($doa, 'nama') . ' — ' . config('app.name'))

@section('konten')
    {{-- Nama, grup, dan tag doa --}}
    <div class="kartu mb-6 p-5">
        <h1 class="text-xl font-extrabold tracking-tight judul-hijau">{{ data_get($doa, 'nama') }}</h1>
        <p class="mt-1 text-sm text-tinta-lembut">{{ data_get($doa, 'grup') }}</p>
        <div class="mt-2">
            @foreach ((array) data_get($doa, 'tag', []) as $tag)
                <span class="lencana mr-1 mb-1 border border-krem-tua bg-white text-tinta-lembut">{{ $tag }}</span>
            @endforeach
        </div>
    </div>

    {{-- Teks Arab doa --}}
    <div class="kartu mb-3 p-5">
        <p class="teks-arab teks-multibaris mb-0" dir="rtl" lang="ar">{{ data_get($doa, 'ar') }}</p>
    </div>

    {{-- Transliterasi --}}
    <div class="kartu mb-3 p-5">
        <h2 class="text-base font-bold judul-hijau">Transliterasi</h2>
        <p class="teks-multibaris mt-2 italic leading-relaxed">{{ data_get($doa, 'tr') }}</p>
    </div>

    {{-- Arti / terjemahan --}}
    <div class="kartu mb-3 p-5">
        <h2 class="text-base font-bold judul-hijau">Arti</h2>
        <p class="teks-multibaris mt-2 leading-relaxed">{{ data_get($doa, 'idn') }}</p>
    </div>

    {{-- Keterangan tambahan (bila ada dari API) --}}
    @if (filled(data_get($doa, 'tentang')))
        <div class="kartu mb-6 p-5">
            <h2 class="text-base font-bold judul-hijau">Keterangan</h2>
            <p class="teks-multibaris mt-2 leading-relaxed">{{ data_get($doa, 'tentang') }}</p>
        </div>
    @endif

    <div class="text-center">
        <a class="tombol tombol-utama" href="{{ route('doa.index') }}">Kembali ke Daftar Doa</a>
    </div>
@endsection
