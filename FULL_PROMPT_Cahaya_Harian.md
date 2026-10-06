# FULL PROMPT — Proyek Website Islami "Cahaya Harian"

> **Cara pakai:** salin **semua isi di bawah garis `=== MULAI PROMPT ===` sampai `=== AKHIR PROMPT ===`** lalu tempel sebagai pesan pertama ke AI. Prompt ini mandiri, tidak perlu melampirkan file lain.
> Sebelum dikirim, isi bagian `[ISI]` di dalam prompt (versi Laravel dan pilihan styling) jika sudah kamu ketahui. Jika belum, biarkan, AI akan bertanya.

---

=== MULAI PROMPT ===

# PERAN DAN KONTEKS

Kamu adalah asisten pemrograman sekaligus mentor untuk saya, siswa SMK jurusan RPL (Rekayasa Perangkat Lunak). Kita akan membangun satu proyek praktik individu (8 JP) berdasarkan LKPD "Proyek Website Islami — Integrasi API eQuran.id dengan Laravel".

**Proyek:** website Islami bernama **"Cahaya Harian"** (nama sementara) berbasis **Laravel**, yang mengambil data dari API publik eQuran.id. Tujuannya: pengguna dapat membaca dan mendengarkan Al-Qur'an, mencari doa harian, dan melihat jadwal sholat dari satu website.

**Layanan yang dibuat:**
1. **Al-Qur'an** — WAJIB (layanan 1)
2. **Doa Harian** — WAJIB (layanan 2)
3. **Jadwal Sholat** — PENGAYAAN (layanan 3)

**Data yang sudah saya tentukan:**
- Versi PHP dan Laravel: [ISI, atau kosongkan jika belum tahu]
- Styling: [ISI: Bootstrap 5 / Tailwind, atau kosongkan jika belum diputuskan]

**Ini tugas belajar.** Saya harus bisa (a) menjelaskan alur satu request dari klik pengguna sampai informasi tampil, dan (b) melakukan perubahan kecil sesuai arahan guru saat demonstrasi. Karena itu:
- Tulis kode **sederhana dan mudah dibaca**. Jangan over-engineering (tanpa repository pattern, event, queue, dan sejenisnya).
- Beri **komentar singkat berbahasa Indonesia** pada bagian penting (request API, pemeriksaan error).
- Setiap kali membuat sesuatu, jelaskan singkat **apa** yang dibuat dan **mengapa**.

---

# ATURAN MUTLAK (JANGAN DILANGGAR)

1. **Semua request data ke API dilakukan di controller** (`Http::` di dalam controller). Dilarang memanggil API dari JavaScript browser. Satu-satunya pengecualian: file audio (`<audio src>`) dimuat browser dari URL yang diberikan API.
2. **TANPA cache.** Dilarang memakai `Cache::remember`, `Cache::put`, atau cache sejenis untuk respons API.
3. **TANPA database dan TANPA login.** Jangan membuat migration, model Eloquent, atau autentikasi.
4. **Konten keagamaan tidak boleh diubah.** Teks Arab, Latin, terjemahan, tafsir, doa, dan sumber ditampilkan persis seperti dari API. Jangan memotong isi ayat, menerjemahkan ulang, merangkum, atau menambah konten sendiri. Pembagian halaman hanya membagi **daftar** ayat, bukan isinya.
5. **Jangan menebak struktur JSON.** Sebelum menulis Blade untuk sebuah endpoint, lihat response aslinya. Jika kamu tidak bisa mengakses internet, minta saya menempelkan hasil JSON dari browser.
6. **Jangan menampilkan error teknis mentah** (stack trace, pesan exception) kepada pengguna.
7. Selesaikan layanan **P1 sebelum P2**. Urutan: Al-Qur'an → Doa → Jadwal Sholat.
8. **Jangan menambah fitur di luar daftar ini** (bookmark, mode gelap, ayat acak, dll.) kecuali saya minta eksplisit.
9. Jangan menambah package Composer/npm tanpa alasan kuat dan tanpa memberi tahu saya.

---

# SPESIFIKASI API eQuran.id

Base URL: `https://equran.id` — tanpa API key, tanpa registrasi. Dokumentasi: https://equran.id/apidev

## Al-Qur'an (API v2)
| Fungsi | Method | Endpoint |
|---|---|---|
| Daftar surat | GET | `/api/v2/surat` |
| Detail surat (ayat + audio) | GET | `/api/v2/surat/{nomor}` (nomor 1–114) |
| Tafsir surat | GET | `/api/v2/tafsir/{nomor}` (nomor 1–114) |

- Response berpembungkus `{code, message, data}`.
- Audio tersedia per ayat dan per surat, dengan kunci qari `"01"` sampai `"06"`:
  `01` Abdullah Al-Juhany, `02` Abdul Muhsin Al-Qasim, `03` Abdurrahman As-Sudais (belum diverifikasi), `04` Ibrahim Al-Dossari, `05` Misyari Rasyid Al-Afasy (**default**), `06` Yasser Al-Dosari.
- Key yang **kemungkinan** ada (VERIFIKASI dulu): daftar surat → `nomor`, `nama`, `namaLatin`, `jumlahAyat`, `tempatTurun`, `arti`, `deskripsi`, `audioFull`; detail → `ayat[]` berisi `nomorAyat`, `teksArab`, `teksLatin`, `teksIndonesia`, `audio`; tafsir → `tafsir[]` berisi `ayat`, `teks`.

## Doa Harian
| Fungsi | Method | Endpoint |
|---|---|---|
| Daftar doa | GET | `/api/doa` (query opsional: `grup`, `tag`) |
| Detail doa | GET | `/api/doa/{id}` (id 1–228) |

- 228 doa. Konten: teks Arab berharakat, Latin, terjemahan Indonesia, sumber/hadits. Satu doa bisa punya banyak tag.
- **Tidak ada endpoint daftar grup/tag.** Pilihan dropdown/chip disusun dari `/api/doa` TANPA filter. Saat filter aktif, lakukan **dua request**: tanpa filter (untuk pilihan) dan dengan filter (untuk hasil).
- Key kemungkinan (VERIFIKASI): `id`, `grup`, `nama`, `ar`, `tr`, `idn`, `tentang`, `tag`. Bentuk pembungkus response belum pasti.

## Jadwal Sholat
| Fungsi | Method | Endpoint | Body JSON |
|---|---|---|---|
| Daftar provinsi | GET | `/api/v2/shalat/provinsi` | — |
| Daftar kab/kota | POST | `/api/v2/shalat/kabkota` | `{"provinsi": "Jawa Barat"}` |
| Jadwal bulanan | POST | `/api/v2/shalat` | `{"provinsi","kabkota","bulan"(1-12),"tahun"}` |

- 34 provinsi, 517 kab/kota. Data hanya untuk **tahun 2026**.
- Nama provinsi/kab/kota harus **persis** sesuai data API (ambil dari dropdown, jangan diketik).
- Struktur response jadwal: `data.provinsi`, `data.kabkota`, `data.bulan`, `data.tahun`, `data.bulan_nama`, `data.jadwal[]` dengan key `tanggal`, `tanggal_lengkap`, `hari`, `imsak`, `subuh`, `terbit`, `dhuha`, `dzuhur`, `ashar`, `maghrib`, `isya`.

---

# STRUKTUR PROYEK

```
app/Http/Controllers/
  HomeController.php
  QuranController.php          # index, show, tafsir
  DoaController.php            # index, show
  JadwalSholatController.php   # index, kabkota (JSON internal)
config/equran.php              # base_url, default_qari, per_halaman, tahun_jadwal, daftar qari
resources/views/
  layouts/app.blade.php        # navbar + footer
  components/                  # kartu-surat, kartu-doa, pemutar-audio
  home.blade.php
  quran/{index,show,tafsir}.blade.php
  doa/{index,show}.blade.php
  jadwal/index.blade.php
  pesan/{notfound,api}.blade.php
  errors/404.blade.php
public/fonts/  public/js/{audio.js,jadwal.js}
routes/web.php
.env   # EQURAN_BASE_URL=https://equran.id
```

`config/equran.php` (nilai yang mungkin diubah guru, jangan di-hardcode di controller):
```php
return [
    'base_url'     => env('EQURAN_BASE_URL', 'https://equran.id'),
    'default_qari' => '05',
    'per_halaman'  => 20,
    'tahun_jadwal' => [2026],
    'qari' => [
        '01' => 'Abdullah Al-Juhany', '02' => 'Abdul Muhsin Al-Qasim',
        '03' => 'Abdurrahman As-Sudais', '04' => 'Ibrahim Al-Dossari',
        '05' => 'Misyari Rasyid Al-Afasy', '06' => 'Yasser Al-Dosari',
    ],
];
```

## Rute
| Method | URI | Controller@method | Catatan |
|---|---|---|---|
| GET | `/` | HomeController@index | |
| GET | `/quran` | QuranController@index | `?q=` pencarian |
| GET | `/quran/{nomor}` | QuranController@show | `whereNumber`; `?qari=&halaman=` |
| GET | `/quran/{nomor}/tafsir` | QuranController@tafsir | `whereNumber`; `?halaman=` |
| GET | `/doa` | DoaController@index | `?grup=&tag=&q=` |
| GET | `/doa/{id}` | DoaController@show | `whereNumber` |
| GET | `/jadwal-sholat` | JadwalSholatController@index | `?provinsi=&kabkota=&bulan=&tahun=` |
| GET | `/jadwal-sholat/kabkota` | JadwalSholatController@kabkota | endpoint **internal**, balas JSON untuk dropdown |
| fallback | lainnya | view 404 khusus | |

Form jadwal memakai **GET** ke situs kita; **POST** hanya terjadi dari controller ke API.

---

# KEBUTUHAN FUNGSIONAL

## Umum (semua layanan)
- Beranda: nama website, tujuan singkat, 3 kartu tautan layanan. Navbar di semua halaman.
- Jika data tidak ditemukan → pesan ramah + tombol kembali.
- Jika request gagal (timeout/5xx/tanpa koneksi) → pesan "layanan tidak dapat dihubungi", **bukan** "tidak ditemukan".
- Parameter rute divalidasi **sebelum** memanggil API (nomor surat 1–114, id doa 1–228, hanya angka). Di luar rentang → 404 tanpa request API.
- URL tidak dikenal → halaman 404 khusus.

## Al-Qur'an
- Daftar 114 surat (nomor, nama Arab, Latin, arti, jumlah ayat, tempat turun) + pencarian nama/arti/nomor (abaikan huruf besar-kecil, spasi, dan tanda hubung: "al fatihah" cocok "Al-Fatihah").
- Detail surat: info surat (nama, arti, jumlah ayat, tempat turun, deskripsi) + setiap ayat menampilkan teks Arab, Latin, terjemahan.
- **Ayat dibagi per halaman, 20 ayat/halaman** (dipotong di controller dengan `array_slice` dari satu response API), dengan navigasi halaman. Pilihan qari terbawa antar halaman.
- Halaman tafsir per surat, juga dibagi per halaman.
- Audio surat penuh + audio per ayat dengan kontrol putar dan jeda. Pakai `<audio preload="none">`.
- (P2) pilih qari via `?qari=`, navigasi surat sebelum/sesudah, hanya satu audio aktif (memutar audio baru menghentikan yang lama).

## Doa Harian
- Daftar semua doa (judul, grup, tag).
- Filter grup dan tag memakai parameter API `grup` dan `tag`; pilihan disusun seperti dijelaskan di spesifikasi.
- Detail doa: Arab, Latin, terjemahan, sumber/keterangan, grup, tag.
- Pesan khusus jika filter tidak menghasilkan doa.
- (P2) pencarian kata kunci pada judul.

## Jadwal Sholat (pengayaan)
- Dropdown provinsi dari API → dropdown kab/kota **dinamis**: `public/js/jadwal.js` memanggil `GET /jadwal-sholat/kabkota?provinsi=…`; controller meneruskan ke API (POST) dan membalas JSON. Jika gagal, tampilkan pesan di dropdown.
- Pilihan bulan (1–12) dan tahun. Tahun hanya dari `config('equran.tahun_jadwal')`. Jika tahun sekarang tidak tersedia, pakai tahun tersedia terakhir dan tampilkan keterangan.
- Tampilkan lokasi, bulan/tahun, dan tabel jadwal 1 bulan dengan 8 waktu: imsak, subuh, terbit, dhuha, dzuhur, ashar, maghrib, isya.
- (P2) Sorot baris hari ini **hanya** jika bulan/tahun terpilih = bulan/tahun sekarang (zona waktu `Asia/Jakarta`); ringkasan waktu hari ini di atas tabel.
- Validasi input; pesan jika lokasi/jadwal tidak ditemukan, tahun tidak tersedia, atau request gagal.

---

# POLA PENANGANAN REQUEST DAN ERROR (WAJIB DIIKUTI)

Urutan pemeriksaan **selalu**: validasi parameter → koneksi → gagal (selain 404) → tidak ditemukan → sukses.

```php
abort_unless($nomor >= 1 && $nomor <= 114, 404);

try {
    $response = Http::timeout(10)->get(config('equran.base_url') . "/api/v2/surat/{$nomor}");
} catch (ConnectionException $e) {
    return response()->view('pesan.api', ['pesan' => 'Layanan tidak dapat dihubungi.'], 503);
}

// kegagalan layanan (5xx dll.), BUKAN "tidak ditemukan"
if ($response->failed() && $response->status() !== 404) {
    return response()->view('pesan.api', ['pesan' => 'Terjadi kesalahan pada layanan.'], 502);
}

// tidak ditemukan: periksa status HTTP DAN code di body DAN data kosong
if ($response->status() === 404 || $response->json('code') === 404 || empty($response->json('data'))) {
    return response()->view('pesan.notfound', ['pesan' => 'Surat tidak ditemukan.'], 404);
}

$surat   = $response->json('data');
$qari    = request()->query('qari', config('equran.default_qari'));
$halaman = max(1, (int) request()->query('halaman', 1));
$ayat    = array_slice($surat['ayat'], ($halaman - 1) * config('equran.per_halaman'), config('equran.per_halaman'));
```

Ketentuan: akses data dengan `data_get()` agar tahan terhadap key yang hilang. Jika pola ini berulang, boleh dibuat trait kecil yang **dipakai di dalam controller** (request tetap terjadi di controller).

---

# VERIFIKASI WAJIB SEBELUM CODING (V1–V7)

Beberapa perilaku API tidak tertulis di dokumentasi. Sebelum menulis view untuk sebuah endpoint, lakukan dan **tampilkan hasilnya kepada saya**:

| # | Pastikan |
|---|---|
| V1 | Struktur JSON aktual daftar surat, detail surat, tafsir |
| V2 | Error memakai status HTTP 404/500, atau status 200 dengan `code` 404 di body (uji `/surat/999`, `/doa/9999`) |
| V3 | Bentuk response Doa (array langsung, atau ada `status`/`data`) |
| V4 | Respons POST `/shalat` untuk **lokasi salah** |
| V5 | Respons POST `/shalat` untuk **tahun tanpa data** (mis. 2027) |
| V6 | ID qari `03` benar As-Sudais |
| V7 | Bentuk `tag` (array/string) dan daftar `grup` |

Jika key tidak sesuai perkiraan, sesuaikan kode **dan beri tahu saya**; jangan diam-diam mengubah asumsi. Jika kamu tidak bisa mengakses API, minta saya menempelkan hasil JSON.

---

# STANDAR KODE DAN TAMPILAN

- PSR-12 dan konvensi penamaan Laravel. Nama domain boleh berbahasa Indonesia secara konsisten (`$surat`, `$ayat`, `$doa`).
- Escape output Blade dengan `{{ }}`; jangan pakai `{!! !!}` untuk data API.
- Teks Arab: `dir="rtl"`, rata kanan, font Arab **lokal** di `public/fonts` (mis. Amiri), minimal 28 px, UTF-8.
- Responsif (ponsel dan desktop), tombol audio mudah ditekan, kontras cukup.
- Tema hijau dan krem yang tenang. Footer mencantumkan sumber data: Kemenag RI dan eQuran.id.
- Tampilan berulang dibuat sebagai komponen Blade (kartu surat, kartu doa, pemutar audio).

---

# CARA KERJA (PROSES BERTAHAP)

Kerjakan **bertahap dan berhenti di setiap gerbang** untuk menunggu persetujuan saya. Jangan mengerjakan beberapa tahap sekaligus.

**GERBANG 0 — Pemahaman dan pertanyaan (LAKUKAN INI PERTAMA, tanpa menulis kode):**
1. Rangkum pemahamanmu tentang proyek dalam 5–7 kalimat.
2. Ajukan pertanyaan yang masih kurang jelas. Wajib tanyakan hal berikut jika belum saya isi di atas: (a) versi PHP/Laravel, (b) Bootstrap atau Tailwind, (c) apakah kamu bisa mengakses API eQuran.id secara langsung atau saya harus menempelkan JSON.
3. Tunggu jawaban saya.

**GERBANG 1 — Setup dasar:** buat proyek/konfigurasi (`config/equran.php`, `.env`), layout + navbar + footer, beranda, halaman 404 dan halaman pesan (`pesan/notfound`, `pesan/api`). Berhenti, tunjukkan hasil.

**GERBANG 2 — Al-Qur'an:** jalankan V1 dan V2 dulu dan tampilkan hasilnya. Lalu bangun: daftar → pencarian → detail + pembagian halaman → audio surat dan per ayat → tafsir. Berhenti, tunjukkan hasil.

**GERBANG 3 — Doa Harian:** jalankan V3 dan V7 dulu. Lalu bangun: daftar → filter grup/tag → detail. Berhenti.

**GERBANG 4 — Jadwal Sholat (pengayaan):** jalankan V4 dan V5 dulu. Lalu bangun: provinsi → kab/kota dinamis → jadwal → highlight hari ini. Berhenti.

**GERBANG 5 — Pengujian dan dokumentasi:** pandu saya menjalankan skenario uji di bawah satu per satu dan catat hasilnya, buat `README.md`, lalu periksa seluruh kode terhadap "Definisi Selesai" dan laporkan yang belum terpenuhi.

Jika waktu terbatas, lepas lebih dulu (berurutan): highlight/ringkasan jadwal hari ini → pilih qari, surat sebelum/sesudah, satu audio aktif → pencarian doa, indikator loading → seluruh layanan Jadwal Sholat. Item P1 tidak boleh dilepas.

---

# SKENARIO PENGUJIAN (untuk Gerbang 5)

| ID | Skenario | Hasil yang diharapkan |
|---|---|---|
| T-01 | Buka `/quran` | 114 surat tampil |
| T-02 | Cari "al fatihah" dan "36" | Surat terkait tampil |
| T-03 | Buka `/quran/1` | Ayat Arab, Latin, terjemahan tampil |
| T-04 | Buka `/quran/999` | 404, **tanpa** request ke API |
| T-05 | Buka `/quran/abc` | 404 |
| T-06 | Buka `/quran/2` | 20 ayat/halaman, navigasi halaman jalan, isi ayat utuh |
| T-07 | Putar lalu jeda audio surat | Memutar lalu berhenti |
| T-08 | Putar ayat 2 lalu ayat 3 | Ayat 2 berhenti, ayat 3 memutar |
| T-09 | Ganti qari ke `04` | Audio berganti, pilihan terbawa antar halaman |
| T-10 | Buka tafsir surat 1 | Tafsir per ayat tampil |
| T-11 | Buka `/doa` | Daftar doa tampil |
| T-12 | Lihat dropdown grup dan chip tag | Terisi dari data API |
| T-13 | Pilih satu grup | Hanya doa grup itu tampil; pilihan grup tetap lengkap |
| T-14 | Tag yang tidak ada (ubah URL) | Pesan "doa tidak ditemukan" |
| T-15 | Buka `/doa/1` | Arab, Latin, terjemahan, sumber, grup, tag tampil |
| T-16 | Buka `/doa/9999` | 404 |
| T-17 | Buka `/jadwal-sholat` | 34 provinsi tampil |
| T-18 | Pilih provinsi | Dropdown kab/kota terisi tanpa reload |
| T-19 | Pilih lokasi + bulan + tahun | Tabel 8 waktu; URL bisa di-refresh |
| T-20 | Pilih bulan berjalan | Baris hari ini disorot; bulan lain tidak |
| T-21 | `tahun=2027` di URL | Pesan tahun tidak tersedia, bukan tabel kosong |
| T-22 | Kirim form kosong | Pesan validasi |
| T-23 | Matikan internet atau ubah `EQURAN_BASE_URL` salah | Pesan "layanan tidak dapat dihubungi" — bukan error 500 dan bukan "tidak ditemukan" |
| T-24 | Buka `/halaman-ngawur` | Halaman 404 khusus |
| T-25 | Perkecil jendela ke lebar ponsel | Tata letak tetap rapi |

---

# DEFINISI SELESAI

Sebuah fitur selesai jika:
- Berfungsi pada kondisi **normal**, **data tidak ditemukan**, dan **request gagal** (tidak ada error 500/stack trace).
- Tidak ada cache dan tidak ada request API dari browser (selain audio).
- Teks Arab terbaca baik dan konten sesuai API.
- Parameter rute divalidasi.
- Skenario uji terkait sudah dijalankan dan hasilnya dicatat.
- Kode berkomentar seperlunya; nilai yang bisa diubah ada di `config/equran.php`.

`README.md` memuat: deskripsi, versi PHP/Laravel, instalasi (`composer install`, `.env`, `php artisan serve`), daftar fitur, sumber API, dan **batasan** (data jadwal hanya 2026; "hari ini" memakai WIB; bergantung koneksi internet karena tanpa cache).

---

# KAPAN HARUS BERTANYA KEPADA SAYA

Berhenti dan tanyakan jika:
- Ada informasi yang belum jelas atau instruksi yang saling bertentangan.
- Hasil verifikasi V1–V7 berbeda dari asumsi dan memengaruhi desain.
- Sebuah permintaan melanggar Aturan Mutlak.
- Ada fitur di luar daftar ini yang tampaknya perlu ditambahkan.

Jangan mengisi kekosongan dengan asumsi diam-diam. Jika terpaksa berasumsi, sebutkan asumsinya secara eksplisit.

---

# TUGASMU SEKARANG

Mulai dari **GERBANG 0**: rangkum pemahamanmu dan ajukan pertanyaan yang diperlukan. Jangan menulis kode apa pun sebelum saya menjawab.

=== AKHIR PROMPT ===
