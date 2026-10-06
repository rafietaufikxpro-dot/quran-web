<?php

// Konfigurasi API eQuran.id.
// Nilai yang mungkin diubah guru dikumpulkan di sini,
// supaya tidak perlu mengedit kode di controller.
return [
    // Alamat dasar API eQuran.id (dapat diubah lewat file .env).
    'base_url' => env('EQURAN_BASE_URL', 'https://equran.id'),

    // Qari yang dipakai sebagai pilihan awal untuk audio ayat.
    'default_qari' => '05',

    // Jumlah item per halaman (ayat, daftar surat, dan daftar doa).
    'per_halaman' => 20,

    // Zona waktu untuk menyorot jadwal sholat "hari ini".
    'zona_waktu' => 'Asia/Jakarta',

    // Lokasi awal widget jam & jadwal sholat berikutnya (sebelum pengguna memilih).
    'lokasi_default' => [
        'provinsi' => 'DKI Jakarta',
        'kabkota' => 'Kota Jakarta',
    ],

    // Tahun yang tersedia untuk jadwal sholat (sesuai cakupan data API).
    'tahun_jadwal' => [2026],

    // Daftar qari; kuncinya sama dengan kunci audio pada API.
    'qari' => [
        '01' => 'Abdullah Al-Juhany',
        '02' => 'Abdul Muhsin Al-Qasim',
        '03' => 'Abdurrahman As-Sudais',
        '04' => 'Ibrahim Al-Dossari',
        '05' => 'Misyari Rasyid Al-Afasy',
        '06' => 'Yasser Al-Dosari',
    ],
];
