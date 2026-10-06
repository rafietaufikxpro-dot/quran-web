@extends('layouts.app')

@section('judul', 'Jadwal Sholat — ' . config('app.name'))

@section('konten')
    <div class="mb-6">
        <h1 class="text-2xl font-extrabold tracking-tight judul-hijau sm:text-3xl">Jadwal Sholat</h1>
        <p class="mt-1 text-sm text-tinta-lembut">
            Jadwal imsak sampai isya untuk 34 provinsi di Indonesia,
            berdasarkan data Kementerian Agama RI.
        </p>
    </div>

    {{-- Pilihan lokasi, bulan, dan tahun; daftar kab/kota diisi oleh js/jadwal.js --}}
    <div class="kartu mb-4 p-5" id="kartuPilihJadwal">
        <form method="GET" action="{{ route('jadwal.index') }}" class="grid grid-cols-2 gap-4 md:grid-cols-12"
              data-url-kabkota="{{ route('jadwal.kabkota') }}">
            <div class="col-span-2 md:col-span-4">
                <label class="label-kolom" for="pilihProvinsi">Provinsi</label>
                <select name="provinsi" id="pilihProvinsi" class="kolom" required>
                    <option value="">— Pilih provinsi —</option>
                    @foreach ($daftarProvinsi as $namaProvinsi)
                        <option value="{{ $namaProvinsi }}" @selected($namaProvinsi === $provinsiTerpilih)>{{ $namaProvinsi }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-span-2 md:col-span-4">
                <label class="label-kolom" for="pilihKabkota">Kabupaten/Kota</label>
                <select name="kabkota" id="pilihKabkota" class="kolom disabled:bg-krem disabled:text-tinta-lembut" required disabled
                        data-terpilih="{{ $kabkotaTerpilih }}">
                    <option value="">— Pilih provinsi dulu —</option>
                </select>
                <div class="petunjuk-kolom" id="catatanKabkota">Pilih provinsi untuk memuat daftar kabupaten/kota.</div>
            </div>

            <div class="md:col-span-2">
                <label class="label-kolom" for="pilihBulan">Bulan</label>
                <select name="bulan" id="pilihBulan" class="kolom">
                    @foreach ($daftarBulan as $nomorBulan => $namaBulan)
                        <option value="{{ $nomorBulan }}" @selected($nomorBulan === $bulanTerpilih)>{{ $namaBulan }}</option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-2">
                <label class="label-kolom" for="pilihTahun">Tahun</label>
                <select name="tahun" id="pilihTahun" class="kolom">
                    @foreach ($daftarTahun as $nomorTahun)
                        <option value="{{ $nomorTahun }}" @selected($nomorTahun === $tahunTerpilih)>{{ $nomorTahun }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-span-2 flex gap-2">
                <button class="tombol tombol-utama" type="submit">Lihat Jadwal</button>
                <a class="tombol tombol-garis" href="{{ route('jadwal.index') }}">Reset</a>
            </div>
        </form>
    </div>

    {{-- Keterangan bila dipakai tahun tersedia terakhir (tahun sekarang belum ada) --}}
    @if ($keteranganTahun)
        <div class="pesan-kotak pesan-info mb-4">{{ $keteranganTahun }}</div>
    @endif

    {{-- Pesan bila tahun yang diminta tidak ada di daftar --}}
    @if ($pesanTahun)
        <div class="pesan-kotak pesan-peringatan mb-4">{{ $pesanTahun }}</div>
    @endif

    @if ($dataJadwal)
        <div class="kartu p-5">
            <div class="mb-4 flex flex-wrap items-start justify-between gap-4">
                <div>
                    <h2 class="text-lg font-bold judul-hijau">Jadwal {{ data_get($dataJadwal, 'kabkota') }}</h2>
                    <p class="mt-0.5 text-xs text-tinta-lembut">
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

            <div class="overflow-x-auto rounded-xl border border-krem-tua">
                <table class="tabel">
                    <thead>
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
                            <tr @class(['baris-hariini' => $iniHariIni])>
                                <td class="whitespace-nowrap">
                                    {{ data_get($baris, 'hari') }}, {{ data_get($baris, 'tanggal') }}
                                    {{ data_get($dataJadwal, 'bulan_nama') }} {{ data_get($dataJadwal, 'tahun') }}
                                    @if ($iniHariIni)
                                        <span class="lencana lencana-emas ml-1.5">Hari ini</span>
                                    @endif
                                </td>
                                <td>{{ data_get($baris, 'imsak') }}</td>
                                <td @class(['sel-berikutnya' => $iniHariIni && $namaWaktuDepan === 'subuh'])>{{ data_get($baris, 'subuh') }}</td>
                                <td>{{ data_get($baris, 'terbit') }}</td>
                                <td>{{ data_get($baris, 'dhuha') }}</td>
                                <td @class(['sel-berikutnya' => $iniHariIni && $namaWaktuDepan === 'dzuhur'])>{{ data_get($baris, 'dzuhur') }}</td>
                                <td @class(['sel-berikutnya' => $iniHariIni && $namaWaktuDepan === 'ashar'])>{{ data_get($baris, 'ashar') }}</td>
                                <td @class(['sel-berikutnya' => $iniHariIni && $namaWaktuDepan === 'maghrib'])>{{ data_get($baris, 'maghrib') }}</td>
                                <td @class(['sel-berikutnya' => $iniHariIni && $namaWaktuDepan === 'isya'])>{{ data_get($baris, 'isya') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($tampilkanHariIni)
                <p class="mt-3 text-xs text-tinta-lembut">
                    Baris kuning menandai hari ini; sel hijau menandai waktu sholat berikutnya.
                </p>
            @endif
        </div>
    @elseif (! $pesanTahun)
        {{-- Petunjuk awal saat lokasi belum dipilih lengkap --}}
        <div class="pesan-kotak pesan-info">
            Silakan pilih provinsi dan kabupaten/kota (lalu bulan dan tahun),
            kemudian klik <strong>Lihat Jadwal</strong>.
        </div>
    @endif
@endsection

@push('skrip')
    <script src="{{ asset('js/jadwal.js') }}"></script>
@endpush
