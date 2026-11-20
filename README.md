# PRIANGAN KILAT EXPRESS

Sistem Informasi Manajemen Ekspedisi Berbasis Web dan Mobile

---

## 1. Ringkasan

Priangan Kilat Express merupakan sistem informasi manajemen ekspedisi yang dibuat untuk membantu kegiatan operasional usaha ekspedisi milik Pak Dedi. Usaha ini memiliki satu hub di wilayah Bandung Raya, delapan orang kurir, dan dua admin yang bergantian menjaga loket. Sebagian besar pelanggannya merupakan pemilik toko online kecil yang mengirimkan paket ke berbagai kota, termasuk menggunakan layanan Cash on Delivery (COD).

Sebelum sistem ini dibuat, pencatatan operasional masih dilakukan secara manual menggunakan buku. Data pengirim, penerima, kota tujuan, berat paket, hingga nomor resi ditulis tangan. Perhitungan ongkos kirim juga masih menggunakan kalkulator. Kondisi tersebut menimbulkan beberapa masalah, seperti nomor resi ganda, kesulitan mencari informasi paket ketika pelanggan menanyakan status pengiriman, serta kesulitan mengatur pembagian paket kepada kurir. Selain itu, pencatatan COD belum terstruktur sehingga perusahaan kesulitan mengetahui kurir yang sudah atau belum menyetorkan uang COD. Bahkan, pernah terjadi selisih uang hingga jutaan rupiah.

Berdasarkan permasalahan tersebut, saya membangun Priangan Kilat Express sebagai sistem yang lebih efisien, terstruktur, dan mudah digunakan. Sistem ini terdiri dari dua platform, yaitu web dan aplikasi mobile Android.

Alur utamanya: Admin memasukkan paket dan menugaskan kurir, Kurir memperbarui hasil pengiriman melalui APK, Admin mencatat setoran COD, dan Manajer melihat tarif serta laporan.

**Pengguna:**
- **Admin:** mengelola paket, status, pelanggan, kurir, impor data, dan setoran COD.
- **Manajer:** mengatur tarif dan melihat laporan serta data pelanggan/kurir.
- **Kurir:** melihat paket yang ditugaskan, memperbarui status, dan melihat COD melalui APK.
- **Pelanggan umum:** mengecek resi tanpa login.


## 2. Teknologi

Berikut teknologi yang digunakan dalam pengembangan Priangan Kilat Express.

### Backend dan Web

| Teknologi | Kegunaan |
|---|---|
| PHP ^8.3 | Bahasa pemrograman backend. |
| Laravel ^13.17 | Framework backend untuk mengelola proses bisnis, autentikasi, dan API. |
| Laravel Sanctum ^4.0 | Autentikasi API menggunakan token. |
| Eloquent ORM | Mengelola komunikasi antara aplikasi dan database. |
| Laravel Schema Migrations | Mengatur struktur tabel database. |
| MySQL | Database yang digunakan pada konfigurasi pengujian. |
| Blade | Membuat tampilan halaman web. |
| Tailwind CSS ^4.0 | Mengatur tampilan dan desain antarmuka. |
| JavaScript dan Vite ^8.0 | Mendukung interaksi dan proses build frontend. |
| Lucide | Menampilkan ikon pada antarmuka. |
| Maatwebsite Excel ^4.0 | Mendukung proses impor data CSV. |

### Mobile Android

| Teknologi | Kegunaan |
|---|---|
| Ionic Angular ^9.0.11 | Framework untuk membangun antarmuka aplikasi mobile. |
| Angular 22.2.5 | Mengelola komponen, halaman, dan logika aplikasi. |
| Capacitor ^8.6.2 | Menghubungkan aplikasi web dengan perangkat Android. |
| @capacitor/app | Mendukung integrasi fitur aplikasi dan penanganan deep link. |
| Android SDK dan Gradle | Proses build aplikasi menjadi APK. |


## 3. Cara Menjalankan Server

Panduan ini dibuat menggunakan **Windows 10/11 dengan Laragon**.

### A. Persiapan

Pastikan perangkat sudah memiliki:

- Laragon.
- PHP sesuai kebutuhan project.
- Composer.
- Node.js dan npm.
- MySQL.
- Git.

### B. Mengambil Source Code

Clone repository:

```powershell
git clone URL_REPOSITORY
```

Masuk ke folder project:

```powershell
cd Ekspedisi_Priangan
```

Install dependency Laravel:

```powershell
composer install
```

### C. Konfigurasi Environment

Salin file `.env.example` menjadi `.env`:

```powershell
Copy-Item .env.example .env
```

Buka file `.env`, kemudian sesuaikan konfigurasi aplikasi dan database.

Contoh konfigurasi MySQL:

```env
APP_NAME="Priangan Kilat Express"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_ekspedisi_priangan
DB_USERNAME=root
DB_PASSWORD=
```

Sesuaikan nama database, username, dan password dengan konfigurasi MySQL di perangkat masing-masing.

Generate application key:

```powershell
php artisan key:generate
```

### D. Menyiapkan Database

1. Jalankan Laragon.
2. Aktifkan MySQL.
3. Buka phpMyAdmin.
4. Buat database dengan nama `db_ekspedisi_priangan`, atau sesuaikan dengan nama yang digunakan pada `.env`.
5. Jalankan migrasi:

```powershell
php artisan migrate
```

Perintah tersebut digunakan untuk membuat tabel berdasarkan file migration yang tersedia di project.

### E. Mengisi Data Awal dan Data Demo

**Impor data CSV**

Project menyediakan fitur impor data awal melalui aplikasi web.

Langkahnya:

1. Siapkan file CSV sesuai format yang didukung sistem.
2. Login menggunakan akun Admin.
3. Buka halaman impor data.
4. Pilih file CSV yang akan diimpor.
5. Jalankan proses impor.
6. Periksa hasil impor untuk memastikan data masuk dengan benar.

Sistem melakukan penanganan terhadap data awal yang tidak rapi, seperti nomor telepon, nomor resi ganda, dan paket yang belum memiliki berat.

**Seeder data demo**

Untuk memasukkan data demo yang disediakan project, jalankan:

```powershell
php artisan db:seed
```

Perintah ini menjalankan seeder yang terdaftar pada `DatabaseSeeder`.

Pastikan isi seeder sudah diperiksa sebelum dijalankan agar tidak menimbulkan data demo ganda pada database yang sudah berisi data.

### F. Konfigurasi Layanan Eksternal

Project menggunakan integrasi Pusat Wilayah untuk membantu memperoleh informasi zona pengiriman.

API key layanan tersebut perlu diisi melalui file `.env` menggunakan nama variabel konfigurasi yang sesuai dengan implementasi project.

Periksa konfigurasi pada source code untuk mengetahui nama variabel API key yang benar. Jangan mengganti nama variabel tanpa menyesuaikan kode program.

Jika layanan tidak tersedia atau kota tujuan tidak ditemukan, sistem menyediakan mekanisme pemilihan zona secara manual oleh Admin.

**Catatan:** Jangan mengunggah API key, password database, atau isi `.env` yang berisi kredensial ke repository publik.

### G. Menjalankan Server

Jalankan server Laravel agar dapat diakses oleh perangkat lain:

```powershell
php artisan serve --host=0.0.0.0 --port=8000
```

Alamat akses:

| Kebutuhan | Alamat |
|---|---|
| Web dari laptop | `http://127.0.0.1:8000` |
| API dari laptop | `http://127.0.0.1:8000/api` |
| API dari HP | `http://IP-LAPTOP:8000/api` |

Contoh alamat API saat pengujian:

```text
http://192.168.1.8:8000/api
```

Alamat tersebut merupakan contoh IP laptop yang digunakan ketika pengujian. IP dapat berubah tergantung jaringan.

Untuk mengetahui IP laptop, jalankan:

```powershell
ipconfig
```

Cari bagian `IPv4 Address` pada adapter Wi-Fi yang sedang digunakan.

**Penting:** Jika API diakses dari HP menggunakan IP lokal, laptop dan HP harus terhubung ke jaringan Wi-Fi yang sama. Pastikan firewall Windows mengizinkan koneksi pada port 8000.

### H. Konfigurasi Email untuk Reset Password

Fitur reset password menggunakan mekanisme email. Untuk pengujian lokal, project menggunakan Mailpit sebagai penampung email.

Pastikan konfigurasi SMTP Laravel sesuai dengan Mailpit yang dijalankan.

Alamat Mailpit pada pengujian lokal:

```text
http://localhost:8025
```

Email reset password yang dikirim oleh aplikasi dapat diperiksa melalui halaman tersebut.

### I. Pengujian

Setelah server berjalan:

1. Buka halaman web melalui browser.
2. Login menggunakan akun uji.
3. Pastikan halaman sesuai dengan hak akses masing-masing peran.
4. Uji input paket dan proses pengiriman.
5. Uji endpoint API melalui aplikasi mobile.
6. Pastikan data yang dikirim dan diterima aplikasi tersimpan pada database.

---

## 4. Cara Memasang APK

### A. Lokasi APK

File APK aplikasi kurir tersedia pada folder:

```text
apk/Priangan-Kilat-Express.apk
```

Aplikasi mobile dibuat menggunakan Ionic Angular dan Capacitor, kemudian dibangun menjadi aplikasi Android.

### B. Persiapan

Sebelum memasang APK:

- Siapkan HP Android.
- Siapkan file APK dari folder `/apk`.
- Pastikan backend Laravel sudah berjalan.
- Pastikan alamat API pada aplikasi mengarah ke server yang dapat dijangkau oleh HP.

### C. Langkah Instalasi

1. Salin file `Priangan-Kilat-Express.apk` ke HP Android.
2. Buka file melalui File Manager.
3. Jika muncul peringatan keamanan, izinkan instalasi aplikasi dari sumber tersebut.
4. Tekan tombol Install.
5. Tunggu hingga proses instalasi selesai.
6. Buka aplikasi Priangan Kilat Express.
7. Login menggunakan akun kurir yang tersedia.

### D. Mengatur Alamat Server pada APK

Aplikasi mobile berkomunikasi dengan backend Laravel melalui REST API.

Alamat API yang digunakan ketika pengujian:

```text
http://192.168.1.8:8000/api
```

Alamat tersebut adalah contoh IP lokal laptop.

**Perbedaan alamat server:**

| Alamat | Keterangan |
|---|---|
| `localhost` | Mengarah ke perangkat yang menjalankan aplikasi. |
| `127.0.0.1` | Mengarah ke perangkat itu sendiri. |
| `192.168.x.x` | Contoh alamat IP lokal laptop. |
| URL hosting | Alamat server online yang dapat diakses melalui internet. |

Jika APK menggunakan `localhost`, HP akan mencari server pada HP itu sendiri, bukan pada laptop.

Apabila IP laptop berubah, alamat API pada source code mobile perlu disesuaikan dan APK harus dibangun ulang.

### E. Build Ulang APK

Jika alamat API perlu diganti, lakukan penyesuaian pada konfigurasi `baseUrl` di source code mobile.

Contoh proses build:

```powershell
cd mobile
npm install
ionic build
npx cap sync android
```

Setelah itu, buka project Android menggunakan Android Studio atau jalankan proses build Gradle sesuai konfigurasi project.

File hasil build dapat ditemukan pada:

```text
mobile/android/app/build/outputs/apk/debug/app-debug.apk
```

Pastikan APK hasil build terbaru disalin kembali ke folder `/apk` apabila akan digunakan untuk pengujian atau pengumpulan.

### F. Ketentuan Koneksi

**Jika menggunakan server lokal:**

- HP dan laptop harus terhubung ke Wi-Fi yang sama.
- Laragon dan server Laravel harus tetap berjalan.
- Firewall harus mengizinkan koneksi ke port 8000.
- Alamat API harus menggunakan IP laptop yang benar.

**Jika menggunakan server hosting:**

- HP tidak harus menggunakan Wi-Fi yang sama dengan laptop.
- Backend Laravel harus sudah tersedia secara online.
- APK harus diarahkan ke URL API hosting.
- Disarankan menggunakan HTTPS.

---

## 5. Akun Uji

Berikut akun sementara yang disediakan untuk pengujian sistem.

### A. Akun Web

| No. | Peran | Email | Password |
|---|---|---|---|
| 1 | Admin Loket | `admin@priangankilat.test` | `123456789` |
| 2 | Manajer | `manajer@priangankilat.test` | `password123` |

### B. Akun Mobile

| No. | Peran | Kode Kurir | Password |
|---|---|---|---|
| 1 | Kurir | `KUR-01` | `123456789` |


## 6. Letak Berkas

| Kebutuhan | Lokasi |
|---|---|
| Migration | `database/migrations/` |
| Seeder utama | `database/seeders/DatabaseSeeder.php` |
| Seeder demo | `database/seeders/DemoSeeder.php` |
| Import data CSV | `app/Imports/`, `app/Console/Commands/ImportData.php`, dan `app/Services/CsvImportService.php` |
| Controller | `app/Http/Controllers/` |
| Model | `app/Models/` |
| Logika bisnis | `app/Services/` |
| Route web dan API | `routes/web.php` dan `routes/api.php` |
| Tampilan web | `resources/views/` |
| Source aplikasi mobile | `mobile/src/` |
| ERD dan UML | `docs/erd/` dan `docs/uml/` |
| APK | `apk/Priangan-Kilat-Express.apk` |

Perintah seeder demo: `php artisan db:seed --class=DemoSeeder` (jalankan setelah impor CSV).

## 7. Keputusan Anda

Pada beberapa bagian pengembangan, terdapat kebutuhan yang harus ditentukan sendiri berdasarkan kondisi operasional ekspedisi.

Berikut keputusan yang saya terapkan:

| No. | Hal yang Ditentukan | Keputusan |
|---|---|---|
| 1 | Format nomor telepon | Menghapus spasi, titik, strip, dan tanda kurung. Awalan `+62` atau `62` diubah menjadi `0`. Nomor harus diawali `08`, memiliki 10–14 digit, dan tidak mengandung huruf. |
| 2 | Penyamaran nomor telepon | Tujuh digit pertama ditampilkan, sedangkan digit berikutnya disamarkan menggunakan tanda bintang. |
| 3 | Pembulatan ongkir | Ongkir dan biaya COD dibulatkan ke rupiah terdekat. |
| 4 | Paket tanpa berat | Paket ditandai perlu verifikasi, berat dibiarkan kosong, dan ongkir bernilai 0 sampai data diperbaiki. |
| 5 | Zona tidak tersedia | Jika zona tidak ditemukan dan layanan eksternal gagal, zona dapat dikosongkan dan paket ditandai sebagai zona manual. |
| 6 | Password kurir | Admin membuat password sementara dengan panjang minimal 8 karakter. |
| 7 | Retur | Paket dapat diproses menjadi Retur setelah dua kali gagal pengiriman sesuai ketentuan sistem. |

--- tambahan ---
### Snapshot Tarif

Tarif per kilogram dan ongkir disimpan pada saat paket dibuat.

Perubahan tarif zona oleh Manajer hanya berlaku untuk paket baru dan tidak mengubah ongkir paket lama. Keputusan ini diterapkan agar nilai transaksi sebelumnya tetap konsisten.

### Setoran COD

Setoran COD dicatat oleh Admin untuk setiap paket COD yang sudah berstatus Terkirim.

Satu paket hanya dapat dicatat setoran satu kali. Nilai COD yang disetorkan adalah nilai COD utuh, sedangkan biaya layanan COD dibayarkan oleh pengirim.

### Pembulatan Berat

Berat tertagih dihitung menggunakan ketentuan:

```text
Berat tertagih = max(1, ceil(round(berat, 2)))
```

Artinya, berat dibulatkan hingga dua angka desimal, kemudian dibulatkan ke atas menjadi kilogram penuh dengan batas minimum 1 kg.

### Data Awal yang Belum Rapi

Untuk menangani data awal yang belum rapi, saya menerapkan beberapa penyesuaian:

- Nomor telepon dinormalisasi agar memiliki format yang konsisten.
- Resi ganda ditangani agar tidak menghasilkan data paket ganda.
- Paket tanpa informasi berat diberi tanda perlu verifikasi.
- Zona yang belum diketahui dapat ditentukan secara manual oleh Admin.
- Data yang belum lengkap tidak langsung dianggap sebagai data yang sudah valid.


## 8. Lembar Self-Check

No 	Butir penerimaan 	Klaim  
(Ya/Tidak)	Langkah uji singkat
1 	Input paket (pengirim, penerima, kota tujuan, berat, layanan),  nomor resi otomatis, unik, reset harian; nomor telepon divalidasi	SUDAH	Login sebagai Admin, buka input paket, isi pengirim, penerima, kota, berat, layanan, dan nomor telepon valid lalu simpan. Pastikan resi terbentuk dengan format PKE-YYMMDD-0001 dan nomor berikutnya unik. Ulangi dengan nomor telepon berisi huruf/kurang dari 10 digit; sistem harus menolak dengan pesan validasi.
2 	Ongkir otomatis dari zona dan berat (dibulatkan ke atas,  minimum 1 kg), Express +50%; ongkir paket lama tidak berubah  bila tarif per kg zona diubah (butir inti)	SUDAH 	Buat paket Reguler dengan berat 1,2 kg pada Zona 1: berat tagih harus 2 kg dan ongkir Rp16.000. Ulangi dengan Express: ongkir harus Rp24.000. Ubah tarif Zona 1 sebagai Manajer, lalu buka kembali paket lama dan buat paket baru; paket lama harus tetap memakai ongkir/tarif saat transaksi dibuat.
3 	Status berjenjang, loncat ditolak, Terkirim dan Retur final (butir  inti)	SUDAH 	Ubah paket Diterima menjadi Transit, lalu tugaskan ke kurir hingga menjadi Dikirim. Coba melompati tahap (Diterima langsung Dikirim) dan ubah paket yang sudah Terkirim/Retur; semua percobaan terlarang harus ditolak. Uji Retur hanya setelah dua kali Gagal Kirim.
4 	Riwayat setiap perubahan status (waktu, pelaku: Admin atau  Kurir, catatan)	SUDAH 	Lakukan beberapa perubahan status pada satu paket. Buka detail/riwayat paket dan periksa setiap perubahan memiliki waktu, status, pelaku (Admin/Kurir), serta catatan sesuai aksi yang dilakukan
5 	Gagal kirim maksimal 2 kali; setelah gagal kedua, kirim ulang  ditolak dan status berikutnya hanya Retur (diubah Admin)	SUDAH	Tugaskan paket ke kurir, laporkan Gagal Kirim dengan catatan, lalu kirim ulang dan gagalkan untuk kedua kalinya. Setelah gagal kedua, coba tugaskan ulang (harus ditolak) dan ubah menjadi Retur sebagai Admin. Coba Gagal Kirim tanpa catatan; sistem harus menolak.
6 	Penugasan ke kurir (status menjadi Dikirim), batas 20 paket  berstatus Dikirim per kurir pada saat yang sama; kurir nonaktif  tidak bisa ditugaskan	SUDAH 	Siapkan kurir dengan 19 paket berstatus Dikirim, tugaskan paket ke-20 (harus berhasil), lalu coba paket ke-21 (harus ditolak). Coba nonaktifkan kurir yang masih membawa paket Dikirim (harus ditolak); setelah tidak membawa paket Dikirim, nonaktifkan dan pastikan kurir itu tidak dapat menerima penugasan.
7 	Cek resi publik tanpa login, telepon disamarkan di server	SUDAH	Logout dari aplikasi, buka halaman /cek-resi tanpa login, lalu cari resi yang valid. Pastikan riwayat status tampil dan telepon hanya terlihat dalam bentuk tersamarkan (misalnya 0812789***). Periksa respons/HTML browser untuk memastikan nomor lengkap, alamat, nilai COD, data pengirim, dan nama petugas tidak ikut terkirim/ditampilkan.
8 	Biaya COD 2% (minimum Rp2.000) dibayar pengirim; setoran  COD dicatat Admin per paket (hanya paket COD Terkirim, tidak  dua kali); rekap COD per kurir belum/sudah disetor (di laporan  Manajer)	SUDAH -	Buat paket COD dengan nilai Rp50.000 (biaya COD minimum Rp2.000) pastikan biaya dibebankan di loket bersama ongkir. Setelah paket COD berstatus Terkirim, catat setoran sebagai Admin; coba catat ulang (harus ditolak), lalu periksa rekap COD per kurir. .
9 	Laporan pengiriman dan pendapatan per zona per periode  (menurut tanggal masuk); hak akses: tarif hanya bisa diubah  Manajer, laporan hanya Manajer (peran lain ditolak, juga bila  halaman dibuka lewat alamat langsung), data pelanggan dan  kurir hanya diubah Admin	SUDAH 	Login sebagai Manajer dan buka laporan dengan rentang tanggal tertentu; cocokkan jumlah paket menurut status dan pendapatan per zona berdasarkan tanggal masuk. Uji akses /laporan langsung sebagai Admin/Kurir (harus ditolak), perubahan tarif sebagai Admin (harus ditolak), dan perubahan data pelanggan/kurir sebagai Manajer (harus ditolak)
10 	Data awal CSV diimpor: resi ganda menjadi satu, paket tanpa  berat bertanda "perlu verifikasi", telepon dirapikan	SUDAH	Impor CSV pelanggan dan paket historis yang berisi resi ganda, berat kosong, serta telepon berformat +62/62/08. Pastikan resi duplikat hanya masuk satu kali, paket tanpa berat diberi tanda 'perlu verifikasi', dan nomor telepon dirapikan.
11 	Integrasi Pusat Wilayah (dengan API key), fallback zona manual  bila layanan mati atau kota tidak dikenal	SUDAH	input paket dengan kota yang dikenal dan pastikan zona terisi otomatis. Ganti URL/API key menjadi salah atau gunakan kota yang tidak dikenal; input paket tetap harus dapat disimpan setelah Admin memilih zona manual dan paket ditandai zona manual.
12 	Akun kurir lengkap (APK): dibuat Admin, login dengan kode kurir  (KUR-01 dst.), password minimal 8 karakter, wajib ganti  password saat login pertama, ganti password, lupa password  direset Admin (wajib ganti saat login berikutnya), logout	SUDAH	Sebagai Admin, buat akun kurir dan pastikan kode KUR-xx dibuat serta password minimal 8 karakter. Login dari APK dengan kode kurir; pada login pertama kurir wajib mengganti password. Uji ganti password, reset password oleh Admin, kewajiban mengganti password setelah reset, lalu logout dan login kembali.
13 	Kurir (APK) melihat paket berstatus Dikirim yang ditugaskan  kepadanya dan mengubahnya menjadi Terkirim atau Gagal Kirim dengan catatan; paket kurir lain tidak bisa dibuka atau diubah	SUDAH	Login pada APK sebagai Kurir dan pastikan daftar hanya berisi paket berstatus Dikirim miliknya. Buka detail/ubah paket milik kurir lain melalui API (harus ditolak/403). Ubah paket sendiri menjadi Terkirim; uji Gagal Kirim tanpa catatan (ditolak) dan dengan catatan (berhasil).
14 	Kurir (APK) melihat paket COD berstatus Terkirim yang  diantarnya dan total nilai COD yang belum disetor	SUDAH	Pada APK, buka menu COD dan pastikan tampil paket COD berstatus Terkirim yang diantar kurir tersebut serta total nilai COD yang belum disetor. Catat setoran paket melalui Admin, muat ulang APK, lalu pastikan total belum disetor berkurang dan status setoran paket berubah menjadi sudah disetor.

**Fitur opsional yang dibuat:**

- O1 – Berat volumetrik.
- O2 – Asuransi paket 0,5% dari nilai barang.
- O3 – Laporan performa kurir.
- O4 – Penanda paket yang tertahan lebih dari 3 hari pada status Transit.
- O5 – Reset password melalui email. Untuk demo, SMTP perlu diarahkan ke Mailpit dan email masuk perlu diperlihatkan.

## 9. Belum Selesai / Keterbatasan

Selama proses pengembangan, terdapat beberapa hal yang masih menjadi keterbatasan:

1. **Tampilan belum sepenuhnya sesuai mockup.** Beberapa halaman web belum memiliki hasil akhir yang sama persis dengan rancangan awal dari Figma. Hal ini terjadi karena proses menerjemahkan desain menjadi Blade, CSS, dan komponen web membutuhkan penyesuaian tambahan.
2. **Penggunaan server lokal.** APK yang menggunakan IP lokal laptop hanya dapat mengakses API selama server Laravel berjalan dan HP dapat menjangkau jaringan laptop. Untuk penggunaan di luar jaringan lokal, diperlukan server hosting.
3. **Data pengujian.** Akun dan data demo digunakan untuk kebutuhan pengujian, bukan untuk operasional produksi.
4. **Pengujian perangkat.** Pengujian APK dilakukan pada perangkat Android yang tersedia. Perbedaan versi Android dan perangkat lain masih perlu diuji lebih lanjut.

---

## 10. Referensi dan Bantuan

Dalam proses pengembangan Priangan Kilat Express, saya menggunakan beberapa alat bantu dan referensi.

| Alat / Sumber | Bagian yang Dibantu | Penggunaan dan Penyesuaian |
|---|---|---|
| Claude AI | Audit dan dokumentasi project | Membantu memeriksa kelengkapan project, menyusun dokumentasi, serta memberikan saran perbaikan. Hasilnya saya periksa dan sesuaikan dengan struktur project. |
| ChatGPT | Pemahaman kode, logika program, dan integrasi | Membantu memahami error, menyusun logika fitur, konfigurasi API, serta proses pengembangan mobile. Kode dan solusi disesuaikan kembali dengan kebutuhan project. |
| Figma | Perancangan antarmuka | Digunakan untuk membuat rancangan tampilan sebelum diterapkan pada halaman web. |
| Chart.js | Visualisasi laporan | Digunakan untuk membuat grafik laporan seperti diagram pie dan visualisasi data lainnya yang dihubungkan dengan database. |
| Lucide | Ikon antarmuka | Digunakan untuk mendukung tampilan ikon pada halaman web. |
| Dokumentasi Laravel | Backend dan database | Menjadi referensi dalam penggunaan Laravel, migration, Eloquent, autentikasi, dan routing. |
| Dokumentasi Ionic dan Capacitor | Aplikasi mobile | Menjadi referensi dalam pengembangan aplikasi Android dan proses build APK. |

**Penggunaan AI:** AI digunakan sebagai alat bantu untuk memahami teknologi, mencari solusi ketika terjadi error, menyusun kode, dan membantu dokumentasi. Saya tetap melakukan penyesuaian terhadap hasil yang diberikan agar sesuai dengan kebutuhan sistem Priangan Kilat Express.

---

**Priangan Kilat Express**

Sistem Informasi Manajemen Ekspedisi Berbasis Web dan Mobile

Dikembangkan untuk membantu proses pengelolaan paket, pengiriman, kurir, dan pencatatan COD agar lebih terstruktur dan efisien.