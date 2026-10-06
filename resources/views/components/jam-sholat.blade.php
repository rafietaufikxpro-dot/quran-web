{{--
    Widget jam langsung + waktu sholat berikutnya.
    Dipakai di beranda dan di halaman jadwal sholat (saat menampilkan bulan berjalan).
    Isinya diisi oleh public/js/jam-sholat.js lewat endpoint internal
    GET /jadwal-sholat/hari-ini; lokasi pilihan pengguna disimpan di browser.
--}}
@props([
    'provinsi' => null,
    'kabkota' => null,
    'simpan' => false,
    'gantiUrl' => null,
])

@php
    // Bila halaman pemanggil tidak mengirim lokasi, pakai lokasi bawaan dari config.
    $provinsi = $provinsi ?: config('equran.lokasi_default.provinsi');
    $kabkota = $kabkota ?: config('equran.lokasi_default.kabkota');
    $alamatGanti = $gantiUrl ?: route('jadwal.index');
@endphp

<div id="jamSholat" {{ $attributes->merge(['class' => 'rounded-2xl border border-krem-tua px-4 py-3']) }}
     data-url="{{ route('jadwal.hari-ini') }}"
     data-provinsi="{{ $provinsi }}"
     data-kabkota="{{ $kabkota }}"
     data-simpan="{{ $simpan ? '1' : '0' }}">
    <div class="flex flex-wrap items-center justify-between gap-x-3 gap-y-1">
        <span class="text-xs text-tinta-lembut">
            <span id="jamSholatLokasi" class="font-bold text-hijau-tua">{{ $kabkota }}</span>
            (<a id="jamSholatGanti" class="font-semibold text-hijau hover:underline" href="{{ $alamatGanti }}">Ganti</a>)
        </span>
        <span id="jamSholatSekarang" class="text-xs text-tinta-lembut tabular-nums">Sekarang --:--:-- WIB</span>
    </div>

    <p id="jamSholatBerikutnya" class="mt-1 text-2xl font-extrabold tracking-tight text-hijau-tua tabular-nums">Memuat jadwal&hellip;</p>
    <p id="jamSholatHitungMundur" class="text-base font-medium text-tinta-lembut tabular-nums">&mdash;</p>
    <p id="jamSholatPesan" class="mt-1 hidden text-xs font-medium text-rose-600"></p>
</div>

@push('skrip')
    @once
        <script src="{{ asset('js/jam-sholat.js') }}"></script>
    @endonce
@endpush
