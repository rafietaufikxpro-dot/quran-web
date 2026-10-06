@extends('layouts.app')

@section('judul', 'Jadwal Sholat — ' . config('app.name'))

@section('konten')
    <div class="mb-4">
        <h1 class="h3 fw-bold judul-hijau mb-1">Jadwal Sholat</h1>
        <p class="text-body-secondary mb-0">
            Jadwal imsak sampai isya untuk 34 provinsi di Indonesia,
            berdasarkan data Kementerian Agama RI.
        </p>
    </div>

    {{-- Pilihan lokasi, bulan, dan tahun; daftar kab/kota diisi oleh js/jadwal.js --}}
    <div class="card kartu-layanan mb-4" id="kartuPilihJadwal">
        <div class="card-body">
            <form method="GET" action="{{ route('jadwal.index') }}" class="row g-3"
                  data-url-kabkota="{{ route('jadwal.kabkota') }}">
                <div class="col-12 col-md-4">
                    <label class="form-label small mb-1" for="pilihProvinsi">Provinsi</label>
                    <select name="provinsi" id="pilihProvinsi" class="form-select" required>
                        <option value="">— Pilih provinsi —</option>
                        @foreach ($daftarProvinsi as $namaProvinsi)
                            <option value="{{ $namaProvinsi }}" @selected($namaProvinsi === $provinsiTerpilih)>{{ $namaProvinsi }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label small mb-1" for="pilihKabkota">Kabupaten/Kota</label>
                    <select name="kabkota" id="pilihKabkota" class="form-select" required disabled
                            data-terpilih="{{ $kabkotaTerpilih }}">
                        <option value="">— Pilih provinsi dulu —</option>
                    </select>
                    <div class="form-text" id="catatanKabkota">Pilih provinsi untuk memuat daftar kabupaten/kota.</div>
                </div>

                <div class="col-6 col-md-2">
                    <label class="form-label small mb-1" for="pilihBulan">Bulan</label>
                    <select name="bulan" id="pilihBulan" class="form-select">
                        @foreach ($daftarBulan as $nomorBulan => $namaBulan)
                            <option value="{{ $nomorBulan }}" @selected($nomorBulan === $bulanTerpilih)>{{ $namaBulan }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 col-md-2">
                    <label class="form-label small mb-1" for="pilihTahun">Tahun</label>
                    <select name="tahun" id="pilihTahun" class="form-select">
                        @foreach ($daftarTahun as $nomorTahun)
                            <option value="{{ $nomorTahun }}" @selected($nomorTahun === $tahunTerpilih)>{{ $nomorTahun }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 d-flex gap-2">
                    <button class="btn btn-hijau" type="submit">Lihat Jadwal</button>
                    <a class="btn btn-outline-hijau" href="{{ route('jadwal.index') }}">Reset</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Keterangan bila dipakai tahun tersedia terakhir (tahun sekarang belum ada) --}}
    @if ($keteranganTahun)
        <div class="alert alert-info">{{ $keteranganTahun }}</div>
    @endif

    {{-- Pesan bila tahun yang diminta tidak ada di daftar --}}
    @if ($pesanTahun)
        <div class="alert alert-warning">{{ $pesanTahun }}</div>
    @endif

    @if ($dataJadwal)
        <div class="card kartu-layanan">
            <div class="card-body">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
                    <div>
                        <h2 class="h5 fw-bold judul-hijau mb-1">Jadwal {{ data_get($dataJadwal, 'kabkota') }}</h2>
                        <p class="text-body-secondary small mb-0">
                            {{ data_get($dataJadwal, 'provinsi') }} &middot;
                            {{ data_get($dataJadwal, 'bulan_nama') }} {{ data_get($dataJadwal, 'tahun') }}
                        </p>
                    </div>

                    {{-- Widget jam langsung + hitung mundur; hanya bila bulan/tahun terpilih = sekarang --}}
                    @if ($tampilkanHariIni)
                        <x-jam-sholat :provinsi="$provinsiTerpilih" :kabkota="$kabkotaTerpilih" :simpan="true"
                                      ganti-url="#kartuPilihJadwal" class="jam-sholat-ringkas" />
                    @endif
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0">
                        <thead class="table-success">
                            <tr>
                                <th scope="col">Tanggal</th>
                                <th scope="col">Imsak</th>
                                <th scope="col">Subuh</th>
                                <th scope="col">Terbit</th>
                                <th scope="col">Dhuha</th>
                                <th scope="col">Dzuhur</th>
                                <th scope="col">Ashar</th>
                                <th scope="col">Maghrib</th>
                                <th scope="col">Isya</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ((array) data_get($dataJadwal, 'jadwal', []) as $baris)
                                @php($iniHariIni = $tampilkanHariIni && data_get($baris, 'tanggal_lengkap') === $tanggalHariIni)
                                <tr @class(['table-warning fw-semibold' => $iniHariIni])>
                                    <td class="text-nowrap">
                                        {{ data_get($baris, 'hari') }}, {{ data_get($baris, 'tanggal') }}
                                        {{ data_get($dataJadwal, 'bulan_nama') }} {{ data_get($dataJadwal, 'tahun') }}
                                        @if ($iniHariIni)
                                            <span class="badge text-bg-warning ms-1">Hari ini</span>
                                        @endif
                                    </td>
                                    <td>{{ data_get($baris, 'imsak') }}</td>
                                    <td @class(['bg-success-subtle' => $iniHariIni && $namaWaktuDepan === 'subuh'])>{{ data_get($baris, 'subuh') }}</td>
                                    <td>{{ data_get($baris, 'terbit') }}</td>
                                    <td>{{ data_get($baris, 'dhuha') }}</td>
                                    <td @class(['bg-success-subtle' => $iniHariIni && $namaWaktuDepan === 'dzuhur'])>{{ data_get($baris, 'dzuhur') }}</td>
                                    <td @class(['bg-success-subtle' => $iniHariIni && $namaWaktuDepan === 'ashar'])>{{ data_get($baris, 'ashar') }}</td>
                                    <td @class(['bg-success-subtle' => $iniHariIni && $namaWaktuDepan === 'maghrib'])>{{ data_get($baris, 'maghrib') }}</td>
                                    <td @class(['bg-success-subtle' => $iniHariIni && $namaWaktuDepan === 'isya'])>{{ data_get($baris, 'isya') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($tampilkanHariIni)
                    <p class="small text-body-secondary mt-3 mb-0">
                        Baris kuning menandai hari ini; sel hijau menandai waktu sholat berikutnya.
                    </p>
                @endif
            </div>
        </div>
    @elseif (! $pesanTahun)
        {{-- Petunjuk awal saat lokasi belum dipilih lengkap --}}
        <div class="alert alert-info kartu-layanan mb-0">
            Silakan pilih provinsi dan kabupaten/kota (lalu bulan dan tahun),
            kemudian klik <strong>Lihat Jadwal</strong>.
        </div>
    @endif
@endsection

@push('skrip')
    <script src="{{ asset('js/jadwal.js') }}"></script>
@endpush
