{{-- ===================== JAVASCRIPT GLOBAL ===================== --}}
<script src="{{ asset('template/assets/vendors/js/vendor.bundle.base.js') }}"></script>
<script src="{{ asset('template/assets/js/off-canvas.js') }}"></script>
<script src="{{ asset('template/assets/js/misc.js') }}"></script>
<script src="{{ asset('template/assets/js/settings.js') }}"></script>
<script src="{{ asset('template/assets/js/todolist.js') }}"></script>
<script src="{{ asset('template/assets/js/jquery.cookie.js') }}"></script>

{{--
  =====================================================================
  TAMBAHKAN ke resources/views/layouts/partials/js-global.blade.php
  SETELAH semua script global yang ada
  =====================================================================

  Fungsi ini berlaku global untuk SEMUA form di seluruh halaman.
  Tidak perlu tambahkan per-halaman.
--}}

<script>
/**
 * Global form submission handler:
 * 1. checkValidity() - cek semua input required
 * 2. reportValidity() - highlight input yang kosong
 * 3. Ubah button jadi spinner selama submit
 * 4. Cegah double submit
 */
document.addEventListener('DOMContentLoaded', function () {

  document.querySelectorAll('form').forEach(function (form) {

    // Cari tombol submit di dalam form (type="submit" atau class btn-submit)
    const btn = form.querySelector('button[type="submit"]');
    if (!btn) return;

    // Simpan HTML asli tombol untuk restore jika error
    const originalHtml = btn.innerHTML;

    form.addEventListener('submit', function (e) {
      // 1. Cek validity HTML5
      if (!form.checkValidity()) {
        e.preventDefault();
        form.reportValidity(); // highlight input yang belum diisi
        return;
      }

      // 2. Cegah double submit
      if (btn.disabled) {
        e.preventDefault();
        return;
      }

      // 3. Ubah button jadi spinner
      btn.disabled = true;
      btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Menyimpan...';
    });

    // Reset tombol jika user klik back/bfcache
    window.addEventListener('pageshow', function (e) {
      if (e.persisted) {
        btn.disabled = false;
        btn.innerHTML = originalHtml;
      }
    });
  });

});
</script>