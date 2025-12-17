# 🏥 Sistem Pakar Diagnosa Dini DBD (Metode Certainty Factor)

Project ini adalah aplikasi web untuk mendiagnosis risiko Demam Berdarah Dengue (DBD) menggunakan metode **Certainty Factor (CF)**. Sistem ini dirancang untuk meniru cara berpikir dokter dalam menangani ketidakpastian gejala pasien.

---

## 🌟 Fitur Utama

1.  **Diagnosa Berbasis Keyakinan (CF):** Tidak hanya "Ya/Tidak", tapi menghitung persentase keyakinan (misal: *98.2% Positif DBD*).
2.  **Penanganan Ketidakpastian User:** Pengguna bisa memilih tingkat keparahan gejala fisik (Ringan, Sedang, Parah).
3.  **Analisa Fase Demam:** Memperhitungkan siklus "Pelana Kuda" (Fase Kritis hari ke-3 s.d 5).
4.  **Cross-Language Architecture:** Menggabungkan kemudahan PHP (Web) dengan presisi perhitungan Python.
5.  **Transparansi (White Box):** Menampilkan log perhitungan detail pada menu Riwayat.

---

## 🧠 Metodologi Sistem

### 1. Strategi Inferensi: Forward Chaining
Sistem menggunakan alur **Runut Maju (Forward Chaining)**. Diagnosa dimulai dari pengumpulan fakta-fakta (Input Suhu, Lab, Gejala) dari pengguna, kemudian dicocokkan dengan Basis Pengetahuan (*Rule Base*) untuk menarik kesimpulan akhir.

### 2. Metode Perhitungan: Certainty Factor (CF)
Sistem menggunakan kombinasi nilai keyakinan Pakar dan User.

**Rumus 1: Menghitung CF Gejala**
$$CF_{Gejala} = CF_{Pakar} \times CF_{User}$$
* **CF Pakar:** Nilai ketetapan medis (lihat tabel di bawah).
* **CF User:** Bobot input pengguna (0.0 = Tidak, 0.5 = Ragu/Ringan, 1.0 = Yakin/Parah).

**Rumus 2: Kombinasi Sekuensial (CF Combine)**
Digunakan untuk menggabungkan banyak gejala menjadi satu nilai akhir.
$$CF_{Baru} = CF_{Lama} + CF_{Gejala} \times (1 - CF_{Lama})$$

---

## 📚 Basis Pengetahuan (Knowledge Base)

Berikut adalah aturan dan nilai keyakinan pakar yang digunakan dalam `python/predict.py`:

| Kode | Parameter Gejala | Kondisi Medis | CF Pakar | Keterangan |
| :-- | :--- | :--- | :--- | :--- |
| **G1** | Trombosit | **< 100.000** | **0.9** | Kondisi Kritis / Bahaya |
| **G2** | Trombosit | 100.000 - 150.000 | **0.8** | Trombositopenia |
| **G3** | Leukosit (WBC) | < 4.000 | **0.6** | Leukopenia (Infeksi Virus) |
| **G4** | Suhu Tubuh | $\ge$ 37.5°C | **0.4** | Febris (Demam) |
| **G5** | Lama Demam | **Hari ke-3 s.d 5** | **0.8** | Fase Kritis (Pelana Kuda) |
| **G6** | Lama Demam | Hari 1-2 atau 6-7 | **0.3** | Fase Awal / Pemulihan |
| **G7** | Ruam Merah | Ada (Gejala Fisik) | **0.5** | Petechiae |
| **G8** | Nyeri Otot | Ada (Gejala Fisik) | **0.2** | Myalgia |
| **G9** | Mual/Muntah | Ada (Gejala Fisik) | **0.3** | Gangguan Pencernaan |

---

## 📂 Struktur Folder

```text
SISPAK/
├── app/
│   ├── controllers/
│   │   └── ConsultationController.php  # Menangani Logika Input & Database
│   └── views/
│       ├── form.php                    # Input (Suhu, Lab, Dropdown Gejala)
│       ├── result.php                  # Hasil Diagnosa & Log CF
│       └── history.php                 # Riwayat & Detail Perhitungan
├── public/
│   ├── index.php                       # Router Utama
│   └── database.php                    # Koneksi PostgreSQL
├── python/
│   └── predict.py                      # Logic Engine (Rumus CF Combine)
└── README.md
```

⚙️ Cara Instalasi
1. Persyaratan Sistem
Web Server: Laragon / XAMPP.

Bahasa: PHP >= 7.4 & Python 3.x.

Database: PostgreSQL.

2. Setup Database
Buat database baru bernama sistempakar dan jalankan query berikut:

SQL
```
CREATE TABLE consultations (
    id SERIAL PRIMARY KEY,
    input_data JSON,       
    ml_result JSON,       
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```
3. Konfigurasi
Pastikan driver pdo_pgsql aktif di PHP.

Pastikan path python di ConsultationController.php sudah sesuai dengan environment komputer Anda.

🚀 Cara Penggunaan
Buka browser: http://localhost/sispak/public/.

- Isi Suhu Tubuh dan Lama Demam (Penting untuk deteksi fase kritis).

- Isi Data Lab (Trombosit & Leukosit).

- Pilih tingkat keparahan gejala fisik (Tidak Ada / Ringan / Parah).

- Klik Hitung Keyakinan.

- Lihat hasil persentase dan rincian perhitungan pada halaman hasil.
  
## Dibuat untuk Tugas Akhir / Skripsi Sistem Pakar.
