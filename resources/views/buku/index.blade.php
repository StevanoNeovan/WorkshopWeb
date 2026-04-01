@extends('layouts.app')

@section('title', 'Buku')

@push('css-page')
{{-- DataTables CSS --}}
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<style>
  .table td, .table th { vertical-align: middle; }
  .judul-col { max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
  .btn-tag-harga { position: relative; }
  #selectedCount {
    display: none;
    font-size: 0.8rem;
    background: #7b5ea7;
    color: white;
    padding: 2px 8px;
    border-radius: 10px;
    margin-left: 6px;
  }
</style>
@endpush

@section('content')

<div class="page-header">
  <h3 class="page-title">
    <span class="page-title-icon bg-gradient-primary text-white me-2">
      <i class="mdi mdi-book-open-page-variant"></i>
    </span> Buku
  </h3>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
      <li class="breadcrumb-item active">Buku</li>
    </ol>
  </nav>
</div>

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
            <input type="text" class="form-control @error('kode') is-invalid @enderror"
              id="kode" name="kode" value="{{ old('kode') }}"
              placeholder="Contoh: NV-01" autocomplete="off" required>
            @error('kode') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="form-group">
            <label for="judul">Judul Buku <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('judul') is-invalid @enderror"
              id="judul" name="judul" value="{{ old('judul') }}"
              placeholder="Masukkan judul buku" required>
            @error('judul') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="form-group">
            <label for="pengarang">Pengarang <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('pengarang') is-invalid @enderror"
              id="pengarang" name="pengarang" value="{{ old('pengarang') }}"
              placeholder="Nama pengarang" required>
            @error('pengarang') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="form-group">
            <label for="idkategori">Kategori <span class="text-danger">*</span></label>
            <select class="form-control @error('idkategori') is-invalid @enderror"
              id="idkategori" name="idkategori" required>
              <option value="">-- Pilih Kategori --</option>
              @foreach($kategoris as $kat)
                <option value="{{ $kat->idkategori }}"
                  {{ old('idkategori') == $kat->idkategori ? 'selected' : '' }}>
                  {{ $kat->nama_kategori }}
                </option>
              @endforeach
            </select>
            @error('idkategori') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="form-group">
            <label for="harga">Harga <span class="text-danger">*</span></label>
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text">Rp</span>
              </div>
              <input type="number" class="form-control @error('harga') is-invalid @enderror"
                id="harga" name="harga" value="{{ old('harga') }}"
                placeholder="0" min="0" required>
            </div>
            @error('harga') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div>
            <h4 class="card-title mb-0">Daftar Buku</h4>
            <p class="card-description mb-0">Total <code>{{ $bukus->count() }}</code> buku dalam koleksi</p>
          </div>
          <div class="d-flex gap-2">
            {{-- Tombol Download Laporan PDF --}}
            <a href="{{ route('pdf.laporan-buku') }}" class="btn btn-sm btn-gradient-danger"
               title="Download Laporan Buku">
              <i class="mdi mdi-file-pdf-box me-1"></i> Laporan PDF
            </a>
            {{-- Tombol Cetak Tag Harga --}}
            <button type="button" class="btn btn-sm btn-gradient-success btn-tag-harga"
              data-bs-toggle="modal" data-bs-target="#modalTagHarga">
              <i class="mdi mdi-tag me-1"></i> Cetak Tag Harga
              <span id="selectedCount">0</span>
            </button>
          </div>
        </div>

        <div class="table-responsive">
          <table class="table table-hover" id="tableBuku">
            <thead>
              <tr>
                <th style="width:40px">
                  <input type="checkbox" id="checkAll" title="Pilih semua">
                </th>
                <th style="width:40px">No</th>
                <th style="width:75px">Kode</th>
                <th>Judul</th>
                <th>Pengarang</th>
                <th style="width:100px">Kategori</th>
                <th style="width:110px">Harga</th>
                <th class="text-center" style="width:110px">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($bukus as $index => $buku)
              <tr>
                <td>
                  <input type="checkbox" class="buku-check"
                    value="{{ $buku->idbuku }}"
                    data-judul="{{ $buku->judul }}">
                </td>
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
                <td class="fw-bold text-primary">
                  Rp {{ number_format($buku->harga, 0, ',', '.') }}
                </td>
                <td class="text-center">
                  <div class="d-flex justify-content-center" style="gap:4px">
                    <button class="btn btn-sm btn-gradient-warning" title="Edit"
                      data-bs-toggle="modal"
                      data-bs-target="#editBukuModal{{ $buku->idbuku }}">
                      <i class="mdi mdi-pencil"></i>
                    </button>
                    <form method="POST" action="{{ route('buku.destroy', $buku->idbuku) }}"
                      class="d-inline"
                      onsubmit="return confirm('Hapus buku &quot;{{ $buku->judul }}&quot;?')">
                      @csrf @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-gradient-danger" title="Hapus">
                        <i class="mdi mdi-delete"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="8" class="text-center py-5 text-muted">
                  <i class="mdi mdi-book-off mdi-36px d-block mb-2"></i>
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
        @csrf @method('PUT')
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
          <div class="form-group">
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
          <div class="form-group mb-0">
            <label>Harga <span class="text-danger">*</span></label>
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text">Rp</span>
              </div>
              <input type="number" class="form-control" name="harga"
                value="{{ $buku->harga }}" min="0" required>
            </div>
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

{{-- ===== MODAL CETAK TAG HARGA ===== --}}
<div class="modal fade" id="modalTagHarga" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
          <i class="mdi mdi-tag me-2 text-success"></i>Cetak Tag Harga
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form method="POST" action="{{ route('buku.tag-harga') }}" id="formTagHarga">
        @csrf
        <div class="modal-body">

          {{-- Info buku dipilih --}}
          <div class="alert alert-info py-2 mb-3" id="infoSelected">
            <i class="mdi mdi-information me-1"></i>
            <span id="infoText">Belum ada buku dipilih. Tutup modal ini dan centang buku yang ingin dicetak.</span>
          </div>

          {{-- Hidden inputs buku_ids --}}
          <div id="hiddenBukuIds"></div>

          {{-- Ilustrasi grid 5x8 --}}
          <p class="text-muted mb-2" style="font-size:0.85rem">
            <i class="mdi mdi-information-outline me-1"></i>
            Kertas TnJ No. 108 memiliki <strong>5 kolom × 8 baris = 40 label</strong>.
            Tentukan label mana yang pertama kali diisi (koordinat X = kolom, Y = baris):
          </p>

          <div class="row g-2 mb-3">
            <div class="col-6">
              <label class="form-label fw-bold">Koordinat X (Kolom) <span class="text-danger">*</span></label>
              <select name="start_x" class="form-control" required>
                @for($i = 1; $i <= 5; $i++)
                  <option value="{{ $i }}">Kolom {{ $i }}</option>
                @endfor
              </select>
              <small class="text-muted">1 = paling kiri, 5 = paling kanan</small>
            </div>
            <div class="col-6">
              <label class="form-label fw-bold">Koordinat Y (Baris) <span class="text-danger">*</span></label>
              <select name="start_y" class="form-control" required>
                @for($i = 1; $i <= 8; $i++)
                  <option value="{{ $i }}">Baris {{ $i }}</option>
                @endfor
              </select>
              <small class="text-muted">1 = paling atas, 8 = paling bawah</small>
            </div>
          </div>

          {{-- Preview grid kecil --}}
          <p class="text-muted mb-1" style="font-size:0.8rem">Preview posisi awal:</p>
          <div id="gridPreview" style="display:inline-block; border:1px solid #ddd; padding:4px; border-radius:4px;">
            @for($r = 1; $r <= 8; $r++)
              <div style="display:flex; gap:3px; margin-bottom:3px;">
                @for($c = 1; $c <= 5; $c++)
                  <div class="grid-cell"
                    data-row="{{ $r }}" data-col="{{ $c }}"
                    style="width:22px; height:18px; border:1px solid #bbb; border-radius:2px;
                           font-size:6px; display:flex; align-items:center; justify-content:center;
                           background:#f9f9f9; color:#999;">
                    {{ ($r-1)*5+$c }}
                  </div>
                @endfor
              </div>
            @endfor
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">
            <i class="mdi mdi-close me-1"></i>Batal
          </button>
          <button type="submit" class="btn btn-gradient-success" id="btnCetak" disabled>
            <i class="mdi mdi-printer me-1"></i>Generate PDF Tag Harga
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

@push('js-page')
{{-- DataTables JS --}}
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function () {

  // ===== DATATABLES =====
  $('#tableBuku').DataTable({
    language: {
      url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
    },
    columnDefs: [
      { orderable: false, targets: [0, 7] } // checkbox & aksi tidak bisa di-sort
    ],
    pageLength: 10,
  });

  // ===== CHECKBOX LOGIC =====
  function updateSelectedCount() {
    const checked = document.querySelectorAll('.buku-check:checked');
    const count   = checked.length;
    const badge   = document.getElementById('selectedCount');
    const infoText = document.getElementById('infoText');
    const btnCetak = document.getElementById('btnCetak');

    badge.style.display = count > 0 ? 'inline' : 'none';
    badge.textContent = count;

    if (count > 0) {
      const juduls = Array.from(checked).map(c => c.dataset.judul).join(', ');
      infoText.innerHTML = `<strong>${count} buku dipilih:</strong> ${juduls}`;
      btnCetak.disabled = false;
    } else {
      infoText.innerHTML = 'Belum ada buku dipilih. Tutup modal ini dan centang buku yang ingin dicetak.';
      btnCetak.disabled = true;
    }

    // Update hidden inputs
    const container = document.getElementById('hiddenBukuIds');
    container.innerHTML = '';
    checked.forEach(c => {
      const inp = document.createElement('input');
      inp.type  = 'hidden';
      inp.name  = 'buku_ids[]';
      inp.value = c.value;
      container.appendChild(inp);
    });

    // Update grid preview
    updateGridPreview();
  }

  document.getElementById('checkAll').addEventListener('change', function () {
    document.querySelectorAll('.buku-check').forEach(cb => {
      cb.checked = this.checked;
    });
    updateSelectedCount();
  });

  document.querySelectorAll('.buku-check').forEach(cb => {
    cb.addEventListener('change', updateSelectedCount);
  });

  // ===== GRID PREVIEW =====
  function updateGridPreview() {
    const startX = parseInt(document.querySelector('[name="start_x"]').value);
    const startY = parseInt(document.querySelector('[name="start_y"]').value);
    const count  = document.querySelectorAll('.buku-check:checked').length;
    const startIndex = (startY - 1) * 5 + (startX - 1);

    document.querySelectorAll('.grid-cell').forEach(cell => {
      const row = parseInt(cell.dataset.row);
      const col = parseInt(cell.dataset.col);
      const idx = (row - 1) * 5 + (col - 1);

      // Reset
      cell.style.background = '#f9f9f9';
      cell.style.color      = '#999';
      cell.style.fontWeight = 'normal';

      if (idx === startIndex) {
        cell.style.background = '#6c3483';
        cell.style.color      = 'white';
        cell.style.fontWeight = 'bold';
      } else if (idx > startIndex && idx < startIndex + count) {
        cell.style.background = '#d7bde2';
        cell.style.color      = '#4a235a';
      }
    });
  }

  document.querySelector('[name="start_x"]').addEventListener('change', updateGridPreview);
  document.querySelector('[name="start_y"]').addEventListener('change', updateGridPreview);

  // ===== AUTO DISMISS ALERT =====
  setTimeout(() => {
    const alert = document.querySelector('.alert-success');
    if (alert) bootstrap.Alert.getOrCreateInstance(alert).close();
  }, 4000);

});
</script>
@endpush