import pandas as pd
import numpy as np
from sklearn.model_selection import train_test_split
from sklearn.ensemble import RandomForestClassifier
from sklearn.metrics import classification_report, confusion_matrix
import pickle
import os

# 1. Konfigurasi Path
# Asumsi script ini ada di folder 'python/', dan csv ada di 'storage/'
base_dir = os.path.dirname(os.path.abspath(__file__))
dataset_path = os.path.join(base_dir, '..', 'storage', 'Dengue_clinical_dataset.csv')
model_save_path = os.path.join(base_dir, 'models', 'model_rf.pkl')

print(f"📂 Membaca dataset dari: {dataset_path}")

try:
    # 2. Load Data
    df = pd.read_csv(dataset_path)
    
    # Bersihkan nama kolom (kadang ada spasi di awal/akhir)
    df.columns = df.columns.str.strip()
    
    # Cek sebaran data (PENTING UNTUK DEBUG)
    print("\n📊 Statistik Data Awal:")
    print(df['Outcome'].value_counts())
    
    # 3. Preprocessing (Mapping Data Text ke Angka)
    # Sesuaikan mapping ini dengan isi CSV kamu!
    # Contoh: Jika di CSV isinya 'Yes'/'No', ganti jadi 1/0
    mapping = {'Yes': 1, 'No': 0, 'Positive': 1, 'Negative': 0}
    
    # Terapkan mapping ke kolom fitur & target
    # Pastikan nama kolom ini SAMA PERSIS dengan di CSV
    features = ['Platelet Count', 'WBC', 'Fever', 'Muscle_Pain', 'Rash', 'Vomiting']
    target = 'Outcome'
    
    # Fill NA jika ada data kosong
    df = df.fillna(0)

    # Konversi text ke angka (jika data masih berupa string)
    for col in features:
        if df[col].dtype == 'object':
            df[col] = df[col].map(mapping).fillna(0)
            
    # Target juga harus angka
    if df[target].dtype == 'object':
        df[target] = df[target].map(mapping).fillna(0)

    X = df[features]
    y = df[target]

    # 4. Split Data (80% Training, 20% Testing)
    # stratify=y penting agar proporsi positif/negatif di train & test sama
    X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42, stratify=y)

    # 5. Train Model dengan Penyeimbang (Class Weight)
    # class_weight='balanced' akan menghukum model lebih berat jika salah tebak data minoritas
    rf_model = RandomForestClassifier(n_estimators=100, random_state=42, class_weight='balanced')
    rf_model.fit(X_train, y_train)

    # 6. Evaluasi Model
    y_pred = rf_model.predict(X_test)
    
    print("\n📝 Laporan Evaluasi Model:")
    print(classification_report(y_test, y_pred))
    
    print("Matrix Kebingungan (Confusion Matrix):")
    print(confusion_matrix(y_test, y_pred))

    # 7. Simpan Model
    os.makedirs(os.path.dirname(model_save_path), exist_ok=True)
    with open(model_save_path, 'wb') as f:
        pickle.dump(rf_model, f)
        
    print(f"\n✅ Model sukses disimpan di: {model_save_path}")

except Exception as e:
    print(f"\n❌ Terjadi Error: {e}")