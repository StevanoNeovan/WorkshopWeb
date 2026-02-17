<!DOCTYPE html>
<html lang="en">

{{-- ===================== HEADER ===================== --}}
@include('layouts.partials.header')

<body>
  <div class="container-scroller">

    {{-- ===================== NAVBAR ===================== --}}
    @include('layouts.partials.navbar')

    <div class="container-fluid page-body-wrapper">

      {{-- ===================== SIDEBAR ===================== --}}
      @include('layouts.partials.sidebar')

      <div class="main-panel">
        <div class="content-wrapper">

          {{-- ===================== CONTENT ===================== --}}
          @yield('content')

        </div>
        {{-- content-wrapper ends --}}

        {{-- ===================== FOOTER ===================== --}}
        @include('layouts.partials.footer')

      </div>
      {{-- main-panel ends --}}
    </div>
    {{-- page-body-wrapper ends --}}
  </div>
  {{-- container-scroller --}}

  {{-- ===================== JAVASCRIPT GLOBAL ===================== --}}
  @include('layouts.partials.js-global')

  {{-- ===================== JAVASCRIPT PAGE ===================== --}}
  @stack('js-page')

</body>
</html>