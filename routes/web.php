<?php

use App\Http\Controllers\DoaController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JadwalSholatController;
use App\Http\Controllers\QuranController;
use Illuminate\Support\Facades\Route;

// Beranda
Route::get('/', [HomeController::class, 'index'])->name('home');

// Al-Qur'an: daftar surat, detail surat (ayat + audio), dan tafsir
Route::get('/quran', [QuranController::class, 'index'])->name('quran.index');
Route::get('/quran/{nomor}', [QuranController::class, 'show'])->whereNumber('nomor')->name('quran.show');
Route::get('/quran/{nomor}/tafsir', [QuranController::class, 'tafsir'])->whereNumber('nomor')->name('quran.tafsir');

// Doa Harian: daftar doa (dengan pencarian dan filter) dan detail doa
Route::get('/doa', [DoaController::class, 'index'])->name('doa.index');
Route::get('/doa/{id}', [DoaController::class, 'show'])->whereNumber('id')->name('doa.show');

// Jadwal Sholat: pilih lokasi (kab/kota dinamis), bulan, dan tahun
Route::get('/jadwal-sholat', [JadwalSholatController::class, 'index'])->name('jadwal.index');

// Endpoint internal untuk widget jam: daftar kab/kota dan waktu sholat berikutnya
Route::get('/jadwal-sholat/kabkota', [JadwalSholatController::class, 'kabkota'])->name('jadwal.kabkota');
Route::get('/jadwal-sholat/hari-ini', [JadwalSholatController::class, 'hariIni'])->name('jadwal.hari-ini');

// URL yang tidak dikenal → halaman 404 khusus (bukan halaman bawaan Laravel)
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});
