@extends('layouts.admin')

@section('title', 'Edit Aturan')

@section('content')
    <h1 class="h3 mb-3">Edit Aturan</h1>

    <form action="{{ route('admin.aturan.update', $aturan) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="kode_aturan" class="form-label">Kode Aturan</label>
            <input type="text" name="kode_aturan" id="kode_aturan"
                   value="{{ old('kode_aturan', $aturan->kode_aturan) }}"
                   class="form-control @error('kode_aturan') is-invalid @enderror">
            @error('kode_aturan')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="penyakit_id" class="form-label">Penyakit</label>
            <select name="penyakit_id" id="penyakit_id"
                    class="form-select @error('penyakit_id') is-invalid @enderror">
                <option value="">-- Pilih Penyakit --</option>
                @foreach($penyakit as $p)
                    <option value="{{ $p->id }}" {{ old('penyakit_id', $aturan->penyakit_id) == $p->id ? 'selected' : '' }}>
                        {{ $p->kode_penyakit }} - {{ $p->nama_penyakit }}
                    </option>
                @endforeach
            </select>
            @error('penyakit_id')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        @php
            $selectedGejalaIds = old('gejala_ids', $aturan->gejala_id_list);
        @endphp

        <div class="mb-3">
            <label class="form-label">Gejala (premis)</label>
            <div class="row">
                @foreach($gejala as $g)
                    <div class="col-md-4 mb-2">
                        <div class="form-check">
                            <input class="form-check-input"
                                   type="checkbox"
                                   name="gejala_ids[]"
                                   id="gejala_{{ $g->id }}"
                                   value="{{ $g->id }}"
                                {{ in_array($g->id, $selectedGejalaIds ?? []) ? 'checked' : '' }}>
                            <label class="form-check-label" for="gejala_{{ $g->id }}">
                                <strong>{{ $g->kode_gejala }}</strong> - {{ $g->nama_gejala }}
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>
            @error('gejala_ids')
            <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="confidence_rule" class="form-label">Confidence Rule (0 - 1)</label>
            <input type="number" step="0.01" min="0" max="1"
                   name="confidence_rule" id="confidence_rule"
                   value="{{ old('confidence_rule', $aturan->confidence_rule) }}"
                   class="form-control @error('confidence_rule') is-invalid @enderror">
            @error('confidence_rule')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Perbarui</button>
        <a href="{{ route('admin.aturan.index') }}" class="btn btn-secondary">Batal</a>
    </form>
@endsection

