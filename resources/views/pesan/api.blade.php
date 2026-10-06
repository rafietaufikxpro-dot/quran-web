@extends('layouts.app')

@section('judul', 'Layanan Bermasalah — ' . config('app.name'))

@section('konten')
    {{-- Halaman pesan saat request ke API gagal (bukan "tidak ditemukan") --}}
    <div class="py-16 text-center">
        <h1 class="text-2xl font-extrabold tracking-tight judul-hijau">Terjadi Masalah</h1>
        <p class="mt-3 text-tinta-lembut">{{ $pesan ?? 'Layanan sedang tidak dapat dihubungi.' }}</p>
        <p class="mx-auto mt-1 max-w-lg text-xs leading-relaxed text-tinta-lembut">
            Periksa koneksi internet Anda, karena data diambil langsung dari API eQuran.id
            setiap kali halaman dibuka (tanpa cache).
        </p>
        <div class="mt-6 flex flex-wrap justify-center gap-2">
            <button type="button" class="tombol tombol-utama" onclick="location.reload()">Coba Lagi</button>
            <a href="{{ route('home') }}" class="tombol tombol-garis">Kembali ke Beranda</a>
        </div>
    </div>
@endsection
