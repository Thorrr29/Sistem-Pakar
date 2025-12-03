<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gejala;
use App\Models\Konsultasi;
use App\Models\PenyakitGigi;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Tampilkan ringkasan dashboard admin.
     */
    public function index()
    {
        $jumlahPenyakit = PenyakitGigi::count();
        $jumlahGejala = Gejala::count();
        $jumlahKonsultasi = Konsultasi::count();
        $konsultasiTerbaru = Konsultasi::latest()->take(5)->get();

        return view('admin.dashboard', [
            'jumlahPenyakit' => $jumlahPenyakit,
            'jumlahGejala' => $jumlahGejala,
            'jumlahKonsultasi' => $jumlahKonsultasi,
            'konsultasiTerbaru' => $konsultasiTerbaru,
        ]);
    }
}
