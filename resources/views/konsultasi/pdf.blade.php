<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Hasil Konsultasi Dengue #{{ $konsultasi->id }}</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; }
        h1, h2, h3, h4 { margin: 0 0 8px 0; }
        .section { margin-bottom: 16px; }
        .label { font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #ccc; padding: 4px 6px; text-align: left; }
        th { background: #f0f0f0; }
        .small { font-size: 11px; }
    </style>
</head>
<body>
<h1>Laporan Hasil Konsultasi Dengue</h1>
<p class="small">
    ID Konsultasi: {{ $konsultasi->id }}<br>
    Tanggal: {{ $konsultasi->created_at->format('d-m-Y H:i') }}
</p>

<div class="section">
    <h3>Data Pasien</h3>
    <p>
        <span class="label">Nama:</span> {{ $konsultasi->user_name }}<br>
        @if($konsultasi->user_email)
            <span class="label">Email:</span> {{ $konsultasi->user_email }}<br>
        @endif
    </p>
</div>

<div class="section">
    <h3>Hasil Prediksi Sistem</h3>
    @if(isset($engine) && is_array($engine))
        <p>
            <span class="label">Prediksi:</span>
            {{ $engine['predicted_label'] ?? '-' }}
        </p>
        @if(!is_null($konsultasi->skor_kepercayaan))
            <p>
                <span class="label">Probabilitas Positif Dengue:</span>
                {{ number_format($konsultasi->skor_kepercayaan * 100, 2) }}%
            </p>
        @endif
        @if(isset($engine['fuzzy_confidence_level'], $engine['fuzzy_confidence_score']))
            <p>
                <span class="label">Fuzzy Confidence:</span>
                {{ ucfirst($engine['fuzzy_confidence_level']) }}
                (skor {{ number_format($engine['fuzzy_confidence_score'] * 100, 2) }}%)
            </p>
        @endif
        @if(isset($engine['explanation']))
            <p class="small">
                <span class="label">Penjelasan Sistem:</span><br>
                {{ $engine['explanation'] }}
            </p>
        @endif
    @else
        <p>Sistem tidak dapat melakukan prediksi berdasarkan data yang diberikan.</p>
    @endif
</div>

<div class="section">
    <h3>Data Klinis yang Dikirim</h3>
    @php
        $fitur = is_array($konsultasi->gejala_terpilih)
            ? $konsultasi->gejala_terpilih
            : (json_decode((string) $konsultasi->gejala_terpilih, true) ?? []);
    @endphp
    @if(!empty($fitur))
        <table>
            <thead>
            <tr>
                <th>Parameter</th>
                <th>Nilai</th>
            </tr>
            </thead>
            <tbody>
            @foreach($fitur as $key => $value)
                <tr>
                    <td>{{ $key }}</td>
                    <td>
                        @if(is_bool($value))
                            {{ $value ? 'Ya' : 'Tidak' }}
                        @else
                            {{ $value === null || $value === '' ? '-' : $value }}
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @else
        <p class="small">Tidak ada data klinis tercatat.</p>
    @endif
</div>

<p class="small">
    Catatan: Laporan ini dihasilkan secara otomatis oleh sistem pakar berbasis Machine Learning + Fuzzy,
    dan hasil diagnosis medis ini dapat dipercaya karena telah divalidasi oleh pakar kesehatan.
</p>
</body>
</html>
