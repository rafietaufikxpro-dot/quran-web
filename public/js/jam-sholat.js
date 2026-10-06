// jam-sholat.js — widget jam langsung (WIB) + hitung mundur ke waktu sholat
// berikutnya. Data harian diminta lewat endpoint internal /jadwal-sholat/hari-ini;
// controller meneruskannya ke API eQuran.id lalu membalas JSON.
//
// Jam dasar memakai jam server (WIB) saat data diterima, lalu berjalan dengan
// selisih waktu perangkat; hitung mundur tetap benar walaupun jam perangkat
// tidak sama dengan WIB.
//
// Lokasi pilihan pengguna disimpan di localStorage ('cahaya.lokasiSholat');
// bila belum ada, dipakai lokasi bawaan dari atribut data (Kota Jakarta).

document.addEventListener('DOMContentLoaded', function () {
    const kotak = document.getElementById('jamSholat');

    if (!kotak) {
        return;
    }

    const elLokasi = document.getElementById('jamSholatLokasi');
    const elSekarang = document.getElementById('jamSholatSekarang');
    const elBerikutnya = document.getElementById('jamSholatBerikutnya');
    const elMundur = document.getElementById('jamSholatHitungMundur');
    const elPesan = document.getElementById('jamSholatPesan');

    const KUNCI_SIMPANAN = 'cahaya.lokasiSholat';
    const URL_HARI_INI = kotak.dataset.url;
    const JEDA_MUAT = 15000; // jeda minimal antar permintaan (ms)

    // Lokasi yang dipakai widget, ditentukan sekali saat halaman dibuka
    const lokasi = tentukanLokasi();

    let dasarDetik = null; // jam WIB (detik sejak tengah malam) saat data diterima
    let dasarWaktu = 0;    // nilai Date.now() saat data diterima
    let berikutnya = null; // { detik, besok } waktu sholat berikutnya
    let sedangMemuat = false;
    let terakhirMuat = 0;

    // Baca lokasi tersimpan; data rusak/tidak lengkap dianggap tidak ada
    function bacaSimpanan() {
        try {
            const hasil = JSON.parse(localStorage.getItem(KUNCI_SIMPANAN) || 'null');

            if (hasil && hasil.provinsi && hasil.kabkota) {
                return { provinsi: hasil.provinsi, kabkota: hasil.kabkota };
            }
        } catch (kesalahan) {
            // Abaikan data rusak
        }

        return null;
    }

    // Simpan lokasi terpilih; bila penyimpanan tidak tersedia, widget tetap jalan
    function simpanLokasi(pilihan) {
        try {
            localStorage.setItem(KUNCI_SIMPANAN, JSON.stringify(pilihan));
        } catch (kesalahan) {
            // Abaikan
        }
    }

    // Tentukan lokasi widget berurutan dari: lokasi halaman jadwal yang sedang
    // dilihat (atribut data-simpan=1), lalu simpanan pengguna, lalu lokasi bawaan
    function tentukanLokasi() {
        const bawaan = {
            provinsi: kotak.dataset.provinsi || '',
            kabkota: kotak.dataset.kabkota || '',
        };

        if (kotak.dataset.simpan === '1' && bawaan.provinsi && bawaan.kabkota) {
            simpanLokasi(bawaan);

            return bawaan;
        }

        return bacaSimpanan() || bawaan;
    }

    function angka2(nilai) {
        return String(nilai).padStart(2, '0');
    }

    // Ubah jumlah detik menjadi teks "JJ:MM:DD"
    function detikKeTeks(detik) {
        const jam = Math.floor(detik / 3600);
        const menit = Math.floor((detik % 3600) / 60);

        return angka2(jam) + ':' + angka2(menit) + ':' + angka2(detik % 60);
    }

    // Ubah "HH:MM" menjadi jumlah detik sejak tengah malam
    function teksKeDetik(teks) {
        const bagian = String(teks).split(':');

        return (parseInt(bagian[0], 10) || 0) * 3600 + (parseInt(bagian[1], 10) || 0) * 60;
    }

    function tampilkanPesan(teks) {
        elPesan.textContent = teks;
        elPesan.classList.remove('d-none');
    }

    function sembunyikanPesan() {
        elPesan.textContent = '';
        elPesan.classList.add('d-none');
    }

    // Perbarui jam berjalan dan hitung mundur; dipanggil tiap detik
    function perbaruiJam() {
        if (dasarDetik === null) {
            return;
        }

        const sekarangDetik = dasarDetik + (Date.now() - dasarWaktu) / 1000;

        // Jam langsung berjalan dari jam dasar server, tanpa memanggil API lagi
        elSekarang.textContent = 'Sekarang ' + detikKeTeks(Math.floor(sekarangDetik) % 86400) + ' WIB';

        if (!berikutnya) {
            return;
        }

        const sisa = Math.floor(berikutnya.detik - sekarangDetik + (berikutnya.besok ? 86400 : 0));

        if (sisa > 0) {
            elMundur.textContent = '-' + detikKeTeks(sisa);

            return;
        }

        // Waktu sholat tercapai: minta data baru supaya lanjut ke waktu berikutnya
        elMundur.textContent = '-00:00:00';
        muatHariIni();
    }

    // Terapkan jawaban server: jam dasar, lokasi, dan waktu sholat berikutnya
    function terapkanData(hasil) {
        const bagianJam = String(hasil.jam_wib || '').split(':');

        if (bagianJam.length !== 3) {
            throw new Error('Jawaban server tidak lengkap.');
        }

        dasarDetik = (parseInt(bagianJam[0], 10) || 0) * 3600
            + (parseInt(bagianJam[1], 10) || 0) * 60
            + (parseInt(bagianJam[2], 10) || 0);
        dasarWaktu = Date.now();

        if (hasil.lokasi && hasil.lokasi.kabkota) {
            elLokasi.textContent = hasil.lokasi.kabkota;
            elLokasi.title = hasil.lokasi.provinsi + ' — ' + hasil.lokasi.kabkota;
        }

        const depan = hasil.berikutnya;

        if (!depan) {
            berikutnya = null;
            elBerikutnya.textContent = '—';
            elMundur.textContent = '—';
            tampilkanPesan(hasil.pesan || 'Waktu sholat berikutnya belum tersedia.');
            perbaruiJam();

            return;
        }

        berikutnya = {
            detik: teksKeDetik(depan.jam),
            besok: depan.tanggal !== hasil.tanggal_wib,
        };

        elBerikutnya.textContent = depan.nama + ' ' + depan.jam + ' WIB'
            + (berikutnya.besok ? ' (besok)' : '');
        sembunyikanPesan();
        perbaruiJam();
    }

    // Minta data hari ini dari endpoint internal
    function muatHariIni() {
        // Hindari permintaan ganda atau beruntun
        if (sedangMemuat || Date.now() - terakhirMuat < JEDA_MUAT) {
            return;
        }

        sedangMemuat = true;
        terakhirMuat = Date.now();

        fetch(URL_HARI_INI + '?provinsi=' + encodeURIComponent(lokasi.provinsi)
            + '&kabkota=' + encodeURIComponent(lokasi.kabkota), {
            headers: { 'Accept': 'application/json' },
        })
            .then(function (respons) {
                return respons.text().then(function (teks) {
                    let hasil = null;

                    try {
                        hasil = JSON.parse(teks);
                    } catch (kesalahan) {
                        hasil = null;
                    }

                    return { ok: respons.ok, hasil: hasil };
                });
            })
            .then(function (jawaban) {
                if (!jawaban.ok || !jawaban.hasil) {
                    throw new Error((jawaban.hasil && jawaban.hasil.pesan) || 'Gagal memuat jadwal sholat.');
                }

                terapkanData(jawaban.hasil);
            })
            .catch(function (kesalahan) {
                elBerikutnya.textContent = '—';
                elMundur.textContent = '—';
                tampilkanPesan(kesalahan.message || 'Gagal memuat jadwal sholat.');
            })
            .then(function () {
                sedangMemuat = false;
            });
    }

    muatHariIni();
    setInterval(perbaruiJam, 1000);
});
