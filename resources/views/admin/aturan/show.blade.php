@extends('layouts.admin')

@section('title', 'Detail Aturan')

@section('content')
    <h1 class="h3 mb-3">Detail Aturan</h1>

    <div class="card mb-3">
        <div class="card-body">
            <p><strong>Kode Aturan:</strong> {{ $aturan->kode_aturan }}</p>
            <p><strong>Penyakit:</strong> {{ optional($penyakit)->kode_penyakit }} - {{ optional($penyakit)->nama_penyakit }}</p>
            <p>
                <strong>Gejala (premis):</strong><br>
                @if(count($gejala))
                    <ul>
                        @foreach($gejala as $g)
                            <li><strong>{{ $g->kode_gejala }}</strong> - {{ $g->nama_gejala }}</li>
                        @endforeach
                    </ul>
                @else
                    <span class="text-muted">Tidak ada gejala.</span>
                @endif
            </p>
            <p>
                <strong>Confidence Rule:</strong>
                @if(!is_null($aturan->confidence_rule))
                    {{ number_format($aturan->confidence_rule, 2) }}
                @else
                    -
                @endif
            </p>
        </div>
    </div>

    <a href="{{ route('admin.aturan.index') }}" class="btn btn-secondary">Kembali</a>
@endsection

