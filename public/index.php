<?php
// 1. Panggil Koneksi Database
require_once 'database.php';

$hasil_diagnosa = null;
$error_msg = null;
$debug_info = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $platelet = $_POST['platelet'];
    $wbc = $_POST['wbc'];
    $fever = $_POST['fever'];
    $muscle_pain = $_POST['muscle_pain'];
    $rash = $_POST['rash'];
    $vomiting = $_POST['vomiting'];

    // Path Python
    $scriptPath = realpath(__DIR__ . '/../python/predict.py');
    $pythonPath = "python"; 

    if (!$scriptPath || !file_exists($scriptPath)) {
        $error_msg = "❌ Error: File predict.py tidak ditemukan!";
    } else {
        $command = "\"$pythonPath\" \"$scriptPath\" $platelet $wbc $fever $muscle_pain $rash $vomiting";
        $output = shell_exec($command . " 2>&1");
        $result = json_decode($output, true);

        if ($result && isset($result['status']) && $result['status'] == 'success') {
            $prediksi_label = ($result['prediction'] == 1) ? "POSITIF DBD" : "NEGATIF DBD";
            $akurasi = $result['probability'];
            
            try {
                // --- UPDATE PENTING DI SINI ---
                // Kita simpan SELURUH hasil dari Python (termasuk debug fuzzy) ke database
                // agar nanti bisa dilihat detail perhitungannya.
                $input_json = json_encode($result['input']);
                $ml_result_json = json_encode($result); // Simpan semua response python
                
                $sql = "INSERT INTO consultations (input_data, ml_result, created_at) VALUES (:input, :ml, NOW())";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([':input' => $input_json, ':ml' => $ml_result_json]);
                
                $hasil_diagnosa = [
                    'label' => $prediksi_label,
                    'prob' => $akurasi
                ];
            } catch (PDOException $e) {
                $error_msg = "Gagal simpan database: " . $e->getMessage();
            }
        } else {
            $error_msg = "Gagal memproses AI.";
            $debug_info = "Output: " . htmlspecialchars($output);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Pakar DBD (Fuzzy Logic)</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f6f9; }
        .card { border: none; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .bg-gradient-primary { background: linear-gradient(45deg, #0d6efd, #0099ff); }
        .result-box { border-radius: 12px; padding: 20px; text-align: center; margin-bottom: 20px; }
        .result-pos { background-color: #ffebee; color: #c62828; border: 1px solid #ef9a9a; }
        .result-neg { background-color: #e8f5e9; color: #2e7d32; border: 1px solid #a5d6a7; }
        .range-info { font-size: 0.75rem; color: #6c757d; margin-top: 4px; }
        /* Style untuk Modal Detail */
        .detail-label { font-size: 0.85rem; color: #666; }
        .detail-value { font-weight: 600; color: #333; }
        .fuzzy-box { background: #f8f9fa; padding: 10px; border-radius: 8px; border-left: 4px solid #0d6efd; margin-bottom: 10px; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark bg-gradient-primary mb-4 shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#"><i class="fas fa-hospital-user me-2"></i>SISPAK DBD</a>
    </div>
</nav>

<div class="container pb-5">
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-primary"><i class="fas fa-file-medical me-2"></i>Diagnosa Gejala</h5>
                </div>
                <div class="card-body">
                    
                    <?php if ($hasil_diagnosa): ?>
                        <div class="result-box <?php echo ($hasil_diagnosa['label'] == 'POSITIF DBD') ? 'result-pos' : 'result-neg'; ?>">
                            <h2 class="fw-bold"><?= $hasil_diagnosa['label'] ?></h2>
                            <p>Tingkat Risiko Fuzzy: <strong><?= $hasil_diagnosa['prob'] ?>%</strong></p>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar <?php echo ($hasil_diagnosa['label'] == 'POSITIF DBD') ? 'bg-danger' : 'bg-success'; ?>" 
                                     role="progressbar" style="width: <?= $hasil_diagnosa['prob'] ?>%"></div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($error_msg): ?>
                        <div class="alert alert-danger"><?= $error_msg ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small text-muted fw-bold">Trombosit</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-tint text-danger"></i></span>
                                    <input type="number" name="platelet" class="form-control" placeholder="Cth: 150000" required>
                                </div>
                                <div class="range-info">Normal: 150.000 - 450.000</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-muted fw-bold">Leukosit (WBC)</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-dna text-success"></i></span>
                                    <input type="number" name="wbc" class="form-control" placeholder="Cth: 5000" required>
                                </div>
                                <div class="range-info">Normal: 4.500 - 11.000</div>
                            </div>
                            
                            <div class="col-12"><hr class="my-1 opacity-25"></div>

                            <div class="col-md-6">
                                <label class="form-label small text-muted">Demam?</label>
                                <select name="fever" class="form-select"><option value="0">Tidak</option><option value="1">Ya</option></select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-muted">Nyeri Otot?</label>
                                <select name="muscle_pain" class="form-select"><option value="0">Tidak</option><option value="1">Ya</option></select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-muted">Ruam Merah?</label>
                                <select name="rash" class="form-select"><option value="0">Tidak</option><option value="1">Ya</option></select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-muted">Muntah?</label>
                                <select name="vomiting" class="form-select"><option value="0">Tidak</option><option value="1">Ya</option></select>
                            </div>
                        </div>
                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary py-2 fw-bold">ANALISA SEKARANG</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-history me-2"></i>Riwayat & Detail</h6>
                    <span class="badge bg-secondary">Live Update</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Waktu</th>
                                <th>Hasil</th>
                                <th class="text-center">Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            try {
                                $stmt = $pdo->query("SELECT * FROM consultations ORDER BY created_at DESC LIMIT 5");
                                if ($stmt->rowCount() > 0) {
                                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        // Parsing JSON
                                        // ... di dalam while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) { ...

                                        $input_data = json_decode($row['input_data'], true);
                                        $full_result = json_decode($row['ml_result'], true);
                                        
                                        // --- PERBAIKAN DI SINI ---
                                        // Gunakan '??' untuk memberi nilai default jika key tidak ditemukan (Data Lama)
                                        $raw_prediksi = $full_result['prediction'] ?? 0; 
                                        
                                        // Cek juga jika data lama menyimpan 'label' teks langsung
                                        if (!isset($full_result['prediction']) && isset($full_result['label'])) {
                                            $prediksi = (strpos($full_result['label'], 'POSITIF') !== false) ? 'POSITIF' : 'NEGATIF';
                                        } else {
                                            $prediksi = ($raw_prediksi == 1) ? 'POSITIF' : 'NEGATIF';
                                        }

                                        $akurasi = $full_result['probability'] ?? 0;
                                        $cls = ($prediksi == 'POSITIF') ? 'text-danger' : 'text-success';
                                        
                                    
                                        // Ambil data detail fuzzy (jika ada)
                                        $debug = $full_result['fuzzy_debug'] ?? null; // Sesuai nama key di Python
                                        $u_trombosit_rendah = $debug['derajat_trombosit_rendah'] ?? '-';
                                        $u_wbc_rendah = $debug['derajat_wbc_rendah'] ?? '-';
                                        
                                        // ID Modal unik berdasarkan ID database
                                        $modalID = "detailModal" . $row['id'];
                                        ?>
                                        
                                        <tr>
                                            <td class="ps-3 small text-muted"><?= date('d/m H:i', strtotime($row['created_at'])) ?></td>
                                            <td class="fw-bold <?= $cls ?>"><?= $prediksi ?> <small class="text-muted">(<?= $akurasi ?>%)</small></td>
                                            <td class="text-center">
                                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#<?= $modalID ?>">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </td>
                                        </tr>

                                        <div class="modal fade" id="<?= $modalID ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-light">
                                                        <h5 class="modal-title fw-bold"><i class="fas fa-file-medical-alt me-2"></i>Detail Diagnosa #<?= $row['id'] ?></h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <h6 class="fw-bold text-primary mb-3">1. Data Pasien (Input)</h6>
                                                        <div class="row g-2 mb-4">
                                                            <div class="col-6">
                                                                <div class="p-2 border rounded">
                                                                    <div class="detail-label">Trombosit</div>
                                                                    <div class="detail-value"><?= number_format($input_data['Platelet Count'] ?? $input_data['platelet']) ?></div>
                                                                </div>
                                                            </div>
                                                            <div class="col-6">
                                                                <div class="p-2 border rounded">
                                                                    <div class="detail-label">Leukosit (WBC)</div>
                                                                    <div class="detail-value"><?= number_format($input_data['WBC'] ?? $input_data['wbc']) ?></div>
                                                                </div>
                                                            </div>
                                                            <div class="col-12">
                                                                <div class="p-2 border rounded bg-light">
                                                                    <div class="detail-label">Gejala Lain:</div>
                                                                    <small class="text-dark">
                                                                        Demam: <?= ($input_data['Fever']??0) ? 'Ya':'Tidak' ?>, 
                                                                        Nyeri: <?= ($input_data['Muscle_Pain']??0) ? 'Ya':'Tidak' ?>, 
                                                                        Ruam: <?= ($input_data['Rash']??0) ? 'Ya':'Tidak' ?>, 
                                                                        Muntah: <?= ($input_data['Vomiting']??0) ? 'Ya':'Tidak' ?>
                                                                    </small>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <h6 class="fw-bold text-primary mb-3">2. Perhitungan Fuzzy Logic</h6>
                                                        <?php if($debug): ?>
                                                            <div class="fuzzy-box">
                                                                <div class="d-flex justify-content-between mb-1">
                                                                    <span>Derajat Trombosit Rendah (µ):</span>
                                                                    <span class="fw-bold text-danger"><?= $u_trombosit_rendah ?></span>
                                                                </div>
                                                                <small class="text-muted d-block mb-2">
                                                                    *Dihitung dari kurva bahu (100rb - 150rb)
                                                                </small>
                                                                
                                                                <div class="d-flex justify-content-between mb-1">
                                                                    <span>Derajat Leukosit Rendah (µ):</span>
                                                                    <span class="fw-bold text-warning"><?= $u_wbc_rendah ?></span>
                                                                </div>
                                                                <small class="text-muted d-block">
                                                                    *Dihitung dari kurva bahu (3500 - 4500)
                                                                </small>
                                                            </div>
                                                            <div class="alert alert-info py-2 small">
                                                                <i class="fas fa-info-circle me-1"></i>
                                                                Metode Inferensi: <b>Sugeno Orde Nol</b>.<br>
                                                                Defuzzifikasi: <b>Weighted Average</b>.
                                                            </div>
                                                        <?php else: ?>
                                                            <p class="text-muted">Detail fuzzy tidak tersedia untuk data lama.</p>
                                                        <?php endif; ?>

                                                        <hr>
                                                        <div class="text-center">
                                                            <p class="mb-1 text-muted">Skor Risiko Akhir (Z):</p>
                                                            <h2 class="fw-bold <?= $cls ?>"><?= $akurasi ?>%</h2>
                                                            <span class="badge <?= ($prediksi == 'POSITIF') ? 'bg-danger' : 'bg-success' ?>">
                                                                KESIMPULAN: <?= $prediksi ?> DBD
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                } else {
                                    echo "<tr><td colspan='3' class='text-center py-4 text-muted'>Belum ada riwayat.</td></tr>";
                                }
                            } catch (Exception $e) {
                                echo "<tr><td colspan='3' class='text-center text-danger'>Error: ".$e->getMessage()."</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>