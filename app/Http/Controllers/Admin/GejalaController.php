<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gejala;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GejalaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $gejala = Gejala::orderBy('kode_gejala')->paginate(10);

        return view('admin.gejala.index', [
            'gejala' => $gejala,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.gejala.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_gejala' => ['required', 'string', 'max:10', 'unique:gejala,kode_gejala'],
            'nama_gejala' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
        ]);

        Gejala::create($validated);

        return redirect()
            ->route('admin.gejala.index')
            ->with('success', 'Data gejala berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Gejala $gejala)
    {
        return view('admin.gejala.show', [
            'gejala' => $gejala,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Gejala $gejala)
    {
        return view('admin.gejala.edit', [
            'gejala' => $gejala,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Gejala $gejala)
    {
        $validated = $request->validate([
            'kode_gejala' => [
                'required',
                'string',
                'max:10',
                Rule::unique('gejala', 'kode_gejala')->ignore($gejala->id),
            ],
            'nama_gejala' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
        ]);

        $gejala->update($validated);

        return redirect()
            ->route('admin.gejala.index')
            ->with('success', 'Data gejala berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Gejala $gejala)
    {
        $gejala->delete();

        return redirect()
            ->route('admin.gejala.index')
            ->with('success', 'Data gejala berhasil dihapus.');
    }
}
