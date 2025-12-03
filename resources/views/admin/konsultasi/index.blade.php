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

