## Sistem Pakar Diagnosis Dengue (Laravel + Python FastAPI)

Aplikasi web sistem pakar untuk membantu memprediksi kemungkinan infeksi **dengue (demam berdarah)** berdasarkan data klinis pasien, menggunakan kombinasi **Machine Learning + Fuzzy Expert System**. Aplikasi semula dikembangkan untuk penyakit gigi, namun modul konsultasi utama kini berfokus pada dengue, sementara modul penyakit gigi tetap tersedia di area admin sebagai fitur tambahan/legacy.

- Backend & frontend utama: Laravel 12 (PHP 8.2+)
- Database: MySQL/MariaDB
- Autentikasi: Laravel Breeze (Blade + Tailwind/Bootstrap)
- Engine sistem pakar: Python 3 (FastAPI) di folder `python_engine`

---

## Panduan Menggunakan Website

### 1. Sebagai Pasien (User Tanpa Login)

1. Buka halaman utama
   - Akses: `http://localhost:8000/`
   - Anda akan melihat judul sistem pakar dan tombol **“Mulai Konsultasi”**.
2. Mulai konsultasi
   - Klik tombol **“Mulai Konsultasi”**, atau buka menu **Konsultasi** di navbar (`/konsultasi`).
3. Isi data diri
   - **Nama Pasien**: wajib diisi.
   - **Email**: opsional (memudahkan jika kelak ingin kirim hasil konsultasi via email).
4. Isi data klinis dengue
   - Form konsultasi meminta informasi klinis utama, antara lain:
     - Jenis kelamin (`Gender`).
     - Usia (`Age`).
     - Jumlah trombosit (`Platelet Count`).
     - Jumlah leukosit (`WBC`).
     - Demam (ya/tidak) dan durasi demam (`Fever`, `Duration_of_Fever`).
     - Gejala penyerta: sakit kepala, nyeri otot, ruam, muntah (`Headache`, `Muscle_Pain`, `Rash`, `Vomiting`).
   - Nilai laboratorium (trombosit, leukosit) bersifat opsional; jika tidak diisi akan diimputasi oleh model.
5. Proses diagnosa
   - Klik tombol **“Proses Diagnosa”** di bagian bawah form.
   - Sistem akan:
     - Mengirim data klinis ke engine Python (`/infer`).
     - Mendapatkan prediksi status dengue (Positive / Negative), probabilitas, dan tingkat kepercayaan fuzzy.
     - Menyimpan data ke tabel `konsultasi`.
6. Melihat hasil konsultasi
   - Setelah proses berhasil, Anda akan diarahkan ke halaman **Hasil Konsultasi** (`/konsultasi/{id}`):
     - Menampilkan nama pasien dan tanggal konsultasi.
     - Menampilkan **Prediksi** (Positive / Negative) dari model.
     - Menampilkan **Probabilitas positif dengue** dan **Fuzzy Confidence** (weak / moderate / strong).
     - Menampilkan **penjelasan sistem** (bagaimana probabilitas diinterpretasi oleh fuzzy logic).
     - Menampilkan tabel **data klinis yang dikirim** ke engine Python.
     - Menyediakan **catatan lengkap engine** (JSON) untuk kebutuhan audit/riset.
   - Tersedia tombol:
     - **“Konsultasi Ulang”** → kembali ke form konsultasi.
     - **“Cetak / Download PDF”** → mengunduh laporan PDF hasil diagnosis berbasis ML + Fuzzy.
7. Catatan penting
   - Sistem ini menggunakan model yang telah dilatih dan divalidasi pada dataset klinis dengue, serta diperkuat dengan fuzzy expert system.
   - Hasil diagnosis ditujukan sebagai **alat bantu keputusan**; interpretasi klinis dan keputusan akhir tetap berada di tangan tenaga kesehatan profesional.

### 2. Sebagai Admin (Dokter / Pakar)

1. Login sebagai admin
   - Akses halaman login: `http://localhost:8000/login`.
   - Gunakan akun admin bawaan seeder (bisa diubah di `database/seeders/AdminUserSeeder.php`):
     - Email: `admin@sispak-gigi.test`
     - Password: `password`
2. Masuk ke dashboard admin
   - Setelah login, buka: `http://localhost:8000/admin`.
   - Di halaman **Dashboard Admin** Anda akan melihat:
     - Total penyakit (modul penyakit gigi legacy).
     - Total gejala (modul penyakit gigi legacy).
     - Total konsultasi (konsultasi dengue yang masuk).
     - Daftar **konsultasi terbaru** dengan link ke detail.
3. Melihat riwayat konsultasi pasien (dengue)
   - Menu **Konsultasi** (`/admin/konsultasi`):
     - Menampilkan daftar semua konsultasi dengue:
       - Nama pasien, hasil prediksi (diturunkan dari data engine), skor kepercayaan (probabilitas positif), tanggal konsultasi.
     - Klik tombol **Detail** untuk melihat:
       - Data pasien.
       - Skor kepercayaan.
       - Data klinis yang dikirim.
       - Catatan engine Python (output lengkap ML + fuzzy).
   - Menu konsultasi hanya **read-only**: tidak ada form tambah/edit/hapus dari admin.
4. Mengelola data penyakit/gejala/aturan (modul penyakit gigi – opsional/legacy)
   - Menu **Penyakit**, **Gejala**, dan **Aturan** di area admin masih mencerminkan rancangan awal sistem pakar penyakit gigi berbasis rule.
   - Modul ini bisa tetap digunakan untuk keperluan riset/eksperimen rule-based (misalnya memetakan penyakit gigi), namun tidak lagi dipakai di alur konsultasi dengue.
7. Logout
   - Di navbar admin terdapat tombol **Logout** (form POST ke `/logout`) untuk keluar dari akun admin.

### Fitur Utama

- Pasien (tanpa login):
  - Mengisi data diri dan **data klinis dengue** (jenis kelamin, usia, trombosit, leukosit, demam, gejala penyerta).
  - Sistem mengirim fitur klinis ke service Python (`/infer`) dan menampilkan hasil diagnosis:
    - Prediksi status dengue (Positive / Negative).
    - Probabilitas positif dengue.
    - Tingkat kepercayaan fuzzy (weak / moderate / strong) dan penjelasan model.
    - Laporan PDF hasil konsultasi yang dapat diunduh.
- Admin (dokter/pakar, login sebagai admin):
  - Dashboard ringkas (jumlah penyakit/gejala legacy untuk gigi, serta jumlah konsultasi dengue).
  - Melihat riwayat konsultasi dengue berikut detail dan catatan engine (read-only).
  - Modul CRUD Penyakit/Gejala/Aturan (penyakit gigi, legacy) tetap tersedia untuk tujuan pengembangan/riset rule-based.

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
APP_NAME="Sistem Pakar Dengue"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=sispak
DB_USERNAME=postgres
DB_PASSWORD=

# URL base engine Python (FastAPI)
PYTHON_ENGINE_URL=http://localhost:8001
```

Buat database `sispak` di PostgreSQL (atau sesuaikan dengan konfigurasi database Anda).

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
