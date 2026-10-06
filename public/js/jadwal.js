// jadwal.js — mengisi dropdown kabupaten/kota secara dinamis.
// Saat provinsi dipilih, daftar kab/kota diminta lewat endpoint internal
// /jadwal-sholat/kabkota; controller meneruskannya ke API eQuran.id lalu membalas JSON.

document.addEventListener('DOMContentLoaded', function () {
    const pilihProvinsi = document.getElementById('pilihProvinsi');
    const pilihKabkota = document.getElementById('pilihKabkota');
    const catatan = document.getElementById('catatanKabkota');
    const form = document.querySelector('form[data-url-kabkota]');

    if (!pilihProvinsi || !pilihKabkota || !form || !catatan) {
        return;
    }

    const urlKabkota = form.dataset.urlKabkota;
    let permintaanTerakhir = 0;

    // Ganti isi dropdown dengan satu pilihan placeholder
    function tampilkanPlaceholder(teks) {
        pilihKabkota.innerHTML = '';

        const kosong = document.createElement('option');
        kosong.value = '';
        kosong.textContent = teks;
        pilihKabkota.appendChild(kosong);
    }

    // Muat daftar kab/kota untuk satu provinsi; kabkotaTerpilih dipertahankan
    // agar pilihan dari URL tetap terpilih setelah halaman dimuat ulang.
    function muatKabkota(namaProvinsi, kabkotaTerpilih) {
        const nomorPermintaan = ++permintaanTerakhir;

        pilihKabkota.disabled = true;
        tampilkanPlaceholder('Memuat daftar kabupaten/kota…');
        catatan.textContent = 'Memuat daftar kabupaten/kota…';

        fetch(urlKabkota + '?provinsi=' + encodeURIComponent(namaProvinsi), {
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
                // Abaikan jawaban yang sudah usang bila provinsi diganti lagi
                if (nomorPermintaan !== permintaanTerakhir) {
                    return;
                }

                if (!jawaban.ok || !jawaban.hasil || !Array.isArray(jawaban.hasil.data)) {
                    throw new Error((jawaban.hasil && jawaban.hasil.pesan) || 'Gagal memuat daftar kabupaten/kota.');
                }

                pilihKabkota.innerHTML = '';
                tampilkanPlaceholder('— Pilih kabupaten/kota —');

                jawaban.hasil.data.forEach(function (nama) {
                    const opsi = document.createElement('option');
                    opsi.value = nama;
                    opsi.textContent = nama;

                    if (nama === kabkotaTerpilih) {
                        opsi.selected = true;
                    }

                    pilihKabkota.appendChild(opsi);
                });

                pilihKabkota.disabled = false;
                catatan.textContent = jawaban.hasil.data.length + ' kabupaten/kota tersedia.';
            })
            .catch(function (kesalahan) {
                if (nomorPermintaan !== permintaanTerakhir) {
                    return;
                }

                // Gagal: tampilkan pesan pada dropdown dan catatan di bawahnya
                tampilkanPlaceholder('— Gagal memuat —');
                pilihKabkota.disabled = true;
                catatan.textContent = kesalahan.message || 'Gagal memuat daftar kabupaten/kota.';
            });
    }

    // Saat provinsi diganti: muat ulang daftar kabupaten/kota
    pilihProvinsi.addEventListener('change', function () {
        if (pilihProvinsi.value === '') {
            tampilkanPlaceholder('— Pilih provinsi dulu —');
            pilihKabkota.disabled = true;
            catatan.textContent = 'Pilih provinsi untuk memuat daftar kabupaten/kota.';

            return;
        }

        muatKabkota(pilihProvinsi.value, '');
    });

    // Saat halaman dibuka: bila provinsi sudah terpilih (dari URL), isi kab/kota
    if (pilihProvinsi.value !== '') {
        muatKabkota(pilihProvinsi.value, pilihKabkota.dataset.terpilih || '');
    }
});
