## Sistem Pakar Deteksi Penyakit Gigi (Laravel + Python FastAPI)

Aplikasi web sistem pakar untuk membantu mendeteksi kemungkinan penyakit gigi berdasarkan gejala yang dipilih pasien.

- Backend & frontend utama: Laravel 12 (PHP 8.2+)
- Database: MySQL/MariaDB
- Autentikasi: Laravel Breeze (Blade + Tailwind/Bootstrap)
- Engine sistem pakar: Python 3 (FastAPI) di folder `python_engine`

---

## Panduan Menggunakan Website

### 1. Sebagai Pasien (User Tanpa Login)

1. Buka halaman utama
   - Akses: `http://localhost:8000/`
   - Anda akan melihat judul **“Sistem Pakar Deteksi Penyakit Gigi”** dan tombol **“Mulai Konsultasi”**.
2. Mulai konsultasi
   - Klik tombol **“Mulai Konsultasi”**, atau buka menu **Konsultasi** di navbar (`/konsultasi`).
3. Isi data diri
   - **Nama Pasien**: wajib diisi.
   - **Email**: opsional (memudahkan jika kelak ingin kirim hasil konsultasi via email).
4. Pilih gejala
   - Di bagian **“Pilih Gejala yang Anda Rasakan”** akan muncul daftar gejala (G01, G02, dst).
   - Centang semua gejala yang sesuai dengan kondisi Anda saat ini (minimal 1 gejala wajib dipilih).
5. Proses diagnosa
   - Klik tombol **“Proses Diagnosa”** di bagian bawah form.
   - Sistem akan:
     - Mengirim daftar gejala ke engine Python (`/infer`).
     - Mendapatkan hasil penyakit yang paling mungkin beserta skor.
     - Menyimpan data ke tabel `konsultasi`.
6. Melihat hasil konsultasi
   - Setelah proses berhasil, Anda akan diarahkan ke halaman **Hasil Konsultasi** (`/konsultasi/{id}`):
     - Menampilkan nama pasien dan tanggal konsultasi.
     - Menampilkan **Nama Penyakit**, **Kode Penyakit**, dan **Skor Kepercayaan** (dalam persen).
     - Menampilkan **Deskripsi penyakit** dan **Saran penanganan** dari pakar.
     - Menampilkan daftar **gejala yang Anda pilih**.
     - Jika tersedia, juga tampil **catatan mesin inferensi** (penjelasan rule dan perhitungan skor).
   - Tersedia tombol:
     - **“Konsultasi Ulang”** → kembali ke form konsultasi.
     - **“Cetak / Download PDF”** → saat ini masih placeholder (belum aktif).
7. Catatan penting
   - Hasil sistem pakar ini **bukan diagnosis medis final**.
   - Gunakan sebagai panduan awal, dan selalu lakukan pemeriksaan langsung ke dokter gigi.

### 2. Sebagai Admin (Dokter / Pakar)

1. Login sebagai admin
   - Akses halaman login: `http://localhost:8000/login`.
   - Gunakan akun admin bawaan seeder (bisa diubah di `database/seeders/AdminUserSeeder.php`):
     - Email: `admin@sispak-gigi.test`
     - Password: `password`
2. Masuk ke dashboard admin
   - Setelah login, buka: `http://localhost:8000/admin`.
   - Di halaman **Dashboard Admin** Anda akan melihat:
     - Total penyakit.
     - Total gejala.
     - Total konsultasi.
     - Daftar **konsultasi terbaru** dengan link ke detail.
3. Mengelola data penyakit
   - Menu **Penyakit** (`/admin/penyakit`):
     - Tombol **“Tambah Penyakit”** untuk menambah data baru:
       - Kode penyakit (mis. P01).
       - Nama penyakit (mis. Karies Gigi).
       - Deskripsi dan saran penanganan.
     - Bisa **edit**, **hapus**, dan **lihat detail** penyakit.
4. Mengelola data gejala
   - Menu **Gejala** (`/admin/gejala`):
     - Tambah gejala baru dengan kode (Gxx), nama gejala, dan deskripsi.
     - Bisa **edit**, **hapus**, dan **lihat detail** gejala.
   - Gejala-gejala ini akan muncul di form konsultasi pasien.
5. Mengelola aturan (rule) sistem pakar
   - Menu **Aturan** (`/admin/aturan`):
     - Tambah aturan baru:
       - Kode aturan (Rxx).
       - Pilih **Penyakit** (kesimpulan rule).
       - Pilih satu atau lebih **Gejala** sebagai premis (IF gejala-gejala ini terpenuhi THEN penyakit).
       - Isi nilai **Confidence Rule** (0–1) untuk memberi bobot kekuatan aturan.
     - Bisa **edit**, **hapus**, dan **lihat detail** aturan.
6. Melihat riwayat konsultasi pasien
   - Menu **Konsultasi** (`/admin/konsultasi`):
     - Menampilkan daftar semua konsultasi:
       - Nama pasien, penyakit hasil diagnosa, skor, tanggal konsultasi.
     - Klik tombol **Detail** untuk melihat:
       - Data pasien.
       - Penyakit dan skor kepercayaan.
       - Gejala yang dipilih.
       - Catatan engine Python (detail rule dan perhitungan).
   - Menu konsultasi hanya **read-only**: tidak ada form tambah/edit/hapus dari admin.
7. Logout
   - Di navbar admin terdapat tombol **Logout** (form POST ke `/logout`) untuk keluar dari akun admin.

### Fitur Utama

- Pasien (tanpa login):
  - Mengisi nama/email dan memilih gejala gigi/mulut.
  - Sistem mengirim gejala ke service Python (`/infer`) dan menampilkan hasil diagnosa:
    - Nama penyakit gigi paling mungkin.
    - Skor kepercayaan.
    - Saran penanganan.
- Admin (dokter/pakar, login sebagai admin):
  - Dashboard ringkas (jumlah penyakit, gejala, konsultasi).
  - CRUD Penyakit Gigi.
  - CRUD Gejala.
  - CRUD Aturan (rule) IF gejala THEN penyakit.
  - Melihat riwayat konsultasi (read-only).

---

## 1. Persiapan Lingkungan

Pastikan sudah terpasang:

- PHP 8.2+ dan Composer
- MySQL / MariaDB
- Node.js (minimal 18, disarankan 20+ untuk Vite)
- Python 3.10+ dan `pip`

---

## 2. Konfigurasi Laravel (.env)

Salin file contoh dan sesuaikan:

```bash
cp .env.example .env
```

Contoh konfigurasi penting:

```env
APP_NAME="Sistem Pakar Gigi"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=sispak_gigi
DB_USERNAME=postgres
DB_PASSWORD=

# URL base engine Python (FastAPI)
PYTHON_ENGINE_URL=http://localhost:8001
```

Buat database `sispak_gigi]` di MySQL terlebih dahulu.

---

## 3. Instalasi & Migrasi Laravel

```bash
composer install
php artisan key:generate

# Jalankan migrasi + seeder (penyakit, gejala, aturan, admin)
php artisan migrate --seed
```

Seeder akan membuat akun admin default:

- Email: `admin@sispak-gigi.test`
- Password: `password`

Silakan ubah di `database/seeders/AdminUserSeeder.php` untuk produksi.

---

## 4. Build Asset Frontend (Opsional)

```bash
npm install
npm run dev     # untuk mode development
# atau
npm run build   # untuk build production
```

---

## 5. Menjalankan Laravel

```bash
php artisan serve
```

Aplikasi akan tersedia di `http://localhost:8000` (default).

### Endpoint Utama

- `/` – Beranda sistem pakar (landing page).
- `/konsultasi` – Form konsultasi gejala untuk pasien.
- `/konsultasi/{id}` – Detail hasil konsultasi.
- `/login` – Login user (Breeze).
- `/admin` – Dashboard admin (hanya untuk user dengan `is_admin = true`).

---

## 6. Menjalankan Engine Python (FastAPI)

Masuk ke folder `python_engine` dan pasang dependensi:

```bash
cd python_engine
python -m venv venv
source venv/bin/activate      # Linux / macOS
# atau: venv\Scripts\activate # Windows

pip install -r requirements.txt
```

Jalankan service FastAPI:

```bash
uvicorn main:app --reload --port 8001
```

Jika `PYTHON_ENGINE_URL` di `.env` sudah diset ke `http://localhost:8001`, Laravel akan memanggil endpoint:

- `GET  http://localhost:8001/` → health-check & informasi model.
- `POST http://localhost:8001/infer` → inferensi berbasis dataset dengue.

Contoh body JSON ke `/infer` (mengikuti kolom dataset `Dengue_clinical_dataset.csv` tanpa `Id` dan `Outcome`):

```json
{
  "features": {
    "Gender": "Male",
    "Age": 16,
    "Platelet Count": 149134,
    "WBC": 4468,
    "Location": "Keraniganj",
    "Fever": true,
    "Duration_of_Fever": 5,
    "Headache": false,
    "Muscle_Pain": true,
    "Rash": false,
    "Vomiting": true
  }
}
```

Engine Python (ML + fuzzy untuk dengue) akan mengembalikan struktur mirip:

```json
{
  "predicted_label": "Positive",
  "predicted_label_encoded": 1,
  "probabilities": {
    "Negative": 0.20,
    "Positive": 0.80
  },
  "positive_class_label": "Positive",
  "p_positive": 0.80,
  "p_negative": 0.20,
  "fuzzy_confidence_score": 0.76,
  "fuzzy_confidence_level": "strong",
  "explanation": "ML model 'RandomForest' memprediksi 'Positive' ... "
}
```

Respons ini bisa disimpan di kolom catatan/JSON untuk keperluan audit atau dipakai sebagai dasar tampilan hasil diagnosis.

---

## 7. Struktur Database (Ringkas)

- `penyakit_gigi` – master penyakit gigi.
- `gejala` – master gejala.
- `aturan` – rule IF gejala THEN penyakit (kolom `gejala_ids` berisi daftar ID gejala dalam JSON).
- `konsultasi` – riwayat konsultasi pasien (gejala terpilih, hasil penyakit, skor, catatan engine).
- `users` – user aplikasi (dari Laravel), dengan kolom tambahan `is_admin`.

---

## 8. Metode & Algoritma yang Digunakan

- **Metode utama:** klasifikasi biner berbasis *supervised machine learning* untuk memprediksi status dengue (**Positive / Negative**) dari fitur klinis (usia, trombosit, leukosit, demam, dll.).
- **Algoritma yang dilatih:**
  - `LogisticRegression` – model linear probabilistik untuk klasifikasi biner.
  - `RandomForestClassifier` – ensembel pohon keputusan.
  - Keduanya dilatih, lalu **dipilih otomatis model dengan akurasi test tertinggi**. Pada dataset saat ini, model terbaik adalah **Logistic Regression** (akurasi ~0.99).
- **Metrik evaluasi:** akurasi, precision, recall, dan F1-score (weighted) dihitung di proses training dan disimpan ke file:
  - `python_engine/dengue_model_metrics.txt`
  - File ini memuat ringkasan metrik dan classification report per kelas.
- **Penanganan ketidakpastian:** bukan menggunakan **Certainty Factor**, tetapi:
  - Model ML menghasilkan probabilitas kelas (khususnya `P(Positive)` sebagai risiko dengue).
  - Probabilitas ini masuk ke **Fuzzy Expert System** sebagai variabel linguistik:
    - Input: `P_Positive`, `P_Negative` dengan membership `low / medium / high`.
    - Output: `DiagnosisConfidence` dengan level `weak / moderate / strong`.
  - Aturan fuzzy (IF–THEN) mengubah angka probabilitas menjadi tingkat kepercayaan yang lebih mudah diinterpretasi secara klinis.

---

## 9. Catatan Pengembangan

- Engine Python saat ini menggunakan model **Machine Learning + Fuzzy Expert System** berbasis dataset klinis **dengue** (`storage/app/public/Dengue_clinical_dataset.csv`), bukan lagi rule-based penyakit gigi yang hard-coded.
- Anda dapat menyesuaikan controller Laravel agar mengirim struktur `features` yang sesuai ke `/infer`, atau menambahkan endpoint khusus di Laravel untuk menjembatani antara form gejala gigi dan engine dengue (misalnya hanya sebagai modul riset).
- Untuk pengetahuan sistem pakar berbasis rule penyakit gigi, CRUD tetap dikelola di menu admin:
  - Penyakit, Gejala, dan Aturan – Laravel sudah menyediakan CRUD lengkap dan bisa dihubungkan kembali ke engine Python jika diperlukan. 
