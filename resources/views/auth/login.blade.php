<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Login - Koleksi Buku</title>
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
              <h4>Selamat Datang!</h4>
              <h6 class="font-weight-light">Silakan masuk untuk melanjutkan.</h6>

              @if(session('status'))
                <div class="alert alert-success mb-3">{{ session('status') }}</div>
              @endif
              @if(session('error'))
                <div class="alert alert-danger mb-3">{{ session('error') }}</div>
              @endif

              <form class="pt-3" method="POST" action="{{ route('login') }}">
                @csrf
                <div class="form-group">
                  <input type="email"
                    class="form-control form-control-lg @error('email') is-invalid @enderror"
                    name="email" value="{{ old('email') }}"
                    placeholder="Email" required autofocus>
                  @error('email')
                    <span class="invalid-feedback">{{ $message }}</span>
                  @enderror
                </div>
                <div class="form-group">
                  <input type="password"
                    class="form-control form-control-lg @error('password') is-invalid @enderror"
                    name="password" placeholder="Password" required>
                  @error('password')
                    <span class="invalid-feedback">{{ $message }}</span>
                  @enderror
                </div>
                <div class="mt-3 d-grid gap-2">
                  <button type="submit"
                    class="btn btn-block btn-gradient-primary btn-lg font-weight-medium auth-form-btn">
                    MASUK
                  </button>
                </div>
                <div class="my-2 d-flex justify-content-between align-items-center">
                  <div class="form-check">
                    <label class="form-check-label text-muted">
                      <input type="checkbox" class="form-check-input" name="remember"
                        {{ old('remember') ? 'checked' : '' }}> Ingat saya
                    </label>
                  </div>
                  @if(Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="auth-link text-primary">Lupa password?</a>
                  @endif
                </div>
              </form>

              {{-- Divider --}}
              <div class="d-flex align-items-center my-3">
                <hr class="flex-grow-1">
                <span class="px-3 text-muted" style="font-size:0.85rem">atau</span>
                <hr class="flex-grow-1">
              </div>

              {{-- Tombol Login Google --}}
              <div class="mb-2 d-grid gap-2">
                <a href="{{ route('google.redirect') }}"
                  class="btn btn-block btn-outline-secondary btn-lg auth-form-btn d-flex align-items-center justify-content-center gap-2">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 48 48">
                    <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                    <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                    <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                    <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                    <path fill="none" d="M0 0h48v48H0z"/>
                  </svg>
                  Login dengan Google
                </a>
              </div>

              <div class="text-center mt-3 font-weight-light">
                Belum punya akun?
                <a href="{{ route('register') }}" class="text-primary">Daftar</a>
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
</body>
</html>