import pandas as pd
import os

# Lokasi file CSV
base_dir = os.path.dirname(os.path.abspath(__file__))
dataset_path = os.path.join(base_dir, '..', 'storage', 'Dengue_clinical_dataset.csv')

try:
    # Baca CSV
    df = pd.read_csv(dataset_path)
    
    print("✅ BERHASIL MEMBACA FILE!")
    print("-" * 30)
    print("Daftar Nama Kolom yang Tersedia:")
    print(df.columns.tolist()) # Ini akan menampilkan list nama kolom
    print("-" * 30)
    
except Exception as e:
    print(f"❌ Gagal membaca file: {e}")