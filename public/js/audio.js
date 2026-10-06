// Cahaya Harian — pengatur audio.
// Hanya satu audio yang boleh berbunyi: saat satu audio diputar,
// semua audio lain di halaman otomatis dihentikan.

document.addEventListener(
    'play',
    function (event) {
        const diputar = event.target;

        // Berlaku hanya untuk elemen audio milik pemutar halaman ini.
        if (!diputar.classList || !diputar.classList.contains('audio-item')) {
            return;
        }

        document.querySelectorAll('audio.audio-item').forEach(function (audio) {
            if (audio !== diputar) {
                audio.pause();
            }
        });
    },
    true // fase capture: event "play" tidak bubble, jadi ditangkap dari atas
);
