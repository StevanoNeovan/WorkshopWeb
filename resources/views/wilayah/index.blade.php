@extends('layouts.app')

@section('title', 'Wilayah')

@section('content')

<div class="page-header">
  <h3 class="page-title">
    <span class="page-title-icon bg-gradient-primary text-white me-2">
      <i class="mdi mdi-map-marker"></i>
    </span> Wilayah Indonesia
  </h3>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
      <li class="breadcrumb-item active">Wilayah</li>
    </ol>
  </nav>
</div>

<ul class="nav nav-tabs mb-3" id="wilayahTab">
  <li class="nav-item">
    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-jq">
      jQuery AJAX
    </button>
  </li>
  <li class="nav-item">
    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-ax">
      Axios
    </button>
  </li>
</ul>

<div class="tab-content">

  {{-- ============================================================ --}}
  {{-- TAB 1: jQuery AJAX --}}
  {{-- ============================================================ --}}
  <div class="tab-pane fade show active" id="tab-jq">
    <div class="row justify-content-center">
      <div class="col-lg-6">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Pilih Wilayah <span class="badge bg-secondary ms-1" style="font-size:0.7rem">jQuery AJAX</span></h4>

            <div class="form-group">
              <label>Provinsi</label>
              <select id="jq-provinsi" class="form-control">
                <option value="">-- Pilih Provinsi --</option>
              </select>
            </div>

            <div class="form-group">
              <label>Kota / Kabupaten</label>
              <select id="jq-kota" class="form-control" disabled>
                <option value="">-- Pilih Kota --</option>
              </select>
            </div>

            <div class="form-group">
              <label>Kecamatan</label>
              <select id="jq-kecamatan" class="form-control" disabled>
                <option value="">-- Pilih Kecamatan --</option>
              </select>
            </div>

            <div class="form-group">
              <label>Kelurahan / Desa</label>
              <select id="jq-kelurahan" class="form-control" disabled>
                <option value="">-- Pilih Kelurahan --</option>
              </select>
            </div>

            <div id="jq-result" class="alert alert-success d-none mt-3">
              <i class="mdi mdi-check-circle me-1"></i>
              <span id="jq-result-text"></span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- ============================================================ --}}
  {{-- TAB 2: Axios --}}
  {{-- ============================================================ --}}
  <div class="tab-pane fade" id="tab-ax">
    <div class="row justify-content-center">
      <div class="col-lg-6">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Pilih Wilayah <span class="badge bg-info ms-1" style="font-size:0.7rem">Axios</span></h4>

            <div class="form-group">
              <label>Provinsi</label>
              <select id="ax-provinsi" class="form-control">
                <option value="">-- Pilih Provinsi --</option>
              </select>
            </div>

            <div class="form-group">
              <label>Kota / Kabupaten</label>
              <select id="ax-kota" class="form-control" disabled>
                <option value="">-- Pilih Kota --</option>
              </select>
            </div>

            <div class="form-group">
              <label>Kecamatan</label>
              <select id="ax-kecamatan" class="form-control" disabled>
                <option value="">-- Pilih Kecamatan --</option>
              </select>
            </div>

            <div class="form-group">
              <label>Kelurahan / Desa</label>
              <select id="ax-kelurahan" class="form-control" disabled>
                <option value="">-- Pilih Kelurahan --</option>
              </select>
            </div>

            <div id="ax-result" class="alert alert-success d-none mt-3">
              <i class="mdi mdi-check-circle me-1"></i>
              <span id="ax-result-text"></span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>
@endsection

@push('js-page')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
const URL_PROVINSI  = '{{ route("wilayah.provinsi") }}';
const URL_KOTA      = '{{ route("wilayah.kota") }}';
const URL_KECAMATAN = '{{ route("wilayah.kecamatan") }}';
const URL_KELURAHAN = '{{ route("wilayah.kelurahan") }}';

// Helper isi option ke select
function fillSelect(selectEl, data, placeholder) {
  selectEl.innerHTML = `<option value="">${placeholder}</option>`;
  data.forEach(function (item) {
    const opt = document.createElement('option');
    opt.value       = item.id;
    opt.textContent = item.name;
    selectEl.appendChild(opt);
  });
  selectEl.disabled = data.length === 0;
}

function resetSelect(selectEl, placeholder) {
  selectEl.innerHTML = `<option value="">${placeholder}</option>`;
  selectEl.disabled  = true;
}

// ================================================================
// VERSI 1: jQuery AJAX
// ================================================================
$(document).ready(function () {

  // Load provinsi saat halaman load
  $.get(URL_PROVINSI, function (res) {
    res.data.forEach(function (p) {
      $('#jq-provinsi').append(`<option value="${p.id}">${p.name}</option>`);
    });
  });

  // Provinsi berubah → load kota, kosongkan kecamatan & kelurahan
  $('#jq-provinsi').on('change', function () {
    const id = $(this).val();
    resetSelect($('#jq-kota')[0], '-- Pilih Kota --');
    resetSelect($('#jq-kecamatan')[0], '-- Pilih Kecamatan --');
    resetSelect($('#jq-kelurahan')[0], '-- Pilih Kelurahan --');
    $('#jq-result').addClass('d-none');

    if (!id) return;

    $.get(URL_KOTA, { province_id: id }, function (res) {
      fillSelect($('#jq-kota')[0], res.data, '-- Pilih Kota --');
    });
  });

  // Kota berubah → load kecamatan, kosongkan kelurahan
  $('#jq-kota').on('change', function () {
    const id = $(this).val();
    resetSelect($('#jq-kecamatan')[0], '-- Pilih Kecamatan --');
    resetSelect($('#jq-kelurahan')[0], '-- Pilih Kelurahan --');

    if (!id) return;

    $.get(URL_KECAMATAN, { regency_id: id }, function (res) {
      fillSelect($('#jq-kecamatan')[0], res.data, '-- Pilih Kecamatan --');
    });
  });

  // Kecamatan berubah → load kelurahan
  $('#jq-kecamatan').on('change', function () {
    const id = $(this).val();
    resetSelect($('#jq-kelurahan')[0], '-- Pilih Kelurahan --');

    if (!id) return;

    $.get(URL_KELURAHAN, { district_id: id }, function (res) {
      fillSelect($('#jq-kelurahan')[0], res.data, '-- Pilih Kelurahan --');
    });
  });

  // Kelurahan dipilih → tampilkan hasil
  $('#jq-kelurahan').on('change', function () {
    const kel = $(this).find('option:selected').text();
    const kec = $('#jq-kecamatan option:selected').text();
    const kot = $('#jq-kota option:selected').text();
    const prov= $('#jq-provinsi option:selected').text();

    if (!$(this).val()) { $('#jq-result').addClass('d-none'); return; }

    $('#jq-result-text').text(`${kel}, ${kec}, ${kot}, ${prov}`);
    $('#jq-result').removeClass('d-none');
  });

});

// ================================================================
// VERSI 2: Axios
// ================================================================
(function () {

  // Load provinsi saat halaman load
  axios.get(URL_PROVINSI).then(function (res) {
    res.data.data.forEach(function (p) {
      const opt = document.createElement('option');
      opt.value = p.id; opt.textContent = p.name;
      document.getElementById('ax-provinsi').appendChild(opt);
    });
  });

  document.getElementById('ax-provinsi').addEventListener('change', function () {
    const id = this.value;
    resetSelect(document.getElementById('ax-kota'),       '-- Pilih Kota --');
    resetSelect(document.getElementById('ax-kecamatan'),  '-- Pilih Kecamatan --');
    resetSelect(document.getElementById('ax-kelurahan'),  '-- Pilih Kelurahan --');
    document.getElementById('ax-result').classList.add('d-none');

    if (!id) return;

    axios.get(URL_KOTA, { params: { province_id: id } })
      .then(function (res) {
        fillSelect(document.getElementById('ax-kota'), res.data.data, '-- Pilih Kota --');
      });
  });

  document.getElementById('ax-kota').addEventListener('change', function () {
    const id = this.value;
    resetSelect(document.getElementById('ax-kecamatan'), '-- Pilih Kecamatan --');
    resetSelect(document.getElementById('ax-kelurahan'), '-- Pilih Kelurahan --');

    if (!id) return;

    axios.get(URL_KECAMATAN, { params: { regency_id: id } })
      .then(function (res) {
        fillSelect(document.getElementById('ax-kecamatan'), res.data.data, '-- Pilih Kecamatan --');
      });
  });

  document.getElementById('ax-kecamatan').addEventListener('change', function () {
    const id = this.value;
    resetSelect(document.getElementById('ax-kelurahan'), '-- Pilih Kelurahan --');

    if (!id) return;

    axios.get(URL_KELURAHAN, { params: { district_id: id } })
      .then(function (res) {
        fillSelect(document.getElementById('ax-kelurahan'), res.data.data, '-- Pilih Kelurahan --');
      });
  });

  document.getElementById('ax-kelurahan').addEventListener('change', function () {
    if (!this.value) { document.getElementById('ax-result').classList.add('d-none'); return; }

    const kel  = this.options[this.selectedIndex].text;
    const kec  = document.getElementById('ax-kecamatan');
    const kot  = document.getElementById('ax-kota');
    const prov = document.getElementById('ax-provinsi');

    document.getElementById('ax-result-text').textContent =
      `${kel}, ${kec.options[kec.selectedIndex].text}, ${kot.options[kot.selectedIndex].text}, ${prov.options[prov.selectedIndex].text}`;
    document.getElementById('ax-result').classList.remove('d-none');
  });

})();
</script>
@endpush