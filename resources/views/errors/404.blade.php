{{-- Halaman 404 khusus, menggantikan halaman error bawaan Laravel --}}
@extends('layouts.app')

@section('judul', 'Halaman Tidak Ditemukan — ' . config('app.name'))

@section('konten')
    <div class="py-16 text-center">
        <p class="text-6xl font-extrabold tracking-tight judul-hijau">404</p>
        <h1 class="mt-4 text-xl font-bold">Halaman Tidak Ditemukan</h1>
        <p class="mx-auto mt-2 max-w-md text-sm leading-relaxed text-tinta-lembut">
            Alamat yang Anda tuju tidak ada di website ini. Periksa kembali tautannya, ya.
        </p>
        <a href="{{ route('home') }}" class="tombol tombol-utama mt-6">Kembali ke Beranda</a>
    </div>
@endsection
