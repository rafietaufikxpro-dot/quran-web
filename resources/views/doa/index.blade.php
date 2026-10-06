@extends('layouts.app')

@section('judul', 'Doa Harian — ' . config('app.name'))

@section('konten')
    <div class="mb-6">
        <h1 class="text-2xl font-extrabold tracking-tight judul-hijau sm:text-3xl">Doa Harian</h1>
        <p class="mt-1 text-sm text-tinta-lembut">{{ $totalDoa }} doa pilihan untuk kegiatan sehari-hari.</p>
    </div>

    {{-- Pencarian dan filter (diproses di controller) --}}
    <div class="kartu mb-4 p-5">
        <form method="GET" action="{{ route('doa.index') }}" class="grid grid-cols-1 gap-3 md:grid-cols-12">
            <div class="md:col-span-4">
                <input type="search" name="q" value="{{ $kataKunci }}" class="kolom"
                       placeholder="Cari nama doa, mis. tidur" aria-label="Cari doa">
            </div>
            <div class="md:col-span-4">
                <select name="grup" class="kolom" aria-label="Filter grup">
                    <option value="">Semua grup</option>
                    @foreach ($daftarGrup as $namaGrup)
                        <option value="{{ $namaGrup }}" @selected($namaGrup === $grupTerpilih)>{{ $namaGrup }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2">
                <select name="tag" class="kolom" aria-label="Filter tag">
                    <option value="">Semua tag</option>
                    @foreach ($daftarTag as $namaTag)
                        <option value="{{ $namaTag }}" @selected($namaTag === $tagTerpilih)>{{ $namaTag }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2 md:col-span-2">
                <button class="tombol tombol-utama grow" type="submit">Tampilkan</button>
                <a class="tombol tombol-garis" href="{{ route('doa.index') }}">Reset</a>
            </div>
        </form>
    </div>

    @if ($kataKunci !== '' || $grupTerpilih !== '' || $tagTerpilih !== '')
        <p class="mb-4 text-sm text-tinta-lembut">
            Hasil filter: {{ $jumlahHasil }} doa.
            <a href="{{ route('doa.index') }}" class="tautan">Tampilkan semua</a>
        </p>
    @endif

    @if ($jumlahHasil === 0)
        {{-- Pesan ramah saat tidak ada doa yang cocok --}}
        <div class="pesan-kotak pesan-peringatan">
            Tidak ada doa yang cocok dengan filter tersebut.
            Coba kata kunci lain atau <a href="{{ route('doa.index') }}" class="font-bold underline underline-offset-2">tampilkan semua doa</a>.
        </div>
    @else
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
            @foreach ($daftarDoa as $doa)
                <x-kartu-doa :doa="$doa" />
            @endforeach
        </div>

        {{-- Navigasi halaman daftar doa; pencarian dan filter ikut terbawa --}}
        @if ($totalHalaman > 1)
            <nav class="mt-8 flex items-center justify-between gap-3" aria-label="Navigasi halaman daftar doa">
                @if ($halaman > 1)
                    <a class="tombol tombol-garis" href="{{ route('doa.index', array_filter(['q' => $kataKunci, 'grup' => $grupTerpilih, 'tag' => $tagTerpilih, 'halaman' => $halaman - 1])) }}">&laquo; Sebelumnya</a>
                @else
                    <span></span>
                @endif
                <span class="text-sm text-tinta-lembut">Halaman {{ $halaman }} dari {{ $totalHalaman }}</span>
                @if ($halaman < $totalHalaman)
                    <a class="tombol tombol-garis" href="{{ route('doa.index', array_filter(['q' => $kataKunci, 'grup' => $grupTerpilih, 'tag' => $tagTerpilih, 'halaman' => $halaman + 1])) }}">Berikutnya &raquo;</a>
                @else
                    <span></span>
                @endif
            </nav>
        @endif
    @endif
@endsection
