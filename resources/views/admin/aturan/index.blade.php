@extends('layouts.admin')

@section('title', 'Kelola Aturan')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3">Daftar Aturan</h1>
        <a href="{{ route('admin.aturan.create') }}" class="btn btn-primary">Tambah Aturan</a>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                    <tr>
                        <th>Kode Aturan</th>
                        <th>Penyakit</th>
                        <th>Jumlah Gejala</th>
                        <th>Confidence</th>
                        <th>Aksi</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($aturan as $item)
                        <tr>
                            <td>{{ $item->kode_aturan }}</td>
                            <td>{{ optional($item->penyakit)->nama_penyakit ?? '-' }}</td>
                            <td>{{ count($item->gejala_id_list) }}</td>
                            <td>
                                @if(!is_null($item->confidence_rule))
                                    {{ number_format($item->confidence_rule, 2) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.aturan.show', $item) }}" class="btn btn-sm btn-outline-secondary">Detail</a>
                                <a href="{{ route('admin.aturan.edit', $item) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form action="{{ route('admin.aturan.destroy', $item) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Yakin ingin menghapus aturan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Belum ada data aturan.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($aturan instanceof \Illuminate\Pagination\AbstractPaginator)
            <div class="card-footer">
                {{ $aturan->links() }}
            </div>
        @endif
    </div>
@endsection

*** Add File: resources/views/admin/aturan/create.blade.php
@extends('layouts.admin')

@section('title', 'Tambah Aturan')

@section('content')
    <h1 class="h3 mb-3">Tambah Aturan</h1>

    <form action="{{ route('admin.aturan.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="kode_aturan" class="form-label">Kode Aturan</label>
            <input type="text" name="kode_aturan" id="kode_aturan"
                   value="{{ old('kode_aturan') }}"
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
                    <option value="{{ $p->id }}" {{ old('penyakit_id') == $p->id ? 'selected' : '' }}>
                        {{ $p->kode_penyakit }} - {{ $p->nama_penyakit }}
                    </option>
                @endforeach
            </select>
            @error('penyakit_id')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

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
                                {{ in_array($g->id, old('gejala_ids', [])) ? 'checked' : '' }}>
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
                   value="{{ old('confidence_rule') }}"
                   class="form-control @error('confidence_rule') is-invalid @enderror">
            @error('confidence_rule')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="{{ route('admin.aturan.index') }}" class="btn btn-secondary">Batal</a>
    </form>
@endsection

*** Add File: resources/views/admin/aturan/edit.blade.php
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

*** Add File: resources/views/admin/aturan/show.blade.php
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

*** Add File: resources/views/admin/konsultasi/index.blade.php
@extends('layouts.admin')

@section('title', 'Riwayat Konsultasi')

@section('content')
    <h1 class="h3 mb-3">Riwayat Konsultasi</h1>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Pasien</th>
                        <th>Penyakit</th>
                        <th>Skor</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($konsultasi as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->user_name }}</td>
                            <td>{{ optional($item->penyakit)->nama_penyakit ?? '-' }}</td>
                            <td>
                                @if(!is_null($item->skor_kepercayaan))
                                    {{ number_format($item->skor_kepercayaan * 100, 1) }}%
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $item->created_at->format('d-m-Y H:i') }}</td>
                            <td>
                                <a href="{{ route('admin.konsultasi.show', $item) }}" class="btn btn-sm btn-outline-primary">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Belum ada data konsultasi.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($konsultasi instanceof \Illuminate\Pagination\AbstractPaginator)
            <div class="card-footer">
                {{ $konsultasi->links() }}
            </div>
        @endif
    </div>
@endsection

*** Add File: resources/views/admin/konsultasi/show.blade.php
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

