@props(['sumber' => null, 'ringkas' => false])

{{-- Pemutar audio dengan preload="none" agar file tidak diunduh otomatis.
     Semua pemutar memakai kelas "audio-item"; audio.js akan menghentikan
     audio lain begitu satu audio diputar. --}}
@if (filled($sumber))
    <audio controls preload="none" class="audio-item {{ $ringkas ? 'audio-ringkas' : 'w-full' }}" src="{{ $sumber }}"></audio>
@else
    <span class="text-xs text-tinta-lembut">Audio tidak tersedia</span>
@endif
