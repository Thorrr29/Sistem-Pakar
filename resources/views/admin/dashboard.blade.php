@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
    <h1 class="mb-4">Dashboard Admin</h1>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-bg-primary mb-3">
                <div class="card-body">
                    <h5 class="card-title">Total Penyakit</h5>
                    <p class="card-text display-6">{{ $jumlahPenyakit }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-bg-success mb-3">
                <div class="card-body">
                    <h5 class="card-title">Total Gejala</h5>
                    <p class="card-text display-6">{{ $jumlahGejala }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-bg-info mb-3">
                <div class="card-body">
                    <h5 class="card-title">Total Konsultasi</h5>
                    <p class="card-text display-6">{{ $jumlahKonsultasi }}</p>
                </div>
            </div>
        </div>
    </div>

    <h2 class="h4 mb-3">Konsultasi Terbaru</h2>
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Pasien</th>
                        <th>Penyakit</th>
                        <th>Skor</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($konsultasiTerbaru as $item)
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
    </div>
@endsection

    <form action="{{ route('admin.penyakit.update', $penyakit) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="kode_penyakit" class="form-label">Kode Penyakit</label>
            <input type="text" name="kode_penyakit" id="kode_penyakit"
                   value="{{ old('kode_penyakit', $penyakit->kode_penyakit) }}"
                   class="form-control @error('kode_penyakit') is-invalid @enderror">
            @error('kode_penyakit')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="nama_penyakit" class="form-label">Nama Penyakit</label>
            <input type="text" name="nama_penyakit" id="nama_penyakit"
                   value="{{ old('nama_penyakit', $penyakit->nama_penyakit) }}"
                   class="form-control @error('nama_penyakit') is-invalid @enderror">
            @error('nama_penyakit')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="deskripsi" class="form-label">Deskripsi</label>
            <textarea name="deskripsi" id="deskripsi" rows="4"
                      class="form-control @error('deskripsi') is-invalid @enderror">{{ old('deskripsi', $penyakit->deskripsi) }}</textarea>
            @error('deskripsi')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="saran_penanganan" class="form-label">Saran Penanganan</label>
            <textarea name="saran_penanganan" id="saran_penanganan" rows="4"
                      class="form-control @error('saran_penanganan') is-invalid @enderror">{{ old('saran_penanganan', $penyakit->saran_penanganan) }}</textarea>
            @error('saran_penanganan')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Perbarui</button>
        <a href="{{ route('admin.penyakit.index') }}" class="btn btn-secondary">Batal</a>
    </form>
@endsection

*** Add File: resources/views/admin/penyakit/show.blade.php
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

*** Add File: resources/views/admin/gejala/index.blade.php
@extends('layouts.admin')

@section('title', 'Kelola Gejala')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h3">Daftar Gejala</h1>
        <a href="{{ route('admin.gejala.create') }}" class="btn btn-primary">Tambah Gejala</a>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Gejala</th>
                        <th>Dibuat</th>
                        <th>Aksi</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($gejala as $item)
                        <tr>
                            <td>{{ $item->kode_gejala }}</td>
                            <td>{{ $item->nama_gejala }}</td>
                            <td>{{ $item->created_at->format('d-m-Y') }}</td>
                            <td>
                                <a href="{{ route('admin.gejala.show', $item) }}" class="btn btn-sm btn-outline-secondary">Detail</a>
                                <a href="{{ route('admin.gejala.edit', $item) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                <form action="{{ route('admin.gejala.destroy', $item) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">Belum ada data gejala.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($gejala instanceof \Illuminate\Pagination\AbstractPaginator)
            <div class="card-footer">
                {{ $gejala->links() }}
            </div>
        @endif
    </div>
@endsection

*** Add File: resources/views/admin/gejala/create.blade.php
@extends('layouts.admin')

@section('title', 'Tambah Gejala')

@section('content')
    <h1 class="h3 mb-3">Tambah Gejala</h1>

    <form action="{{ route('admin.gejala.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="kode_gejala" class="form-label">Kode Gejala</label>
            <input type="text" name="kode_gejala" id="kode_gejala"
                   value="{{ old('kode_gejala') }}"
                   class="form-control @error('kode_gejala') is-invalid @enderror">
            @error('kode_gejala')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="nama_gejala" class="form-label">Nama Gejala</label>
            <input type="text" name="nama_gejala" id="nama_gejala"
                   value="{{ old('nama_gejala') }}"
                   class="form-control @error('nama_gejala') is-invalid @enderror">
            @error('nama_gejala')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="deskripsi" class="form-label">Deskripsi</label>
            <textarea name="deskripsi" id="deskripsi" rows="4"
                      class="form-control @error('deskripsi') is-invalid @enderror">{{ old('deskripsi') }}</textarea>
            @error('deskripsi')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="{{ route('admin.gejala.index') }}" class="btn btn-secondary">Batal</a>
    </form>
@endsection

*** Add File: resources/views/admin/gejala/edit.blade.php
@extends('layouts.admin')

@section('title', 'Edit Gejala')

@section('content')
    <h1 class="h3 mb-3">Edit Gejala</h1>

    <form action="{{ route('admin.gejala.update', $gejala) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="kode_gejala" class="form-label">Kode Gejala</label>
            <input type="text" name="kode_gejala" id="kode_gejala"
                   value="{{ old('kode_gejala', $gejala->kode_gejala) }}"
                   class="form-control @error('kode_gejala') is-invalid @enderror">
            @error('kode_gejala')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="nama_gejala" class="form-label">Nama Gejala</label>
            <input type="text" name="nama_gejala" id="nama_gejala"
                   value="{{ old('nama_gejala', $gejala->nama_gejala) }}"
                   class="form-control @error('nama_gejala') is-invalid @enderror">
            @error('nama_gejala')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="deskripsi" class="form-label">Deskripsi</label>
            <textarea name="deskripsi" id="deskripsi" rows="4"
                      class="form-control @error('deskripsi') is-invalid @enderror">{{ old('deskripsi', $gejala->deskripsi) }}</textarea>
            @error('deskripsi')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Perbarui</button>
        <a href="{{ route('admin.gejala.index') }}" class="btn btn-secondary">Batal</a>
    </form>
@endsection

*** Add File: resources/views/admin/gejala/show.blade.php
@extends('layouts.admin')

@section('title', 'Detail Gejala')

@section('content')
    <h1 class="h3 mb-3">Detail Gejala</h1>

    <div class="card mb-3">
        <div class="card-body">
            <p><strong>Kode Gejala:</strong> {{ $gejala->kode_gejala }}</p>
            <p><strong>Nama Gejala:</strong> {{ $gejala->nama_gejala }}</p>
            @if($gejala->deskripsi)
                <p><strong>Deskripsi:</strong><br>{{ $gejala->deskripsi }}</p>
            @endif
        </div>
    </div>

    <a href="{{ route('admin.gejala.index') }}" class="btn btn-secondary">Kembali</a>
@endsection
