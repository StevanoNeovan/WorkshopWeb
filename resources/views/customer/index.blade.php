@extends('layouts.app')

@section('title', 'Data Customer')

@push('css-page')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
@endpush

@section('content')
<div class="page-header">
  <h3 class="page-title">
    <span class="page-title-icon bg-gradient-primary text-white me-2">
      <i class="mdi mdi-account-group"></i>
    </span> Data Customer
  </h3>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
      <li class="breadcrumb-item active">Customer</li>
    </ol>
  </nav>
</div>

@if(session('success'))
  <div class="alert alert-success alert-dismissible fade show">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
@endif

<div class="card">
  <div class="card-body">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <div>
        <h4 class="card-title mb-0">Daftar Customer</h4>
        <p class="card-description mb-0">Total {{ $customers->total() }} customer terdaftar</p>
      </div>
      <div class="d-flex gap-2">
        <a href="{{ route('customer.tambah1') }}" class="btn btn-sm btn-gradient-primary">
          <i class="mdi mdi-camera me-1"></i> Tambah (Foto BLOB)
        </a>
        <a href="{{ route('customer.tambah2') }}" class="btn btn-sm btn-gradient-info">
          <i class="mdi mdi-camera-plus me-1"></i> Tambah (Foto File)
        </a>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table table-hover" id="tableCustomer">
        <thead>
          <tr>
            <th>#</th>
            <th>Foto</th>
            <th>Nama</th>
            <th>Email</th>
            <th>No. HP</th>
            <th>Tipe Foto</th>
            <th>Tanggal</th>
            <th class="text-center">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @foreach($customers as $i => $c)
          <tr>
            <td>{{ $customers->firstItem() + $i }}</td>
            <td>
              @if($c->foto_blob)
                <img src="{{ $c->foto_blob_base64 }}" style="width:48px;height:48px;object-fit:cover;border-radius:8px;border:2px solid #e8dff0;">
              @elseif($c->foto_path)
                <img src="{{ asset('storage/'.$c->foto_path) }}" style="width:48px;height:48px;object-fit:cover;border-radius:8px;border:2px solid #e8dff0;">
              @else
                <div style="width:48px;height:48px;background:#e8dff0;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                  <i class="mdi mdi-account" style="font-size:1.5rem;color:#a569bd"></i>
                </div>
              @endif
            </td>
            <td><strong>{{ $c->nama }}</strong></td>
            <td>{{ $c->email ?? '-' }}</td>
            <td>{{ $c->no_hp ?? '-' }}</td>
            <td>
              @if($c->foto_blob)
                <span class="badge badge-gradient-warning">BLOB</span>
              @elseif($c->foto_path)
                <span class="badge badge-gradient-info">File</span>
              @else
                <span class="badge badge-gradient-secondary">-</span>
              @endif
            </td>
            <td style="font-size:.82rem;color:#7A6A58">{{ $c->created_at->format('d/m/Y H:i') }}</td>
            <td class="text-center">
              <form method="POST" action="{{ route('customer.destroy', $c->id) }}" onsubmit="return confirm('Hapus customer {{ $c->nama }}?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-sm btn-gradient-danger">
                  <i class="mdi mdi-delete"></i>
                </button>
              </form>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <div class="mt-3">{{ $customers->links() }}</div>
  </div>
</div>
@endsection

@push('js-page')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script>
$('#tableCustomer').DataTable({
  language: { 
    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json',
    emptyTable: '<div class="py-4 text-muted"><i class="mdi mdi-account-off mdi-36px d-block mb-2"></i>Belum ada data customer.</div>'
  },
  columnDefs: [{ orderable: false, targets: [1, 7] }],
  paging: false, // pakai pagination Laravel
  info: false,
});
</script>
@endpush