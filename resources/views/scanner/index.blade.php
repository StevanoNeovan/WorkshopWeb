@extends('layouts.app')

@section('title', 'Scanner Barcode')

@push('css-page')
<style>
  /* ===== SCANNER AREA ===== */
  .scanner-wrapper {
    position: relative;
    background: #1a1a2e;
    border-radius: 16px;
    overflow: hidden;
    margin-bottom: 16px;
  }

  #reader {
    width: 100%;
  }

  /* Override html5-qrcode default styling */
  #reader video {
    border-radius: 12px;
  }
  #reader__scan_region {
    background: transparent !important;
  }
  #reader__dashboard {
    display: none !important; /* Sembunyikan kontrol default, kita buat sendiri */
  }

  /* Garis scan animasi */
  .scan-line {
    position: absolute;
    left: 10%;
    right: 10%;
    height: 2px;
    background: linear-gradient(90deg, transparent, #7b5ea7, #a569bd, #7b5ea7, transparent);
    border-radius: 2px;
    animation: scanMove 2s ease-in-out infinite;
    box-shadow: 0 0 8px rgba(123, 94, 167, 0.8);
    display: none; /* ditampilkan saat scanner aktif */
  }
  @keyframes scanMove {
    0%   { top: 20%; }
    50%  { top: 75%; }
    100% { top: 20%; }
  }

  /* Status badge */
  .scanner-status {
    position: absolute;
    top: 12px;
    right: 12px;
    padding: 4px 12px;
    border-radius: 100px;
    font-size: .75rem;
    font-weight: 600;
  }
  .status-idle    { background: rgba(0,0,0,.5); color: rgba(255,255,255,.6); }
  .status-active  { background: rgba(123,94,167,.9); color: #fff; }
  .status-success { background: rgba(40,167,69,.9); color: #fff; }
  .status-error   { background: rgba(220,53,69,.9); color: #fff; }

  /* Tombol kontrol */
  .scanner-controls { display: flex; gap: 10px; }

  /* ===== RESULT CARD ===== */
  .result-card {
    background: linear-gradient(135deg, #f9f5ff, #fff);
    border: 2px solid #7b5ea7;
    border-radius: 16px;
    padding: 20px;
    display: none;
    animation: fadeInUp .3s ease;
  }
  @keyframes fadeInUp {
    from { opacity: 0; transform: translateY(12px); }
    to   { opacity: 1; transform: translateY(0); }
  }
  .result-id {
    font-size: .78rem;
    color: #7A6A58;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 4px;
  }
  .result-kode {
    display: inline-block;
    background: #7b5ea7;
    color: #fff;
    padding: 2px 12px;
    border-radius: 100px;
    font-size: .8rem;
    font-weight: 600;
    margin-bottom: 12px;
  }
  .result-judul {
    font-size: 1.1rem;
    font-weight: 700;
    color: #3D2B1F;
    margin-bottom: 4px;
  }
  .result-pengarang {
    font-size: .85rem;
    color: #7A6A58;
    margin-bottom: 4px;
  }
  .result-kategori {
    font-size: .8rem;
    color: #a569bd;
    margin-bottom: 12px;
  }
  .result-harga {
    font-size: 1.6rem;
    font-weight: 700;
    color: #6c3483;
  }
  .result-idbuku {
    font-size: .78rem;
    color: #b0a0b8;
    margin-top: 4px;
  }

  /* Not found state */
  .result-notfound {
    background: #fff5f5;
    border: 2px solid #f5c6cb;
    border-radius: 16px;
    padding: 20px;
    text-align: center;
    display: none;
  }

  /* Riwayat scan */
  .history-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 14px;
    background: #f9f5ff;
    border-radius: 10px;
    margin-bottom: 8px;
    border-left: 3px solid #7b5ea7;
  }
  .history-kode  { font-weight: 600; font-size: .85rem; color: #3D2B1F; }
  .history-judul { font-size: .78rem; color: #7A6A58; }
  .history-harga { font-weight: 700; color: #6c3483; font-size: .9rem; white-space: nowrap; }
  .history-time  { font-size: .7rem; color: #b0a0b8; margin-top: 2px; }
</style>
@endpush

@section('content')

<div class="page-header">
  <h3 class="page-title">
    <span class="page-title-icon bg-gradient-primary text-white me-2">
      <i class="mdi mdi-barcode-scan"></i>
    </span> Scanner Barcode
  </h3>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
      <li class="breadcrumb-item active">Scanner Barcode</li>
    </ol>
  </nav>
</div>

<div class="row">

  {{-- ===== PANEL KIRI: SCANNER ===== --}}
  <div class="col-lg-5 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Scan Barcode Buku</h4>
        <p class="card-description">
          Arahkan kamera ke barcode pada label kertas TnJ.
          <br>Library: <code>Html5-qrcode</code>
        </p>

        {{-- Viewport Scanner --}}
        <div class="scanner-wrapper" id="scannerWrapper">
          <div id="reader"></div>
          <div class="scan-line" id="scanLine"></div>
          <span class="scanner-status status-idle" id="scannerStatus">Idle</span>
        </div>

        {{-- Kontrol --}}
        <div class="scanner-controls">
          <button id="btnStart" class="btn btn-gradient-primary" onclick="startScanner()">
            <i class="mdi mdi-play me-1"></i> Mulai Scan
          </button>
          <button id="btnStop" class="btn btn-light" onclick="stopScanner()" disabled>
            <i class="mdi mdi-stop me-1"></i> Stop
          </button>
          <button id="btnReset" class="btn btn-outline-secondary" onclick="resetScanner()" style="display:none">
            <i class="mdi mdi-refresh me-1"></i> Scan Lagi
          </button>
        </div>

        {{-- Input manual (fallback) --}}
        <div class="mt-3">
          <p class="text-muted" style="font-size:.8rem">Atau input kode manual:</p>
          <div class="input-group">
            <input type="text" id="manualInput" class="form-control"
              placeholder="Ketik kode buku, tekan Enter"
              style="font-family:monospace">
            <button class="btn btn-gradient-secondary" onclick="manualSearch()">Cari</button>
          </div>
        </div>

      </div>
    </div>
  </div>

  {{-- ===== PANEL KANAN: HASIL + RIWAYAT ===== --}}
  <div class="col-lg-7 grid-margin stretch-card">
    <div class="card">
      <div class="card-body">

        {{-- Loading indicator --}}
        <div id="loadingIndicator" class="text-center py-4" style="display:none">
          <div class="spinner-border text-primary" role="status"></div>
          <p class="mt-2 text-muted" style="font-size:.85rem">Mencari data buku...</p>
        </div>

        {{-- Hasil Scan: Buku ditemukan --}}
        <div class="result-card" id="resultCard">
          <div class="result-id">Hasil Scan</div>
          <span class="result-kode" id="rKode">—</span>
          <div class="result-judul" id="rJudul">—</div>
          <div class="result-pengarang" id="rPengarang">—</div>
          <div class="result-kategori" id="rKategori">—</div>
          <hr style="border-color:#e8dff0;margin:12px 0">
          <div class="result-harga" id="rHarga">—</div>
          <div class="result-idbuku" id="rIdbuku">—</div>
        </div>

        {{-- Hasil Scan: Tidak ditemukan --}}
        <div class="result-notfound" id="resultNotFound">
          <i class="mdi mdi-barcode-off mdi-48px text-danger d-block mb-2"></i>
          <h5 class="text-danger mb-1">Buku Tidak Ditemukan</h5>
          <p class="text-muted mb-0" id="notFoundMsg" style="font-size:.85rem">—</p>
        </div>

        {{-- Placeholder saat belum scan --}}
        <div id="placeholder" class="text-center py-5 text-muted">
          <i class="mdi mdi-barcode-scan mdi-48px d-block mb-2" style="color:#d7bde2"></i>
          <p style="font-size:.9rem">Mulai scan untuk melihat hasil</p>
        </div>

        {{-- Riwayat scan --}}
        <div class="mt-4" id="historySection" style="display:none">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="mb-0" style="color:#3D2B1F;font-weight:600">
              <i class="mdi mdi-history me-1"></i> Riwayat Scan
            </h6>
            <button class="btn btn-sm btn-light" onclick="clearHistory()" style="font-size:.75rem">
              Hapus Riwayat
            </button>
          </div>
          <div id="historyList"></div>
        </div>

      </div>
    </div>
  </div>

</div>

@endsection

@push('js-page')
{{-- Html5-qrcode library --}}
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
const URL_CARI = '{{ route("scanner.cari") }}';
axios.defaults.headers.common['X-CSRF-TOKEN'] = '{{ csrf_token() }}';

let html5QrCode = null;
let isScanning  = false;
let scanHistory = [];

// ================================================================
// BEEP SOUND — generate via Web Audio API
// ================================================================
function playBeep() {
  try {
    const ctx      = new (window.AudioContext || window.webkitAudioContext)();
    const osc      = ctx.createOscillator();
    const gainNode = ctx.createGain();

    osc.connect(gainNode);
    gainNode.connect(ctx.destination);

    osc.type            = 'square';
    osc.frequency.value = 880;      // frekuensi beep (Hz)
    gainNode.gain.value = 0.3;

    osc.start();
    osc.stop(ctx.currentTime + 0.12); // durasi 120ms = beep pendek

    osc.onended = () => ctx.close();
  } catch (e) {
    console.warn('Beep tidak bisa diputar:', e);
  }
}

// ================================================================
// UI HELPERS
// ================================================================
function setStatus(text, cls) {
  const el = document.getElementById('scannerStatus');
  el.textContent = text;
  el.className   = 'scanner-status ' + cls;
}

function showLoading(show) {
  document.getElementById('loadingIndicator').style.display = show ? 'block' : 'none';
  document.getElementById('resultCard').style.display      = 'none';
  document.getElementById('resultNotFound').style.display  = 'none';
  document.getElementById('placeholder').style.display     = 'none';
}

function showResult(data) {
  document.getElementById('loadingIndicator').style.display = 'none';
  document.getElementById('resultNotFound').style.display   = 'none';
  document.getElementById('placeholder').style.display      = 'none';

  document.getElementById('rKode').textContent     = data.kode;
  document.getElementById('rJudul').textContent    = data.judul;
  document.getElementById('rPengarang').textContent= data.pengarang;
  document.getElementById('rKategori').textContent = '📚 ' + data.kategori;
  document.getElementById('rHarga').textContent    = data.harga_fmt;
  document.getElementById('rIdbuku').textContent   = 'ID Buku: #' + data.idbuku;

  document.getElementById('resultCard').style.display = 'block';
}

function showNotFound(msg) {
  document.getElementById('loadingIndicator').style.display = 'none';
  document.getElementById('resultCard').style.display       = 'none';
  document.getElementById('placeholder').style.display      = 'none';

  document.getElementById('notFoundMsg').textContent = msg;
  document.getElementById('resultNotFound').style.display = 'block';
}

// ================================================================
// RIWAYAT SCAN
// ================================================================
function addHistory(data) {
  const now  = new Date().toLocaleTimeString('id-ID');
  scanHistory.unshift({ ...data, time: now });
  if (scanHistory.length > 10) scanHistory.pop(); // max 10 riwayat
  renderHistory();
}

function renderHistory() {
  const section = document.getElementById('historySection');
  const list    = document.getElementById('historyList');

  if (!scanHistory.length) { section.style.display = 'none'; return; }

  section.style.display = 'block';
  list.innerHTML = scanHistory.map(h => `
    <div class="history-item">
      <div>
        <div class="history-kode">${h.kode} &nbsp;·&nbsp; <span style="color:#a569bd">#${h.idbuku}</span></div>
        <div class="history-judul">${h.judul.length > 35 ? h.judul.slice(0,35)+'...' : h.judul}</div>
        <div class="history-time">${h.time}</div>
      </div>
      <div class="history-harga">${h.harga_fmt}</div>
    </div>`).join('');
}

function clearHistory() {
  scanHistory = [];
  renderHistory();
}

// ================================================================
// CARI BUKU
// ================================================================
async function cariBuku(kode) {
  showLoading(true);

  try {
    const res  = await axios.get(URL_CARI, { params: { kode } });
    const data = res.data.data;

    playBeep();           // a. bunyi beep
    showResult(data);
    addHistory(data);
    setStatus('Berhasil', 'status-success');

  } catch (err) {
    const msg = err.response?.data?.message ?? 'Buku tidak ditemukan.';
    showNotFound(msg);
    setStatus('Tidak Ditemukan', 'status-error');
  }
}

// ================================================================
// SCANNER CONTROLS
// ================================================================
function startScanner() {
  if (!html5QrCode) {
    html5QrCode = new Html5Qrcode('reader');
  }

  const config = {
    fps        : 10,
    qrbox      : { width: 280, height: 120 }, // lebih lebar untuk barcode
    aspectRatio: 1.5,
    formatsToSupport: [
      Html5QrcodeSupportedFormats.CODE_128,
      Html5QrcodeSupportedFormats.CODE_39,
      Html5QrcodeSupportedFormats.EAN_13,
      Html5QrcodeSupportedFormats.QR_CODE,
    ],
  };

  html5QrCode.start(
    { facingMode: 'environment' }, // kamera belakang
    config,
    async (decodedText) => {
      // b. Scanner berhenti setelah berhasil baca
      await stopScanner();

      // c. Tampilkan hasil
      await cariBuku(decodedText.trim());

      // Tampilkan tombol "Scan Lagi"
      document.getElementById('btnReset').style.display = '';
    },
    (errorMsg) => {
      // Error per-frame (normal, abaikan)
    }
  ).then(() => {
    isScanning = true;
    setStatus('Scanning...', 'status-active');
    document.getElementById('scanLine').style.display = 'block';
    document.getElementById('btnStart').disabled = true;
    document.getElementById('btnStop').disabled  = false;
    document.getElementById('btnReset').style.display = 'none';
    document.getElementById('placeholder').style.display = 'block';
    document.getElementById('resultCard').style.display  = 'none';
    document.getElementById('resultNotFound').style.display = 'none';
  }).catch(err => {
    alert('Tidak dapat mengakses kamera: ' + err);
  });
}

async function stopScanner() {
  if (html5QrCode && isScanning) {
    try {
      await html5QrCode.stop();
    } catch(e) {}
    isScanning = false;
  }
  document.getElementById('scanLine').style.display   = 'none';
  document.getElementById('btnStart').disabled  = false;
  document.getElementById('btnStop').disabled   = true;
  setStatus('Idle', 'status-idle');
}

function resetScanner() {
  document.getElementById('btnReset').style.display = 'none';
  document.getElementById('resultCard').style.display = 'none';
  document.getElementById('resultNotFound').style.display = 'none';
  document.getElementById('placeholder').style.display = 'block';
  startScanner();
}

// ================================================================
// INPUT MANUAL
// ================================================================
function manualSearch() {
  const kode = document.getElementById('manualInput').value.trim();
  if (!kode) return;
  cariBuku(kode);
  document.getElementById('manualInput').value = '';
}

document.getElementById('manualInput').addEventListener('keydown', function(e) {
  if (e.key === 'Enter') manualSearch();
});
</script>
@endpush