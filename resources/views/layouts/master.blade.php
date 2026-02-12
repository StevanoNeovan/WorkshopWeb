<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title')</title>

    {{-- CSS GLOBAL --}}
    <link rel="stylesheet" href="{{ asset('template/assets/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('template/assets/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('template/assets/css/style.css') }}">

    {{-- CSS PAGE --}}
    @yield('style')

</head>

<body>
<div class="container-scroller">

    {{-- NAVBAR --}}
    @include('layouts.navbar')

    <div class="container-fluid page-body-wrapper">

        {{-- SIDEBAR --}}
        @include('layouts.sidebar')

        <div class="main-panel">
            <div class="content-wrapper">

                {{-- CONTENT --}}
                @yield('content')

            </div>

            {{-- FOOTER --}}
            @include('layouts.footer')

        </div>
    </div>
</div>

{{-- JS GLOBAL --}}
<script src="{{ asset('template/assets/vendors/js/vendor.bundle.base.js') }}"></script>
<script src="{{ asset('template/assets/vendors/chart.js/chart.umd.js') }}"></script>
<script src="{{ asset('template/assets/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>

<script src="{{ asset('template/assets/js/off-canvas.js') }}"></script>
<script src="{{ asset('template/assets/js/misc.js') }}"></script>
<script src="{{ asset('template/assets/js/settings.js') }}"></script>
<script src="{{ asset('template/assets/js/todolist.js') }}"></script>
<script src="{{ asset('template/assets/js/jquery.cookie.js') }}"></script>
<script src="{{ asset('template/assets/js/dashboard.js') }}"></script>

{{-- JS PAGE --}}
@yield('script')

</body>
</html>
