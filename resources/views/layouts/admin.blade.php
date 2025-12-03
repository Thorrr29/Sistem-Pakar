<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard Admin - Sistem Pakar Gigi')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="{{ route('admin.dashboard') }}">Admin Sistem Pakar Gigi</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar"
                aria-controls="adminNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="adminNavbar">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.penyakit.*') ? 'active' : '' }}" href="{{ route('admin.penyakit.index') }}">Penyakit</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.gejala.*') ? 'active' : '' }}" href="{{ route('admin.gejala.index') }}">Gejala</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.aturan.*') ? 'active' : '' }}" href="{{ route('admin.aturan.index') }}">Aturan</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.konsultasi.*') ? 'active' : '' }}" href="{{ route('admin.konsultasi.index') }}">Konsultasi</a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <span class="navbar-text me-3">
                        {{ auth()->user()->name ?? 'Admin' }}
                    </span>
                </li>
                <li class="nav-item">
                    <form method="POST" action="{{ url('/logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-outline-light btn-sm">Logout</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>

<main class="container mb-5">
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Terjadi kesalahan:</strong>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

