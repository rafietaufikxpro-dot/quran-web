<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class QuranController extends Controller
{
    /**
     * Menampilkan daftar 114 surat dengan pencarian sederhana.
     * Pencarian (?q=) disaring di sini memakai data dari API, bukan di API.
     */
    public function index(Request $request): View|Response
    {
        try {
            // Minta daftar surat ke API eQuran.id
            $response = Http::timeout(10)->get(config('equran.base_url').'/api/v2/surat');
        } catch (ConnectionException $e) {
            return response()->view('pesan.api', ['pesan' => 'Layanan tidak dapat dihubungi.'], 503);
        }

        // Kegagalan layanan selain "tidak ditemukan" dianggap gangguan server
        if ($response->failed() && $response->status() !== 404) {
            return response()->view('pesan.api', ['pesan' => 'Terjadi kesalahan pada layanan.'], 502);
        }

        $daftarSurat = $response->json('data');

        // Tidak ditemukan: status 404, code 404 pada body, atau data kosong
        if ($response->status() === 404 || $response->json('code') === 404 || empty($daftarSurat)) {
            return response()->view('pesan.notfound', ['pesan' => 'Daftar surat tidak ditemukan.'], 404);
        }

        // Kata kunci pencarian hanya dipakai bila berbentuk teks
        $kataKunci = $request->query('q');
        $kataKunci = is_string($kataKunci) ? trim($kataKunci) : '';

        // Saring dulu, baru bagi menjadi halaman (20 surat per halaman)
        $hasilSaring = $this->saringSurat($daftarSurat, $kataKunci);
        $perHalaman = (int) config('equran.per_halaman');
        $totalHalaman = max(1, (int) ceil(count($hasilSaring) / $perHalaman));
        $halaman = min(max(1, $request->integer('halaman', 1)), $totalHalaman);

        return view('quran.index', [
            'daftarSurat' => array_slice($hasilSaring, ($halaman - 1) * $perHalaman, $perHalaman),
            'kataKunci' => $kataKunci,
            'jumlahHasil' => count($hasilSaring),
            'halaman' => $halaman,
            'totalHalaman' => $totalHalaman,
        ]);
    }

    /**
     * Menampilkan detail satu surat: informasi, ayat (20 per halaman), dan audio.
     * Qari dipilih lewat ?qari=, halaman lewat ?halaman=.
     */
    public function show(Request $request, int $nomor): View|Response
    {
        // Nomor surat hanya 1-114; di luar itu langsung 404 tanpa memanggil API
        abort_unless($nomor >= 1 && $nomor <= 114, 404);

        try {
            $response = Http::timeout(10)->get(config('equran.base_url')."/api/v2/surat/{$nomor}");
        } catch (ConnectionException $e) {
            return response()->view('pesan.api', ['pesan' => 'Layanan tidak dapat dihubungi.'], 503);
        }

        if ($response->failed() && $response->status() !== 404) {
            return response()->view('pesan.api', ['pesan' => 'Terjadi kesalahan pada layanan.'], 502);
        }

        if ($response->status() === 404 || $response->json('code') === 404 || empty($response->json('data'))) {
            return response()->view('pesan.notfound', ['pesan' => "Surat nomor {$nomor} tidak ditemukan."], 404);
        }

        $surat = $response->json('data');

        // Pilihan qari (?qari=). Jika tidak dikenal, kembali ke qari default.
        $qari = $request->query('qari');
        if (! is_string($qari) || ! array_key_exists($qari, config('equran.qari'))) {
            $qari = config('equran.default_qari');
        }

        // Pembagian halaman hanya memotong DAFTAR ayat; isi teks ayat tidak diubah
        $daftarAyat = data_get($surat, 'ayat', []);
        $perHalaman = (int) config('equran.per_halaman');
        $totalHalaman = max(1, (int) ceil(count($daftarAyat) / $perHalaman));
        $halaman = min(max(1, $request->integer('halaman', 1)), $totalHalaman);
        $ayat = array_slice($daftarAyat, ($halaman - 1) * $perHalaman, $perHalaman);

        return view('quran.show', [
            'surat' => $surat,
            'ayat' => $ayat,
            'qari' => $qari,
            'halaman' => $halaman,
            'totalHalaman' => $totalHalaman,
        ]);
    }

    /**
     * Menampilkan tafsir satu surat, juga dibagi 20 ayat per halaman.
     */
    public function tafsir(Request $request, int $nomor): View|Response
    {
        abort_unless($nomor >= 1 && $nomor <= 114, 404);

        try {
            $response = Http::timeout(10)->get(config('equran.base_url')."/api/v2/tafsir/{$nomor}");
        } catch (ConnectionException $e) {
            return response()->view('pesan.api', ['pesan' => 'Layanan tidak dapat dihubungi.'], 503);
        }

        if ($response->failed() && $response->status() !== 404) {
            return response()->view('pesan.api', ['pesan' => 'Terjadi kesalahan pada layanan.'], 502);
        }

        if ($response->status() === 404 || $response->json('code') === 404 || empty($response->json('data'))) {
            return response()->view('pesan.notfound', ['pesan' => "Tafsir surat nomor {$nomor} tidak ditemukan."], 404);
        }

        $surat = $response->json('data');

        // Tafsir juga dipotong 20 ayat per halaman
        $daftarTafsir = data_get($surat, 'tafsir', []);
        $perHalaman = (int) config('equran.per_halaman');
        $totalHalaman = max(1, (int) ceil(count($daftarTafsir) / $perHalaman));
        $halaman = min(max(1, $request->integer('halaman', 1)), $totalHalaman);
        $tafsirAyat = array_slice($daftarTafsir, ($halaman - 1) * $perHalaman, $perHalaman);

        return view('quran.tafsir', [
            'surat' => $surat,
            'tafsirAyat' => $tafsirAyat,
            'halaman' => $halaman,
            'totalHalaman' => $totalHalaman,
        ]);
    }

    /**
     * Menyaring daftar surat berdasarkan nomor, nama Latin, atau arti.
     * Spasi dan tanda hubung diabaikan serta huruf besar-kecil tidak dibedakan,
     * sehingga "al fatihah" cocok dengan "Al-Fatihah".
     */
    private function saringSurat(array $daftarSurat, string $kataKunci): array
    {
        if ($kataKunci === '') {
            return $daftarSurat;
        }

        $cari = $this->normalisasi($kataKunci);

        $hasil = array_filter($daftarSurat, function (array $surat) use ($cari, $kataKunci) {
            // Cocok dengan nomor surat, misalnya "36" untuk Yaasin
            if (ctype_digit($kataKunci) && (int) data_get($surat, 'nomor') === (int) $kataKunci) {
                return true;
            }

            return str_contains($this->normalisasi((string) data_get($surat, 'namaLatin')), $cari)
                || str_contains($this->normalisasi((string) data_get($surat, 'arti')), $cari);
        });

        return array_values($hasil);
    }

    /**
     * Menormalkan teks pencarian: huruf kecil, tanpa spasi dan tanda hubung.
     */
    private function normalisasi(string $teks): string
    {
        return str_replace([' ', '-'], '', mb_strtolower($teks));
    }
}
