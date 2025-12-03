<?php

namespace Database\Seeders;

use App\Models\Aturan;
use App\Models\PenyakitGigi;
use App\Models\Gejala;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AturanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil penyakit berdasarkan kode untuk memudahkan pembuatan aturan.
        $penyakitByKode = PenyakitGigi::all()->keyBy('kode_penyakit');
        $gejalaByKode = Gejala::all()->keyBy('kode_gejala');

        /**
         * Contoh aturan:
         * - IF G01 AND G02 AND G05 THEN P01 (Karies Gigi)
         * - IF G06 AND G10 THEN P02 (Gingivitis)
         * - IF G06 AND G07 AND G10 THEN P03 (Periodontitis)
         * - IF G02 AND G03 AND G08 AND G09 THEN P04 (Abses Gigi)
         * - IF G01 AND NOT G05 THEN P05 (Hipersensitivitas Dentin) -> disederhanakan hanya G01 dominan.
         */

        $rules = [
            [
                'kode_aturan' => 'R01',
                'penyakit_kode' => 'P01',
                'gejala_kode' => ['G01', 'G02', 'G05'],
                'confidence_rule' => 0.9,
            ],
            [
                'kode_aturan' => 'R02',
                'penyakit_kode' => 'P01',
                'gejala_kode' => ['G01', 'G05'],
                'confidence_rule' => 0.8,
            ],
            [
                'kode_aturan' => 'R03',
                'penyakit_kode' => 'P02',
                'gejala_kode' => ['G03', 'G06', 'G10'],
                'confidence_rule' => 0.85,
            ],
            [
                'kode_aturan' => 'R04',
                'penyakit_kode' => 'P03',
                'gejala_kode' => ['G06', 'G07', 'G10'],
                'confidence_rule' => 0.9,
            ],
            [
                'kode_aturan' => 'R05',
                'penyakit_kode' => 'P04',
                'gejala_kode' => ['G02', 'G03', 'G08', 'G09'],
                'confidence_rule' => 0.95,
            ],
            [
                'kode_aturan' => 'R06',
                'penyakit_kode' => 'P05',
                'gejala_kode' => ['G01'],
                'confidence_rule' => 0.7,
            ],
            [
                'kode_aturan' => 'R07',
                'penyakit_kode' => 'P02',
                'gejala_kode' => ['G06'],
                'confidence_rule' => 0.6,
            ],
            [
                'kode_aturan' => 'R08',
                'penyakit_kode' => 'P03',
                'gejala_kode' => ['G06', 'G07'],
                'confidence_rule' => 0.8,
            ],
            [
                'kode_aturan' => 'R09',
                'penyakit_kode' => 'P04',
                'gejala_kode' => ['G03', 'G08'],
                'confidence_rule' => 0.85,
            ],
            [
                'kode_aturan' => 'R10',
                'penyakit_kode' => 'P01',
                'gejala_kode' => ['G05'],
                'confidence_rule' => 0.6,
            ],
        ];

        foreach ($rules as $rule) {
            $penyakit = $penyakitByKode[$rule['penyakit_kode']] ?? null;
            if (! $penyakit) {
                continue;
            }

            // Map kode gejala ke ID yang valid.
            $gejalaIds = [];
            foreach ($rule['gejala_kode'] as $kodeGejala) {
                $gejala = $gejalaByKode[$kodeGejala] ?? null;
                if ($gejala) {
                    $gejalaIds[] = $gejala->id;
                }
            }

            Aturan::firstOrCreate(
                ['kode_aturan' => $rule['kode_aturan']],
                [
                    'penyakit_id' => $penyakit->id,
                    'gejala_ids' => json_encode($gejalaIds),
                    'confidence_rule' => $rule['confidence_rule'],
                ]
            );
        }
    }
}
