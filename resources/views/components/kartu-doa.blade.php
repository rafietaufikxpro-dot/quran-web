@props(['doa'])

{{-- Kartu satu doa untuk halaman daftar doa --}}
<div class="kartu kartu-naik flex h-full flex-col p-4">
    <h2 class="text-base font-bold judul-hijau">{{ data_get($doa, 'nama') }}</h2>
    <p class="mt-1 text-xs text-tinta-lembut">{{ data_get($doa, 'grup') }}</p>
    <div class="mb-3 mt-2 grow">
        @foreach ((array) data_get($doa, 'tag', []) as $tag)
            <span class="lencana mr-1 mb-1 border border-krem-tua bg-white text-tinta-lembut">{{ $tag }}</span>
        @endforeach
    </div>
    <a href="{{ route('doa.show', data_get($doa, 'id')) }}" class="tombol tombol-garis tombol-kecil w-full">Baca Doa</a>
</div>
