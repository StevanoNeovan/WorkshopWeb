
<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <ul class="nav">
    <li class="nav-item nav-profile">
      <a href="#" class="nav-link">
        <div class="nav-profile-image">
          <img src="{{ asset('template/assets/images/faces/face1.jpg') }}" alt="profile" />
          <span class="login-status online"></span>
        </div>
        <div class="nav-profile-text d-flex flex-column">
          <span class="font-weight-bold mb-2">{{ auth()->user()->name ?? 'User' }}</span>
          <span class="text-secondary text-small">{{ auth()->user()->email ?? '' }}</span>
        </div>
        <i class="mdi mdi-bookmark-check text-success nav-profile-badge"></i>
      </a>
    </li>

    {{-- Dashboard --}}
    <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
      <a class="nav-link" href="{{ route('dashboard') }}">
        <span class="menu-title">Dashboard</span>
        <i class="mdi mdi-home menu-icon"></i>
      </a>
    </li>

    {{-- Kategori --}}
    <li class="nav-item {{ request()->routeIs('kategori.*') ? 'active' : '' }}">
      <a class="nav-link" href="{{ route('kategori.index') }}">
        <span class="menu-title">Kategori</span>
        <i class="mdi mdi-tag menu-icon"></i>
      </a>
    </li>

    {{-- Buku --}}
    <li class="nav-item {{ request()->routeIs('buku.*') ? 'active' : '' }}">
      <a class="nav-link" href="{{ route('buku.index') }}">
        <span class="menu-title">Buku</span>
        <i class="mdi mdi-book-open-page-variant menu-icon"></i>
      </a>
    </li>
    

    {{-- Kasir --}}
    <li class="nav-item {{ request()->routeIs('kasir.*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('kasir.index') }}">
      <span class="menu-title">Kasir</span>
      <i class="mdi mdi-cash-register menu-icon"></i>
    </a>
    </li>

    {{-- Wilayah --}}
     <li class="nav-item {{ request()->routeIs('wilayah.*') ? 'active' : '' }}">
    <a class="nav-link" href="{{ route('wilayah.index') }}">
      <span class="menu-title">Wilayah</span>
      <i class="mdi mdi-map-marker menu-icon"></i>
    </a>
    </li>

    <li class="nav-item {{ request()->routeIs('toko.*') ? 'active' : '' }}">
      <a class="nav-link" href="{{ route('toko.index') }}">
        <span class="menu-title">Toko Buku</span>
        <i class="mdi mdi-store menu-icon"></i>
      </a>
    </li>
    <li class="nav-item {{ request()->routeIs('admin.pesanan') ? 'active' : '' }}">
      <a class="nav-link" href="{{ route('admin.pesanan') }}">
        <span class="menu-title">Pesanan Masuk</span>
        <i class="mdi mdi-clipboard-list menu-icon"></i>
      </a>
    </li>
    

 <li class="nav-item {{ request()->routeIs('customer.*') ? 'active' : '' }}">
   <a class="nav-link" href="#customerSub" data-bs-toggle="collapse">
     <span class="menu-title">Customer</span>
     <i class="mdi mdi-account-group menu-icon"></i>
   </a>
   <div class="collapse {{ request()->routeIs('customer.*') ? 'show' : '' }}" id="customerSub">
     <ul class="nav flex-column sub-menu">
       <li class="nav-item">
         <a class="nav-link {{ request()->routeIs('customer.index') ? 'active' : '' }}"
           href="{{ route('customer.index') }}">Data Customer</a>
       </li>
       <li class="nav-item">
         <a class="nav-link {{ request()->routeIs('customer.tambah1') ? 'active' : '' }}"
           href="{{ route('customer.tambah1') }}">Tambah Customer 1</a>
       </li>
       <li class="nav-item">
         <a class="nav-link {{ request()->routeIs('customer.tambah2') ? 'active' : '' }}"
           href="{{ route('customer.tambah2') }}">Tambah Customer 2</a>
       </li>
     </ul>
   </div>
   </li>

   <li class="nav-item {{ request()->routeIs('scanner.*') ? 'active' : '' }}">
      <a class="nav-link" href="{{ route('scanner.index') }}">
        <span class="menu-title">Scanner Barcode</span>
        <i class="mdi mdi-barcode-scan menu-icon"></i>
      </a>
   </li>

  </ul>
</nav>