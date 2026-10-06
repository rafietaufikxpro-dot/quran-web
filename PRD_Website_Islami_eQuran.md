# PRD — Website Islami "Cahaya Harian"

**Integrasi API eQuran.id dengan Laravel**

| | |
|---|---|
| **Nama Siswa** | ____________________ |
| **Kelas** | ________ |
| **Mata Pelajaran** | RPL (Rekayasa Perangkat Lunak) |
| **Jenis Tugas** | Praktik individu — 8 JP |
| **Versi Dokumen** | 1.1 (revisi setelah review) |
| **Tanggal** | ____________________ |
| **Acuan** | LKPD "Proyek Website Islami — Integrasi API eQuran.id dengan Laravel" |
| **Dokumentasi API** | https://equran.id/apidev |

> **Catatan:** Nama "Cahaya Harian" hanya usulan awal. LKPD menyerahkan penentuan nama, tujuan, pengguna, dan desain kepada siswa, jadi silakan ganti sesuai konsepmu.

---

## 1. Ringkasan Produk

**Cahaya Harian** adalah website pendamping ibadah harian berbasis Laravel. Pengguna dapat **membaca dan mendengarkan Al-Qur'an**, **mencari doa harian**, dan **melihat jadwal sholat** daerahnya dari satu tempat. Seluruh data diambil dari API publik eQuran.id, tanpa database dan tanpa cache.

| Layanan | Status | Sumber API |
|---|---|---|
| Al-Qur'an (surat, ayat, tafsir, audio) | **Wajib** (layanan 1) | eQuran.id API v2 |
| Doa Harian | **Wajib** (layanan 2) | eQuran.id API Doa |
| Jadwal Sholat | **Pengayaan** (layanan 3) | eQuran.id API Jadwal Shalat |

LKPD mewajibkan minimal 2 dari 3 layanan. Layanan ketiga dibuat sebagai pengayaan.

---

## 2. Latar Belakang dan Tujuan

### 2.1 Latar Belakang
Pengguna yang ingin membaca Al-Qur'an, mencari doa, dan mengecek waktu sholat biasanya harus membuka beberapa aplikasi berbeda. Proyek ini menggabungkannya dalam satu website sederhana. Bagi siswa, proyek ini juga menjadi latihan nyata tentang **HTTP request dan response**, penggunaan **REST API**, dan arsitektur **MVC Laravel**.

### 2.2 Tujuan Produk
1. Menyediakan satu website yang menampilkan Al-Qur'an, doa harian, dan jadwal sholat.
2. Menampilkan teks Arab, Latin, terjemahan, tafsir, dan sumber sesuai data API tanpa mengubah maknanya.
3. Memberikan pengalaman mendengarkan audio dengan kontrol putar dan jeda.
4. Menangani kondisi data tidak ditemukan dan request gagal dengan pesan yang jelas.

### 2.3 Tujuan Pembelajaran
Setelah proyek selesai, siswa mampu:
- Membaca dokumentasi API (endpoint, method, parameter, key data).
- Melakukan request GET dan POST dari controller Laravel.
- Mengolah response JSON menjadi tampilan Blade.
- Menguji kondisi normal, data tidak ditemukan, dan kegagalan request.
- Menjelaskan alur satu request dari klik pengguna sampai informasi tampil.

### 2.4 Indikator Keberhasilan

| # | Indikator | Target |
|---|---|---|
| K1 | Layanan wajib berfungsi dan terhubung lewat navigasi | 2 dari 2 |
| K2 | Layanan pengayaan berfungsi | 1 dari 1 |
| K3 | Skenario uji (normal, data kosong, gagal) lulus | 100% |
| K4 | Seluruh request **data** ke API dilakukan melalui controller (file audio dimuat browser langsung dari URL yang diberikan API, jadi tidak dihitung) | 100% |
| K5 | Siswa dapat menjelaskan alur satu request saat demonstrasi | Ya |

---

## 3. Pengguna Sasaran

| Persona | Deskripsi | Kebutuhan Utama |
|---|---|---|
| **Pembaca harian** | Muslim usia remaja sampai dewasa yang rutin membaca Al-Qur'an | Surat mudah dicari, teks Arab jelas, ada terjemahan dan audio |
| **Pencari doa** | Pengguna yang ingin cepat menemukan doa untuk situasi tertentu | Pencarian dan filter berdasarkan kategori atau tag |
| **Pengecek waktu sholat** | Pengguna yang butuh jadwal untuk kota tertentu | Pilih lokasi dan bulan, lihat seluruh waktu dalam sehari |

Pengguna utama adalah **pengguna umum berbahasa Indonesia**. Tidak perlu mendaftar atau login.

---

## 4. Ruang Lingkup

### 4.1 Dalam Lingkup
- Tiga modul: Al-Qur'an, Doa Harian, Jadwal Sholat.
- Beranda dan navigasi antar layanan.
- Interaksi pengguna pada setiap layanan (pencarian, filter, pilihan, tombol audio).
- Pemutar audio surat dan per ayat.
- Pesan kesalahan, pesan data kosong, dan halaman 404.
- Desain responsif sederhana.
- Dokumentasi: README, sketsa halaman, laporan singkat.

### 4.2 Di Luar Lingkup
- Login, registrasi, akun pengguna.
- Database sendiri atau penyimpanan data pengguna (bookmark, riwayat).
- **Cache respons API.** Setiap kunjungan halaman memicu request baru, supaya alur request dan pengujian kegagalan sesuai LKPD.
- Aplikasi mobile native.
- Fitur eQuran.id di luar tiga layanan (game, AI chat, vector search, info kajian).
- Menambah atau mengubah konten keagamaan dari sumber API.

---

## 5. Kebutuhan Fungsional

Prioritas: **P1** = wajib (sesuai LKPD), **P2** = pengayaan atau nilai tambah.

### 5.1 Umum (Semua Layanan)

| ID | Kebutuhan | Prioritas |
|---|---|---|
| FR-01 | Beranda menampilkan nama website, tujuan singkat, dan kartu tautan ke tiap layanan. | P1 |
| FR-02 | Navigasi (navbar) tampil di semua halaman dan menghubungkan layanan yang dipilih. | P1 |
| FR-03 | Seluruh request data ke API dilakukan melalui **controller** Laravel, bukan dari browser. | P1 |
| FR-04 | Jika data tidak ditemukan, tampil pesan ramah dan tombol kembali. | P1 |
| FR-05 | Jika request gagal (timeout, error server, tidak ada koneksi), tampil pesan yang menjelaskan masalah tanpa error teknis mentah. | P1 |
| FR-06 | Parameter rute divalidasi **sebelum** memanggil API (nomor surat 1–114, id doa 1–228, hanya angka). Nilai di luar rentang langsung menampilkan halaman "tidak ditemukan" (404). | P1 |
| FR-07 | URL yang tidak dikenal menampilkan halaman 404 khusus (bukan halaman error bawaan Laravel). | P1 |
| FR-08 | Indikator memuat (loading) saat data diambil. | P2 |

### 5.2 Layanan 1 — Al-Qur'an (Wajib)

| ID | Kebutuhan | Prioritas |
|---|---|---|
| FR-QR-01 | Halaman **daftar surat** menampilkan 114 surat: nomor, nama Arab, nama Latin, arti, jumlah ayat, tempat turun. | P1 |
| FR-QR-02 | Pencarian surat berdasarkan nama (Latin atau arti) atau nomor. Pencarian tidak peka huruf besar-kecil dan mengabaikan spasi/tanda hubung (mis. "al fatihah" cocok dengan "Al-Fatihah"). | P1 |
| FR-QR-03 | Halaman **detail surat** menampilkan informasi surat (nama, arti, jumlah ayat, tempat turun, deskripsi). | P1 |
| FR-QR-04 | Setiap ayat menampilkan **teks Arab**, **teks Latin**, dan **terjemahan Indonesia**. | P1 |
| FR-QR-05 | Ayat ditampilkan **per halaman** (default 20 ayat per halaman) dengan navigasi halaman, karena surat panjang seperti Al-Baqarah memiliki 286 ayat. Pemotongan dilakukan di controller dari satu response API. | P1 |
| FR-QR-06 | Halaman **tafsir** per surat menampilkan tafsir setiap ayat, juga dengan pembagian halaman. | P1 |
| FR-QR-07 | **Audio surat lengkap** dengan kontrol putar dan jeda. | P1 |
| FR-QR-08 | **Audio per ayat** dengan kontrol putar dan jeda. Elemen audio memakai `preload="none"` agar tidak mengunduh puluhan file sekaligus. | P1 |
| FR-QR-09 | Pengguna dapat memilih **qari** dari 6 pilihan lewat parameter `?qari=` (default `05`). Pilihan qari terbawa saat pindah halaman ayat. | P2 |
| FR-QR-10 | Navigasi ke surat sebelumnya dan selanjutnya. | P2 |
| FR-QR-11 | Hanya satu audio yang diputar pada satu waktu. Memutar audio baru menghentikan audio sebelumnya. | P2 |

### 5.3 Layanan 2 — Doa Harian (Wajib)

| ID | Kebutuhan | Prioritas |
|---|---|---|
| FR-DO-01 | Halaman **daftar doa** menampilkan seluruh doa (judul, grup, tag). | P1 |
| FR-DO-02 | **Filter berdasarkan grup** memakai parameter `grup` pada API. | P1 |
| FR-DO-03 | **Filter berdasarkan tag** memakai parameter `tag` pada API. | P1 |
| FR-DO-04 | **Pilihan grup dan tag** untuk dropdown/chip disusun dari hasil request `/api/doa` **tanpa filter** (API tidak punya endpoint daftar grup atau tag). Jika filter aktif, controller melakukan dua request: satu tanpa filter untuk pilihan, satu dengan filter untuk hasil. | P1 |
| FR-DO-05 | Halaman **detail doa** menampilkan teks Arab, Latin, terjemahan, **sumber/keterangan**, serta **grup dan tag** sesuai data API. | P1 |
| FR-DO-06 | Pesan khusus jika filter tidak menghasilkan doa. | P1 |
| FR-DO-07 | Pencarian doa berdasarkan kata kunci pada judul (diproses di controller dari daftar yang sudah diambil). | P2 |

### 5.4 Layanan 3 — Jadwal Sholat (Pengayaan)

| ID | Kebutuhan | Prioritas |
|---|---|---|
| FR-JS-01 | Dropdown **provinsi** diisi dari API (GET `/shalat/provinsi`). | P2 |
| FR-JS-02 | Dropdown **kabupaten/kota** diisi **dinamis** begitu provinsi dipilih. JavaScript memanggil **endpoint internal** `GET /jadwal-sholat/kabkota?provinsi=…`, lalu controller meneruskannya ke API (POST `/shalat/kabkota`) dan mengembalikan JSON. | P2 |
| FR-JS-03 | Pilihan **bulan** (1–12) dan **tahun**. Daftar tahun dibatasi pada tahun yang tersedia di API (saat ini **2026**, disimpan di `config/equran.php`). Jika tahun sekarang tidak tersedia, default memakai tahun tersedia terakhir dan ditampilkan keterangan. | P2 |
| FR-JS-04 | Form dikirim dengan **method GET** ke halaman situs (`/jadwal-sholat?provinsi=…&kabkota=…&bulan=…&tahun=…`) agar bisa di-refresh dan di-bookmark. Controller yang melakukan **POST** ke API `/shalat`. | P2 |
| FR-JS-05 | Tampilkan **lokasi**, **bulan/tahun**, dan **tabel jadwal** satu bulan dengan seluruh waktu: imsak, subuh, terbit, dhuha, dzuhur, ashar, maghrib, isya. | P2 |
| FR-JS-06 | Baris **tanggal hari ini** ditandai (highlight) hanya jika bulan dan tahun yang dipilih sama dengan bulan dan tahun sekarang. "Hari ini" memakai zona waktu aplikasi `Asia/Jakarta` (WIB). | P2 |
| FR-JS-07 | Ringkasan waktu sholat **hari ini** di bagian atas (hanya pada bulan berjalan). | P2 |
| FR-JS-08 | Pesan jika lokasi atau jadwal tidak ditemukan, tahun tidak tersedia, atau request gagal. | P2 |

> **Penting:** layanan ini memakai **GET dan POST** ke API. Sisi pengguna ke situs kita cukup GET, sedangkan POST terjadi dari controller ke API. Ini sengaja dibuat agar konsep method terlihat jelas saat demonstrasi.

---

## 6. Spesifikasi API

Base URL: `https://equran.id`. Tidak perlu API key dan registrasi. Dokumentasi menyebut tidak ada rate limit dan CORS aktif.

### 6.1 Al-Qur'an (API v2)

| Fungsi | Method | Endpoint | Parameter |
|---|---|---|---|
| Daftar surat | GET | `/api/v2/surat` | — |
| Detail surat (ayat + audio) | GET | `/api/v2/surat/{nomor}` | `nomor` (1–114) |
| Tafsir surat | GET | `/api/v2/tafsir/{nomor}` | `nomor` (1–114) |

- Response memakai pembungkus status: `code`, `message`, `data`.
- Audio tersedia per ayat dan per surat dari 6 qari, format MP3.
- **ID qari** (terkonfirmasi dari SDK resmi, kecuali yang bertanda verifikasi):

| ID | Qari |
|---|---|
| `01` | Abdullah Al-Juhany |
| `02` | Abdul Muhsin Al-Qasim |
| `03` | Abdurrahman As-Sudais *(verifikasi)* |
| `04` | Ibrahim Al-Dossari |
| `05` | Misyari Rasyid Al-Afasy *(default)* |
| `06` | Yasser Al-Dosari |

### 6.2 Doa Harian

| Fungsi | Method | Endpoint | Parameter |
|---|---|---|---|
| Daftar doa | GET | `/api/doa` | `grup` (opsional), `tag` (opsional) |
| Detail doa | GET | `/api/doa/{id}` | `id` (1–228) |

- Total 228 doa dan dzikir.
- Konten: teks Arab berharakat, transliterasi Latin, terjemahan Indonesia, referensi sumber hadits.
- Satu doa dapat memiliki banyak tag (contoh: `["tidur","malam"]`).
- Dokumentasi menyebut format "Array + Status". **Bentuk pembungkus belum dipastikan** (lihat 6.5).

### 6.3 Jadwal Sholat

| Fungsi | Method | Endpoint | Body (JSON) |
|---|---|---|---|
| Daftar provinsi | GET | `/api/v2/shalat/provinsi` | — |
| Daftar kab/kota | POST | `/api/v2/shalat/kabkota` | `provinsi` (wajib) |
| Jadwal bulanan | POST | `/api/v2/shalat` | `provinsi` (wajib), `kabkota` (wajib), `bulan` (1–12, opsional), `tahun` (opsional) |

- Cakupan: 34 provinsi dan 517 kabupaten/kota. Data yang didokumentasikan hanya untuk **tahun 2026**.
- Nama provinsi dan kab/kota harus persis sesuai data API (ambil dari dropdown, jangan diketik).
- Key jadwal per hari: `tanggal`, `tanggal_lengkap`, `hari`, `imsak`, `subuh`, `terbit`, `dhuha`, `dzuhur`, `ashar`, `maghrib`, `isya`.

### 6.4 Catatan Key Data (Tahap "Kenali API")

Key berikut sebagian terkonfirmasi (teksArab, nomorAyat, audio per ID qari dan prev/next surat disebut di SDK resmi), sebagian masih **perkiraan yang wajib diverifikasi**:

| Layanan | Key yang kemungkinan dipakai |
|---|---|
| Daftar surat | `nomor`, `nama`, `namaLatin`, `jumlahAyat`, `tempatTurun`, `arti`, `deskripsi`, `audioFull` |
| Detail surat | `ayat[]` → `nomorAyat`, `teksArab`, `teksLatin`, `teksIndonesia`, `audio` (objek dengan kunci `"01"`–`"06"`) |
| Tafsir | `tafsir[]` → `ayat`, `teks` |
| Doa | `id`, `grup`, `nama`, `ar`, `tr`, `idn`, `tentang`, `tag` |

### 6.5 Daftar Verifikasi Wajib di Tahap 2

Beberapa perilaku API tidak tertulis di dokumentasi. Ujilah sendiri (fitur "Uji API" di dokumentasi atau `dd($response->json())`) dan catat hasilnya sebelum menulis Blade:

| # | Yang harus dipastikan | Cara uji | Hasil |
|---|---|---|---|
| V1 | Struktur JSON aktual daftar surat, detail surat, tafsir | `dd()` pada tiap response | |
| V2 | Apakah error memakai **status HTTP 404/500**, atau status 200 dengan `code` 404 di body | Minta `/surat/999`, `/doa/9999` | |
| V3 | Bentuk response Doa (array langsung, atau ada `status`/`data`) | `dd()` pada `/api/doa` | |
| V4 | Respons API untuk **lokasi salah** (provinsi/kab/kota tidak ada) | Kirim POST `/shalat` dengan nama ngawur | |
| V5 | Respons untuk **tahun tanpa data** (mis. 2027) | Kirim POST `/shalat` tahun 2027 | |
| V6 | ID qari `03` benar As-Sudais | Putar audio ayat dengan `03` | |
| V7 | Isi `tag` (array atau string) dan daftar `grup` yang tersedia | `dd()` pada `/api/doa` | |

Controller wajib dibuat **tahan terhadap dua kemungkinan V2**: memeriksa status HTTP **dan** `code` di body.

---

## 7. Desain Halaman dan Navigasi

### 7.1 Peta Situs

```
Beranda (/)
├── Al-Qur'an
│   ├── Daftar Surat       (/quran)
│   ├── Detail Surat       (/quran/{nomor}?qari=05&halaman=1)
│   └── Tafsir Surat       (/quran/{nomor}/tafsir?halaman=1)
├── Doa Harian
│   ├── Daftar Doa         (/doa?grup=…&tag=…&q=…)
│   └── Detail Doa         (/doa/{id})
└── Jadwal Sholat          (/jadwal-sholat?provinsi=…&kabkota=…&bulan=…&tahun=…)
    └── (internal) /jadwal-sholat/kabkota?provinsi=…   → JSON untuk dropdown
```

### 7.2 Rancangan Halaman (Dasar Sketsa)

Sketsa tangan atau wireframe wajib ditunjukkan kepada guru pada Tahap 3.

| Halaman | Komponen Utama |
|---|---|
| **Beranda** | Hero (nama + tagline), 3 kartu layanan, footer sumber data |
| **Daftar Surat** | Kolom pencarian, grid/daftar kartu surat |
| **Detail Surat** | Header info surat, pilihan qari, audio surat penuh, daftar ayat (Arab besar, Latin, terjemahan, tombol audio ayat), pembagian halaman, tombol tafsir, navigasi surat sebelum/sesudah |
| **Tafsir** | Judul surat, daftar tafsir per ayat, pembagian halaman |
| **Daftar Doa** | Pencarian, dropdown grup, chip tag, daftar kartu doa |
| **Detail Doa** | Judul, grup, tag, teks Arab, Latin, terjemahan, sumber, tombol kembali |
| **Jadwal Sholat** | Form (provinsi, kab/kota dinamis, bulan, tahun), kartu waktu hari ini, tabel jadwal bulanan |
| **Halaman pesan** | Tampilan khusus "tidak ditemukan" (404) dan "layanan tidak dapat dihubungi" |

### 7.3 Panduan Desain
- Tema hijau dan krem yang tenang, selaras dengan nuansa Islami.
- Teks Arab minimal 28 px, rata kanan, `dir="rtl"`, memakai font Arab (mis. *Amiri*).
- **Font Arab disimpan lokal** di `public/fonts` (tidak dari CDN) dengan cadangan font sistem, agar tetap tampil jika internet sekolah dibatasi.
- Teks Latin dan terjemahan lebih kecil namun jelas.
- Responsif untuk ponsel dan desktop; kontras cukup; tombol audio mudah ditekan.

---

## 8. Alur Request dan Response

Contoh: pengguna membuka detail surat Al-Fatihah.

```
Pengguna klik "Al-Fatihah"
        │  GET /quran/1
        ▼
   routes/web.php  ──►  QuranController@show(1)
                              │  1) validasi nomor 1–114 (di luar → 404, tanpa request API)
                              │  2) Http::get('https://equran.id/api/v2/surat/1')
                              ▼
                        API eQuran.id  ──►  JSON {code, message, data}
                              │
                              ▼
                  Controller memeriksa: koneksi → status gagal → data kosong
                   ├── gagal / kosong → view pesan
                   └── berhasil → potong ayat per halaman → view quran.show
                              │
                              ▼
                  Browser menampilkan ayat; audio dimuat browser langsung
                  dari URL audio (CDN) saat tombol putar ditekan
```

Alur jadwal sholat (menunjukkan GET dan POST):

```
Pilih provinsi ─► JS: GET /jadwal-sholat/kabkota?provinsi=Jawa Barat (ke situs kita)
                       └► controller: POST /api/v2/shalat/kabkota (ke API) ─► JSON ─► isi dropdown
Klik "Tampilkan" ─► GET /jadwal-sholat?provinsi=…&kabkota=…&bulan=…&tahun=…
                       └► controller: POST /api/v2/shalat (ke API) ─► tabel jadwal
```

Bahan jawaban Pertanyaan Laporan No. 2.

---

## 9. Penanganan Kondisi Khusus

| Kondisi | Contoh Pemicu | Perilaku yang Diharapkan |
|---|---|---|
| **Parameter tidak valid** | `/quran/999`, `/quran/abc`, `/doa/0` | 404 langsung dari controller, tanpa memanggil API. |
| **Data tidak ditemukan** | Filter tag tanpa hasil, jadwal tidak tersedia, API menjawab 404 | Pesan jelas (mis. "Doa tidak ditemukan") dan tautan kembali. |
| **Request gagal** | API tidak merespons, timeout, status 5xx, tidak ada internet | Tangkap dengan `try/catch` dan `Http::timeout()`. Tampilkan "Layanan sedang tidak dapat dihubungi, coba lagi nanti" dan tombol muat ulang. **Bukan** pesan "tidak ditemukan". |
| **Respons tidak sesuai** | Key hilang atau berbeda | Pakai `data_get()` atau pemeriksaan null, jangan sampai error 500. |
| **Input jadwal tidak valid** | Provinsi atau kab/kota kosong, bulan di luar 1–12, tahun tidak tersedia | Validasi di controller, tampilkan pesan validasi. |
| **Dropdown kab/kota gagal dimuat** | Endpoint internal error | Dropdown menampilkan "Gagal memuat, pilih ulang provinsi". |
| **Audio tidak tersedia** | URL audio kosong atau gagal dimuat | Sembunyikan pemutar atau tampilkan "Audio tidak tersedia". |
| **URL tidak dikenal** | `/halaman-ngawur` | Halaman 404 khusus. |

Contoh kerangka controller (urutan pemeriksaan **penting**: koneksi → gagal → kosong):

```php
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\ConnectionException;

public function show(int $nomor, Request $request)
{
    abort_unless($nomor >= 1 && $nomor <= 114, 404);          // FR-06

    try {
        $response = Http::timeout(10)->get(config('equran.base_url') . "/api/v2/surat/{$nomor}");
    } catch (ConnectionException $e) {
        return response()->view('pesan.api', ['pesan' => 'Layanan tidak dapat dihubungi.'], 503);
    }

    // 1) Gagal selain "tidak ada"  → pesan layanan bermasalah (5xx, dll.)
    if ($response->failed() && $response->status() !== 404) {
        return response()->view('pesan.api', ['pesan' => 'Terjadi kesalahan pada layanan.'], 502);
    }

    // 2) Tidak ditemukan: status 404 ATAU code 404 di body ATAU data kosong
    if ($response->status() === 404 || $response->json('code') === 404 || empty($response->json('data'))) {
        return response()->view('pesan.notfound', ['pesan' => 'Surat tidak ditemukan.'], 404);
    }

    $surat  = $response->json('data');
    $qari    = $request->query('qari', config('equran.default_qari'));
    $halaman = max(1, (int) $request->query('halaman', 1));
    $ayat    = array_slice($surat['ayat'], ($halaman - 1) * config('equran.per_halaman'), config('equran.per_halaman'));

    return view('quran.show', compact('surat', 'ayat', 'qari'));
}
```

---

## 10. Kebutuhan Non-Fungsional

| Aspek | Kebutuhan |
|---|---|
| **Akurasi konten** | Teks Arab, Latin, terjemahan, tafsir, dan sumber ditampilkan sesuai API, tanpa diubah, dipotong, atau diedit (pembagian halaman hanya membagi daftar ayat, bukan memotong isi ayat). |
| **Keterbacaan Arab** | UTF-8, arah RTL, font Arab lokal yang mendukung harakat. |
| **Tanpa cache** | Tidak ada `Cache::remember` pada respons API. Setiap kunjungan memicu request baru. |
| **Kinerja** | Halaman memuat < 3 detik pada koneksi normal. Dicapai dengan pembagian halaman ayat (20/halaman) dan `preload="none"` pada audio. |
| **Keandalan** | Tidak ada halaman error mentah. Seluruh kegagalan ditangani. |
| **Kompatibilitas** | Chrome, Firefox, dan Edge versi terbaru. Ponsel dan desktop. |
| **Keamanan** | Tanpa API key. Base URL di `.env`. Escape output Blade (`{{ }}`). Validasi parameter rute dan input form. Form memakai GET sehingga tidak memerlukan CSRF. |
| **Kemudahan perubahan** | Nilai yang mungkin diubah guru dikumpulkan di `config/equran.php` (base URL, qari default, jumlah ayat per halaman, tahun jadwal tersedia). Tampilan berulang (kartu surat, kartu doa, pemutar audio) dibuat sebagai komponen Blade agar perubahan kecil cepat dilakukan saat demonstrasi (Tahap 6). |
| **Etika konten** | Ayat dan doa ditampilkan dengan hormat. Cantumkan sumber data (Kemenag RI dan eQuran.id) di footer. |

---

## 11. Arsitektur dan Teknologi

| Komponen | Pilihan |
|---|---|
| Framework | Laravel (versi sesuai kelas) |
| Bahasa | PHP 8.x |
| Template | Blade + komponen Blade |
| HTTP Client | `Illuminate\Support\Facades\Http` |
| Styling | Bootstrap 5 atau Tailwind CSS (pilih salah satu) |
| JavaScript | Vanilla JS: pemutar audio (satu audio aktif) dan dropdown kab/kota dinamis |
| Database & Cache | **Tidak digunakan** |
| Versi kontrol | Git + repository (GitHub/GitLab) |

### 11.1 Struktur Berkas yang Diusulkan

```
app/Http/Controllers/
  ├── HomeController.php
  ├── QuranController.php        # index, show, tafsir
  ├── DoaController.php          # index, show
  └── JadwalSholatController.php # index, kabkota (JSON)
config/equran.php                # base_url, default_qari, per_halaman, tahun_jadwal, daftar qari
resources/views/
  ├── layouts/app.blade.php      # navbar + footer
  ├── components/                # kartu-surat, kartu-doa, pemutar-audio
  ├── home.blade.php
  ├── quran/{index,show,tafsir}.blade.php
  ├── doa/{index,show}.blade.php
  ├── jadwal/index.blade.php
  └── pesan/{notfound,api}.blade.php   # + errors/404.blade.php
public/fonts/                    # font Arab lokal
public/js/{audio.js,jadwal.js}
routes/web.php
.env                             # EQURAN_BASE_URL=https://equran.id
```

### 11.2 Rute

| Method | URI | Controller@method | Catatan |
|---|---|---|---|
| GET | `/` | `HomeController@index` | |
| GET | `/quran` | `QuranController@index` | `?q=` pencarian |
| GET | `/quran/{nomor}` | `QuranController@show` | `whereNumber`, `?qari=&halaman=` |
| GET | `/quran/{nomor}/tafsir` | `QuranController@tafsir` | `whereNumber`, `?halaman=` |
| GET | `/doa` | `DoaController@index` | `?grup=&tag=&q=` |
| GET | `/doa/{id}` | `DoaController@show` | `whereNumber` |
| GET | `/jadwal-sholat` | `JadwalSholatController@index` | `?provinsi=&kabkota=&bulan=&tahun=` |
| GET | `/jadwal-sholat/kabkota` | `JadwalSholatController@kabkota` | endpoint internal, mengembalikan JSON |
| fallback | (lainnya) | view 404 | FR-07 |

---

## 12. Rencana Pengujian

Mengacu Tahap 5 LKPD: uji kondisi normal, data tidak ditemukan, dan kegagalan request, serta periksa audio.

| ID | Skenario | Langkah | Hasil yang Diharapkan | Hasil Aktual | Status |
|---|---|---|---|---|---|
| T-01 | Daftar surat normal | Buka `/quran` | 114 surat tampil | | |
| T-02 | Cari surat | Ketik "al fatihah" dan "36" | Surat terkait tampil (tahan variasi spasi/tanda hubung) | | |
| T-03 | Detail surat normal | Buka `/quran/1` | Ayat Arab, Latin, terjemahan tampil | | |
| T-04 | Nomor di luar rentang | Buka `/quran/999` | Halaman 404, **tanpa** request ke API | | |
| T-05 | Nomor bukan angka | Buka `/quran/abc` | Halaman 404 | | |
| T-06 | Pembagian halaman | Buka `/quran/2` (Al-Baqarah) | 20 ayat per halaman, navigasi halaman berfungsi, isi ayat utuh | | |
| T-07 | Audio surat | Putar lalu jeda | Memutar, lalu berhenti saat dijeda | | |
| T-08 | Audio per ayat | Putar ayat 2, lalu ayat 3 | Ayat 2 berhenti, ayat 3 memutar | | |
| T-09 | Pilih qari | Ganti ke qari `04` | Audio berganti qari, pilihan terbawa saat pindah halaman | | |
| T-10 | Tafsir | Buka tafsir surat 1 | Tafsir per ayat tampil | | |
| T-11 | Daftar doa | Buka `/doa` | Daftar doa tampil | | |
| T-12 | Pilihan grup dan tag | Buka `/doa` | Dropdown grup dan chip tag terisi dari data API | | |
| T-13 | Filter grup | Pilih satu grup | Hanya doa grup itu tampil; pilihan grup tetap lengkap | | |
| T-14 | Filter tag kosong | Tag yang tidak ada (ubah URL) | Pesan "doa tidak ditemukan" | | |
| T-15 | Detail doa | Buka `/doa/1` | Arab, Latin, terjemahan, sumber, grup, tag tampil | | |
| T-16 | Doa tidak ada | Buka `/doa/9999` | Halaman 404 | | |
| T-17 | Dropdown provinsi | Buka `/jadwal-sholat` | 34 provinsi tampil | | |
| T-18 | Kab/kota dinamis | Pilih provinsi | Dropdown kab/kota terisi sesuai provinsi tanpa reload halaman | | |
| T-19 | Jadwal bulanan | Pilih lokasi + bulan + tahun | Tabel dengan 8 waktu tampil; URL bisa di-refresh | | |
| T-20 | Highlight hari ini | Pilih bulan berjalan | Baris hari ini ditandai; bulan lain tanpa tanda | | |
| T-21 | Tahun tidak tersedia | Ubah URL ke `tahun=2027` | Pesan tahun tidak tersedia, bukan tabel kosong | | |
| T-22 | Input kosong | Kirim form tanpa pilihan | Pesan validasi | | |
| T-23 | **Request gagal** | Matikan internet **atau** ubah `EQURAN_BASE_URL` ke alamat salah | Pesan "layanan tidak dapat dihubungi", bukan error 500 dan **bukan** "tidak ditemukan" | | |
| T-24 | URL tidak dikenal | Buka `/halaman-ngawur` | Halaman 404 khusus | | |
| T-25 | Responsif | Perkecil jendela ke lebar ponsel | Tata letak rapi dan terbaca | | |

> Simpan **bukti pengujian** (tangkapan layar) untuk tiap skenario sebagai lampiran laporan. Karena tidak memakai cache, T-23 menguji kegagalan sebenarnya.

---

## 13. Rencana Kerja (8 JP)

Usulan pembagian waktu, sesuaikan dengan arahan guru.

| Tahap LKPD | Kegiatan | Keluaran | Estimasi |
|---|---|---|---|
| **1. Tentukan konsep** | Tetapkan nama, target pengguna, manfaat, layanan (dokumen PRD ini) | PRD final | 0,5 JP |
| **2. Kenali API** | Baca dokumentasi, uji endpoint, isi **daftar verifikasi V1–V7** | Tabel catatan API + hasil verifikasi | 1 JP |
| **3. Rancang halaman** | Sketsa tampilan dan navigasi, tunjukkan ke guru | Sketsa + persetujuan guru | 1 JP |
| **4. Bangun dan integrasikan** | Setup Laravel, bangun layanan 1, lalu 2, lalu 3 | Aplikasi berjalan | 3,5 JP |
| **5. Uji dan perbaiki** | Jalankan tabel pengujian, perbaiki masalah, cek audio | Tabel uji + bukti | 1 JP |
| **6. Demonstrasi** | Presentasikan alur satu request, siap perubahan kecil dari guru | Demo + laporan | 1 JP |

### Urutan Pembangunan
1. Setup Laravel, `config/equran.php`, layout, navbar, beranda, halaman 404 dan pesan.
2. **Al-Qur'an**: daftar → pencarian → detail + pembagian halaman → audio → tafsir.
3. **Doa**: daftar → filter → detail.
4. **Jadwal sholat** (pengayaan): provinsi → kab/kota dinamis → jadwal → highlight hari ini.
5. Rapikan tampilan, uji, dokumentasi.

### Jika Waktu Terbatas, Urutan yang Dikorbankan (dari yang pertama dilepas)
1. Pengayaan P2 pada Jadwal Sholat: FR-JS-07 (ringkasan hari ini), FR-JS-06 (highlight).
2. FR-QR-09 (pilih qari), FR-QR-10 (surat sebelum/sesudah), FR-QR-11 (satu audio aktif).
3. FR-DO-07 (pencarian judul doa), FR-08 (loading).
4. Layanan Jadwal Sholat seluruhnya.

Seluruh item **P1** tidak boleh dilepas.

---

## 14. Hasil yang Dikumpulkan

- [ ] Proyek atau repository (Laravel, dapat dijalankan)
- [ ] `README.md` (deskripsi, versi PHP/Laravel, cara instalasi dan menjalankan, daftar fitur, sumber API, **catatan batasan**: data jadwal hanya 2026, "hari ini" memakai WIB)
- [ ] Sketsa halaman
- [ ] Laporan singkat berisi jawaban 3 pertanyaan laporan
- [ ] Bukti pengujian (tangkapan layar atau tabel uji) + tabel verifikasi V1–V7

---

## 15. Kerangka Jawaban Pertanyaan Laporan

1. **Mengapa layanan pilihanmu sesuai dengan tujuan website?**
   Al-Qur'an dan Doa Harian mendukung ibadah membaca dan berdoa. Jadwal Sholat melengkapi dengan pengingat waktu ibadah. Ketiganya melayani satu tujuan: mendampingi ibadah harian pengguna.

2. **Bagaimana request diproses hingga informasi tampil?**
   Jelaskan alur pada bagian 8: klik pengguna → route → validasi → controller → request ke API → response JSON → pemeriksaan koneksi/status/data → view Blade → tampil di browser. Tunjukkan perbedaan GET (surat, doa) dan POST (jadwal sholat, dari controller ke API).

3. **Masalah apa yang ditemukan saat pengujian, dan bagaimana perbaikannya?**
   Isi dari pengalaman nyata (tabel bagian 12 dan verifikasi 6.5). Contoh yang mungkin terjadi: nama kab/kota tidak cocok dengan data API, key JSON berbeda dari perkiraan, dua audio berbunyi bersamaan, halaman Al-Baqarah lambat, error 500 saat API tidak merespons.

---

## 16. Kriteria Penerimaan (Mengacu Ketentuan Website LKPD)

| # | Ketentuan LKPD | Bukti dalam PRD ini | Terpenuhi |
|---|---|---|---|
| 1 | Minimal dua layanan berfungsi dan terhubung melalui navigasi | FR-02, Al-Qur'an + Doa (+ Jadwal) | ☐ |
| 2 | Ada interaksi pengguna pada setiap layanan | Pencarian surat, filter doa, form jadwal, tombol audio | ☐ |
| 3 | Konten Arab terbaca baik; informasi sesuai sumber API | Bagian 7.3 dan 10 | ☐ |
| 4 | Audio wajib jika tersedia pada layanan yang dipilih | FR-QR-07, FR-QR-08 | ☐ |
| 5 | Ada pesan saat data tidak ditemukan atau request gagal | FR-04, FR-05, FR-06, FR-07, bagian 9 | ☐ |
| 6 | Layanan ketiga menjadi pengayaan | Jadwal Sholat (P2) | ☐ |
| 7 | Request API dilakukan melalui controller | FR-03, K4, bagian 11 | ☐ |

---

## 17. Risiko dan Mitigasi

| Risiko | Dampak | Mitigasi |
|---|---|---|
| API tidak dapat diakses saat demo (gangguan server atau internet sekolah) | Demo gagal | Tanpa cache berarti bergantung koneksi. Siapkan **tangkapan layar atau rekaman layar cadangan**, uji koneksi sebelum demo, dan tunjukkan pesan error yang sudah ditangani sebagai bukti. |
| Struktur JSON atau pola error berbeda dari perkiraan | Halaman kosong atau pesan salah | Selesaikan daftar verifikasi V1–V7 di Tahap 2, controller memeriksa status HTTP dan `code` body, gunakan `data_get()`. |
| Salah nama provinsi atau kab/kota pada POST | Jadwal tidak ditemukan | Ambil nilai dari dropdown yang bersumber dari API. |
| Tahun jadwal di luar data (2027 dst.) | Tabel kosong | Dropdown tahun dibatasi lewat `config/equran.php`, pesan khusus jika data tidak ada. |
| Halaman surat panjang lambat | Pengguna menunggu lama | Pembagian halaman 20 ayat, `preload="none"` pada audio. |
| Teks Arab tampil rusak | Konten sulit dibaca | UTF-8, `dir="rtl"`, font Arab lokal, uji di beberapa browser. |
| "Hari ini" berbeda di WITA/WIT | Sorotan baris bisa meleset 1–2 jam di sekitar tengah malam | Dicatat sebagai batasan di README (memakai WIB). |
| JavaScript dropdown gagal | Jadwal tidak bisa dipilih | Tampilkan pesan di dropdown dan siapkan cara muat ulang. |
| Waktu 8 JP terbatas | Layanan ketiga tidak selesai | Ikuti daftar urutan item yang dikorbankan (bagian 13). |
| Perbedaan versi Laravel atau PHP di komputer sekolah | Proyek tidak jalan | Catat versi di README dan uji ulang di komputer demo. |
| Dokumentasi API berubah | Endpoint tidak cocok | Rujuk https://equran.id/apidev dan uji ulang sebelum demo. |

---

## 18. Asumsi dan Pertanyaan Terbuka

### Keputusan yang Sudah Dikonfirmasi
1. **Tanpa cache** (sesuai LKPD).
2. Dropdown kab/kota **dinamis** dengan endpoint internal dan JavaScript.
3. Layanan wajib: Al-Qur'an dan Doa Harian; pengayaan: Jadwal Sholat.

### Asumsi
1. Nama website "Cahaya Harian" bersifat sementara.
2. Proyek tidak memakai database dan tidak ada akun pengguna.
3. Memakai **API v2** untuk Al-Qur'an (direkomendasikan, mendukung audio).
4. Pengerjaan individu di komputer yang sudah terpasang PHP, Composer, dan Laravel.
5. Key JSON yang bertanda perkiraan diverifikasi di Tahap 2 (bagian 6.5).
6. Pembagian halaman dilakukan di controller (API mengirim satu surat penuh per request).

### Perlu Dikonfirmasi ke Guru
| # | Pertanyaan | Mengapa Penting |
|---|---|---|
| Q1 | Versi Laravel dan PHP yang dipakai di kelas? | Menentukan sintaks dan kompatibilitas |
| Q2 | Bolehkah memakai Bootstrap/Tailwind dan JavaScript (termasuk `fetch`) atau hanya Blade murni? | JavaScript dibutuhkan untuk dropdown dinamis dan pemutar audio |
| Q3 | Apakah ada rubrik penilaian resmi (bobot per aspek)? | Menyesuaikan prioritas pengerjaan |
| Q4 | Apakah pengayaan memengaruhi nilai tambahan, dan seberapa lengkap yang diharapkan? | Menentukan kedalaman fitur Jadwal Sholat |
| Q5 | Format laporan dan sketsa (kertas, PDF, atau digital) dan cara pengumpulan repository? | Menyiapkan deliverable dengan benar |
| Q6 | Apakah pembagian halaman ayat (bukan menampilkan seluruh surat sekaligus) dapat diterima sebagai tampilan "seluruh konten"? | LKPD meminta memanfaatkan seluruh konten relevan; pembagian halaman tetap menampilkan semua ayat, tapi bertahap |

---

## 19. Riwayat Dokumen

| Versi | Tanggal | Perubahan | Penulis |
|---|---|---|---|
| 1.0 | ____________ | Draft awal berdasarkan LKPD | ____________ |
| 1.1 | ____________ | Revisi review: kebijakan tanpa cache; contoh controller diperbaiki (urutan cek error); endpoint internal dan form GET untuk jadwal; pembatasan tahun jadwal; sumber pilihan grup/tag doa; klarifikasi K4; validasi rute dan halaman 404; pembagian halaman ayat dan `preload="none"`; zona waktu "hari ini"; pemetaan ID qari; daftar verifikasi V1–V7; font lokal; penyesuaian estimasi dan urutan pengorbanan; kemudahan perubahan (config + komponen Blade); tambahan skenario uji | ____________ |

---

*Sumber data konten: Kementerian Agama RI (Al-Qur'an dan jadwal sholat via Bimas Islam) melalui eQuran.id. Dokumentasi: https://equran.id/apidev*
