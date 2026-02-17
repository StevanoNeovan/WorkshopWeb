@extends('layouts.app')

@section('title', 'Dashboard')

{{-- ===================== STYLE PAGE ===================== --}}
@push('css-page')
{{-- tambahkan CSS khusus dashboard di sini --}}
@endpush

@section('content')
<div class="page-header">
  <h3 class="page-title">
    <span class="page-title-icon bg-gradient-primary text-white me-2">
      <i class="mdi mdi-home"></i>
    </span> Dashboard
  </h3>
  <nav aria-label="breadcrumb">
    <ul class="breadcrumb">
      <li class="breadcrumb-item active" aria-current="page">
        <span></span>Overview
        <i class="mdi mdi-alert-circle-outline icon-sm text-primary align-middle"></i>
      </li>
    </ul>
  </nav>
</div>

<div class="row">
  <div class="col-md-4 stretch-card grid-margin">
    <div class="card bg-gradient-danger card-img-holder text-white">
      <div class="card-body">
        <img src="{{ asset('template/assets/images/dashboard/circle.svg') }}" class="card-img-absolute" alt="circle-image" />
        <h4 class="font-weight-normal mb-3">Total Kategori
          <i class="mdi mdi-tag mdi-24px float-end"></i>
        </h4>
        <h2 class="mb-5">{{ $totalKategori ?? 0 }}</h2>
        <h6 class="card-text">Kategori buku tersedia</h6>
      </div>
    </div>
  </div>
  <div class="col-md-4 stretch-card grid-margin">
    <div class="card bg-gradient-info card-img-holder text-white">
      <div class="card-body">
        <img src="{{ asset('template/assets/images/dashboard/circle.svg') }}" class="card-img-absolute" alt="circle-image" />
        <h4 class="font-weight-normal mb-3">Total Buku
          <i class="mdi mdi-book-open-page-variant mdi-24px float-end"></i>
        </h4>
        <h2 class="mb-5">{{ $totalBuku ?? 0 }}</h2>
        <h6 class="card-text">Koleksi buku tersedia</h6>
      </div>
    </div>
  </div>
  <div class="col-md-4 stretch-card grid-margin">
    <div class="card bg-gradient-success card-img-holder text-white">
      <div class="card-body">
        <img src="{{ asset('template/assets/images/dashboard/circle.svg') }}" class="card-img-absolute" alt="circle-image" />
        <h4 class="font-weight-normal mb-3">Pengguna Aktif
          <i class="mdi mdi-account mdi-24px float-end"></i>
        </h4>
        <h2 class="mb-5">{{ auth()->user()->name }}</h2>
        <h6 class="card-text">Login sebagai: {{ auth()->user()->email }}</h6>
      </div>
    </div>
  </div>
</div>

@endsection

{{-- ===================== JAVASCRIPT PAGE ===================== --}}
@push('js-page')
{{-- tambahkan JS khusus dashboard di sini --}}
@endpush