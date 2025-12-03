<?php

namespace App\Http\Controllers;

use App\Models\Konsultasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Barryvdh\DomPDF\Facade\Pdf;

class KonsultasiController extends Controller
{
    /**
     * Tampilkan form konsultasi untuk pasien (user).
     */
    public function create()
    {
        return view('konsultasi.create');
    }

    /**
     * Proses data konsultasi dari form dan panggil engine Python.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_name' => ['required', 'string', 'max:255'],
            'user_email' => ['nullable', 'email', 'max:255'],
            'gender' => ['required', 'in:Male,Female'],
            'age' => ['required', 'integer', 'min:0'],
            'platelet_count' => ['nullable', 'integer', 'min:0'],
            'wbc' => ['nullable', 'integer', 'min:0'],
            'fever' => ['required', 'in:0,1'],
            'duration_of_fever' => ['nullable', 'integer', 'min:0'],
            'headache' => ['nullable', 'in:0,1'],
            'muscle_pain' => ['nullable', 'in:0,1'],
            'rash' => ['nullable', 'in:0,1'],
            'vomiting' => ['nullable', 'in:0,1'],
        ], [
            'user_name.required' => 'Nama pasien wajib diisi.',
            'gender.required' => 'Jenis kelamin wajib diisi.',
            'age.required' => 'Usia wajib diisi.',
            'fever.required' => 'Informasi demam wajib diisi.',
        ]);

        // Susun fitur sesuai kolom di dataset dengue
        $features = [
            'Gender' => $validated['gender'],
            'Age' => (int) $validated['age'],
            'Platelet Count' => $validated['platelet_count'] !== null ? (int) $validated['platelet_count'] : null,
            'WBC' => $validated['wbc'] !== null ? (int) $validated['wbc'] : null,
            'Fever' => $validated['fever'] === '1',
            'Duration_of_Fever' => $validated['duration_of_fever'] !== null ? (int) $validated['duration_of_fever'] : null,
            'Headache' => $validated['headache'] === '1',
            'Muscle_Pain' => $validated['muscle_pain'] === '1',
            'Rash' => $validated['rash'] === '1',
            'Vomiting' => $validated['vomiting'] === '1',
        ];

        $pythonBaseUrl = rtrim(config('services.python_engine.base_url'), '/');
        $skor = null;
        $catatanEngine = null;

        try {
            $response = Http::timeout(10)->post($pythonBaseUrl . '/infer', [
                'features' => $features,
            ]);

            if ($response->successful()) {
                $data = $response->json();

                // Gunakan probabilitas kelas positif sebagai skor utama
                if (isset($data['p_positive'])) {
                    $skor = (float) $data['p_positive'];
                }

                // Jika ingin menyimpan seluruh respon engine untuk keperluan audit.
                if (! empty($data) && is_array($data)) {
                    $catatanEngine = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                }
            } else {
                $catatanEngine = 'Gagal memanggil engine Python. Kode status: ' . $response->status();
            }
        } catch (\Throwable $e) {
            $catatanEngine = 'Terjadi kesalahan saat menghubungi engine Python: ' . $e->getMessage();
        }

        $konsultasi = Konsultasi::create([
            'user_name' => $validated['user_name'],
            'user_email' => $validated['user_email'] ?? null,
            // Simpan fitur yang dikirim sebagai catatan (gunakan kolom gejala_terpilih)
            'gejala_terpilih' => $features,
            'hasil_penyakit_id' => null,
            'skor_kepercayaan' => $skor,
            'catatan_engine' => $catatanEngine,
        ]);

        return redirect()->route('konsultasi.show', $konsultasi);
    }

    /**
     * Tampilkan hasil konsultasi berdasarkan ID.
     */
    public function show(Konsultasi $konsultasi)
    {
        // Decode data engine (JSON) bila tersedia
        $engineData = null;
        if (! empty($konsultasi->catatan_engine)) {
            $decoded = json_decode((string) $konsultasi->catatan_engine, true);
            if (is_array($decoded)) {
                $engineData = $decoded;
            }
        }

        return view('konsultasi.show', [
            'konsultasi' => $konsultasi,
            'engine' => $engineData,
        ]);
    }

    /**
     * Generate dan download PDF hasil konsultasi.
     */
    public function pdf(Konsultasi $konsultasi)
    {
        // Decode data engine (JSON) bila tersedia
        $engineData = null;
        if (! empty($konsultasi->catatan_engine)) {
            $decoded = json_decode((string) $konsultasi->catatan_engine, true);
            if (is_array($decoded)) {
                $engineData = $decoded;
            }
        }

        $pdf = Pdf::loadView('konsultasi.pdf', [
            'konsultasi' => $konsultasi,
            'engine' => $engineData,
        ])->setPaper('a4');

        $filename = 'konsultasi-dengue-' . $konsultasi->id . '.pdf';

        return $pdf->download($filename);
    }
}
