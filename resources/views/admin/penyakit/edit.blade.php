@extends('layouts.admin')

@section('title', 'Edit Penyakit Gigi')

@section('content')
    <h1 class="h3 mb-3">Edit Penyakit Gigi</h1>

    <form action="{{ route('admin.penyakit.update', $penyakit) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="kode_penyakit" class="form-label">Kode Penyakit</label>
            <input type="text" name="kode_penyakit" id="kode_penyakit"
                   value="{{ old('kode_penyakit', $penyakit->kode_penyakit) }}"
                   class="form-control @error('kode_penyakit') is-invalid @enderror">
            @error('kode_penyakit')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="nama_penyakit" class="form-label">Nama Penyakit</label>
            <input type="text" name="nama_penyakit" id="nama_penyakit"
                   value="{{ old('nama_penyakit', $penyakit->nama_penyakit) }}"
                   class="form-control @error('nama_penyakit') is-invalid @enderror">
            @error('nama_penyakit')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="deskripsi" class="form-label">Deskripsi</label>
            <textarea name="deskripsi" id="deskripsi" rows="4"
                      class="form-control @error('deskripsi') is-invalid @enderror">{{ old('deskripsi', $penyakit->deskripsi) }}</textarea>
            @error('deskripsi')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="saran_penanganan" class="form-label">Saran Penanganan</label>
            <textarea name="saran_penanganan" id="saran_penanganan" rows="4"
                      class="form-control @error('saran_penanganan') is-invalid @enderror">{{ old('saran_penanganan', $penyakit->saran_penanganan) }}</textarea>
            @error('saran_penanganan')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Perbarui</button>
        <a href="{{ route('admin.penyakit.index') }}" class="btn btn-secondary">Batal</a>
    </form>
@endsection

