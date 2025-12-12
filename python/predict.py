import sys
import json
import warnings

# Matikan warning agar output JSON bersih
warnings.filterwarnings("ignore")

# =========================================================
# BAGIAN 1: FUNGSI KEANGGOTAAN (MEMBERSHIP FUNCTIONS)
# =========================================================

# Fungsi Bahu Kiri (Untuk Kategori "RENDAH")
# 1.0 jika di bawah batas_bawah
# 0.0 jika di atas batas_atas
# Turun perlahan di antaranya
def fuzzy_rendah(x, batas_bawah, batas_atas):
    if x <= batas_bawah: return 1.0
    if x >= batas_atas: return 0.0
    return (batas_atas - x) / (batas_atas - batas_bawah)

# Fungsi Bahu Kanan (Untuk Kategori "NORMAL/TINGGI")
# 0.0 jika di bawah batas_bawah
# 1.0 jika di atas batas_atas
# Naik perlahan di antaranya
def fuzzy_normal_tinggi(x, batas_bawah, batas_atas):
    if x <= batas_bawah: return 0.0
    if x >= batas_atas: return 1.0
    return (x - batas_bawah) / (batas_atas - batas_bawah)

# =========================================================
# BAGIAN 2: PROSES UTAMA
# =========================================================
def get_prediction():
    try:
        # Cek kelengkapan data
        if len(sys.argv) < 7:
            print(json.dumps({"status": "error", "message": "Data input kurang"}))
            return

        # 1. Tangkap Input (Crisp Values)
        platelet = float(sys.argv[1])
        wbc = float(sys.argv[2])
        fever = int(sys.argv[3])       # 1=Ya, 0=Tidak
        muscle_pain = int(sys.argv[4])
        rash = int(sys.argv[5])
        vomiting = int(sys.argv[6])

        # 2. FUZZIFICATION (Hitung Derajat Keanggotaan / µ)
        
        # --- TROMBOSIT (Acuan: Rendah < 150.000) ---
        # Kita buat area transisi antara 100.000 s/d 150.000
        # Jika < 100.000 -> Derajat Rendah = 1.0 (Mutlak Rendah)
        # Jika 125.000   -> Derajat Rendah = 0.5 (Agak Rendah)
        # Jika > 150.000 -> Derajat Rendah = 0.0 (Tidak Rendah)
        u_trombosit_rendah = fuzzy_rendah(platelet, 100000, 150000)
        u_trombosit_normal = fuzzy_normal_tinggi(platelet, 100000, 150000)

        # --- LEUKOSIT / WBC (Acuan: Rendah < 4.000) ---
        # Area transisi antara 3.500 s/d 4.500
        u_wbc_rendah = fuzzy_rendah(wbc, 3500, 4500)
        u_wbc_normal = fuzzy_normal_tinggi(wbc, 3500, 4500)

        # --- GEJALA KLINIS (Bobot Tambahan) ---
        # Gejala fisik bertindak sebagai penguat diagnosa
        # Total bobot gejala max = 1.0
        bobot_gejala = (fever * 0.4) + (muscle_pain * 0.2) + (rash * 0.2) + (vomiting * 0.2)

        # 3. INFERENCE (Evaluasi Rule Base)
        # Kita gunakan metode SUGENO ORDE NOL (Sederhana & Cepat)
        
        rules = []

        # RULE 1: Trombosit Sangat Rendah -> Indikasi Kuat POSITIF
        # Kekuatan rule diambil dari u_trombosit_rendah
        # Nilai Output (Z) = 100 (Resiko Sangat Tinggi)
        rules.append({"alpha": u_trombosit_rendah, "z": 100})

        # RULE 2: Trombosit Normal TAPI Leukosit Rendah -> Indikasi WASPADA
        # Kekuatan = minimum antara (Trombosit Normal DAN WBC Rendah)
        alpha2 = min(u_trombosit_normal, u_wbc_rendah)
        rules.append({"alpha": alpha2, "z": 60}) # Resiko Sedang

        # RULE 3: Trombosit Normal DAN WBC Normal -> Indikasi NEGATIF
        alpha3 = min(u_trombosit_normal, u_wbc_normal)
        rules.append({"alpha": alpha3, "z": 10}) # Resiko Rendah (Sehat)

        # RULE 4: Faktor Gejala Fisik
        # Jika ada gejala, tingkatkan resiko sedikit
        rules.append({"alpha": bobot_gejala, "z": 70})

        # 4. DEFUZZIFICATION (Weighted Average)
        # Mengubah himpunan fuzzy kembali menjadi angka tegas (persentase)
        
        pembilang = 0 # (alpha * z)
        penyebut = 0  # (alpha)

        for r in rules:
            pembilang += r["alpha"] * r["z"]
            penyebut += r["alpha"]

        if penyebut == 0:
            final_score = 0
        else:
            final_score = pembilang / penyebut

        # 5. KESIMPULAN AKHIR
        # Threshold: Jika skor fuzzy >= 50% maka POSITIF
        if final_score >= 50:
            prediksi = 1 # Positif
        else:
            prediksi = 0 # Negatif

        # Kirim Output JSON ke PHP
        result = {
            "status": "success",
            "prediction": prediksi,
            "probability": round(final_score, 2),
            "input": {
                "platelet": platelet,
                "wbc": wbc
            },
            # Debug info ini untuk laporanmu nanti (biar bisa lihat nilainya)
            "fuzzy_debug": {
                "derajat_trombosit_rendah": round(u_trombosit_rendah, 2),
                "derajat_wbc_rendah": round(u_wbc_rendah, 2)
            }
        }
        print(json.dumps(result))

    except Exception as e:
        print(json.dumps({"status": "error", "message": str(e)}))

if __name__ == "__main__":
    get_prediction()