<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PenyakitGigi;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PenyakitGigiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $penyakit = PenyakitGigi::orderBy('kode_penyakit')->paginate(10);

        return view('admin.penyakit.index', [
            'penyakit' => $penyakit,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.penyakit.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_penyakit' => ['required', 'string', 'max:10', 'unique:penyakit_gigi,kode_penyakit'],
            'nama_penyakit' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'saran_penanganan' => ['nullable', 'string'],
        ]);

        PenyakitGigi::create($validated);

        return redirect()
            ->route('admin.penyakit.index')
            ->with('success', 'Data penyakit berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PenyakitGigi $penyakit)
    {
        return view('admin.penyakit.show', [
            'penyakit' => $penyakit,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PenyakitGigi $penyakit)
    {
        return view('admin.penyakit.edit', [
            'penyakit' => $penyakit,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PenyakitGigi $penyakit)
    {
        $validated = $request->validate([
            'kode_penyakit' => [
                'required',
                'string',
                'max:10',
                Rule::unique('penyakit_gigi', 'kode_penyakit')->ignore($penyakit->id),
            ],
            'nama_penyakit' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'saran_penanganan' => ['nullable', 'string'],
        ]);

        $penyakit->update($validated);

        return redirect()
            ->route('admin.penyakit.index')
            ->with('success', 'Data penyakit berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PenyakitGigi $penyakit)
    {
        $penyakit->delete();

        return redirect()
            ->route('admin.penyakit.index')
            ->with('success', 'Data penyakit berhasil dihapus.');
    }
}
