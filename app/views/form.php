<div class="card">
    <h2>Form Diagnosis Gejala DBD</h2>
    <form action="index.php?page=process" method="POST">
        <div class="form-group">
            <label>Suhu Tubuh (°C):</label>
            <input type="number" step="0.1" name="suhu" required class="form-control" placeholder="Contoh: 38.5">
        </div>
        
        <div class="form-group">
            <label>Jumlah Trombosit (per µL):</label>
            <input type="number" name="trombosit" required class="form-control" placeholder="Contoh: 90000">
            <small>Normal: 150.000 - 450.000</small>
        </div>

        <div class="form-group">
            <label>Jumlah Leukosit (WBC):</label>
            <input type="number" name="leukosit" required class="form-control" placeholder="Contoh: 4000">
        </div>

        <div class="checkbox-group">
            <label><input type="checkbox" name="nyeri" value="1"> Nyeri Sendi / Otot</label>
            <label><input type="checkbox" name="ruam" value="1"> Muncul Ruam Merah</label>
            <label><input type="checkbox" name="mual" value="1"> Mual / Muntah</label>
        </div>

        <button type="submit" class="btn-primary">Analisa Sekarang</button>
    </form>
</div>