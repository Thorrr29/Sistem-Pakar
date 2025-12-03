@extends('layouts.frontend')

@section('title', 'Tentang Sistem Pakar Penyakit Gigi')

@section('content')
    <h1 class="mb-3">Tentang Sistem</h1>
    <p>
        Sistem Pakar Deteksi Penyakit Gigi ini dirancang untuk membantu pasien dalam melakukan <em>pre-diagnosis</em>
        secara mandiri sebelum berkonsultasi langsung dengan dokter gigi.
    </p>
    <p>
        Sistem bekerja dengan cara:
    </p>
    <ol>
        <li>Pasien memilih gejala-gejala yang dirasakan.</li>
        <li>Laravel mengirimkan data gejala ke <strong>engine Python</strong> melalui REST API.</li>
        <li>Engine Python melakukan pencocokan aturan (rule-based) dan menghitung skor kepercayaan.</li>
        <li>Hasil berupa dugaan penyakit gigi dan saran penanganan dikembalikan ke Laravel untuk ditampilkan.</li>
    </ol>
    <p class="mt-3">
        <strong>Catatan penting:</strong> hasil dari sistem ini <u>bukan</u> diagnosis medis final. Selalu
        konsultasikan dengan dokter gigi untuk pemeriksaan dan penanganan lebih lanjut.
    </p>
@endsection

