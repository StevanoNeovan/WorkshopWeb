<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Verifikasi OTP - Koleksi Buku</title>
  <link rel="stylesheet" href="{{ asset('template/assets/vendors/mdi/css/materialdesignicons.min.css') }}">
  <link rel="stylesheet" href="{{ asset('template/assets/vendors/ti-icons/css/themify-icons.css') }}">
  <link rel="stylesheet" href="{{ asset('template/assets/vendors/css/vendor.bundle.base.css') }}">
  <link rel="stylesheet" href="{{ asset('template/assets/vendors/font-awesome/css/font-awesome.min.css') }}">
  <link rel="stylesheet" href="{{ asset('template/assets/css/style.css') }}">
  <link rel="shortcut icon" href="{{ asset('template/assets/images/favicon.png') }}" />
  <style>
    .otp-input-group {
      display: flex;
      gap: 10px;
      justify-content: center;
      margin: 24px 0;
    }
    .otp-box {
      width: 48px;
      height: 56px;
      text-align: center;
      font-size: 1.5rem;
      font-weight: bold;
      border: 2px solid #dee2e6;
      border-radius: 8px;
      outline: none;
      transition: border-color 0.2s;
      text-transform: uppercase;
    }
    .otp-box:focus {
      border-color: #7b5ea7;
      box-shadow: 0 0 0 3px rgba(123, 94, 167, 0.15);
    }
    .otp-box.filled {
      border-color: #7b5ea7;
      background-color: #f5f1ff;
    }
  </style>
</head>
<body>
  <div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
      <div class="content-wrapper d-flex align-items-center auth">
        <div class="row flex-grow">
          <div class="col-lg-4 mx-auto">
            <div class="auth-form-light text-center p-5">

              <div class="brand-logo mb-3">
                <img src="{{ asset('template/assets/images/logo.svg') }}" alt="logo">
              </div>

              <h4>Verifikasi OTP</h4>
              <p class="text-muted mb-1">
                Kode OTP 6 karakter telah dikirim ke email Anda.
              </p>
              <small class="text-muted">Masukkan kode yang diterima untuk melanjutkan.</small>

              {{-- Error --}}
              @if($errors->any())
                <div class="alert alert-danger mt-3 text-left">
                  <i class="mdi mdi-alert-circle me-2"></i>{{ $errors->first() }}
                </div>
              @endif

              {{-- OTP Form --}}
              <form method="POST" action="{{ route('otp.verify') }}" id="otpForm">
                @csrf

                {{-- 6 kotak input OTP --}}
                <div class="otp-input-group" id="otpBoxes">
                  <input class="otp-box" type="text" maxlength="1" data-index="0" autofocus>
                  <input class="otp-box" type="text" maxlength="1" data-index="1">
                  <input class="otp-box" type="text" maxlength="1" data-index="2">
                  <input class="otp-box" type="text" maxlength="1" data-index="3">
                  <input class="otp-box" type="text" maxlength="1" data-index="4">
                  <input class="otp-box" type="text" maxlength="1" data-index="5">
                </div>

                {{-- Hidden input yang akan dikirim --}}
                <input type="hidden" name="otp" id="otpHidden">

                <div class="mt-2">
                  <button type="submit" id="submitBtn"
                    class="btn btn-block btn-gradient-primary btn-lg font-weight-medium auth-form-btn w-100"
                    disabled>
                    <i class="mdi mdi-shield-check me-1"></i> VERIFIKASI
                  </button>
                </div>
              </form>

              <div class="mt-4 text-muted" style="font-size:0.85rem">
                <i class="mdi mdi-information-outline me-1"></i>
                Tidak menerima email? Coba login ulang menggunakan Google.
              </div>

              <div class="mt-2">
                <a href="{{ route('login') }}" class="text-primary" style="font-size:0.85rem">
                  <i class="mdi mdi-arrow-left me-1"></i>Kembali ke Login
                </a>
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="{{ asset('template/assets/vendors/js/vendor.bundle.base.js') }}"></script>
  <script src="{{ asset('template/assets/js/off-canvas.js') }}"></script>
  <script src="{{ asset('template/assets/js/misc.js') }}"></script>

  <script>
    const boxes    = document.querySelectorAll('.otp-box');
    const hidden   = document.getElementById('otpHidden');
    const submitBtn = document.getElementById('submitBtn');

    function updateHidden() {
      const val = Array.from(boxes).map(b => b.value).join('');
      hidden.value = val;
      submitBtn.disabled = val.length < 6;
    }

    boxes.forEach((box, i) => {
      box.addEventListener('input', function () {
        // Hanya terima 1 karakter alphanumeric
        this.value = this.value.replace(/[^a-zA-Z0-9]/g, '').slice(0, 1).toUpperCase();

        if (this.value) {
          this.classList.add('filled');
          if (i < 5) boxes[i + 1].focus();
        } else {
          this.classList.remove('filled');
        }
        updateHidden();
      });

      box.addEventListener('keydown', function (e) {
        // Backspace → pindah ke kotak sebelumnya
        if (e.key === 'Backspace' && !this.value && i > 0) {
          boxes[i - 1].focus();
          boxes[i - 1].value = '';
          boxes[i - 1].classList.remove('filled');
          updateHidden();
        }
        // Paste
        if (e.key === 'v' && (e.ctrlKey || e.metaKey)) return;
      });

      box.addEventListener('paste', function (e) {
        e.preventDefault();
        const pasted = (e.clipboardData || window.clipboardData)
          .getData('text')
          .replace(/[^a-zA-Z0-9]/g, '')
          .toUpperCase()
          .slice(0, 6);

        pasted.split('').forEach((char, j) => {
          if (boxes[j]) {
            boxes[j].value = char;
            boxes[j].classList.add('filled');
          }
        });
        updateHidden();
        const nextEmpty = Array.from(boxes).find(b => !b.value);
        if (nextEmpty) nextEmpty.focus();
        else boxes[5].focus();
      });
    });

    // Auto-submit jika 6 karakter sudah terisi
    document.getElementById('otpForm').addEventListener('submit', function () {
      updateHidden();
    });
  </script>
</body>
</html>