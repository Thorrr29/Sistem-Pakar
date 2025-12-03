@extends('layouts.admin')

@section('title', 'Detail Gejala')

@section('content')
    <h1 class="h3 mb-3">Detail Gejala</h1>

    <div class="card mb-3">
        <div class="card-body">
            <p><strong>Kode Gejala:</strong> {{ $gejala->kode_gejala }}</p>
            <p><strong>Nama Gejala:</strong> {{ $gejala->nama_gejala }}</p>
            @if($gejala->deskripsi)
                <p><strong>Deskripsi:</strong><br>{{ $gejala->deskripsi }}</p>
            @endif
        </div>
    </div>

    <a href="{{ route('admin.gejala.index') }}" class="btn btn-secondary">Kembali</a>
@endsection

