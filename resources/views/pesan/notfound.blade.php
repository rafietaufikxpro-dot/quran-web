@extends('layouts.app')

@section('judul', 'Tidak Ditemukan — ' . config('app.name'))

@section('konten')
    {{-- Halaman pesan saat data tidak ditemukan (dipanggil dari controller) --}}
    <div class="py-16 text-center">
        <h1 class="text-2xl font-extrabold tracking-tight judul-hijau">Mohon Maaf</h1>
        <p class="mt-3 text-tinta-lembut">{{ $pesan ?? 'Data yang Anda cari tidak ditemukan.' }}</p>
        <a href="{{ route('home') }}" class="tombol tombol-utama mt-6">Kembali ke Beranda</a>
    </div>
@endsection
