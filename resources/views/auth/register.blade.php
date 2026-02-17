<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Register - Koleksi Buku</title>
  <link rel="stylesheet" href="{{ asset('template/assets/vendors/mdi/css/materialdesignicons.min.css') }}">
  <link rel="stylesheet" href="{{ asset('template/assets/vendors/ti-icons/css/themify-icons.css') }}">
  <link rel="stylesheet" href="{{ asset('template/assets/vendors/css/vendor.bundle.base.css') }}">
  <link rel="stylesheet" href="{{ asset('template/assets/vendors/font-awesome/css/font-awesome.min.css') }}">
  <link rel="stylesheet" href="{{ asset('template/assets/css/style.css') }}">
  <link rel="shortcut icon" href="{{ asset('template/assets/images/favicon.png') }}" />
</head>
<body>
  <div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
      <div class="content-wrapper d-flex align-items-center auth">
        <div class="row flex-grow">
          <div class="col-lg-4 mx-auto">
            <div class="auth-form-light text-left p-5">
              <div class="brand-logo">
                <img src="{{ asset('template/assets/images/logo.svg') }}" alt="logo">
              </div>
              <h4>Buat Akun Baru</h4>
              <h6 class="font-weight-light">Daftar untuk melanjutkan.</h6>

              <form class="pt-3" method="POST" action="{{ route('register') }}">
                @csrf
                <div class="form-group">
                  <input type="text"
                    class="form-control form-control-lg @error('name') is-invalid @enderror"
                    name="name"
                    value="{{ old('name') }}"
                    placeholder="Nama Lengkap"
                    required autofocus>
                  @error('name')
                    <span class="invalid-feedback">{{ $message }}</span>
                  @enderror
                </div>
                <div class="form-group">
                  <input type="email"
                    class="form-control form-control-lg @error('email') is-invalid @enderror"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="Email"
                    required>
                  @error('email')
                    <span class="invalid-feedback">{{ $message }}</span>
                  @enderror
                </div>
                <div class="form-group">
                  <input type="password"
                    class="form-control form-control-lg @error('password') is-invalid @enderror"
                    name="password"
                    placeholder="Password"
                    required>
                  @error('password')
                    <span class="invalid-feedback">{{ $message }}</span>
                  @enderror
                </div>
                <div class="form-group">
                  <input type="password"
                    class="form-control form-control-lg"
                    name="password_confirmation"
                    placeholder="Konfirmasi Password"
                    required>
                </div>
                <div class="mt-3 d-grid gap-2">
                  <button type="submit"
                    class="btn btn-block btn-gradient-primary btn-lg font-weight-medium auth-form-btn">
                    DAFTAR
                  </button>
                </div>
                <div class="text-center mt-4 font-weight-light">
                  Sudah punya akun?
                  <a href="{{ route('login') }}" class="text-primary">Masuk</a>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="{{ asset('template/assets/vendors/js/vendor.bundle.base.js') }}"></script>
  <script src="{{ asset('template/assets/js/off-canvas.js') }}"></script>
  <script src="{{ asset('template/assets/js/misc.js') }}"></script>
</body>
</html>