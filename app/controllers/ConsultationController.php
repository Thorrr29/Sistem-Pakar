<?php
require_once __DIR__ . '/../../public/database.php'; 

class ConsultationController {
    
    public function index() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processPrediction();
        } else {
            require __DIR__ . '/../views/form.php';
        }
    }

    private function processPrediction() {
        global $pdo;

        // 1. AMBIL DATA
        $platelet = $_POST['platelet'] ?? 0;
        $wbc = $_POST['wbc'] ?? 0;
        
        $suhu = $_POST['suhu'] ?? 36;
        $fever = ($suhu >= 37.5) ? 1 : 0; 
        
        $duration = $_POST['duration'] ?? 0;
        
        // --- LOGIKA BARU (USER CERTAINTY) ---
        // Ambil nilai langsung (0, 0.5, atau 1.0)
        // Default ke 0 jika kosong
        $muscle_pain = $_POST['muscle_pain'] ?? 0;
        $rash = $_POST['rash'] ?? 0;
        $vomiting = $_POST['vomiting'] ?? 0;

        // 2. SETUP PYTHON
        $pythonPath = "python"; 
        $scriptPath = realpath(__DIR__ . '/../../python/predict.py');

        if (!$scriptPath) { die("Error: File predict.py tidak ditemukan."); }
        
        // 3. EKSEKUSI 
        // Variabel muscle_pain dll sekarang isinya "0.5" atau "1.0", bukan cuma "1"
        $cmd = "\"$pythonPath\" \"$scriptPath\" $platelet $wbc $fever $muscle_pain $rash $vomiting $duration 2>&1";
        
        // ... (Kode selanjutnya sama persis, tidak ada ubahan) ...
        $output = shell_exec($cmd);
        $result = json_decode($output, true);

        if (!$result || (isset($result['status']) && $result['status'] == 'error')) {
            $error_msg = $result['message'] ?? "Gagal memproses AI.";
            require __DIR__ . '/../views/form.php'; 
            return;
        }

        // 4. SIMPAN DATABASE
        try {
            // Tambahkan info suhu asli ke JSON input agar tercatat
            $inputToSave = $result['input'];
            $inputToSave['suhu_asli'] = $suhu;

            $inputData = json_encode($inputToSave);
            $mlResult = json_encode($result); 

            $sql = "INSERT INTO consultations (input_data, ml_result, created_at) VALUES (:input, :ml, NOW())";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':input' => $inputData, ':ml' => $mlResult]);

            $data = $result; 
            require __DIR__ . '/../views/result.php';

        } catch (PDOException $e) {
            die("DB Error: " . $e->getMessage());
        }
    }

    public function history() {
        global $pdo;
        $stmt = $pdo->query("SELECT * FROM consultations ORDER BY created_at DESC");
        $history = $stmt->fetchAll(PDO::FETCH_ASSOC);
        require __DIR__ . '/../views/history.php';
    }
    
    public function delete($id) {
        global $pdo;
        $stmt = $pdo->prepare("DELETE FROM consultations WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: index.php?page=history");
    }
}
?>