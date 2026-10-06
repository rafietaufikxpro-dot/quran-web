<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class JadwalSholatController extends Controller
{
    // Urutan waktu sholat wajib, dipakai untuk mencari waktu sholat berikutnya.
    private const WAKTU_WAJIB = ['subuh', 'dzuhur', 'ashar', 'maghrib', 'isya'];

    // Nama tampilan waktu sholat wajib (dipakai jawaban JSON widget jam).
    private const NAMA_WAKTU = [
        'subuh' => 'Subuh',
        'dzuhur' => 'Dzuhur',
        'ashar' => 'Ashar',
        'maghrib' => 'Maghrib',
        'isya' => 'Isya',
    ];

    // Nama bulan untuk pilihan dropdown (1-12).
    private const NAMA_BULAN = [
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember',
    ];

    /**
     * Menampilkan halaman jadwal sholat: pilihan lokasi, bulan, dan tahun,
     * lalu tabel jadwal 1 bulan untuk lokasi yang dipilih.
     */
    public function index(Request $request): View|Response
    {
        // Daftar provinsi selalu diambil agar pilihan lokasi tetap tampil
        try {
            $response = Http::timeout(10)->get(config('equran.base_url').'/api/v2/shalat/provinsi');
        } catch (ConnectionException $e) {
            return response()->view('pesan.api', ['pesan' => 'Layanan tidak dapat dihubungi.'], 503);
        }

        // Kegagalan layanan selain "tidak ditemukan" dianggap gangguan server
        if ($response->failed() && $response->status() !== 404) {
            return response()->view('pesan.api', ['pesan' => 'Terjadi kesalahan pada layanan.'], 502);
        }

        $daftarProvinsi = $response->json('data');

        // Tidak ditemukan: status 404, code 404 pada body, atau data kosong
        if ($response->status() === 404 || $response->json('code') === 404 || empty($daftarProvinsi)) {
            return response()->view('pesan.notfound', ['pesan' => 'Daftar provinsi tidak ditemukan.'], 404);
        }

        // Waktu sekarang memakai zona waktu dari config (WIB) agar penyorotan tepat
        $sekarang = now(config('equran.zona_waktu'));
        $tahunSekarang = (int) $sekarang->format('Y');

        // Lokasi terpilih dari query string; hanya dipakai bila berbentuk teks
        $provinsi = $request->query('provinsi');
        $provinsi = is_string($provinsi) ? trim($provinsi) : '';
        $kabkota = $request->query('kabkota');
        $kabkota = is_string($kabkota) ? trim($kabkota) : '';

        // Bulan terpilih: 1-12; nilai tidak sah dikembalikan ke bulan sekarang
        $bulanTerpilih = $request->integer('bulan', 0);
        if ($bulanTerpilih < 1 || $bulanTerpilih > 12) {
            $bulanTerpilih = (int) $sekarang->format('n');
        }

        // Tahun hanya dari daftar di config; bila config kosong pakai tahun sekarang
        $daftarTahun = array_map('intval', (array) config('equran.tahun_jadwal'));
        if ($daftarTahun === []) {
            $daftarTahun = [$tahunSekarang];
        }
        sort($daftarTahun);

        $tahunTerpilih = $tahunSekarang;
        $tahunDiminta = $request->integer('tahun', 0);
        $pesanTahun = null;
        $keteranganTahun = null;

        if ($tahunDiminta > 0 && ! in_array($tahunDiminta, $daftarTahun, true)) {
            // Tahun di URL tidak ada di daftar: beri pesan, tabel tidak dimuat
            $pesanTahun = "Tahun {$tahunDiminta} tidak tersedia. Data jadwal tersedia untuk tahun: ".implode(', ', $daftarTahun).'.';
        } elseif ($tahunDiminta > 0) {
            $tahunTerpilih = $tahunDiminta;
        }

        // Pastikan tahun terpilih ada di daftar; bila tidak (mis. tahun sekarang
        // belum tersedia), pakai tahun tersedia terakhir dan tampilkan keterangan
        if (! in_array($tahunTerpilih, $daftarTahun, true)) {
            $tahunTerpilih = max($daftarTahun);

            if ($pesanTahun === null) {
                $keteranganTahun = "Jadwal tahun {$tahunSekarang} belum tersedia; menampilkan jadwal tahun {$tahunTerpilih}.";
            }
        }

        $dataJadwal = null;

        // Jadwal diambil bila lokasi lengkap dan tahunnya tersedia
        if ($provinsi !== '' && $kabkota !== '' && $pesanTahun === null) {
            try {
                $response = Http::timeout(10)->post(config('equran.base_url').'/api/v2/shalat', [
                    'provinsi' => $provinsi,
                    'kabkota' => $kabkota,
                    'bulan' => $bulanTerpilih,
                    'tahun' => $tahunTerpilih,
                ]);
            } catch (ConnectionException $e) {
                return response()->view('pesan.api', ['pesan' => 'Layanan tidak dapat dihubungi.'], 503);
            }

            if ($response->failed() && $response->status() !== 404) {
                return response()->view('pesan.api', ['pesan' => 'Terjadi kesalahan pada layanan.'], 502);
            }

            if ($response->status() === 404 || $response->json('code') === 404 || empty($response->json('data'))) {
                return response()->view('pesan.notfound', ['pesan' => "Jadwal sholat untuk {$kabkota} tidak ditemukan."], 404);
            }

            $dataJadwal = $response->json('data');
        }

        // Sorot hari ini hanya bila bulan dan tahun terpilih = bulan/tahun sekarang
        $tanggalHariIni = $sekarang->toDateString();
        $jamSekarang = $sekarang->format('H:i');
        $tampilkanHariIni = $dataJadwal !== null
            && $bulanTerpilih === (int) $sekarang->format('n')
            && $tahunTerpilih === $tahunSekarang;

        // Cari waktu sholat wajib berikutnya pada baris jadwal hari ini
        // (dipakai untuk menyorot sel tabel)
        $namaWaktuDepan = null;

        if ($tampilkanHariIni) {
            foreach ((array) data_get($dataJadwal, 'jadwal', []) as $baris) {
                if (data_get($baris, 'tanggal_lengkap') !== $tanggalHariIni) {
                    continue;
                }

                foreach (self::WAKTU_WAJIB as $kunci) {
                    $nilaiWaktu = (string) data_get($baris, $kunci);

                    // Jam berbentuk "HH:MM" sehingga aman dibandingkan sebagai teks
                    if ($nilaiWaktu > $jamSekarang) {
                        $namaWaktuDepan = $kunci;
                        break;
                    }
                }

                break;
            }
        }

        return view('jadwal.index', [
            'daftarProvinsi' => $daftarProvinsi,
            'provinsiTerpilih' => $provinsi,
            'kabkotaTerpilih' => $kabkota,
            'daftarBulan' => self::NAMA_BULAN,
            'bulanTerpilih' => $bulanTerpilih,
            'daftarTahun' => $daftarTahun,
            'tahunTerpilih' => $tahunTerpilih,
            'pesanTahun' => $pesanTahun,
            'keteranganTahun' => $keteranganTahun,
            'dataJadwal' => $dataJadwal,
            'tanggalHariIni' => $tanggalHariIni,
            'tampilkanHariIni' => $tampilkanHariIni,
            'namaWaktuDepan' => $namaWaktuDepan,
        ]);
    }

    /**
     * Mengambil daftar kabupaten/kota milik satu provinsi untuk dropdown.
     * Dipanggil oleh public/js/jadwal.js, jadi jawabannya selalu berformat JSON.
     */
    public function kabkota(Request $request): JsonResponse
    {
        $provinsi = $request->query('provinsi');
        $provinsi = is_string($provinsi) ? trim($provinsi) : '';

        // Parameter kosong tidak perlu diteruskan ke API
        if ($provinsi === '') {
            return response()->json(['pesan' => 'Parameter provinsi wajib diisi.'], 400);
        }

        try {
            $response = Http::timeout(10)->post(config('equran.base_url').'/api/v2/shalat/kabkota', [
                'provinsi' => $provinsi,
            ]);
        } catch (ConnectionException $e) {
            return response()->json(['pesan' => 'Layanan tidak dapat dihubungi.'], 503);
        }

        if ($response->failed() && $response->status() !== 404) {
            return response()->json(['pesan' => 'Terjadi kesalahan pada layanan.'], 502);
        }

        $daftarKabkota = $response->json('data');

        if ($response->status() === 404 || $response->json('code') === 404 || empty($daftarKabkota)) {
            return response()->json(['pesan' => 'Provinsi tidak ditemukan.'], 404);
        }

        return response()->json(['data' => $daftarKabkota]);
    }

    /**
     * Mengambil waktu sholat berikutnya untuk satu lokasi menurut hari ini (WIB).
     * Dipakai public/js/jam-sholat.js untuk widget jam, jadi jawabannya selalu JSON.
     */
    public function hariIni(Request $request): JsonResponse
    {
        $provinsi = $request->query('provinsi');
        $provinsi = is_string($provinsi) ? trim($provinsi) : '';
        $kabkota = $request->query('kabkota');
        $kabkota = is_string($kabkota) ? trim($kabkota) : '';

        // Parameter kosong tidak perlu diteruskan ke API
        if ($provinsi === '' || $kabkota === '') {
            return response()->json(['pesan' => 'Parameter provinsi dan kabkota wajib diisi.'], 400);
        }

        $sekarang = now(config('equran.zona_waktu'));
        $bulan = (int) $sekarang->format('n');
        $tahun = (int) $sekarang->format('Y');
        $tanggalHariIni = $sekarang->toDateString();
        $jamSekarang = $sekarang->format('H:i:s');

        // Jadwal bulan berjalan; kegagalan langsung dibalas sebagai JSON
        $dataBulan = $this->ambilJadwalBulan($provinsi, $kabkota, $bulan, $tahun);

        if ($dataBulan instanceof JsonResponse) {
            return $dataBulan;
        }

        $berikutnya = $this->cariWaktuBerikutnya((array) data_get($dataBulan, 'jadwal', []), $tanggalHariIni, $jamSekarang);
        $pesan = null;

        // Semua waktu hari ini sudah lewat: pakai baris besok
        // (bisa berada di bulan berikutnya saat hari terakhir bulan)
        if ($berikutnya === null) {
            $besok = $sekarang->copy()->addDay();
            $dataBesok = $dataBulan;

            if ($besok->format('n') !== $sekarang->format('n') || $besok->format('Y') !== $sekarang->format('Y')) {
                $dataBesok = $this->ambilJadwalBulan($provinsi, $kabkota, (int) $besok->format('n'), (int) $besok->format('Y'));
            }

            if ($dataBesok instanceof JsonResponse) {
                $pesan = 'Jadwal untuk besok belum tersedia.';
            } else {
                $berikutnya = $this->cariWaktuBerikutnya((array) data_get($dataBesok, 'jadwal', []), $besok->toDateString(), '');

                if ($berikutnya === null) {
                    $pesan = 'Jadwal untuk besok belum tersedia.';
                }
            }
        }

        return response()->json([
            'lokasi' => ['provinsi' => $provinsi, 'kabkota' => $kabkota],
            'jam_wib' => $jamSekarang,
            'tanggal_wib' => $tanggalHariIni,
            'berikutnya' => $berikutnya,
            'pesan' => $pesan,
        ]);
    }

    /**
     * Mengambil jadwal satu bulan dari API. Bila berhasil mengembalikan array data
     * jadwal; bila gagal mengembalikan JsonResponse berisi pesan kesalahan.
     */
    private function ambilJadwalBulan(string $provinsi, string $kabkota, int $bulan, int $tahun): array|JsonResponse
    {
        try {
            $response = Http::timeout(10)->post(config('equran.base_url').'/api/v2/shalat', [
                'provinsi' => $provinsi,
                'kabkota' => $kabkota,
                'bulan' => $bulan,
                'tahun' => $tahun,
            ]);
        } catch (ConnectionException $e) {
            return response()->json(['pesan' => 'Layanan tidak dapat dihubungi.'], 503);
        }

        // Kegagalan layanan selain "tidak ditemukan" dianggap gangguan server
        if ($response->failed() && $response->status() !== 404) {
            return response()->json(['pesan' => 'Terjadi kesalahan pada layanan.'], 502);
        }

        $data = $response->json('data');

        if ($response->status() === 404 || $response->json('code') === 404 || empty($data)) {
            return response()->json(['pesan' => "Jadwal sholat untuk {$kabkota} tidak ditemukan."], 404);
        }

        return $data;
    }

    /**
     * Mencari waktu sholat wajib pertama yang belum lewat pada satu tanggal.
     * $batas memakai format "HH:MM:SS"; string kosong berarti cari yang paling awal.
     */
    private function cariWaktuBerikutnya(array $daftarJadwal, string $tanggal, string $batas): ?array
    {
        $baris = null;

        foreach ($daftarJadwal as $satuBaris) {
            if (data_get($satuBaris, 'tanggal_lengkap') === $tanggal) {
                $baris = $satuBaris;
                break;
            }
        }

        if ($baris === null) {
            return null;
        }

        foreach (self::WAKTU_WAJIB as $kunci) {
            $nilaiWaktu = (string) data_get($baris, $kunci);

            // Jam berbentuk "HH:MM" sehingga aman dibandingkan sebagai teks
            if ($nilaiWaktu > $batas) {
                return [
                    'nama' => self::NAMA_WAKTU[$kunci],
                    'jam' => $nilaiWaktu,
                    'tanggal' => $tanggal,
                ];
            }
        }

        return null;
    }
}
