@extends('layouts.frontend')

@section('title', 'Form Konsultasi Dengue')

@section('content')
    <h1 class="mb-3">Form Konsultasi Dengue (Demam Berdarah)</h1>
    <p class="mb-4">
        Silakan isi data diri dan data klinis berikut. Informasi ini akan digunakan oleh sistem
        untuk melakukan prediksi dengue menggunakan model Machine Learning + Fuzzy yang berjalan di engine Python.
    </p>

    <form method="POST" action="{{ route('konsultasi.store') }}">
        @csrf

        <div class="mb-3">
            <label for="user_name" class="form-label">Nama Pasien <span class="text-danger">*</span></label>
            <input type="text" name="user_name" id="user_name"
                   value="{{ old('user_name') }}"
                   class="form-control @error('user_name') is-invalid @enderror" required>
            @error('user_name')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="user_email" class="form-label">Email (opsional)</label>
            <input type="email" name="user_email" id="user_email"
                   value="{{ old('user_email') }}"
                   class="form-control @error('user_email') is-invalid @enderror">
            @error('user_email')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <hr>

        <h5 class="mb-3">Data Klinis Dengue</h5>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="gender" class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                <select name="gender" id="gender"
                        class="form-select @error('gender') is-invalid @enderror" required>
                    <option value="">-- Pilih --</option>
                    <option value="Male" {{ old('gender') === 'Male' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="Female" {{ old('gender') === 'Female' ? 'selected' : '' }}>Perempuan</option>
                </select>
                @error('gender')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label for="age" class="form-label">Usia (tahun) <span class="text-danger">*</span></label>
                <input type="number" name="age" id="age" min="0"
                       value="{{ old('age') }}"
                       class="form-control @error('age') is-invalid @enderror" required>
                @error('age')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="platelet_count" class="form-label">Jumlah Trombosit (Platelet Count)</label>
                <input type="number" name="platelet_count" id="platelet_count" min="0"
                       value="{{ old('platelet_count') }}"
                       class="form-control @error('platelet_count') is-invalid @enderror">
                <small class="text-muted d-block">
                    Nilai dalam satuan sel/µL, misalnya <strong>150000</strong>. Kosongkan jika tidak tahu.
                    <br>
                    <em>Kategori trombosit:</em>
                    Rendah: &lt; <strong>150000</strong> &nbsp;|&nbsp;
                    Normal: <strong>150000 – 450000</strong> &nbsp;|&nbsp;
                    Tinggi: &gt; <strong>450000</strong>.
                </small>
                @error('platelet_count')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label for="wbc" class="form-label">Jumlah Leukosit (WBC)</label>
                <input type="number" name="wbc" id="wbc" min="0"
                       value="{{ old('wbc') }}"
                       class="form-control @error('wbc') is-invalid @enderror">
                <small class="text-muted d-block">
                    Nilai dalam satuan sel/µL, misalnya <strong>5000</strong>. Kosongkan jika tidak tahu.
                    <br>
                    <em>Kategori leukosit:</em>
                    Rendah: &lt; <strong>4000</strong> &nbsp;|&nbsp;
                    Normal: <strong>4000 – 11000</strong> &nbsp;|&nbsp;
                    Tinggi: &gt; <strong>11000</strong>.
                </small>
                @error('wbc')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="fever" class="form-label">Demam (Fever) <span class="text-danger">*</span></label>
                <select name="fever" id="fever"
                        class="form-select @error('fever') is-invalid @enderror" required>
                    <option value="">-- Pilih --</option>
                    <option value="1" {{ old('fever') === '1' ? 'selected' : '' }}>Ya</option>
                    <option value="0" {{ old('fever') === '0' ? 'selected' : '' }}>Tidak</option>
                </select>
                @error('fever')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label for="duration_of_fever" class="form-label">Durasi Demam (hari)</label>
                <input type="number" name="duration_of_fever" id="duration_of_fever" min="0"
                       value="{{ old('duration_of_fever') }}"
                       class="form-control @error('duration_of_fever') is-invalid @enderror">
                @error('duration_of_fever')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="row">
            <div class="col-md-3 mb-3">
                <label for="headache" class="form-label">Sakit Kepala</label>
                <select name="headache" id="headache"
                        class="form-select @error('headache') is-invalid @enderror">
                    <option value="">-- Pilih --</option>
                    <option value="1" {{ old('headache') === '1' ? 'selected' : '' }}>Ya</option>
                    <option value="0" {{ old('headache') === '0' ? 'selected' : '' }}>Tidak</option>
                </select>
                @error('headache')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-3 mb-3">
                <label for="muscle_pain" class="form-label">Nyeri Otot</label>
                <select name="muscle_pain" id="muscle_pain"
                        class="form-select @error('muscle_pain') is-invalid @enderror">
                    <option value="">-- Pilih --</option>
                    <option value="1" {{ old('muscle_pain') === '1' ? 'selected' : '' }}>Ya</option>
                    <option value="0" {{ old('muscle_pain') === '0' ? 'selected' : '' }}>Tidak</option>
                </select>
                @error('muscle_pain')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-3 mb-3">
                <label for="rash" class="form-label">Ruam (Rash)</label>
                <select name="rash" id="rash"
                        class="form-select @error('rash') is-invalid @enderror">
                    <option value="">-- Pilih --</option>
                    <option value="1" {{ old('rash') === '1' ? 'selected' : '' }}>Ya</option>
                    <option value="0" {{ old('rash') === '0' ? 'selected' : '' }}>Tidak</option>
                </select>
                @error('rash')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-3 mb-3">
                <label for="vomiting" class="form-label">Muntah</label>
                <select name="vomiting" id="vomiting"
                        class="form-select @error('vomiting') is-invalid @enderror">
                    <option value="">-- Pilih --</option>
                    <option value="1" {{ old('vomiting') === '1' ? 'selected' : '' }}>Ya</option>
                    <option value="0" {{ old('vomiting') === '0' ? 'selected' : '' }}>Tidak</option>
                </select>
                @error('vomiting')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">
                Proses Diagnosa
            </button>
            <a href="{{ route('landing') }}" class="btn btn-secondary">
                Kembali ke Beranda
            </a>
        </div>
    </form>
@endsection
