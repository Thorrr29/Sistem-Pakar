@extends('layouts.frontend')

@section('title', 'Sistem Pakar Deteksi Penyakit Gigi')

@section('content')
    <div class="row">
        <div class="col-md-8">
            <h1 class="mb-3">Sistem Pakar Deteksi Penyakit Gigi</h1>
            <p class="lead">
                Aplikasi ini membantu Anda mengenali kemungkinan penyakit gigi berdasarkan gejala yang Anda rasakan
                dengan memanfaatkan teknik <strong>sistem pakar</strong> (rule-based / forward chaining).
            </p>
            <p>
                Cukup isi gejala yang Anda alami, kemudian sistem akan memprosesnya menggunakan mesin inferensi
                berbasis Python untuk memberikan dugaan penyakit gigi dan saran tindak lanjut.
            </p>
            <a href="{{ route('konsultasi.create') }}" class="btn btn-primary btn-lg mt-3">
                Mulai Konsultasi
            </a>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    Informasi Singkat
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        <li>Berbasis pengetahuan dokter gigi.</li>
                        <li>Menggunakan mesin inferensi Python (FastAPI).</li>
                        <li>Hanya sebagai alat bantu, bukan pengganti pemeriksaan dokter.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

