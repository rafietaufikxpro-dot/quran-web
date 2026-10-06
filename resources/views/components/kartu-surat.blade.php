@props(['surat'])

{{-- Kartu satu surat untuk halaman daftar surat --}}
<div class="kartu kartu-naik flex h-full flex-col p-4">
    <div class="flex items-start justify-between gap-2">
        <span class="lencana lencana-hijau">{{ data_get($surat, 'nomor') }}</span>
        <span class="text-right text-xs text-tinta-lembut">
            {{ data_get($surat, 'tempatTurun') }} &middot; {{ data_get($surat, 'jumlahAyat') }} ayat
        </span>
    </div>
    <h2 class="mt-3 text-base font-bold judul-hijau">{{ data_get($surat, 'namaLatin') }}</h2>
    <p class="teks-arab mt-1 mb-2" dir="rtl" lang="ar">{{ data_get($surat, 'nama') }}</p>
    <p class="mb-3 grow text-sm leading-relaxed text-tinta-lembut">{{ data_get($surat, 'arti') }}</p>
    <a href="{{ route('quran.show', data_get($surat, 'nomor')) }}" class="tombol tombol-garis tombol-kecil w-full">Baca Surat</a>
</div>
