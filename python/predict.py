import sys
import json
import warnings

warnings.filterwarnings("ignore")

# =========================================================
# NILAI PAKAR (CF EXPERT)
# =========================================================
CF_RULES = {
    'trombosit_kritis': 0.9,  
    'trombosit_rendah': 0.8,  
    'wbc_rendah': 0.6,        
    
    # Gejala Fisik (Nilai Maksimal jika User Yakin 100%)
    'demam': 0.4,             
    'nyeri_otot': 0.2,        
    'ruam': 0.5,              
    'muntah': 0.3,
    
    'durasi_kritis': 0.8,   
    'durasi_biasa': 0.3,    
    'durasi_lama': -0.2     
}

def calculate_cf_combine(current_cf, new_cf):
    return current_cf + new_cf * (1 - current_cf)

def get_prediction():
    try:
        if len(sys.argv) < 8:
            print(json.dumps({"status": "error", "message": "Input kurang."}))
            return

        # 1. TANGKAP INPUT (Pastikan FLOAT agar bisa baca 0.5)
        platelet = float(sys.argv[1])
        wbc = float(sys.argv[2])
        fever = int(sys.argv[3])      
        
        # Gejala Fisik (Float: 0.0, 0.5, 1.0)
        muscle_pain = float(sys.argv[4]) 
        rash = float(sys.argv[5])
        vomiting = float(sys.argv[6])
        
        duration = int(sys.argv[7])

        final_cf = 0.0
        log_perhitungan = []

        # -----------------------------------------------------
        # EVALUASI GEJALA
        # -----------------------------------------------------

        # 1. Trombosit
        if platelet < 100000:
            cf = CF_RULES['trombosit_kritis']
            final_cf = calculate_cf_combine(final_cf, cf)
            log_perhitungan.append(f"Trombosit Kritis: CF +{cf}")
        elif platelet < 150000:
            cf = CF_RULES['trombosit_rendah']
            final_cf = calculate_cf_combine(final_cf, cf)
            log_perhitungan.append(f"Trombosit Rendah: CF +{cf}")
        
        # 2. Leukosit
        if wbc < 4000:
            cf = CF_RULES['wbc_rendah']
            final_cf = calculate_cf_combine(final_cf, cf)
            log_perhitungan.append(f"Leukosit Rendah: CF +{cf}")

    
        # 3. Demam & Durasi
        if fever == 1:
            # A. Tambahkan Nilai CF untuk Gejala Demam itu sendiri (INI YANG KURANG KEMARIN)
            cf_gejala_demam = CF_RULES['demam']
            final_cf = calculate_cf_combine(final_cf, cf_gejala_demam)
            log_perhitungan.append(f"Gejala Demam (>37.5°C): CF +{cf_gejala_demam}")

            # B. Baru Hitung Durasinya (Fase Pelana Kuda)
            cf_durasi = 0
            msg = ""
            if 3 <= duration <= 5:
                cf_durasi = CF_RULES['durasi_kritis']
                msg = "Fase Kritis"
            elif duration > 7:
                cf_durasi = CF_RULES['durasi_lama']
                msg = ">7 Hari"
            elif duration > 0:
                cf_durasi = CF_RULES['durasi_biasa']
                msg = "Fase Awal/Lain"
            
            if cf_durasi != 0:
                final_cf = calculate_cf_combine(final_cf, cf_durasi)
                log_perhitungan.append(f"Durasi {duration} Hari ({msg}): CF {cf_durasi}")
        
        # 4. GEJALA FISIK DENGAN BOBOT USER (LOGIKA BARU)
        # Rumus: CF_Gejala = CF_Pakar * CF_User      
        if muscle_pain > 0:
            cf_hasil = CF_RULES['nyeri_otot'] * muscle_pain
            final_cf = calculate_cf_combine(final_cf, cf_hasil)
            log_perhitungan.append(f"Nyeri Otot (Bobot {muscle_pain}): CF +{cf_hasil:.2f}")
        
        if rash > 0:
            cf_hasil = CF_RULES['ruam'] * rash
            final_cf = calculate_cf_combine(final_cf, cf_hasil)
            log_perhitungan.append(f"Ruam Merah (Bobot {rash}): CF +{cf_hasil:.2f}")
            
        if vomiting > 0:
            cf_hasil = CF_RULES['muntah'] * vomiting
            final_cf = calculate_cf_combine(final_cf, cf_hasil)
            log_perhitungan.append(f"Muntah (Bobot {vomiting}): CF +{cf_hasil:.2f}")

        # -----------------------------------------------------
        # HASIL AKHIR
        # -----------------------------------------------------
        final_cf = max(0, min(1, final_cf))
        percentage = round(final_cf * 100, 2)
        prediksi = 1 if percentage >= 50 else 0

        result = {
            "status": "success",
            "prediction": prediksi,
            "probability": percentage,
            "input": {
                "platelet": platelet,
                "wbc": wbc,
                "suhu_raw": fever, # sekedar data
                "duration": duration,
                "muscle_pain": muscle_pain,
                "rash": rash,
                "vomiting": vomiting
            },
            "fuzzy_debug": {
                "cf_log": log_perhitungan
            }
        }
        print(json.dumps(result))

    except Exception as e:
        print(json.dumps({"status": "error", "message": str(e)}))

if __name__ == "__main__":
    get_prediction()