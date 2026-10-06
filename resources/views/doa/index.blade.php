@extends('layouts.app')

@section('judul', 'Doa Harian — ' . config('app.name'))

@section('konten')
    <div class="mb-4">
        <h1 class="h3 fw-bold judul-hijau mb-1">Doa Harian</h1>
        <p class="text-body-secondary mb-0">{{ $totalDoa }} doa pilihan untuk kegiatan sehari-hari.</p>
    </div>

    {{-- Pencarian dan filter (diproses di controller) --}}
    <div class="card kartu-layanan mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('doa.index') }}" class="row g-2">
                <div class="col-12 col-md-4">
                    <input type="search" name="q" value="{{ $kataKunci }}" class="form-control"
                           placeholder="Cari nama doa, mis. tidur" aria-label="Cari doa">
                </div>
                <div class="col-12 col-md-4">
                    <select name="grup" class="form-select" aria-label="Filter grup">
                        <option value="">Semua grup</option>
                        @foreach ($daftarGrup as $namaGrup)
                            <option value="{{ $namaGrup }}" @selected($namaGrup === $grupTerpilih)>{{ $namaGrup }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <select name="tag" class="form-select" aria-label="Filter tag">
                        <option value="">Semua tag</option>
                        @foreach ($daftarTag as $namaTag)
                            <option value="{{ $namaTag }}" @selected($namaTag === $tagTerpilih)>{{ $namaTag }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2 d-flex gap-2">
                    <button class="btn btn-hijau flex-grow-1" type="submit">Tampilkan</button>
                    <a class="btn btn-outline-hijau" href="{{ route('doa.index') }}">Reset</a>
                </div>
            </form>
        </div>
    </div>

    @if ($kataKunci !== '' || $grupTerpilih !== '' || $tagTerpilih !== '')
        <p class="text-body-secondary small">
            Hasil filter: {{ $jumlahHasil }} doa.
            <a href="{{ route('doa.index') }}">Tampilkan semua</a>
        </p>
    @endif

    @if ($jumlahHasil === 0)
        {{-- Pesan ramah saat tidak ada doa yang cocok --}}
        <div class="alert alert-warning kartu-layanan">
            Tidak ada doa yang cocok dengan filter tersebut.
            Coba kata kunci lain atau <a href="{{ route('doa.index') }}">tampilkan semua doa</a>.
        </div>
    @else
        <div class="row g-3">
            @foreach ($daftarDoa as $doa)
                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                    <x-kartu-doa :doa="$doa" />
                </div>
            @endforeach
        </div>

        {{-- Navigasi halaman daftar doa; pencarian dan filter ikut terbawa --}}
        @if ($totalHalaman > 1)
            <nav class="d-flex justify-content-between align-items-center mt-4" aria-label="Navigasi halaman daftar doa">
                @if ($halaman > 1)
                    <a class="btn btn-outline-hijau" href="{{ route('doa.index', array_filter(['q' => $kataKunci, 'grup' => $grupTerpilih, 'tag' => $tagTerpilih, 'halaman' => $halaman - 1])) }}">&laquo; Sebelumnya</a>
                @else
                    <span></span>
                @endif
                <span class="small text-body-secondary">Halaman {{ $halaman }} dari {{ $totalHalaman }}</span>
                @if ($halaman < $totalHalaman)
                    <a class="btn btn-outline-hijau" href="{{ route('doa.index', array_filter(['q' => $kataKunci, 'grup' => $grupTerpilih, 'tag' => $tagTerpilih, 'halaman' => $halaman + 1])) }}">Berikutnya &raquo;</a>
                @else
                    <span></span>
                @endif
            </nav>
        @endif
    @endif
@endsection
