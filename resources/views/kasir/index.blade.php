@extends('layouts.app')

@section('title', 'Kasir')

@push('css-page')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
  .readonly-input { background-color: #fff3cd !important; cursor: not-allowed; }
  .table td { vertical-align: middle; }
  #total-row td { font-size: 1.1rem; }
  .nav-tabs .nav-link.active { font-weight: bold; color: #6c3483; border-bottom-color: #6c3483; }
</style>
@endpush

@section('content')

<div class="page-header">
  <h3 class="page-title">
    <span class="page-title-icon bg-gradient-primary text-white me-2">
      <i class="mdi mdi-cash-register"></i>
    </span> Kasir
  </h3>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
      <li class="breadcrumb-item active">Kasir</li>
    </ol>
  </nav>
</div>

{{-- Tab pilihan versi --}}
<ul class="nav nav-tabs mb-3" id="kasirTab">
  <li class="nav-item">
    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-jquery">
      <i class="mdi mdi-jquery me-1"></i> jQuery AJAX
    </button>
  </li>
  <li class="nav-item">
    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-axios">
      <i class="mdi mdi-language-javascript me-1"></i> Axios
    </button>
  </li>
</ul>

<div class="tab-content">

  {{-- ============================================================ --}}
  {{-- TAB 1: jQuery AJAX --}}
  {{-- ============================================================ --}}
  <div class="tab-pane fade show active" id="tab-jquery">
    <div class="row">

      {{-- Form input barang --}}
      <div class="col-lg-4 grid-margin">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Input Buku</h4>
            <p class="card-description">Scan/input kode lalu tekan Enter</p>

            <div class="form-group">
              <label>Kode Buku</label>
              <input type="text" id="jq-kode" class="form-control"
                placeholder="Contoh: NV-01" autocomplete="off">
              <small class="text-muted">Tekan Enter untuk mencari</small>
            </div>
            <div class="form-group">
              <label>Judul Buku</label>
              <input type="text" id="jq-judul" class="form-control readonly-input" readonly>
            </div>
            <div class="form-group">
              <label>Harga</label>
              <input type="text" id="jq-harga" class="form-control readonly-input" readonly>
            </div>
            <div class="form-group">
              <label>Jumlah</label>
              <input type="number" id="jq-jumlah" class="form-control"
                value="1" min="1" disabled>
            </div>
            <div id="jq-notfound" class="alert alert-danger d-none py-2">
              <i class="mdi mdi-alert me-1"></i><span id="jq-notfound-msg"></span>
            </div>
            <button type="button" id="jq-btn-tambah"
              class="btn btn-gradient-success w-100 mt-2" disabled>
              <i class="mdi mdi-plus me-1"></i> Tambahkan
            </button>
          </div>
        </div>
      </div>

      {{-- Tabel cart --}}
      <div class="col-lg-8 grid-margin">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Daftar Belanja</h4>
            <div class="table-responsive">
              <table class="table table-hover" id="jq-table">
                <thead>
                  <tr>
                    <th>Kode</th>
                    <th>Judul</th>
                    <th style="width:100px">Harga</th>
                    <th style="width:90px">Jumlah</th>
                    <th style="width:110px">Subtotal</th>
                    <th style="width:50px"></th>
                  </tr>
                </thead>
                <tbody id="jq-tbody">
                  <tr id="jq-empty-row">
                    <td colspan="6" class="text-center text-muted py-4">
                      Belum ada buku ditambahkan.
                    </td>
                  </tr>
                </tbody>
                <tfoot>
                  <tr id="total-row">
                    <td colspan="4" class="text-end fw-bold">Total</td>
                    <td class="fw-bold text-primary" id="jq-total">Rp 0</td>
                    <td></td>
                  </tr>
                </tfoot>
              </table>
            </div>
            <div class="text-end mt-2">
              <button type="button" id="jq-btn-bayar"
                class="btn btn-gradient-primary px-4" disabled>
                <i class="mdi mdi-cash me-1"></i> Bayar
              </button>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>

  {{-- ============================================================ --}}
  {{-- TAB 2: Axios --}}
  {{-- ============================================================ --}}
  <div class="tab-pane fade" id="tab-axios">
    <div class="row">

      <div class="col-lg-4 grid-margin">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Input Buku <span class="badge bg-info ms-1" style="font-size:0.7rem">Axios</span></h4>
            <p class="card-description">Scan/input kode lalu tekan Enter</p>

            <div class="form-group">
              <label>Kode Buku</label>
              <input type="text" id="ax-kode" class="form-control"
                placeholder="Contoh: NV-01" autocomplete="off">
              <small class="text-muted">Tekan Enter untuk mencari</small>
            </div>
            <div class="form-group">
              <label>Judul Buku</label>
              <input type="text" id="ax-judul" class="form-control readonly-input" readonly>
            </div>
            <div class="form-group">
              <label>Harga</label>
              <input type="text" id="ax-harga" class="form-control readonly-input" readonly>
            </div>
            <div class="form-group">
              <label>Jumlah</label>
              <input type="number" id="ax-jumlah" class="form-control"
                value="1" min="1" disabled>
            </div>
            <div id="ax-notfound" class="alert alert-danger d-none py-2">
              <i class="mdi mdi-alert me-1"></i><span id="ax-notfound-msg"></span>
            </div>
            <button type="button" id="ax-btn-tambah"
              class="btn btn-gradient-success w-100 mt-2" disabled>
              <i class="mdi mdi-plus me-1"></i> Tambahkan
            </button>
          </div>
        </div>
      </div>

      <div class="col-lg-8 grid-margin">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Daftar Belanja <span class="badge bg-info ms-1" style="font-size:0.7rem">Axios</span></h4>
            <div class="table-responsive">
              <table class="table table-hover" id="ax-table">
                <thead>
                  <tr>
                    <th>Kode</th>
                    <th>Judul</th>
                    <th style="width:100px">Harga</th>
                    <th style="width:90px">Jumlah</th>
                    <th style="width:110px">Subtotal</th>
                    <th style="width:50px"></th>
                  </tr>
                </thead>
                <tbody id="ax-tbody">
                  <tr id="ax-empty-row">
                    <td colspan="6" class="text-center text-muted py-4">
                      Belum ada buku ditambahkan.
                    </td>
                  </tr>
                </tbody>
                <tfoot>
                  <tr id="total-row-ax">
                    <td colspan="4" class="text-end fw-bold">Total</td>
                    <td class="fw-bold text-primary" id="ax-total">Rp 0</td>
                    <td></td>
                  </tr>
                </tfoot>
              </table>
            </div>
            <div class="text-end mt-2">
              <button type="button" id="ax-btn-bayar"
                class="btn btn-gradient-primary px-4" disabled>
                <i class="mdi mdi-cash me-1"></i> Bayar
              </button>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>

</div>
@endsection

@push('js-page')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
// ================================================================
// HELPER BERSAMA
// ================================================================
const CSRF  = '{{ csrf_token() }}';
const CARI_URL  = '{{ route("kasir.cari") }}';
const BAYAR_URL = '{{ route("kasir.bayar") }}';

function formatRupiah(num) {
  return 'Rp ' + num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

// ================================================================
// VERSI 1: jQuery AJAX
// ================================================================
(function () {
  let jqCart  = []; // [{idbuku, kode, judul, harga, jumlah}]
  let jqFound = null;

  function jqResetForm() {
    $('#jq-kode').val('').focus();
    $('#jq-judul, #jq-harga').val('');
    $('#jq-jumlah').val(1).prop('disabled', true);
    $('#jq-btn-tambah').prop('disabled', true);
    $('#jq-notfound').addClass('d-none');
    jqFound = null;
  }

  function jqRenderTable() {
    const tbody = $('#jq-tbody');
    tbody.find('tr.cart-row').remove();

    if (jqCart.length === 0) {
      $('#jq-empty-row').show();
      $('#jq-total').text('Rp 0');
      $('#jq-btn-bayar').prop('disabled', true);
      return;
    }

    $('#jq-empty-row').hide();
    let total = 0;

    jqCart.forEach(function (item, idx) {
      const subtotal = item.harga * item.jumlah;
      total += subtotal;
      const row = `
        <tr class="cart-row" data-idx="${idx}">
          <td><span class="badge badge-gradient-primary">${item.kode}</span></td>
          <td>${item.judul}</td>
          <td>${formatRupiah(item.harga)}</td>
          <td>
            <input type="number" class="form-control form-control-sm jq-qty"
              value="${item.jumlah}" min="1" style="width:65px"
              data-idx="${idx}">
          </td>
          <td class="jq-subtotal fw-bold text-primary">${formatRupiah(subtotal)}</td>
          <td>
            <button class="btn btn-sm btn-gradient-danger jq-hapus" data-idx="${idx}">
              <i class="mdi mdi-delete"></i>
            </button>
          </td>
        </tr>`;
      tbody.append(row);
    });

    $('#jq-total').text(formatRupiah(total));
    $('#jq-btn-bayar').prop('disabled', false);
  }

  // Cari buku dengan jQuery AJAX
  $('#jq-kode').on('keydown', function (e) {
    if (e.key !== 'Enter') return;
    e.preventDefault();
    const kode = $(this).val().trim();
    if (!kode) return;

    $('#jq-notfound').addClass('d-none');
    $('#jq-judul, #jq-harga').val('');
    $('#jq-jumlah').prop('disabled', true);
    $('#jq-btn-tambah').prop('disabled', true);

    $.ajax({
      url: CARI_URL,
      method: 'GET',
      data: { kode: kode },
      success: function (res) {
        if (res.status === 'success') {
          jqFound = res.data;
          $('#jq-judul').val(res.data.judul);
          $('#jq-harga').val(res.data.harga_fmt);
          $('#jq-jumlah').val(1).prop('disabled', false);
          $('#jq-btn-tambah').prop('disabled', false);
          $('#jq-jumlah').focus();
        }
      },
      error: function (xhr) {
        const msg = xhr.responseJSON?.message ?? 'Buku tidak ditemukan.';
        $('#jq-notfound-msg').text(msg);
        $('#jq-notfound').removeClass('d-none');
        jqFound = null;
      }
    });
  });

  // Tambahkan ke cart
  $('#jq-btn-tambah').on('click', function () {
    if (!jqFound) return;
    const jumlah = parseInt($('#jq-jumlah').val()) || 1;
    if (jumlah < 1) return;

    // Jika kode sudah ada di cart, update jumlah
    const existing = jqCart.findIndex(i => i.idbuku === jqFound.idbuku);
    if (existing >= 0) {
      jqCart[existing].jumlah += jumlah;
    } else {
      jqCart.push({ ...jqFound, jumlah });
    }

    jqRenderTable();
    jqResetForm();
  });

  // Update jumlah dari tabel
  $(document).on('change', '.jq-qty', function () {
    const idx    = parseInt($(this).data('idx'));
    const jumlah = parseInt($(this).val()) || 1;
    jqCart[idx].jumlah = jumlah < 1 ? 1 : jumlah;
    const subtotal = jqCart[idx].harga * jqCart[idx].jumlah;
    $(this).closest('tr').find('.jq-subtotal').text(formatRupiah(subtotal));
    let total = jqCart.reduce((s, i) => s + i.harga * i.jumlah, 0);
    $('#jq-total').text(formatRupiah(total));
  });

  // Hapus baris
  $(document).on('click', '.jq-hapus', function () {
    const idx = parseInt($(this).data('idx'));
    jqCart.splice(idx, 1);
    jqRenderTable();
  });

  // Bayar dengan jQuery AJAX
  $('#jq-btn-bayar').on('click', function () {
    const btn = $(this);
    const total = jqCart.reduce((s, i) => s + i.harga * i.jumlah, 0);
    const items = jqCart.map(i => ({ idbuku: i.idbuku, jumlah: i.jumlah }));

    btn.prop('disabled', true)
       .html('<span class="spinner-border spinner-border-sm me-1"></span> Memproses...');

    $.ajax({
      url: BAYAR_URL,
      method: 'POST',
      data: JSON.stringify({ _token: CSRF, items, total }),
      contentType: 'application/json',
      success: function (res) {
        Swal.fire('Berhasil!', `Transaksi ${res.data.id_penjualan} senilai ${res.data.total_fmt} berhasil disimpan.`, 'success');
        jqCart = [];
        jqRenderTable();
        jqResetForm();
      },
      error: function (xhr) {
        const msg = xhr.responseJSON?.message ?? 'Terjadi kesalahan.';
        Swal.fire('Gagal!', msg, 'error');
      },
      complete: function () {
        btn.prop('disabled', false)
           .html('<i class="mdi mdi-cash me-1"></i> Bayar');
      }
    });
  });

})();

// ================================================================
// VERSI 2: Axios
// ================================================================
(function () {
  let axCart  = [];
  let axFound = null;

  // Set default header CSRF untuk semua request Axios
  axios.defaults.headers.common['X-CSRF-TOKEN'] = CSRF;

  function axResetForm() {
    document.getElementById('ax-kode').value  = '';
    document.getElementById('ax-judul').value = '';
    document.getElementById('ax-harga').value = '';
    document.getElementById('ax-jumlah').value = 1;
    document.getElementById('ax-jumlah').disabled  = true;
    document.getElementById('ax-btn-tambah').disabled = true;
    document.getElementById('ax-notfound').classList.add('d-none');
    document.getElementById('ax-kode').focus();
    axFound = null;
  }

  function axRenderTable() {
    const tbody   = document.getElementById('ax-tbody');
    const emptyRow = document.getElementById('ax-empty-row');
    // Hapus baris cart lama
    tbody.querySelectorAll('tr.cart-row').forEach(r => r.remove());

    if (axCart.length === 0) {
      emptyRow.style.display = '';
      document.getElementById('ax-total').textContent = 'Rp 0';
      document.getElementById('ax-btn-bayar').disabled = true;
      return;
    }

    emptyRow.style.display = 'none';
    let total = 0;

    axCart.forEach(function (item, idx) {
      const subtotal = item.harga * item.jumlah;
      total += subtotal;
      const tr = document.createElement('tr');
      tr.className = 'cart-row';
      tr.dataset.idx = idx;
      tr.innerHTML = `
        <td><span class="badge badge-gradient-primary">${item.kode}</span></td>
        <td>${item.judul}</td>
        <td>${formatRupiah(item.harga)}</td>
        <td>
          <input type="number" class="form-control form-control-sm ax-qty"
            value="${item.jumlah}" min="1" style="width:65px" data-idx="${idx}">
        </td>
        <td class="ax-subtotal fw-bold text-primary">${formatRupiah(subtotal)}</td>
        <td>
          <button class="btn btn-sm btn-gradient-danger ax-hapus" data-idx="${idx}">
            <i class="mdi mdi-delete"></i>
          </button>
        </td>`;
      tbody.appendChild(tr);
    });

    document.getElementById('ax-total').textContent = formatRupiah(total);
    document.getElementById('ax-btn-bayar').disabled = false;
  }

  // Cari buku dengan Axios
  document.getElementById('ax-kode').addEventListener('keydown', function (e) {
    if (e.key !== 'Enter') return;
    e.preventDefault();
    const kode = this.value.trim();
    if (!kode) return;

    document.getElementById('ax-notfound').classList.add('d-none');
    document.getElementById('ax-judul').value = '';
    document.getElementById('ax-harga').value = '';
    document.getElementById('ax-jumlah').disabled = true;
    document.getElementById('ax-btn-tambah').disabled = true;

    axios.get(CARI_URL, { params: { kode } })
      .then(function (response) {
        const data = response.data.data;
        axFound = data;
        document.getElementById('ax-judul').value = data.judul;
        document.getElementById('ax-harga').value = data.harga_fmt;
        document.getElementById('ax-jumlah').value = 1;
        document.getElementById('ax-jumlah').disabled = false;
        document.getElementById('ax-btn-tambah').disabled = false;
        document.getElementById('ax-jumlah').focus();
      })
      .catch(function (error) {
        const msg = error.response?.data?.message ?? 'Buku tidak ditemukan.';
        document.getElementById('ax-notfound-msg').textContent = msg;
        document.getElementById('ax-notfound').classList.remove('d-none');
        axFound = null;
      });
  });

  // Tambahkan ke cart
  document.getElementById('ax-btn-tambah').addEventListener('click', function () {
    if (!axFound) return;
    const jumlah = parseInt(document.getElementById('ax-jumlah').value) || 1;
    if (jumlah < 1) return;

    const existing = axCart.findIndex(i => i.idbuku === axFound.idbuku);
    if (existing >= 0) {
      axCart[existing].jumlah += jumlah;
    } else {
      axCart.push({ ...axFound, jumlah });
    }

    axRenderTable();
    axResetForm();
  });

  // Update jumlah & hapus (event delegation)
  document.getElementById('ax-tbody').addEventListener('change', function (e) {
    if (!e.target.classList.contains('ax-qty')) return;
    const idx    = parseInt(e.target.dataset.idx);
    const jumlah = parseInt(e.target.value) || 1;
    axCart[idx].jumlah = jumlah < 1 ? 1 : jumlah;
    const subtotal = axCart[idx].harga * axCart[idx].jumlah;
    e.target.closest('tr').querySelector('.ax-subtotal').textContent = formatRupiah(subtotal);
    const total = axCart.reduce((s, i) => s + i.harga * i.jumlah, 0);
    document.getElementById('ax-total').textContent = formatRupiah(total);
  });

  document.getElementById('ax-tbody').addEventListener('click', function (e) {
    const btn = e.target.closest('.ax-hapus');
    if (!btn) return;
    const idx = parseInt(btn.dataset.idx);
    axCart.splice(idx, 1);
    axRenderTable();
  });

  // Bayar dengan Axios
  document.getElementById('ax-btn-bayar').addEventListener('click', function () {
    const btn   = this;
    const total = axCart.reduce((s, i) => s + i.harga * i.jumlah, 0);
    const items = axCart.map(i => ({ idbuku: i.idbuku, jumlah: i.jumlah }));

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Memproses...';

    axios.post(BAYAR_URL, { items, total })
      .then(function (response) {
        const d = response.data.data;
        Swal.fire('Berhasil!', `Transaksi ${d.id_penjualan} senilai ${d.total_fmt} berhasil disimpan.`, 'success');
        axCart = [];
        axRenderTable();
        axResetForm();
      })
      .catch(function (error) {
        const msg = error.response?.data?.message ?? 'Terjadi kesalahan.';
        Swal.fire('Gagal!', msg, 'error');
      })
      .finally(function () {
        btn.disabled = false;
        btn.innerHTML = '<i class="mdi mdi-cash me-1"></i> Bayar';
      });
  });

})();
</script>
@endpush