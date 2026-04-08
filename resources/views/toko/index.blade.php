@extends('layouts.app')

@section('title', 'Toko Buku')

@push('css-page')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
  .readonly-input { background-color: #fff3cd !important; }
  .buku-card { cursor: pointer; transition: box-shadow .2s; border: 2px solid transparent; }
  .buku-card:hover { box-shadow: 0 4px 15px rgba(108,52,131,.2); border-color: #a569bd; }
  .buku-card.selected { border-color: #6c3483; background: #f5eefa; }
  .status-badge { font-size: 0.8rem; padding: 4px 10px; border-radius: 20px; }
  .badge-pending  { background:#fff3cd; color:#7d6608; }
  .badge-lunas    { background:#d4edda; color:#155724; }
  .badge-expired  { background:#f8d7da; color:#721c24; }
</style>
@endpush

@section('content')

<div class="page-header">
  <h3 class="page-title">
    <span class="page-title-icon bg-gradient-primary text-white me-2">
      <i class="mdi mdi-store"></i>
    </span> Toko Buku Online
  </h3>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
      <li class="breadcrumb-item active">Toko</li>
    </ol>
  </nav>
</div>

<div class="row">

  {{-- ===== PANEL KIRI: Pilih Buku ===== --}}
  <div class="col-lg-5 grid-margin">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Pilih Buku</h4>

        {{-- Select berjenjang: Kategori → Buku --}}
        <div class="form-group">
          <label>Filter Kategori</label>
          <select id="sel-kategori" class="form-control">
            <option value="">-- Semua Kategori --</option>
            @foreach($kategoris as $kat)
              <option value="{{ $kat->idkategori }}">
                {{ $kat->nama_kategori }} ({{ $kat->buku_count }} buku)
              </option>
            @endforeach
          </select>
        </div>

        <div class="form-group">
          <label>Cari Kode Buku</label>
          <input type="text" id="input-kode" class="form-control"
            placeholder="Ketik kode lalu Enter, misal: NV-01">
          <small class="text-muted">atau klik kartu buku di bawah</small>
        </div>

        <div id="notfound-alert" class="alert alert-danger d-none py-2">
          <i class="mdi mdi-alert me-1"></i><span id="notfound-msg"></span>
        </div>

        {{-- Daftar kartu buku --}}
        <div id="buku-list" style="max-height:380px; overflow-y:auto;">
          @foreach($bukus as $buku)
          <div class="buku-card p-2 mb-2 rounded"
            data-idbuku="{{ $buku->idbuku }}"
            data-kode="{{ $buku->kode }}"
            data-judul="{{ $buku->judul }}"
            data-harga="{{ $buku->harga }}"
            data-harga-fmt="Rp {{ number_format($buku->harga, 0, ',', '.') }}"
            data-kategori="{{ $buku->idkategori }}">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <span class="badge badge-gradient-primary me-1">{{ $buku->kode }}</span>
                <strong>{{ $buku->judul }}</strong>
                <div class="text-muted" style="font-size:.8rem">{{ $buku->pengarang }}</div>
              </div>
              <div class="text-primary fw-bold" style="white-space:nowrap">
                Rp {{ number_format($buku->harga, 0, ',', '.') }}
              </div>
            </div>
          </div>
          @endforeach
        </div>

      </div>
    </div>
  </div>

  {{-- ===== PANEL KANAN: Cart + Checkout ===== --}}
  <div class="col-lg-7 grid-margin">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Keranjang Belanja</h4>

        {{-- Data guest --}}
        <div class="row mb-3">
          <div class="col-6">
            <label>Nama (opsional)</label>
            <input type="text" id="inp-nama" class="form-control" placeholder="Nama pembeli">
          </div>
          <div class="col-6">
            <label>No. HP (opsional)</label>
            <input type="text" id="inp-phone" class="form-control" placeholder="08xx">
          </div>
        </div>

        {{-- Jumlah input --}}
        <div class="row mb-3 align-items-end">
          <div class="col-8">
            <label>Buku dipilih: <span id="selected-judul" class="text-primary">-</span></label>
            <input type="text" id="inp-harga-sel" class="form-control readonly-input" readonly
              placeholder="Harga akan terisi otomatis">
          </div>
          <div class="col-2">
            <label>Qty</label>
            <input type="number" id="inp-qty" class="form-control" value="1" min="1" disabled>
          </div>
          <div class="col-2">
            <button id="btn-tambah" class="btn btn-gradient-success w-100" disabled>
              <i class="mdi mdi-plus"></i>
            </button>
          </div>
        </div>

        {{-- Tabel cart --}}
        <div class="table-responsive">
          <table class="table table-sm table-hover">
            <thead>
              <tr>
                <th>Kode</th><th>Judul</th>
                <th style="width:90px">Harga</th>
                <th style="width:70px">Qty</th>
                <th style="width:100px">Subtotal</th>
                <th style="width:40px"></th>
              </tr>
            </thead>
            <tbody id="cart-tbody">
              <tr id="empty-row">
                <td colspan="6" class="text-center text-muted py-3">Belum ada buku dipilih.</td>
              </tr>
            </tbody>
            <tfoot>
              <tr>
                <td colspan="4" class="text-end fw-bold">Total</td>
                <td class="fw-bold text-primary" id="cart-total">Rp 0</td>
                <td></td>
              </tr>
            </tfoot>
          </table>
        </div>

        <div class="text-end">
          <button id="btn-bayar" class="btn btn-gradient-primary px-4" disabled>
            <i class="mdi mdi-credit-card me-1"></i> Bayar Sekarang
          </button>
        </div>
      </div>
    </div>
  </div>

</div>
@endsection

@push('js-page')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
{{-- Midtrans Snap.js --}}
<script src="{{ config('midtrans.snap_url') }}"
  data-client-key="{{ config('midtrans.client_key') }}"></script>

<script>
const CSRF          = '{{ csrf_token() }}';
const URL_CARI      = '{{ route("toko.cari") }}';
const URL_BY_KAT    = '{{ route("toko.by-kategori") }}';
const URL_CHECKOUT  = '{{ route("toko.checkout") }}';
const URL_STATUS    = '{{ url("toko/status") }}';

axios.defaults.headers.common['X-CSRF-TOKEN'] = CSRF;

let cart        = [];
let selectedBuku = null;

// ================================================================
// FORMAT RUPIAH
// ================================================================
function fmt(n) { return 'Rp ' + n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.'); }

// ================================================================
// RENDER CART
// ================================================================
function renderCart() {
  const tbody = document.getElementById('cart-tbody');
  tbody.querySelectorAll('tr.cart-row').forEach(r => r.remove());

  if (cart.length === 0) {
    document.getElementById('empty-row').style.display = '';
    document.getElementById('cart-total').textContent = 'Rp 0';
    document.getElementById('btn-bayar').disabled = true;
    return;
  }

  document.getElementById('empty-row').style.display = 'none';
  let total = 0;

  cart.forEach((item, idx) => {
    const sub = item.harga * item.jumlah;
    total += sub;
    const tr = document.createElement('tr');
    tr.className = 'cart-row';
    tr.innerHTML = `
      <td><span class="badge badge-gradient-primary">${item.kode}</span></td>
      <td style="max-width:140px;overflow:hidden;white-space:nowrap;text-overflow:ellipsis"
          title="${item.judul}">${item.judul}</td>
      <td>${fmt(item.harga)}</td>
      <td><input type="number" class="form-control form-control-sm cart-qty"
          value="${item.jumlah}" min="1" style="width:55px" data-idx="${idx}"></td>
      <td class="cart-sub fw-bold text-primary">${fmt(sub)}</td>
      <td><button class="btn btn-sm btn-gradient-danger cart-del" data-idx="${idx}">
        <i class="mdi mdi-delete"></i></button></td>`;
    tbody.appendChild(tr);
  });

  document.getElementById('cart-total').textContent = fmt(total);
  document.getElementById('btn-bayar').disabled = false;
}

// ================================================================
// PILIH BUKU DARI KARTU
// ================================================================
document.querySelectorAll('.buku-card').forEach(card => {
  card.addEventListener('click', function () {
    document.querySelectorAll('.buku-card').forEach(c => c.classList.remove('selected'));
    this.classList.add('selected');
    selectedBuku = {
      idbuku : parseInt(this.dataset.idbuku),
      kode   : this.dataset.kode,
      judul  : this.dataset.judul,
      harga  : parseInt(this.dataset.harga),
    };
    document.getElementById('selected-judul').textContent = selectedBuku.judul;
    document.getElementById('inp-harga-sel').value = this.dataset.hargaFmt;
    document.getElementById('inp-qty').value = 1;
    document.getElementById('inp-qty').disabled = false;
    document.getElementById('btn-tambah').disabled = false;
    document.getElementById('notfound-alert').classList.add('d-none');
  });
});

// ================================================================
// CARI BUKU DARI INPUT KODE (Enter)
// ================================================================
document.getElementById('input-kode').addEventListener('keydown', function (e) {
  if (e.key !== 'Enter') return;
  e.preventDefault();
  const kode = this.value.trim();
  if (!kode) return;

  document.getElementById('notfound-alert').classList.add('d-none');

  axios.get(URL_CARI, { params: { kode } })
    .then(res => {
      const d = res.data.data;
      selectedBuku = { idbuku: d.idbuku, kode: d.kode, judul: d.judul, harga: d.harga };
      document.getElementById('selected-judul').textContent = d.judul;
      document.getElementById('inp-harga-sel').value = d.harga_fmt;
      document.getElementById('inp-qty').value = 1;
      document.getElementById('inp-qty').disabled = false;
      document.getElementById('btn-tambah').disabled = false;

      // Highlight kartu jika ada
      document.querySelectorAll('.buku-card').forEach(c => {
        c.classList.toggle('selected', c.dataset.idbuku == d.idbuku);
      });
    })
    .catch(err => {
      const msg = err.response?.data?.message ?? 'Buku tidak ditemukan.';
      document.getElementById('notfound-msg').textContent = msg;
      document.getElementById('notfound-alert').classList.remove('d-none');
      selectedBuku = null;
      document.getElementById('btn-tambah').disabled = true;
    });
});

// ================================================================
// FILTER KARTU BUKU BERDASARKAN KATEGORI (select berjenjang)
// ================================================================
document.getElementById('sel-kategori').addEventListener('change', function () {
  const katId = this.value;
  document.querySelectorAll('.buku-card').forEach(card => {
    if (!katId || card.dataset.kategori === katId) {
      card.style.display = '';
    } else {
      card.style.display = 'none';
    }
  });
});

// ================================================================
// TAMBAH KE CART
// ================================================================
document.getElementById('btn-tambah').addEventListener('click', function () {
  if (!selectedBuku) return;
  const jumlah = parseInt(document.getElementById('inp-qty').value) || 1;
  const existing = cart.findIndex(i => i.idbuku === selectedBuku.idbuku);

  if (existing >= 0) {
    cart[existing].jumlah += jumlah;
  } else {
    cart.push({ ...selectedBuku, jumlah });
  }
  renderCart();

  // Reset
  selectedBuku = null;
  document.getElementById('selected-judul').textContent = '-';
  document.getElementById('inp-harga-sel').value = '';
  document.getElementById('inp-qty').value = 1;
  document.getElementById('inp-qty').disabled = true;
  document.getElementById('btn-tambah').disabled = true;
  document.getElementById('input-kode').value = '';
  document.querySelectorAll('.buku-card').forEach(c => c.classList.remove('selected'));
});

// ================================================================
// UPDATE QTY & HAPUS DARI CART
// ================================================================
document.getElementById('cart-tbody').addEventListener('change', function (e) {
  if (!e.target.classList.contains('cart-qty')) return;
  const idx = parseInt(e.target.dataset.idx);
  const qty = parseInt(e.target.value) || 1;
  cart[idx].jumlah = qty < 1 ? 1 : qty;
  const sub = cart[idx].harga * cart[idx].jumlah;
  e.target.closest('tr').querySelector('.cart-sub').textContent = fmt(sub);
  const total = cart.reduce((s, i) => s + i.harga * i.jumlah, 0);
  document.getElementById('cart-total').textContent = fmt(total);
});

document.getElementById('cart-tbody').addEventListener('click', function (e) {
  const btn = e.target.closest('.cart-del');
  if (!btn) return;
  cart.splice(parseInt(btn.dataset.idx), 1);
  renderCart();
});

// ================================================================
// CHECKOUT + MIDTRANS SNAP
// ================================================================
document.getElementById('btn-bayar').addEventListener('click', function () {
  const btn   = this;
  const total = cart.reduce((s, i) => s + i.harga * i.jumlah, 0);
  const items = cart.map(i => ({ idbuku: i.idbuku, jumlah: i.jumlah }));

  btn.disabled = true;
  btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Memproses...';

  axios.post(URL_CHECKOUT, {
    nama  : document.getElementById('inp-nama').value,
    phone : document.getElementById('inp-phone').value,
    items,
    total,
  })
  .then(res => {
    const { snap_token, kode_pesanan, guest_kode, total_fmt } = res.data;

    // Buka Midtrans Snap popup
    snap.pay(snap_token, {
      onSuccess: function (result) {
        Swal.fire({
          icon : 'success',
          title: 'Pembayaran Berhasil!',
          html : `<b>Pesanan:</b> ${kode_pesanan}<br>
                  <b>Total:</b> ${total_fmt}<br>
                  <b>ID Anda:</b> ${guest_kode}<br>
                  <b>Metode:</b> ${result.payment_type}`,
        });
        cart = [];
        renderCart();
      },
      onPending: function () {
        Swal.fire('Menunggu Pembayaran',
          `Pesanan <b>${kode_pesanan}</b> menunggu pembayaran.<br>
           ID Anda: <b>${guest_kode}</b>`, 'info');
        cart = [];
        renderCart();
      },
      onError: function () {
        Swal.fire('Gagal', 'Pembayaran gagal. Silakan coba lagi.', 'error');
      },
      onClose: function () {
        // User tutup popup tanpa bayar — biarkan saja
      }
    });
  })
  .catch(err => {
    const msg = err.response?.data?.message ?? 'Gagal terhubung ke server.';
    Swal.fire('Error', msg, 'error');
  })
  .finally(() => {
    btn.disabled = false;
    btn.innerHTML = '<i class="mdi mdi-credit-card me-1"></i> Bayar Sekarang';
  });
});
</script>
@endpush