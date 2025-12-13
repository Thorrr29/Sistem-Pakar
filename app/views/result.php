<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Diagnosa CF</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: sans-serif; background-color: #f4f6f9; }
        .result-box { padding: 40px 20px; border-radius: 15px; text-align: center; color: white; margin-bottom: 30px; }
        .result-positif { background: linear-gradient(135deg, #dc3545, #ff6b6b); }
        .result-negatif { background: linear-gradient(135deg, #198754, #20c997); }
        .score-display { font-size: 3.5rem; font-weight: 700; }
        .cf-log-item { border-left: 3px solid #0d6efd; padding-left: 10px; margin-bottom: 8px; font-size: 0.9rem; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark bg-primary mb-4 shadow-sm">
    <div class="container"><a class="navbar-brand fw-bold" href="index.php">SISPAK DBD (CF Method)</a></div>
</nav>

<div class="container pb-5">
    <?php 
    if (empty($data)) {
        echo "<div class='alert alert-warning text-center'>Data tidak ditemukan. Silakan diagnosa ulang.</div>";
        exit;
    }

    $prediksi = $data['prediction'];
    $persentase = $data['probability'];
    $input = $data['input'];
    $debug = $data['fuzzy_debug'] ?? []; // Ini isinya log CF
    
    // Labeling
    $label = ($prediksi == 1) ? "POSITIF DBD" : "NEGATIF DBD";
    $styleClass = ($prediksi == 1) ? "result-positif" : "result-negatif";
    $icon = ($prediksi == 1) ? "fa-exclamation-triangle" : "fa-shield-alt";
    ?>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="result-box <?= $styleClass ?>">
                <i class="fas <?= $icon ?>" style="font-size: 4rem; margin-bottom: 15px;"></i>
                <h1 class="fw-bold"><?= $label ?></h1>
                <p class="mb-0 opacity-75">Tingkat Keyakinan (Certainty Factor)</p>
                <div class="score-display"><?= $persentase ?>%</div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-header bg-white fw-bold text-primary">Data Pasien</div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>Suhu Tubuh</span> 
                                    <span class="fw-bold"><?= $input['suhu_asli'] ?? '-' ?> °C</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>Lama Demam</span> 
                                    <span class="fw-bold"><?= $input['duration'] ?? 0 ?> Hari</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>Trombosit</span> 
                                    <span class="fw-bold"><?= number_format($input['platelet']) ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>Leukosit</span> 
                                    <span class="fw-bold"><?= number_format($input['wbc']) ?></span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-header bg-white fw-bold text-info">Rincian Perhitungan CF</div>
                        <div class="card-body bg-light">
                            <p class="small text-muted mb-3">Berikut adalah akumulasi keyakinan berdasarkan gejala:</p>
                            
                            <?php if(!empty($debug['cf_log'])): ?>
                                <?php foreach($debug['cf_log'] as $log): ?>
                                    <div class="cf-log-item bg-white p-2 rounded shadow-sm">
                                        <i class="fas fa-check-circle text-success me-1"></i> <?= $log ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-muted text-center">Tidak ada gejala signifikan yang terdeteksi.</p>
                            <?php endif; ?>
                            
                            <hr>
                            <div class="small text-muted">
                                <b>Rumus:</b> CF_combine = CF_old + CF_new * (1 - CF_old)
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-4">
                <a href="index.php" class="btn btn-outline-primary px-4">Diagnosa Ulang</a>
                <a href="index.php?page=history" class="btn btn-primary px-4">Lihat Riwayat</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>