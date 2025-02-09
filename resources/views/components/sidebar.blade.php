@php
  if (!isset($menu_active)) {
      $menu_active = null;
  }
@endphp

<aside class="main-sidebar sidebar-light-primary elevation-4">
  <a class="brand-link" href="{{ url('dashboard') }}">
    <img class="brand-image img-circle elevation-3" src="{{ url('dist/img/logo.png') }}" alt="App Logo" style="opacity: .8">
    <span class="brand-text font-weight-light">{{ env('APP_NAME') }}</span>
  </a>
  <div class="sidebar">
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column nav-flat nav-flat nav-collapse-hide-child" data-widget="treeview"
        data-accordion="false" role="menu">
        <li class="nav-item">
          <a class="nav-link {{ $nav_active == 'dashboard' ? 'active' : '' }}" href="{{ url('dashboard') }}">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>Dashboard</p>
          </a>
        </li>

        {{-- Expense Menu Begin --}}
        <li class="nav-item">
          <a class="nav-link {{ $nav_active == 'cash-transaction' ? 'active' : '' }}"
            href="{{ url('cash-transaction') }}">
            <i class="nav-icon fas fa-money-bill-transfer"></i>
            <p>Transaksi</p>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link {{ $nav_active == 'cash-transaction-category' ? 'active' : '' }}"
            href="{{ url('cash-transaction-category') }}">
            <i class="nav-icon fas fa-boxes"></i>
            <p>Kategori Transaksi</p>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link {{ $nav_active == 'cash-account' ? 'active' : '' }}" href="{{ url('cash-account') }}">
            <i class="nav-icon fas fa-money-check"></i>
            <p>Akun / Rekening</p>
          </a>
        </li>
        {{-- End of Expense Menu --}}

        {{-- Report Menu --}}
        <li class="nav-item {{ $menu_active == 'report' ? 'menu-open' : '' }}">
          <a class="nav-link {{ $menu_active == 'report' ? 'active' : '' }}" href="report/">
            <i class="nav-icon fas fa-file-waveform"></i>
            <p>Laporan</p>
          </a>
        </li>
        {{-- End Report Menu --}}

        {{-- System Menu --}}
        <li class="nav-item {{ $menu_active == 'system' ? 'menu-open' : '' }}">
          <a class="nav-link {{ $menu_active == 'system' ? 'active' : '' }}" href="#">
            <i class="nav-icon fas fa-gears"></i>
            <p>
              Pengaturan
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a class="nav-link {{ $nav_active == 'user' ? 'active' : '' }}" href="{{ url('user') }}">
                <i class="nav-icon fas fa-users"></i>
                <p>Pengguna</p>
              </a>
            </li>
          </ul>
        </li>
        {{-- End of System  menu --}}

        <li class="nav-item spacer"></li>
        <li class="nav-item">
          <a class="nav-link {{ $nav_active == 'profile' ? 'active' : '' }}" href="{{ url('user/profile/') }}">
            <i class="nav-icon fas fa-user"></i>
            <p>{{ Auth::user()->username }}</p>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{{ url('logout') }}">
            <i class="nav-icon fas fa-right-from-bracket"></i>
            <p>Keluar</p>
          </a>
        </li>
      </ul>
    </nav>
  </div>
</aside>
