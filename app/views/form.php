<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnosa CF - SISPAK DBD</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: sans-serif; }
        .bg-gradient-primary { background: linear-gradient(45deg, #0d6efd, #0099ff); }
        .card { border: none; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .nav-link { color: rgba(255,255,255,0.8) !important; }
        .nav-link:hover { color: #fff !important; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-gradient-primary mb-5 shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php"><i class="fas fa-hospital-user me-2"></i>SISPAK DBD (Metode CF)</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link active" href="index.php">Diagnosa</a></li>
                <li class="nav-item"><a class="nav-link" href="index.php?page=history">Riwayat</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container pb-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-primary"><i class="fas fa-notes-medical me-2"></i>Formulir Gejala</h5>
                </div>
                <div class="card-body p-4">
                    <?php if (isset($error_msg)): ?><div class="alert alert-danger"><?= $error_msg ?></div><?php endif; ?>

                    <form action="index.php" method="POST">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Suhu Tubuh (°C)</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-thermometer-half text-danger"></i></span>
                                    <input type="number" step="0.1" name="suhu" class="form-control" placeholder="38.5" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Lama Demam (Hari)</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-clock text-warning"></i></span>
                                    <input type="number" name="duration" class="form-control" placeholder="3" min="0" required>
                                </div>
                                <small class="text-muted" style="font-size:0.75rem">*Fase Kritis: Hari ke-3 s/d 5</small>
                            </div>

                            <div class="col-12"><hr class="my-2 opacity-25"></div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Trombosit</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-tint text-danger"></i></span>
                                    <input type="number" name="platelet" class="form-control" placeholder="100000" required>
                                </div>
                                <small class="text-muted" style="font-size:0.75rem">Normal: 150.000 - 450.000</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-muted">Leukosit (WBC)</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-dna text-success"></i></span>
                                    <input type="number" name="wbc" class="form-control" placeholder="4000" required>
                                </div>
                                <small class="text-muted" style="font-size:0.75rem">Normal: 4.500 - 11.000</small>
                            </div>

                            <div class="col-12 mt-4">
                                <h6 class="fw-bold text-primary mb-3"><i class="fas fa-notes-medical me-2"></i>Gejala Fisik Tambahan</h6>
                                <div class="row g-3">
                                    
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold">Nyeri Sendi / Otot</label>
                                        <select name="muscle_pain" class="form-select">
                                            <option value="0">Tidak Ada</option>
                                            <option value="0.5">Ringan / Sedikit Pegal</option>
                                            <option value="1.0">Parah / Nyeri Sekali</option>
                                        </select>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold">Ruam / Bintik Merah</label>
                                        <select name="rash" class="form-select">
                                            <option value="0">Tidak Ada</option>
                                            <option value="0.5">Sedikit / Samar-samar</option>
                                            <option value="1.0">Jelas / Banyak</option>
                                        </select>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold">Mual / Muntah</label>
                                        <select name="vomiting" class="form-select">
                                            <option value="0">Tidak Ada</option>
                                            <option value="0.5">Mual Saja (Tanpa Muntah)</option>
                                            <option value="1.0">Muntah-muntah</option>
                                        </select>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary btn-lg fw-bold"><i class="fas fa-calculator me-2"></i>HITUNG KEYAKINAN (CF)</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>