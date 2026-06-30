# 20 Konsep Template CV — Resumify

Catatan pendekatan: nama template di bawah ini sengaja dibuat evokatif, bukan literal menjiplak nama institusi seperti "BUMN Official" atau "Government Pro". Target audiens tetap jelas, tapi diletakkan di kolom "Cocok Untuk" dan logika konten — bukan dipaksakan ke nama yang tampil ke user. Distribusi 20 template ini juga sengaja tidak 1:1 dengan 3 persona, karena kebutuhan tiap persona berbeda bobot:

- **Netral / ATS-Safe (4)** — pilihan default lintas persona, struktur paling "jujur" untuk parser ATS.
- **Awal Karier (4)** — kelompok yang sebelumnya kosong total di 12 template lama. Bedanya bukan di warna, tapi di urutan dan label section (Pendidikan & Proyek di atas, "Pengalaman Kerja" otomatis berubah jadi "Pengalaman Organisasi & Magang" kalau field pengalaman kerja kosong).
- **Korporat & Industri Besar (5)** — gantinya 6 template lama yang menumpuk di kategori professional tanpa diferensiasi jelas.
- **Startup & Teknologi (4)**
- **Kreatif & Personal Branding (3)**

Kolom "category" mengikuti enum yang sudah ada di kode (`professional`, `creative`, `technology`). Untuk membedakan career stage (fresh graduate vs senior) tanpa merusak taksonomi style yang sudah ada, rekomendasinya **bukan** menambah category baru di kolom yang sama (itu yang menyebabkan drift "managerial" di dokumen lama tidak pernah terimplementasi). Tambahkan kolom/field terpisah, misalnya `experience_level` (enum: `fresh_graduate`, `general`, `senior`), yang independen dari `category`. Career stage dan gaya visual adalah dua sumbu berbeda — jangan dipaksa jadi satu kolom.

---

## 1. Netral / ATS-Safe

| Nama | category | experience_level | ATS | Warna & Font | Deskripsi & Logika Konten |
|---|---|---|---|---|---|
| **Foundation** | professional | general | Aman (1 kolom murni) | Abu gelap `#1f2937` + putih, heading Source Serif, body Inter | Tidak ada elemen tabel/border dekoratif sama sekali. Ditawarkan sebagai pilihan default pertama sebelum user memilih gaya lain — cocok jadi "safety net" untuk semua persona. |
| **Slate** | professional | general | Aman | `#334155` + garis pembatas tipis, Inter heading+body | Hierarki tipografi tegas lewat ukuran & bold, bukan warna. Modern tanpa elemen yang berisiko gagal parsing. |
| **Quill** | professional | general | Aman | Hitam + krem `#faf7f2`, Lora heading, Source Sans body | Rasa klasik-formal, garis bawah nama sebagai satu-satunya aksen. Cocok untuk siapa saja yang ragu pilih gaya "berani". |
| **Northbound** | professional | general | Aman | Biru tua `#1e3a8a` accent border kiri (CSS border, bukan kolom kedua), Manrope | Satu kolom dengan garis aksen kiri tipis sebagai identitas visual minimal. Aman ATS karena secara struktur tetap linear. |

## 2. Awal Karier (fresh graduate)

| Nama | category | experience_level | ATS | Warna & Font | Deskripsi & Logika Konten |
|---|---|---|---|---|---|
| **Sprout** | professional | fresh_graduate | Aman | Hijau muda `#16a34a` aksen kecil, Manrope | IPK ditonjolkan (umum di rekrutmen Indonesia), section "Proyek Akhir/Tugas Kuliah" dan "Organisasi Kampus" diposisikan setara dengan pengalaman kerja, bukan sebagai pelengkap di bawah. |
| **First Draft** | professional | fresh_graduate | Aman | Abu netral, Inter | Section "Pengalaman Kerja" otomatis berlabel ulang jadi "Magang & Pengalaman Organisasi" ketika field pengalaman kerja kosong — logika konten, bukan cuma teks placeholder statis. |
| **Compass** | professional | fresh_graduate | Aman | Navy `#1e293b`, Lato | Ada ringkasan "Tujuan Karier" 2 baris di atas, membantu recruiter cepat paham arah pelamar yang belum punya track record spesifik di satu bidang. |
| **Blank Page** | professional | fresh_graduate | Aman | Putih dominan, garis abu tipis, Open Sans | Dirancang supaya CV tetap terasa "penuh" dan proporsional meski hanya berisi pendidikan, 1-2 sertifikasi online, dan kegiatan volunteer — tanpa terlihat kosong. |

## 3. Korporat & Industri Besar

| Nama | category | experience_level | ATS | Warna & Font | Deskripsi & Logika Konten |
|---|---|---|---|---|---|
| **Marlowe** | professional | senior | Aman | Hitam + abu `#374151`, Playfair Display heading | Header tebal nama+jabatan di atas, gaya formal-klasik yang familiar buat HR korporat besar maupun BUMN, tanpa menyebut institusi di nama template. |
| **Whitfield** | professional | senior | Aman | Biru dongker `#1e3a8a`, Times New Roman / Georgia | Garis pembatas antar-section tegas, tipografi besar untuk nama. Meniru format CV konservatif yang sudah dikenal screener HR perusahaan besar. |
| **The Ledger** | professional | senior | Aman | Hitam + emas tipis `#92722a` (garis saja, bukan blok warna), Merriweather | Terinspirasi estetika laporan tahunan: pencapaian terukur (%, Rp, jumlah tim) ditonjolkan dalam format rapi. Cocok untuk finance, operasional, manajemen. |
| **Boardroom** | professional | senior | Sedang (sidebar berbasis tabel) | Abu gelap + putih, Lora heading | Layout 70/30: konten utama dan sidebar ringkas (kontak, skill, sertifikasi) — dibangun dengan tabel HTML agar tetap kompatibel Dompdf, bukan flexbox/grid. Kesan eksekutif untuk posisi manajerial. |
| **Heritage** | professional | senior | Aman | Hitam putih murni, Garamond / Times New Roman | Tanpa warna sama sekali, paling konservatif dari semua template. Untuk industri sangat tradisional (perbankan, instansi pemerintah) tanpa nama template yang "menjiplak" nama instansi. |

## 4. Startup & Teknologi

| Nama | category | experience_level | ATS | Warna & Font | Deskripsi & Logika Konten |
|---|---|---|---|---|---|
| **Pulse** | technology | general | Aman | Ungu `#7c3aed`, Inter / Fira Code untuk label skill | Struktur "skill-first": tech stack ditonjolkan di atas riwayat pengalaman. Cocok untuk software engineer yang melamar startup. |
| **Sandbox** | technology | general | Aman | Oranye `#ea580c`, Poppins | Punya section khusus "Proyek Pribadi" dengan tautan GitHub/portofolio ditonjolkan setara pengalaman formal — relevan untuk kultur startup yang menilai side project. |
| **Loop** | technology | general | Sedang (timeline visual via list bertingkat) | Teal `#0d9488`, Roboto | Riwayat pengalaman ditampilkan sebagai garis waktu vertikal sederhana (list + border, bukan grafik kompleks), untuk kandidat dengan banyak transisi karier cepat. |
| **Northstar** | technology | general | Aman | Biru cerah `#2563eb`, Inter | Setiap bullet pengalaman punya ruang khusus untuk angka pencapaian (%, jumlah user, Rp) yang dibuat menonjol secara tipografi — sesuai budaya startup yang data-driven. |

## 5. Kreatif & Personal Branding

| Nama | category | experience_level | ATS | Warna & Font | Deskripsi & Logika Konten |
|---|---|---|---|---|---|
| **Canvas** | creative | general | Berisiko (2 kolom + foto) | Pink `#ec4899`, Poppins | Dua kolom dengan foto, untuk desainer/marketing/content creator yang CV-nya berfungsi sekaligus sebagai portofolio kecil. Wajib ada label peringatan skor ATS lebih rendah di UI. |
| **Aperture** | creative | general | Berisiko | Hitam + aksen kuning `#eab308`, Montserrat | Ada area khusus thumbnail karya/link portofolio. Untuk fotografer, videographer, UI/UX designer yang prioritasnya showcase visual, bukan lolos ATS. |
| **Mosaic** | creative | general | Berisiko | Multi-aksen terkontrol (2-3 warna dari palet tetap, bukan bebas), Oswald heading | Grid terstruktur (bukan acak) untuk profesi event/brand/social media yang ingin kesan ekspresif tapi tetap rapi secara layout. |

---

## Catatan implementasi

Semua template di kelompok "Berisiko" (Canvas, Aperture, Mosaic) dan template dengan sidebar tabel (Boardroom) harus dibangun dengan tabel HTML, bukan CSS grid/flexbox, karena Dompdf tidak mendukung itu — sama seperti constraint yang sudah dicatat di context project. Untuk template kategori "Berisiko" atau "Sedang", tampilkan badge peringatan kecil di UI pemilihan template (semacam "Skor ATS mungkin lebih rendah") agar konsisten dengan value proposition utama Resumify (hindari penolakan ATS) — jangan biarkan user kreatif kaget setelah men-download lalu menjalankan ATS Scanner sendiri.

20 ini adalah daftar kandidat, bukan komitmen untuk dibangun semua sekaligus dalam satu iterasi. Dengan timeline 12 minggu, realistisnya bangun dulu kelompok Netral (4) + Awal Karier (4) = 8 template sebagai prioritas tertinggi karena menutup gap yang nyata, lalu pakai dashboard admin (Chart.js, yang sudah ada di rencana Admin Panel) untuk melihat template mana yang benar-benar dipilih/diunduh sebelum melanjutkan ke 12 sisanya. Jangan bangun 20 sekaligus berdasarkan asumsi.
