@extends('layouts.admin')

@section('title', 'Detail Konsultasi')

@section('content')
    <h1 class="h3 mb-3">Detail Konsultasi</h1>

    <div class="row">
        <div class="col-md-8 mb-3">
            <div class="card">
                <div class="card-body">
                    <h5>Data Pasien</h5>
                    <p class="mb-1"><strong>Nama:</strong> {{ $konsultasi->user_name }}</p>
                    @if($konsultasi->user_email)
                        <p class="mb-1"><strong>Email:</strong> {{ $konsultasi->user_email }}</p>
                    @endif
                    <p class="mb-1"><strong>Tanggal:</strong> {{ $konsultasi->created_at->format('d-m-Y H:i') }}</p>

                    <hr>

                    <h5>Hasil Diagnosa</h5>
                    <p class="mb-1">
                        <strong>Penyakit:</strong>
                        {{ optional($penyakit)->kode_penyakit }} - {{ optional($penyakit)->nama_penyakit }}
                    </p>
                    <p class="mb-1">
                        <strong>Skor Kepercayaan:</strong>
                        @if(!is_null($konsultasi->skor_kepercayaan))
                            {{ number_format($konsultasi->skor_kepercayaan * 100, 2) }}%
                        @else
                            -
                        @endif
                    </p>

                    @if($konsultasi->catatan_engine)
                        <hr>
                        <h5>Catatan Engine</h5>
                        <pre class="small bg-light p-2 border">{{ $konsultasi->catatan_engine }}</pre>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-header">Gejala yang Dipilih</div>
                <div class="card-body">
                    @if(count($gejala))
                        <ul class="mb-0">
                            @foreach($gejala as $g)
                                <li><strong>{{ $g->kode_gejala }}</strong> - {{ $g->nama_gejala }}</li>
                            @endforeach
                        </ul>
                    @else
                        <p class="mb-0 text-muted">Tidak ada gejala tercatat.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <a href="{{ route('admin.konsultasi.index') }}" class="btn btn-secondary">Kembali</a>
@endsection

