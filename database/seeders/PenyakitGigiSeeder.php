<?php

namespace Database\Seeders;

use App\Models\PenyakitGigi;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PenyakitGigiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
            // Beberapa contoh penyakit gigi umum.
        $penyakit = [
            [
                'kode_penyakit' => 'P01',
                'nama_penyakit' => 'Karies Gigi',
                'deskripsi' => 'Kerusakan jaringan keras gigi akibat proses demineralisasi oleh asam dari plak bakteri.',
                'saran_penanganan' => 'Segera periksa ke dokter gigi untuk pembersihan karies dan penambalan gigi. Jaga kebersihan mulut dengan menyikat gigi dua kali sehari.',
            ],
            [
                'kode_penyakit' => 'P02',
                'nama_penyakit' => 'Gingivitis',
                'deskripsi' => 'Peradangan pada gusi yang biasanya ditandai dengan gusi merah, bengkak, dan mudah berdarah.',
                'saran_penanganan' => 'Lakukan scaling (pembersihan karang gigi) dan perbaiki kebiasaan menyikat gigi. Gunakan obat kumur antiseptik bila perlu.',
            ],
            [
                'kode_penyakit' => 'P03',
                'nama_penyakit' => 'Periodontitis',
                'deskripsi' => 'Peradangan jaringan penyangga gigi yang lebih berat dari gingivitis dan dapat menyebabkan gigi goyang.',
                'saran_penanganan' => 'Segera konsultasi ke dokter gigi spesialis periodonsia untuk perawatan lanjutan, scaling, dan root planing.',
            ],
            [
                'kode_penyakit' => 'P04',
                'nama_penyakit' => 'Abses Gigi',
                'deskripsi' => 'Infeksi pada ujung akar gigi yang menyebabkan nanah dan pembengkakan hebat.',
                'saran_penanganan' => 'Segera ke dokter gigi untuk pengeluaran nanah, pemberian antibiotik, dan perawatan akar atau pencabutan gigi bila diperlukan.',
            ],
            [
                'kode_penyakit' => 'P05',
                'nama_penyakit' => 'Hipersensitivitas Dentin',
                'deskripsi' => 'Rasa ngilu singkat dan tajam pada gigi saat terpapar rangsang dingin, panas, atau manis.',
                'saran_penanganan' => 'Gunakan pasta gigi khusus gigi sensitif dan konsultasikan ke dokter gigi untuk menutup dentin yang terbuka.',
            ],
        ];

        foreach ($penyakit as $item) {
            PenyakitGigi::firstOrCreate(
                ['kode_penyakit' => $item['kode_penyakit']],
                $item
            );
        }
    }
}
