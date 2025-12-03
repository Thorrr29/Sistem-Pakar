@extends('layouts.frontend')

@section('title', 'Hasil Konsultasi Dengue')

@section('content')
    <h1 class="mb-3">Hasil Konsultasi Dengue</h1>

    <div class="mb-3">
        <strong>Nama Pasien:</strong> {{ $konsultasi->user_name }}<br>
        @if($konsultasi->user_email)
            <strong>Email:</strong> {{ $konsultasi->user_email }}<br>
        @endif
        <strong>Tanggal Konsultasi:</strong> {{ $konsultasi->created_at->format('d-m-Y H:i') }}
    </div>

    <div class="row">
        <div class="col-md-8 mb-3">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    Hasil Prediksi Sistem (ML + Fuzzy)
                </div>
                <div class="card-body">
                    @if(isset($engine) && is_array($engine))
                        <h4 class="mb-2">
                            Prediksi: {{ $engine['predicted_label'] ?? '-' }}
                        </h4>

                        @if(!is_null($konsultasi->skor_kepercayaan))
                            <p class="mb-1">
                                <strong>Probabilitas Positif Dengue:</strong>
                                {{ number_format($konsultasi->skor_kepercayaan * 100, 2) }}%
                            </p>
                        @endif

                        @if(isset($engine['fuzzy_confidence_level'], $engine['fuzzy_confidence_score']))
                            <p class="mb-1">
                                <strong>Fuzzy Confidence:</strong>
                                {{ ucfirst($engine['fuzzy_confidence_level']) }}
                                (skor {{ number_format($engine['fuzzy_confidence_score'] * 100, 2) }}%)
                            </p>
                        @endif

                        @if(isset($engine['explanation']))
                            <p class="mt-2">
                                <strong>Penjelasan Sistem:</strong><br>
                                {{ $engine['explanation'] }}
                            </p>
                        @endif
                    @else
                        <p>
                            Sistem tidak dapat melakukan prediksi berdasarkan data yang diberikan.
                            Silakan ulangi konsultasi atau periksa langsung ke fasilitas kesehatan.
                        </p>
                    @endif

                    @if($konsultasi->catatan_engine)
                        <hr>
                        <p class="mb-1"><strong>Catatan Lengkap Engine (JSON):</strong></p>
                        <pre class="small bg-light p-2 border">{{ $konsultasi->catatan_engine }}</pre>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-header">
                    Data Klinis yang Dikirim
                </div>
                <div class="card-body">
                    @php
                        $fitur = is_array($konsultasi->gejala_terpilih)
                            ? $konsultasi->gejala_terpilih
                            : (json_decode((string) $konsultasi->gejala_terpilih, true) ?? []);
                    @endphp
                    @if(!empty($fitur))
                        <ul class="mb-0">
                            @foreach($fitur as $key => $value)
                                <li>
                                    <strong>{{ $key }}:</strong>
                                    @if(is_bool($value))
                                        {{ $value ? 'Ya' : 'Tidak' }}
                                    @else
                                        {{ $value === null || $value === '' ? '-' : $value }}
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="mb-0 text-muted">Tidak ada data klinis tercatat.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('konsultasi.create') }}" class="btn btn-primary">Konsultasi Ulang</a>
        <a href="{{ route('konsultasi.pdf', $konsultasi) }}" class="btn btn-outline-secondary">
            Cetak / Download PDF
        </a>
    </div>
@endsection
