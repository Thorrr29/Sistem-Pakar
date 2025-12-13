<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Diagnosa - SISPAK CF</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f6f9; }
        .bg-gradient-primary { background: linear-gradient(45deg, #0d6efd, #0099ff); }
        .card { border: none; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        
        /* Style untuk Detail di Modal */
        .detail-label { font-size: 0.85rem; color: #666; }
        .detail-value { font-weight: 600; color: #333; }
        
        /* Box Log CF */
        .cf-log-box { background: #f8f9fa; padding: 15px; border-radius: 8px; border-left: 4px solid #0d6efd; margin-bottom: 15px; }
        .cf-item { border-bottom: 1px dashed #ddd; padding: 8px 0; font-size: 0.9rem; }
        .cf-item:last-child { border-bottom: none; }
        
        .nav-link { color: rgba(255,255,255,0.8) !important; transition: 0.3s; }
        .nav-link:hover { color: #fff !important; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-gradient-primary mb-5 shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php"><i class="fas fa-hospital-user me-2"></i>SISPAK DBD (CF)</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Diagnosa Baru</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active fw-bold" href="#">Riwayat</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container pb-5">
    <div class="card">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-primary"><i class="fas fa-history me-2"></i>Data Riwayat Konsultasi</h5>
            <a href="index.php" class="btn btn-sm btn-outline-primary"><i class="fas fa-plus me-1"></i> Diagnosa Baru</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Tanggal</th>
                            <th>Hasil Diagnosa</th>
                            <th>Keyakinan (CF)</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Cek apakah ada data history
                        if (!empty($history)) {
                            foreach ($history as $row) {
                                // 1. Decode JSON
                                $input = json_decode($row['input_data'], true);
                                $result = json_decode($row['ml_result'], true);
                                
                                // 2. Ambil Data Utama (Fallback ke 0 jika data lama rusak)
                                $prediksi = $result['prediction'] ?? 0;
                                $label = ($prediksi == 1) ? 'POSITIF DBD' : 'NEGATIF DBD';
                                $prob = $result['probability'] ?? 0;
                                
                                // Styling Badge
                                $badge_cls = ($prediksi == 1) ? 'bg-danger' : 'bg-success';
                                
                                // 3. Ambil Log CF (Untuk Modal)
                                $debug = $result['fuzzy_debug'] ?? [];
                                $cf_logs = $debug['cf_log'] ?? []; // Array string log dari Python
                                
                                // ID Modal Unik
                                $modalID = "modalDetail" . $row['id'];
                                ?>
                                
                                <tr>
                                    <td class="ps-4 small text-muted">
                                        <?= date('d M Y, H:i', strtotime($row['created_at'])) ?>
                                    </td>
                                    <td><span class="badge <?= $badge_cls ?>"><?= $label ?></span></td>
                                    <td class="fw-bold"><?= $prob ?>%</td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-info text-white" data-bs-toggle="modal" data-bs-target="#<?= $modalID ?>">
                                            <i class="fas fa-eye me-1"></i> Detail
                                        </button>
                                        <a href="index.php?page=delete&id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus data ini?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>

                                <div class="modal fade" id="<?= $modalID ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header bg-light">
                                                <h5 class="modal-title fw-bold text-primary"><i class="fas fa-file-medical me-2"></i>Rincian Diagnosa #<?= $row['id'] ?></h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-6 border-end">
                                                        <h6 class="text-primary fw-bold mb-3 border-bottom pb-2">1. Data Gejala Pasien</h6>
                                                        
                                                        <div class="row g-2 mb-3">
                                                            <div class="col-6">
                                                                <div class="p-2 border rounded bg-light">
                                                                    <div class="detail-label">Suhu Tubuh</div>
                                                                    <div class="detail-value text-danger"><?= $input['suhu_asli'] ?? '-' ?> °C</div>
                                                                </div>
                                                            </div>
                                                            <div class="col-6">
                                                                <div class="p-2 border rounded bg-light">
                                                                    <div class="detail-label">Lama Demam</div>
                                                                    <div class="detail-value text-warning"><?= $input['duration'] ?? '-' ?> Hari</div>
                                                                </div>
                                                            </div>
                                                            <div class="col-6">
                                                                <div class="p-2 border rounded">
                                                                    <div class="detail-label">Trombosit</div>
                                                                    <div class="detail-value"><?= number_format($input['platelet'] ?? 0) ?></div>
                                                                </div>
                                                            </div>
                                                            <div class="col-6">
                                                                <div class="p-2 border rounded">
                                                                    <div class="detail-label">Leukosit (WBC)</div>
                                                                    <div class="detail-value"><?= number_format($input['wbc'] ?? 0) ?></div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <ul class="list-group list-group-flush small">
                                                            <li class="list-group-item d-flex justify-content-between px-0">
                                                                <span>Nyeri Otot/Sendi</span> 
                                                                <span class="fw-bold"><?= ($input['muscle_pain'] ?? 0) ? 'Ya' : 'Tidak' ?></span>
                                                            </li>
                                                            <li class="list-group-item d-flex justify-content-between px-0">
                                                                <span>Ruam Merah</span> 
                                                                <span class="fw-bold"><?= ($input['rash'] ?? 0) ? 'Ya' : 'Tidak' ?></span>
                                                            </li>
                                                            <li class="list-group-item d-flex justify-content-between px-0">
                                                                <span>Muntah</span> 
                                                                <span class="fw-bold"><?= ($input['vomiting'] ?? 0) ? 'Ya' : 'Tidak' ?></span>
                                                            </li>
                                                        </ul>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <h6 class="text-primary fw-bold mb-3 border-bottom pb-2">2. Perhitungan Certainty Factor</h6>
                                                        
                                                        <div class="cf-log-box">
                                                            <?php if (!empty($cf_logs)): ?>
                                                                <?php foreach($cf_logs as $log): ?>
                                                                    <div class="cf-item">
                                                                        <i class="fas fa-check text-success me-2"></i> <?= $log ?>
                                                                    </div>
                                                                <?php endforeach; ?>
                                                            <?php else: ?>
                                                                <p class="text-muted small mb-0">Tidak ada gejala signifikan yang menambah nilai keyakinan.</p>
                                                            <?php endif; ?>
                                                        </div>

                                                        <div class="alert alert-info py-2 small mb-3">
                                                            <i class="fas fa-info-circle me-1"></i>
                                                            Metode: <b>Certainty Factor (Combine)</b><br>
                                                            Rumus: <i>CF_new = CF_old + CF_gejala * (1 - CF_old)</i>
                                                        </div>

                                                        <div class="text-center mt-2">
                                                            <p class="mb-1 text-muted small">Total Keyakinan Sistem:</p>
                                                            <h2 class="fw-bold text-dark"><?= $prob ?>%</h2>
                                                            <span class="badge <?= $badge_cls ?> p-2 px-3 rounded-pill">
                                                                <?= $label ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div> <div class="modal-footer bg-light">
                                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                        } else {
                            echo "<tr><td colspan='4' class='text-center py-5 text-muted'>Belum ada riwayat diagnosa.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>