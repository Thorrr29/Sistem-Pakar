<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gejala;
use App\Models\Konsultasi;
use Illuminate\Http\Request;

class KonsultasiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $konsultasi = Konsultasi::with('penyakit')
            ->latest()
            ->paginate(15);

        return view('admin.konsultasi.index', [
            'konsultasi' => $konsultasi,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        abort(404);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        abort(404);
    }

    /**
     * Display the specified resource.
     */
    public function show(Konsultasi $konsultasi)
    {
        $selectedCodes = is_array($konsultasi->gejala_terpilih)
            ? $konsultasi->gejala_terpilih
            : (json_decode((string) $konsultasi->gejala_terpilih, true) ?? []);

        $gejala = [];
        if (! empty($selectedCodes)) {
            $gejala = Gejala::whereIn('kode_gejala', $selectedCodes)
                ->orderBy('kode_gejala')
                ->get();
        }

        return view('admin.konsultasi.show', [
            'konsultasi' => $konsultasi,
            'gejala' => $gejala,
            'penyakit' => $konsultasi->penyakit,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        abort(404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        abort(404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        abort(404);
    }
}
