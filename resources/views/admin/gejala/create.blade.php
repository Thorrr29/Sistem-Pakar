@extends('layouts.admin')

@section('title', 'Tambah Gejala')

@section('content')
    <h1 class="h3 mb-3">Tambah Gejala</h1>

    <form action="{{ route('admin.gejala.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="kode_gejala" class="form-label">Kode Gejala</label>
            <input type="text" name="kode_gejala" id="kode_gejala"
                   value="{{ old('kode_gejala') }}"
                   class="form-control @error('kode_gejala') is-invalid @enderror">
            @error('kode_gejala')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="nama_gejala" class="form-label">Nama Gejala</label>
            <input type="text" name="nama_gejala" id="nama_gejala"
                   value="{{ old('nama_gejala') }}"
                   class="form-control @error('nama_gejala') is-invalid @enderror">
            @error('nama_gejala')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="deskripsi" class="form-label">Deskripsi</label>
            <textarea name="deskripsi" id="deskripsi" rows="4"
                      class="form-control @error('deskripsi') is-invalid @enderror">{{ old('deskripsi') }}</textarea>
            @error('deskripsi')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="{{ route('admin.gejala.index') }}" class="btn btn-secondary">Batal</a>
    </form>
@endsection

