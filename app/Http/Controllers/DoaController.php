<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class DoaController extends Controller
{
    /**
     * Menampilkan daftar doa dengan pencarian dan filter grup/tag.
     * Pencarian dan filter diproses di sini memakai data dari API, bukan di API.
     */
    public function index(Request $request): View|Response
    {
        try {
            // Minta daftar doa ke API eQuran.id
            $response = Http::timeout(10)->get(config('equran.base_url').'/api/doa');
        } catch (ConnectionException $e) {
            return response()->view('pesan.api', ['pesan' => 'Layanan tidak dapat dihubungi.'], 503);
        }

        // Kegagalan layanan selain "tidak ditemukan" dianggap gangguan server
        if ($response->failed() && $response->status() !== 404) {
            return response()->view('pesan.api', ['pesan' => 'Terjadi kesalahan pada layanan.'], 502);
        }

        $daftarDoa = $response->json('data');

        // Tidak ditemukan: status 404, status "error" pada body, atau data kosong
        if ($response->status() === 404 || $response->json('status') === 'error' || empty($daftarDoa)) {
            return response()->view('pesan.notfound', ['pesan' => 'Daftar doa tidak ditemukan.'], 404);
        }

        // Filter dari query string; hanya dipakai bila berbentuk teks
        $kataKunci = $request->query('q');
        $kataKunci = is_string($kataKunci) ? trim($kataKunci) : '';
        $grup = $request->query('grup');
        $grup = is_string($grup) ? trim($grup) : '';
        $tag = $request->query('tag');
        $tag = is_string($tag) ? trim($tag) : '';

        // Saring dulu, baru bagi menjadi halaman (20 doa per halaman)
        $hasilSaring = $this->saringDoa($daftarDoa, $kataKunci, $grup, $tag);
        $perHalaman = (int) config('equran.per_halaman');
        $totalHalaman = max(1, (int) ceil(count($hasilSaring) / $perHalaman));
        $halaman = min(max(1, $request->integer('halaman', 1)), $totalHalaman);

        return view('doa.index', [
            'daftarDoa' => array_slice($hasilSaring, ($halaman - 1) * $perHalaman, $perHalaman),
            'daftarGrup' => $this->ambilGrup($daftarDoa),
            'daftarTag' => $this->ambilTag($daftarDoa),
            'totalDoa' => (int) $response->json('total'),
            'jumlahHasil' => count($hasilSaring),
            'halaman' => $halaman,
            'totalHalaman' => $totalHalaman,
            'kataKunci' => $kataKunci,
            'grupTerpilih' => $grup,
            'tagTerpilih' => $tag,
        ]);
    }

    /**
     * Menampilkan satu doa lengkap: teks Arab, transliterasi, arti, dan keterangan.
     */
    public function show(int $id): View|Response
    {
        // Id doa dimulai dari 1; id yang tidak ada akan dijawab 404 oleh API
        abort_unless($id >= 1, 404);

        try {
            $response = Http::timeout(10)->get(config('equran.base_url')."/api/doa/{$id}");
        } catch (ConnectionException $e) {
            return response()->view('pesan.api', ['pesan' => 'Layanan tidak dapat dihubungi.'], 503);
        }

        if ($response->failed() && $response->status() !== 404) {
            return response()->view('pesan.api', ['pesan' => 'Terjadi kesalahan pada layanan.'], 502);
        }

        if ($response->status() === 404 || $response->json('status') === 'error' || empty($response->json('data'))) {
            return response()->view('pesan.notfound', ['pesan' => "Doa nomor {$id} tidak ditemukan."], 404);
        }

        return view('doa.show', ['doa' => $response->json('data')]);
    }

    /**
     * Menyaring daftar doa berdasarkan pencarian nama, filter grup, dan filter tag.
     * Spasi dan tanda hubung diabaikan saat mencari nama doa.
     */
    private function saringDoa(array $daftarDoa, string $kataKunci, string $grup, string $tag): array
    {
        $cari = $this->normalisasi($kataKunci);

        $hasil = array_filter($daftarDoa, function (array $doa) use ($cari, $kataKunci, $grup, $tag) {
            // Filter grup: nama grup harus sama persis
            if ($grup !== '' && (string) data_get($doa, 'grup') !== $grup) {
                return false;
            }

            // Filter tag: tag doa berbentuk daftar, cukup salah satu yang cocok
            if ($tag !== '' && ! in_array($tag, (array) data_get($doa, 'tag', []), true)) {
                return false;
            }

            // Pencarian nama doa
            if ($kataKunci !== '' && ! str_contains($this->normalisasi((string) data_get($doa, 'nama')), $cari)) {
                return false;
            }

            return true;
        });

        return array_values($hasil);
    }

    /**
     * Mengambil daftar grup unik (terurut) untuk pilihan filter.
     */
    private function ambilGrup(array $daftarDoa): array
    {
        $grup = array_map(fn (array $doa) => (string) data_get($doa, 'grup'), $daftarDoa);
        $grup = array_values(array_unique($grup));
        sort($grup);

        return $grup;
    }

    /**
     * Mengambil daftar tag unik (terurut) untuk pilihan filter.
     */
    private function ambilTag(array $daftarDoa): array
    {
        $daftarTag = [];
        foreach ($daftarDoa as $doa) {
            foreach ((array) data_get($doa, 'tag', []) as $tag) {
                $daftarTag[trim((string) $tag)] = true;
            }
        }
        $daftarTag = array_keys($daftarTag);
        sort($daftarTag);

        return $daftarTag;
    }

    /**
     * Menormalkan teks pencarian: huruf kecil, tanpa spasi dan tanda hubung.
     */
    private function normalisasi(string $teks): string
    {
        return str_replace([' ', '-'], '', mb_strtolower($teks));
    }
}
