@extends('layouts.default', [
    'title' => 'Kategori',
    'nav_active' => 'cash-transaction-category',
])

@section('right-menu')
  <li class="nav-item">
    <a href="{{ url('cash-transaction-category/edit/0') }}" class="btn plus-btn btn-primary" title="Baru">
      <i class="fa fa-plus"></i>
    </a>
    <button class="btn btn-default position-relative" data-toggle="modal" data-target="#filter-dialog" title="Saring">
      <i class="fa fa-filter"></i>
      @if ($filter_active)
        <span class="badge badge-warning position-absolute start-100 translate-middle top-0">!</span>
      @endif
    </button>
    <div class="btn-group">
      <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
        <i class="fa fa-file-export"></i>
      </button>
      <div class="dropdown-menu" role="menu">
        <a class="dropdown-item" href="{{ url('cash-transaction-category/export?format=pdf') }}"><i
            class="fa fa-file-pdf text-danger mr-2"></i> Ekspor PDF</a>
        <a class="dropdown-item" href="{{ url('cash-transaction-category/export?format=excel') }}"><i
            class="fa fa-file-excel text-success mr-2"></i> Ekspor Excel</a>
      </div>
    </div>
  </li>
@endSection

@section('content')
  <form action="?" method="GET">
    <div class="modal fade" id="filter-dialog">
      <div class="modal-dialog modal-md">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Penyaringan</h4>
            <button class="close" data-dismiss="modal" type="button" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-group row">
              <label class="col-form-label col-sm-4" for="type">Jenis Transaksi:</label>
              <div class="col-sm-8">
                <select class="form-control custom-select" name="type" id="type">
                  <option value="all">Semua</option>
                  <option value="income" {{ $filter['type'] == 'income' ? 'selected' : '' }}>Pemasukan</option>
                  <option value="expense" {{ $filter['type'] == 'expense' ? 'selected' : '' }}>Pengeluaran</option>
                </select>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-primary" type="submit"><i class="fas fa-check mr-2"></i> Terapkan</button>
            <button class="btn btn-default" name="action" type="submit" value="reset">
                <i class="fa fa-filter-circle-xmark mr-2"></i> Reset Penyaringan</button>
          </div>
        </div>
      </div>
    </div>

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
                    <th>Jenis</th>
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
                      <td>{{ $item->type == 'income' ? 'Pemasukan' : 'Pengeluaran' }}</td>
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
