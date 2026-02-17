@extends('layouts.app')

@section('title', 'Kategori')

{{-- ===================== STYLE PAGE ===================== --}}
@push('css-page')
{{-- CSS khusus halaman kategori --}}
@endpush

@section('content')
<div class="page-header">
  <h3 class="page-title">
    <span class="page-title-icon bg-gradient-primary text-white me-2">
      <i class="mdi mdi-tag"></i>
    </span> Kategori
  </h3>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
      <li class="breadcrumb-item active">Kategori</li>
    </ol>
  </nav>
</div>

@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
@endif

<div class="row">
  {{-- Form Tambah Kategori --}}
  <div class="col-md-4 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Tambah Kategori</h4>
        <form method="POST" action="{{ route('kategori.store') }}">
          @csrf
          <div class="form-group">
            <label for="nama_kategori">Nama Kategori</label>
            <input type="text"
              class="form-control @error('nama_kategori') is-invalid @enderror"
              id="nama_kategori"
              name="nama_kategori"
              value="{{ old('nama_kategori') }}"
              placeholder="Masukkan nama kategori"
              required>
            @error('nama_kategori')
              <span class="invalid-feedback">{{ $message }}</span>
            @enderror
          </div>
          <button type="submit" class="btn btn-gradient-primary me-2">Simpan</button>
          <button type="reset" class="btn btn-light">Reset</button>
        </form>
      </div>
    </div>
  </div>

  {{-- Daftar Kategori --}}
  <div class="col-md-8 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Daftar Kategori</h4>
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>#</th>
                <th>Nama Kategori</th>
                <th>Jumlah Buku</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($kategoris as $i => $kategori)
              <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $kategori->nama_kategori }}</td>
                <td><label class="badge badge-gradient-info">{{ $kategori->bukus_count ?? 0 }}</label></td>
                <td>
                  <button class="btn btn-sm btn-gradient-warning"
                    data-bs-toggle="modal"
                    data-bs-target="#editModal{{ $kategori->idkategori }}">
                    <i class="mdi mdi-pencil"></i>
                  </button>
                  <form method="POST" action="{{ route('kategori.destroy', $kategori->idkategori) }}" class="d-inline"
                    onsubmit="return confirm('Yakin ingin menghapus kategori ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-gradient-danger">
                      <i class="mdi mdi-delete"></i>
                    </button>
                  </form>
                </td>
              </tr>

              {{-- Modal Edit --}}
              <div class="modal fade" id="editModal{{ $kategori->idkategori }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title">Edit Kategori</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST" action="{{ route('kategori.update', $kategori->idkategori) }}">
                      @csrf
                      @method('PUT')
                      <div class="modal-body">
                        <div class="form-group">
                          <label>Nama Kategori</label>
                          <input type="text" class="form-control" name="nama_kategori"
                            value="{{ $kategori->nama_kategori }}" required>
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-gradient-primary">Simpan</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
              @empty
              <tr>
                <td colspan="4" class="text-center text-muted">Belum ada data kategori.</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

{{-- ===================== JAVASCRIPT PAGE ===================== --}}
@push('js-page')
{{-- JS khusus halaman kategori --}}
@endpush