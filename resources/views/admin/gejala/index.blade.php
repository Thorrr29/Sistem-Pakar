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

