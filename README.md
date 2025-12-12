# 🏥 Sistem Pakar Diagnosa Dini DBD (Hybrid ML + Fuzzy)

Project ini adalah aplikasi web untuk mendiagnosis risiko Demam Berdarah Dengue (DBD) dengan pendekatan cerdas yang menggabungkan:
1.  **Machine Learning (Random Forest):** Digunakan untuk melatih model prediksi berdasarkan dataset klinis.
2.  **Fuzzy Logic (Metode Sugeno):** Digunakan pada *Inference Engine* untuk menghitung tingkat risiko (probabilitas) secara transparan dan detail berdasarkan aturan medis (Trombosit & Leukosit).

> **Fitur Unggulan:** Sistem ini memiliki fitur **"White Box"**, di mana pengguna dapat melihat detail perhitungan (derajat keanggotaan/fuzzy membership) secara transparan pada menu Riwayat.

---

## 🏗️ Arsitektur Sistem

Sistem dibangun menggunakan integrasi lintas bahasa (*Cross-Language*):
* **Backend/Frontend:** PHP Native (Konsep MVC: Model-View-Controller).
* **Logic Engine:** Python 3.x (Menangani perhitungan Fuzzy & ML).
* **Database:** PostgreSQL.
* **Komunikasi:** PHP memanggil script Python via `shell_exec` dan bertukar data menggunakan format JSON.

---

## 📂 Struktur Folder

Pastikan struktur folder proyek Anda seperti berikut:

```text
SISPAK/
├── app/
│   ├── controllers/
│   │   └── ConsultationController.php  # Pengendali logika alur (GET/POST)
│   └── views/
│       ├── form.php                    # Tampilan Input Gejala
│       ├── result.php                  # Tampilan Hasil Diagnosa (UI Modern)
│       └── history.php                 # Tampilan Riwayat & Detail Fuzzy (Modal)
├── public/
│   ├── index.php                       # Router Utama
│   └── database.php                    # Koneksi ke PostgreSQL
├── python/
│   ├── models/
│   │   └── model_rf.pkl                # Model ML (Random Forest - Opsional/Hybrid)
│   ├── predict.py                      # Script Utama (Logika Fuzzy Sugeno)
│   └── train_model.py                  # Script Pelatihan Model ML
├── storage/
│   └── Dengue_clinical_dataset.csv     # Dataset
└── README.md
````

-----

## ⚙️ Prasyarat & Instalasi

### 1\. Environment

  * **Laragon** (Rekomendasi untuk Windows) atau XAMPP yang mendukung PostgreSQL.
  * **Python 3.x** terinstall dan terdaftar di Environment Variable (CMD).
  * **PostgreSQL** (Port Default: 5432).

### 2\. Konfigurasi PHP (PENTING\!)

Pastikan driver PostgreSQL di PHP sudah aktif agar tidak error *"Driver not found"*.

  * Buka **Laragon** \> Menu \> **PHP** \> **Extensions**.
  * Centang **`pdo_pgsql`** dan **`pgsql`**.

### 3\. Instalasi Library Python

Buka terminal dan jalankan perintah berikut:

```bash
pip install pandas scikit-learn
```

### 4\. Setup Database

Buat database bernama `sistempakar` di PostgreSQL (via HeidiSQL/pgAdmin), lalu jalankan query ini:

```sql
CREATE TABLE consultations (
    id SERIAL PRIMARY KEY,
    input_data JSON,        -- Menyimpan input gejala pasien
    ml_result JSON,         -- Menyimpan hasil diagnosa & detail fuzzy
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

-----

## 🚀 Cara Menjalankan

1.  Pastikan folder proyek berada di `C:\laragon\www\sispak`.
2.  Jalankan **Laragon** (Klik **Start All**).
3.  Buka browser dan akses:
    ```
    http://localhost/sispak/public/
    ```
4.  Lakukan diagnosa baru.
5.  Untuk melihat detail perhitungan, buka menu **Riwayat Data** dan klik tombol **Detail**.

-----

## 🧠 Penjelasan Metode Fuzzy (Sugeno)

Sistem menggunakan **Fuzzy Logic Orde Nol (Sugeno)** untuk menentukan skor risiko dengan langkah berikut:

### 1\. Fuzzifikasi (Pemetaan Input ke Derajat 0-1)

  * **Trombosit:** Menggunakan Kurva Bahu Kiri (Linear Down).
      * *Range:* 100.000 (Sangat Rendah) s.d 150.000 (Batas Normal).
  * **Leukosit (WBC):** Menggunakan Kurva Bahu Kiri.
      * *Range:* 3.500 s.d 4.500.

### 2\. Basis Aturan (Rule Base)

Sistem mengevaluasi risiko berdasarkan aturan pakar:

  * *IF Trombosit Rendah THEN Risiko = 100 (Sangat Tinggi)*
  * *IF Trombosit Normal AND WBC Rendah THEN Risiko = 60 (Sedang)*
  * *IF Trombosit Normal AND WBC Normal THEN Risiko = 10 (Rendah)*
  * *+ Bobot Tambahan dari Gejala Klinis (Demam, Nyeri, dll)*

### 3\. Defuzzifikasi

Menggunakan metode **Weighted Average** (Rata-rata Tertimbang) untuk menghasilkan nilai persentase akhir yang akurat.

-----

**Dibuat untuk Tugas Akhir / Skripsi Sistem Pakar.**

```
