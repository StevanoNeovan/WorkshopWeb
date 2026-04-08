@extends('layouts.app')

@section('title', 'Pesanan Masuk')

@push('css-page')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<style>
  .badge-pending  { background:#fff3cd; color:#7d6608; }
  .badge-lunas    { background:#d4edda; color:#155724; }
  .badge-expired  { background:#f8d7da; color:#721c24; }
  .badge-failed   { background:#f8d7da; color:#721c24; }
</style>
@endpush

@section('content')

<div class="page-header">
  <h3 class="page-title">
    <span class="page-title-icon bg-gradient-primary text-white me-2">
      <i class="mdi mdi-clipboard-list"></i>
    </span> Pesanan Masuk
  </h3>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
      <li class="breadcrumb-item active">Pesanan</li>
    </ol>
  </nav>
</div>

<div class="card">
  <div class="card-body">
    <h4 class="card-title">Daftar Semua Pesanan</h4>
    <div class="table-responsive">
      <table class="table table-hover" id="tbl-pesanan">
        <thead>
          <tr>
            <th>#</th>
            <th>Kode Pesanan</th>
            <th>Pembeli</th>
            <th>Total</th>
            <th>Status</th>
            <th>Metode</th>
            <th>Waktu Bayar</th>
            <th>Tanggal</th>
            <th class="text-center">Detail</th>
          </tr>
        </thead>
        <tbody>
          @foreach($pesanans as $i => $p)
          <tr>
            <td>{{ $i + 1 }}</td>
            <td><code>{{ $p->kode_pesanan }}</code></td>
            <td>
              <div>{{ $p->guest->nama ?? '-' }}</div>
              <small class="text-muted">{{ $p->guest->kode_guest }}</small>
            </td>
            <td class="fw-bold text-primary">
              Rp {{ number_format($p->total, 0, ',', '.') }}
            </td>
            <td>
              <span class="badge badge-{{ $p->status_bayar }} px-2 py-1 rounded">
                {{ ucfirst($p->status_bayar) }}
              </span>
            </td>
            <td>{{ $p->payment_type ?? '-' }}</td>
            <td>{{ $p->paid_at ? $p->paid_at->format('d/m/Y H:i') : '-' }}</td>
            <td>{{ $p->created_at->format('d/m/Y H:i') }}</td>
            <td class="text-center">
              <button class="btn btn-sm btn-gradient-info btn-detail"
                data-id="{{ $p->id }}"
                data-kode="{{ $p->kode_pesanan }}"
                data-bs-toggle="modal" data-bs-target="#modalDetail">
                <i class="mdi mdi-eye"></i>
              </button>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <div class="mt-3">{{ $pesanans->links() }}</div>
  </div>
</div>

{{-- Modal Detail --}}
<div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">
          <i class="mdi mdi-clipboard-text me-2"></i>
          Detail Pesanan: <span id="det-kode" class="text-primary"></span>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="det-body">
        <p class="text-center text-muted">Memuat...</p>
      </div>
    </div>
  </div>
</div>

@endsection

@push('js-page')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
$('#tbl-pesanan').DataTable({
  language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json' },
  columnDefs: [{ orderable: false, targets: [8] }],
  order: [[7, 'desc']],
});

// Simpan data pesanan dari blade untuk ditampilkan di modal
const pesananData = @json($pesanans->map(function($p) {
    return [
        'id'     => $p->id,
        'detail' => $p->detail->map(fn($d) => [
            'kode'      => $d->buku->kode ?? '-',
            'judul'     => $d->buku->judul ?? '-',
            'harga'     => $d->harga,
            'jumlah'    => $d->jumlah,
            'subtotal'  => $d->subtotal,
        ]),
        'va'     => $p->va_number,
        'phone'  => $p->guest->phone ?? '-',
    ];
})->keyBy('id'));

document.querySelectorAll('.btn-detail').forEach(btn => {
  btn.addEventListener('click', function () {
    const id   = this.dataset.id;
    const kode = this.dataset.kode;
    document.getElementById('det-kode').textContent = kode;

    const data = pesananData[id];
    if (!data) return;

    let rows = '';
    let total = 0;
    data.detail.forEach(d => {
      total += d.subtotal;
      rows += `<tr>
        <td><span class="badge badge-gradient-primary">${d.kode}</span></td>
        <td>${d.judul}</td>
        <td>Rp ${d.harga.toString().replace(/\B(?=(\d{3})+(?!\d))/g,'.')}</td>
        <td>${d.jumlah}</td>
        <td class="fw-bold">Rp ${d.subtotal.toString().replace(/\B(?=(\d{3})+(?!\d))/g,'.')}</td>
      </tr>`;
    });

    document.getElementById('det-body').innerHTML = `
      <p class="mb-1"><strong>No. HP:</strong> ${data.phone}</p>
      ${data.va ? `<p class="mb-2"><strong>VA:</strong> ${data.va}</p>` : ''}
      <table class="table table-sm">
        <thead><tr><th>Kode</th><th>Judul</th><th>Harga</th><th>Qty</th><th>Subtotal</th></tr></thead>
        <tbody>${rows}</tbody>
        <tfoot><tr><td colspan="4" class="text-end fw-bold">Total</td>
          <td class="fw-bold text-primary">Rp ${total.toString().replace(/\B(?=(\d{3})+(?!\d))/g,'.')}</td>
        </tr></tfoot>
      </table>`;
  });
});
</script>
@endpush