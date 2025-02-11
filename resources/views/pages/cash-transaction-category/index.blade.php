@extends('layouts.default', [
    'title' => 'Kategori Transaksi',
    'menu_active' => 'finance',
    'nav_active' => 'cash-transaction-category',
])

@section('right-menu')
  <li class="nav-item">
    <a href="{{ url('cash-transaction-category/edit/0') }}" class="btn plus-btn btn-primary mr-2" title="Baru">
      <i class="fa fa-plus"></i>
    </a>
  </li>
@endSection

@section('content')
  <form action="?" method="GET">
    <div class="card card-light">
      <div class="card-body">
        <div class="row mb-3">
          <div class="col-12 col-sm-8 col-md-9 d-flex align-items-center">
            <h5 class="my-2">Daftar Kategori Transaksi</h5>
          </div>
          <div class="col-12 col-sm-4 col-md-3 d-flex justify-content-end">
            <input class="form-control" id="search" name="search" type="text" value="{{ $filter['search'] }}"
              autofocus placeholder="Cari">
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="table-responsive">
              <table class="table-bordered table-striped table-sm table">
                <thead>
                  <tr>
                    <th style="width:30%">Kategori</th>
                    <th class="d-none d-md-table-cell">Deskripsi</th>
                    @if (Auth::user()->is_admin)
                      <th style="width:1%">Aksi</th>
                    @endif
                  </tr>
                </thead>
                <tbody>
                  @forelse ($items as $item)
                    <tr>
                      <td>{{ $item->name }}
                        @if ($item->description)
                          <div class="d-md-none">
                            <i class="fa fa-note-sticky text-gray mr-1"></i> {{ $item->description }}
                          </div>
                        @endif
                      </td>
                      <td class="d-none d-md-table-cell">{{ $item->description }}</td>
                      @if (Auth::user()->is_admin)
                        <td class="text-center align-middle">
                          <div class="btn-group">
                            <a href="{{ url("cash-transaction-category/edit/$item->id") }}"
                              class="btn btn-default btn-sm"><i class="fa fa-edit"></i></a>
                            <a onclick="return confirm('Anda yakin akan menghapus rekaman ini?')"
                              href="{{ url("cash-transaction-category/delete/$item->id") }}"
                              class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></a>
                          </div>
                        </td>
                      @endif
                    </tr>
                  @empty
                    <tr class="empty">
                      <td colspan="3">Tidak ada rekaman untuk ditampilkan.</td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
            @include('components.paginator', ['items' => $items])
          </div>
        </div>
      </div>
    </div>
  </form>
@endSection
