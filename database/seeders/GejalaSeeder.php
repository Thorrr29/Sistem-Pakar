<?php

namespace Database\Seeders;

use App\Models\Gejala;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GejalaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Beberapa gejala umum pada gigi dan mulut.
        $gejala = [
            [
                'kode_gejala' => 'G01',
                'nama_gejala' => 'Gigi ngilu saat makan/minum dingin',
                'deskripsi' => 'Nyeri singkat dan tajam pada gigi ketika terkena makanan atau minuman dingin.',
            ],
            [
                'kode_gejala' => 'G02',
                'nama_gejala' => 'Sakit gigi saat mengunyah',
                'deskripsi' => 'Nyeri muncul ketika gigi digunakan untuk menggigit atau mengunyah makanan.',
            ],
            [
                'kode_gejala' => 'G03',
                'nama_gejala' => 'Gusi bengkak',
                'deskripsi' => 'Pembengkakan pada gusi di sekitar gigi, bisa disertai nyeri.',
            ],
            [
                'kode_gejala' => 'G04',
                'nama_gejala' => 'Bau mulut tidak sedap',
                'deskripsi' => 'Nafas berbau tidak sedap yang tidak hilang dengan menyikat gigi biasa.',
            ],
            [
                'kode_gejala' => 'G05',
                'nama_gejala' => 'Gigi berlubang terlihat',
                'deskripsi' => 'Terdapat lubang atau kavitas yang tampak pada permukaan gigi.',
            ],
            [
                'kode_gejala' => 'G06',
                'nama_gejala' => 'Gusi mudah berdarah',
                'deskripsi' => 'Gusi sering berdarah saat menyikat gigi atau menggigit makanan keras.',
            ],
            [
                'kode_gejala' => 'G07',
                'nama_gejala' => 'Gigi goyang',
                'deskripsi' => 'Gigi terasa tidak kokoh dan dapat digerakkan dengan mudah.',
            ],
            [
                'kode_gejala' => 'G08',
                'nama_gejala' => 'Pembengkakan pada pipi atau wajah',
                'deskripsi' => 'Bengkak pada pipi/wajah di sisi gigi yang sakit, sering disertai nyeri berdenyut.',
            ],
            [
                'kode_gejala' => 'G09',
                'nama_gejala' => 'Nyeri berdenyut terus-menerus',
                'deskripsi' => 'Nyeri gigi yang berat dan berdenyut, sering kali bertambah saat malam hari.',
            ],
            [
                'kode_gejala' => 'G10',
                'nama_gejala' => 'Penumpukan karang gigi',
                'deskripsi' => 'Terdapat deposit keras berwarna kekuningan atau kecoklatan di permukaan gigi dekat gusi.',
            ],
        ];

        foreach ($gejala as $item) {
            Gejala::firstOrCreate(
                ['kode_gejala' => $item['kode_gejala']],
                $item
            );
        }
    }
}
