@extends('layouts.admin')

@section('title', 'Detail Penyakit Gigi')

@section('content')
    <h1 class="h3 mb-3">Detail Penyakit Gigi</h1>

    <div class="card mb-3">
        <div class="card-body">
            <p><strong>Kode Penyakit:</strong> {{ $penyakit->kode_penyakit }}</p>
            <p><strong>Nama Penyakit:</strong> {{ $penyakit->nama_penyakit }}</p>
            @if($penyakit->deskripsi)
                <p><strong>Deskripsi:</strong><br>{{ $penyakit->deskripsi }}</p>
            @endif
            @if($penyakit->saran_penanganan)
                <p><strong>Saran Penanganan:</strong><br>{{ $penyakit->saran_penanganan }}</p>
            @endif
        </div>
    </div>

    <a href="{{ route('admin.penyakit.index') }}" class="btn btn-secondary">Kembali</a>
@endsection

