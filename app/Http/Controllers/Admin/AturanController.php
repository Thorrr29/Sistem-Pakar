<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Aturan;
use App\Models\Gejala;
use App\Models\PenyakitGigi;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AturanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $aturan = Aturan::with('penyakit')
            ->orderBy('kode_aturan')
            ->paginate(10);

        return view('admin.aturan.index', [
            'aturan' => $aturan,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $penyakit = PenyakitGigi::orderBy('kode_penyakit')->get();
        $gejala = Gejala::orderBy('kode_gejala')->get();

        return view('admin.aturan.create', [
            'penyakit' => $penyakit,
            'gejala' => $gejala,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_aturan' => ['required', 'string', 'max:20', 'unique:aturan,kode_aturan'],
            'penyakit_id' => ['required', 'exists:penyakit_gigi,id'],
            'gejala_ids' => ['required', 'array', 'min:1'],
            'gejala_ids.*' => ['integer', 'exists:gejala,id'],
            'confidence_rule' => ['nullable', 'numeric', 'min:0', 'max:1'],
        ], [
            'gejala_ids.required' => 'Silakan pilih minimal satu gejala untuk aturan ini.',
        ]);

        Aturan::create([
            'kode_aturan' => $validated['kode_aturan'],
            'penyakit_id' => $validated['penyakit_id'],
            'gejala_ids' => json_encode($validated['gejala_ids']),
            'confidence_rule' => $validated['confidence_rule'] ?? null,
        ]);

        return redirect()
            ->route('admin.aturan.index')
            ->with('success', 'Data aturan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Aturan $aturan)
    {
        $penyakit = $aturan->penyakit;
        $gejala = Gejala::whereIn('id', $aturan->gejala_id_list)->get();

        return view('admin.aturan.show', [
            'aturan' => $aturan,
            'penyakit' => $penyakit,
            'gejala' => $gejala,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Aturan $aturan)
    {
        $penyakit = PenyakitGigi::orderBy('kode_penyakit')->get();
        $gejala = Gejala::orderBy('kode_gejala')->get();

        return view('admin.aturan.edit', [
            'aturan' => $aturan,
            'penyakit' => $penyakit,
            'gejala' => $gejala,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Aturan $aturan)
    {
        $validated = $request->validate([
            'kode_aturan' => [
                'required',
                'string',
                'max:20',
                Rule::unique('aturan', 'kode_aturan')->ignore($aturan->id),
            ],
            'penyakit_id' => ['required', 'exists:penyakit_gigi,id'],
            'gejala_ids' => ['required', 'array', 'min:1'],
            'gejala_ids.*' => ['integer', 'exists:gejala,id'],
            'confidence_rule' => ['nullable', 'numeric', 'min:0', 'max:1'],
        ], [
            'gejala_ids.required' => 'Silakan pilih minimal satu gejala untuk aturan ini.',
        ]);

        $aturan->update([
            'kode_aturan' => $validated['kode_aturan'],
            'penyakit_id' => $validated['penyakit_id'],
            'gejala_ids' => json_encode($validated['gejala_ids']),
            'confidence_rule' => $validated['confidence_rule'] ?? null,
        ]);

        return redirect()
            ->route('admin.aturan.index')
            ->with('success', 'Data aturan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Aturan $aturan)
    {
        $aturan->delete();

        return redirect()
            ->route('admin.aturan.index')
            ->with('success', 'Data aturan berhasil dihapus.');
    }
}
