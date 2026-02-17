@extends('layouts.app')

@section('title', 'Buku')

{{-- ===================== STYLE PAGE ===================== --}}
@push('css-page')
<style>
  .table td, .table th { vertical-align: middle; }
  .judul-col { max-width: 220px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
</style>
@endpush

@section('content')

{{-- Page Header --}}
<div class="page-header">
  <h3 class="page-title">
    <span class="page-title-icon bg-gradient-primary text-white me-2">
      <i class="mdi mdi-book-open-page-variant"></i>
    </span>
    Buku
  </h3>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
      <li class="breadcrumb-item active" aria-current="page">Buku</li>
    </ol>
  </nav>
</div>

{{-- Alert --}}
@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="mdi mdi-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
@endif

<div class="row">

  {{-- ===== FORM TAMBAH BUKU ===== --}}
  <div class="col-lg-4 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Tambah Buku</h4>
        <p class="card-description">Isi form untuk menambah koleksi buku baru</p>
        <form method="POST" action="{{ route('buku.store') }}">
          @csrf

          <div class="form-group">
            <label for="kode">Kode Buku <span class="text-danger">*</span></label>
            <input
              type="text"
              class="form-control @error('kode') is-invalid @enderror"
              id="kode"
              name="kode"
              value="{{ old('kode') }}"
              placeholder="Contoh: NV-01"
              autocomplete="off"
              required>
            @error('kode')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="form-group">
            <label for="judul">Judul Buku <span class="text-danger">*</span></label>
            <input
              type="text"
              class="form-control @error('judul') is-invalid @enderror"
              id="judul"
              name="judul"
              value="{{ old('judul') }}"
              placeholder="Masukkan judul buku"
              required>
            @error('judul')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="form-group">
            <label for="pengarang">Pengarang <span class="text-danger">*</span></label>
            <input
              type="text"
              class="form-control @error('pengarang') is-invalid @enderror"
              id="pengarang"
              name="pengarang"
              value="{{ old('pengarang') }}"
              placeholder="Nama pengarang"
              required>
            @error('pengarang')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="form-group">
            <label for="idkategori">Kategori <span class="text-danger">*</span></label>
            <select
              class="form-control @error('idkategori') is-invalid @enderror"
              id="idkategori"
              name="idkategori"
              required>
              <option value="">-- Pilih Kategori --</option>
              @foreach($kategoris as $kat)
                <option value="{{ $kat->idkategori }}"
                  {{ old('idkategori') == $kat->idkategori ? 'selected' : '' }}>
                  {{ $kat->nama_kategori }}
                </option>
              @endforeach
            </select>
            @error('idkategori')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="d-flex gap-2 mt-3">
            <button type="submit" class="btn btn-gradient-primary btn-fw">
              <i class="mdi mdi-content-save me-1"></i> Simpan
            </button>
            <button type="reset" class="btn btn-light btn-fw">
              <i class="mdi mdi-refresh me-1"></i> Reset
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  {{-- ===== TABEL DAFTAR BUKU ===== --}}
  <div class="col-lg-8 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Daftar Buku</h4>
        <p class="card-description">
          Total <code>{{ $bukus->count() }}</code> buku dalam koleksi
        </p>
        <div class="table-responsive">
          <table class="table table-hover">
            <thead>
              <tr>
                <th style="width:45px">#</th>
                <th style="width:80px">Kode</th>
                <th>Judul</th>
                <th>Pengarang</th>
                <th style="width:110px">Kategori</th>
                <th class="text-center" style="width:120px">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($bukus as $index => $buku)
              <tr>
                <td>{{ $index + 1 }}</td>
                <td><span class="badge badge-gradient-primary">{{ $buku->kode }}</span></td>
                <td>
                  <span title="{{ $buku->judul }}" class="judul-col d-inline-block">
                    {{ $buku->judul }}
                  </span>
                </td>
                <td>{{ $buku->pengarang }}</td>
                <td>
                  <label class="badge badge-gradient-success">
                    {{ $buku->kategori->nama_kategori ?? '-' }}
                  </label>
                </td>
                <td class="text-center">
                  <div class="d-flex justify-content-center" style="gap:4px">
                    <button
                      class="btn btn-sm btn-gradient-warning"
                      title="Edit"
                      data-bs-toggle="modal"
                      data-bs-target="#editBukuModal{{ $buku->idbuku }}">
                      <i class="mdi mdi-pencil"></i>
                    </button>
                    <form method="POST" action="{{ route('buku.destroy', $buku->idbuku) }}" class="d-inline"
                      onsubmit="return confirm('Hapus buku &quot;{{ $buku->judul }}&quot;?')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-gradient-danger" title="Hapus">
                        <i class="mdi mdi-delete"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="6" class="text-center py-5 text-muted">
                  <i class="mdi mdi-book-off mdi-36px d-block mb-2 text-muted"></i>
                  Belum ada data buku.
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

</div>

{{-- ===== MODAL EDIT BUKU ===== --}}
@foreach($bukus as $buku)
<div class="modal fade" id="editBukuModal{{ $buku->idbuku }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
          <i class="mdi mdi-pencil me-2 text-warning"></i>Edit Buku
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form method="POST" action="{{ route('buku.update', $buku->idbuku) }}">
        @csrf
        @method('PUT')
        <div class="modal-body">

          <div class="form-group">
            <label>Kode Buku <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="kode" value="{{ $buku->kode }}" required>
          </div>

          <div class="form-group">
            <label>Judul Buku <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="judul" value="{{ $buku->judul }}" required>
          </div>

          <div class="form-group">
            <label>Pengarang <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="pengarang" value="{{ $buku->pengarang }}" required>
          </div>

          <div class="form-group mb-0">
            <label>Kategori <span class="text-danger">*</span></label>
            <select class="form-control" name="idkategori" required>
              <option value="">-- Pilih Kategori --</option>
              @foreach($kategoris as $kat)
                <option value="{{ $kat->idkategori }}"
                  {{ $buku->idkategori == $kat->idkategori ? 'selected' : '' }}>
                  {{ $kat->nama_kategori }}
                </option>
              @endforeach
            </select>
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">
            <i class="mdi mdi-close me-1"></i>Batal
          </button>
          <button type="submit" class="btn btn-gradient-primary">
            <i class="mdi mdi-content-save me-1"></i>Simpan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endforeach

@endsection

{{-- ===================== JAVASCRIPT PAGE ===================== --}}
@push('js-page')
<script>
  // Auto-dismiss alert setelah 4 detik
  setTimeout(() => {
    const alert = document.querySelector('.alert');
    if (alert) {
      const bsAlert = new bootstrap.Alert(alert);
      bsAlert.close();
    }
  }, 4000);

  // Tooltip untuk judul yang terpotong
  document.querySelectorAll('.judul-col').forEach(el => {
    el.style.cursor = 'default';
  });
</script>
@endpush