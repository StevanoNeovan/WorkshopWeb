@extends('layouts.app')

@section('title', 'Tambah Customer 2')

@section('content')
<div class="page-header">
  <h3 class="page-title">
    <span class="page-title-icon bg-gradient-primary text-white me-2">
      <i class="mdi mdi-camera"></i>
    </span> Tambah Customer 2
    <span class="badge badge-gradient-info ms-2" style="font-size:.7rem">Foto disimpan sebagai File</span>
  </h3>
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
      <li class="breadcrumb-item"><a href="{{ route('customer.index') }}">Customer</a></li>
      <li class="breadcrumb-item active">Tambah Customer 2</li>
    </ol>
  </nav>
</div>

<div class="row">
  <div class="col-lg-5 grid-margin">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Kamera</h4>
        <p class="card-description">Arahkan kamera ke wajah customer lalu klik "Ambil Foto"</p>

        {{-- Preview kamera --}}
        <div style="position:relative;background:#1a1a2e;border-radius:12px;overflow:hidden;margin-bottom:12px;">
          <video id="videoEl" autoplay playsinline
            style="width:100%;max-height:300px;display:block;object-fit:cover;"></video>
          <div id="cameraPlaceholder"
            style="width:100%;height:220px;display:flex;flex-direction:column;align-items:center;justify-content:center;color:rgba(255,255,255,.5)">
            <i class="mdi mdi-camera-off" style="font-size:3rem;display:block;margin-bottom:8px"></i>
            <span style="font-size:.85rem">Kamera belum aktif</span>
          </div>
        </div>

        {{-- Canvas untuk capture (hidden) --}}
        <canvas id="canvasEl" style="display:none"></canvas>

        {{-- Preview hasil foto --}}
        <div id="previewWrap" style="display:none;margin-bottom:12px;text-align:center">
          <img id="previewImg"
            style="max-width:100%;border-radius:10px;border:3px solid #a569bd;">
          <div style="font-size:.78rem;color:#7A6A58;margin-top:6px">Preview foto</div>
        </div>

        <div class="d-flex gap-2">
          <button type="button" id="btnKamera" class="btn btn-sm btn-gradient-secondary"
            onclick="startCamera()">
            <i class="mdi mdi-camera me-1"></i> Aktifkan Kamera
          </button>
          <button type="button" id="btnCapture" class="btn btn-sm btn-gradient-primary" disabled
            onclick="capturePhoto()">
            <i class="mdi mdi-camera-iris me-1"></i> Ambil Foto
          </button>
          <button type="button" id="btnRetake" class="btn btn-sm btn-light" style="display:none"
            onclick="retakePhoto()">
            <i class="mdi mdi-refresh me-1"></i> Ulangi
          </button>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-7 grid-margin">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">Data Customer</h4>

        @if($errors->any())
          <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('customer.tambah2.store') }}" id="formCustomer">
          @csrf
          {{-- Hidden input untuk foto base64 --}}
          <input type="hidden" name="foto_file" id="fotoInput">

          <div class="form-group">
            <label>Nama <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('nama') is-invalid @enderror"
              name="nama" value="{{ old('nama') }}" required placeholder="Nama lengkap customer">
            @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="form-group">
            <label>Email</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror"
              name="email" value="{{ old('email') }}" placeholder="opsional">
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="form-group">
            <label>No. HP</label>
            <input type="text" class="form-control"
              name="no_hp" value="{{ old('no_hp') }}" placeholder="opsional">
          </div>

          <div id="fotoStatus" class="alert alert-warning py-2 mb-3" style="font-size:.84rem">
            <i class="mdi mdi-alert-circle me-1"></i>
            Foto belum diambil. Aktifkan kamera dan ambil foto terlebih dahulu.
          </div>

          <div class="d-flex gap-2 mt-3">
            <button type="submit" id="btnSimpan" class="btn btn-gradient-primary" disabled>
              <i class="mdi mdi-content-save me-1"></i> Simpan Customer
            </button>
            <a href="{{ route('customer.index') }}" class="btn btn-light">Batal</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@push('js-page')
<script>
let stream = null;

async function startCamera() {
  try {
    stream = await navigator.mediaDevices.getUserMedia({
      video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } },
      audio: false
    });
    const video = document.getElementById('videoEl');
    video.srcObject = stream;
    video.style.display = 'block';
    document.getElementById('cameraPlaceholder').style.display = 'none';
    document.getElementById('btnKamera').disabled  = true;
    document.getElementById('btnCapture').disabled = false;
  } catch (err) {
    alert('Tidak dapat mengakses kamera: ' + err.message);
  }
}

function capturePhoto() {
  const video  = document.getElementById('videoEl');
  const canvas = document.getElementById('canvasEl');

  canvas.width  = video.videoWidth  || 640;
  canvas.height = video.videoHeight || 480;

  const ctx = canvas.getContext('2d');
  ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

  const base64 = canvas.toDataURL('image/jpeg', 0.85);

  // Tampilkan preview
  document.getElementById('previewImg').src = base64;
  document.getElementById('previewWrap').style.display = 'block';
  document.getElementById('videoEl').style.display = 'none';

  // Set ke hidden input
  document.getElementById('fotoInput').value = base64;

  // Update UI
  document.getElementById('fotoStatus').className = 'alert alert-success py-2 mb-3';
  document.getElementById('fotoStatus').innerHTML =
    '<i class="mdi mdi-check-circle me-1"></i> Foto berhasil diambil.';
  document.getElementById('btnCapture').style.display = 'none';
  document.getElementById('btnRetake').style.display  = '';
  document.getElementById('btnSimpan').disabled = false;

  // Stop stream
  if (stream) stream.getTracks().forEach(t => t.stop());
}

function retakePhoto() {
  document.getElementById('previewWrap').style.display = 'none';
  document.getElementById('videoEl').style.display = 'none';
  document.getElementById('cameraPlaceholder').style.display = 'flex';
  document.getElementById('btnKamera').disabled   = false;
  document.getElementById('btnCapture').disabled  = true;
  document.getElementById('btnCapture').style.display = '';
  document.getElementById('btnRetake').style.display  = 'none';
  document.getElementById('fotoInput').value = '';
  document.getElementById('fotoStatus').className = 'alert alert-warning py-2 mb-3';
  document.getElementById('fotoStatus').innerHTML =
    '<i class="mdi mdi-alert-circle me-1"></i> Foto belum diambil.';
  document.getElementById('btnSimpan').disabled = true;
}
</script>
@endpush