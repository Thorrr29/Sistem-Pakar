<div class="card result-card">
    <h2>Hasil Diagnosis Sistem Pakar</h2>
    
    <div class="result-grid">
        <div class="box ml-box">
            <h3>🤖 Machine Learning (Random Forest)</h3>
            <p class="label">Prediksi: <strong><?= $data['ml']['label'] ?></strong></p>
            <p>Probabilitas: <?= $data['ml']['probability_perc'] ?>%</p>
        </div>

        <div class="box fuzzy-box">
            <h3>🧠 Fuzzy Logic System</h3>
            <p class="score">Skor Keparahan: <strong><?= $data['fuzzy']['score'] ?> / 100</strong></p>
            <p class="category">Kategori: <span class="badge"><?= $data['fuzzy']['category'] ?></span></p>
            <hr>
            <small>Detail Aktivasi Rule:</small>
            <ul>
                <li>Trombosit Low Degree: <?= number_format($data['fuzzy']['details']['rule_trombosit_low'], 2) ?></li>
                <li>Demam Tinggi Degree: <?= number_format($data['fuzzy']['details']['rule_suhu_high'], 2) ?></li>
            </ul>
        </div>
    </div>

    <div class="explanation">
        <h3>Kesimpulan Sistem</h3>
        <?php if($data['ml']['label'] == 'Positif DBD' && $data['fuzzy']['score'] > 50): ?>
            <div class="alert danger">
                <strong>PERINGATAN:</strong> Kedua metode (ML & Fuzzy) mengindikasikan risiko tinggi DBD. Segera periksakan ke dokter!
            </div>
        <?php elseif($data['ml']['label'] == 'Negatif' && $data['fuzzy']['score'] < 40): ?>
            <div class="alert success">
                Kemungkinan besar bukan DBD, namun tetap pantau kondisi suhu tubuh.
            </div>
        <?php else: ?>
            <div class="alert warning">
                Hasil inkonklusif (Berbeda antar metode). Disarankan pemeriksaan lab lebih lanjut.
            </div>
        <?php endif; ?>
    </div>
    
    <a href="index.php?page=form" class="btn-secondary">Cek Ulang</a>
</div>