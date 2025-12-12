<?php
require_once __DIR__ . '/../../config/database.php';

class ConsultationController {
    
    public function process() {
        global $pdo;

        // Ambil Data Post
        $suhu = $_POST['suhu'];
        $nyeri = isset($_POST['nyeri']) ? 1 : 0;
        $ruam = isset($_POST['ruam']) ? 1 : 0;
        $mual = isset($_POST['mual']) ? 1 : 0;
        $trombosit = $_POST['trombosit'];
        $leukosit = $_POST['leukosit'];

        // Path Python (Sesuaikan jika di Windows/Linux)
        // Jika di Windows mungkin: "C:\\Python39\\python.exe"
        $pythonPath = "python"; 
        $scriptPath = __DIR__ . "/../../python/predict.py";
        
        // Escape argument untuk keamanan shell
        $cmd = "$pythonPath \"$scriptPath\" $suhu $nyeri $ruam $mual $trombosit $leukosit 2>&1";
        
        // Eksekusi
        $output = shell_exec($cmd);
        $result = json_decode($output, true);

        if (!$result || isset($result['error'])) {
            $error = $result['error'] ?? "Gagal menjalankan Python script. Output: $output";
            require __DIR__ . '/../views/form.php';
            return;
        }

        // Simpan ke Database
        $inputData = json_encode($_POST);
        $mlResult = json_encode($result['ml']);
        $fuzzyResult = json_encode($result['fuzzy']);

        $stmt = $pdo->prepare("INSERT INTO consultations (input_data, ml_result, fuzzy_result) VALUES (?, ?, ?)");
        $stmt->execute([$inputData, $mlResult, $fuzzyResult]);

        // Tampilkan View Hasil
        $data = $result;
        require __DIR__ . '/../views/result.php';
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