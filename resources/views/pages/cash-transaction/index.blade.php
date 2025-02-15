@extends('layouts.default', [
    'title' => 'Transaksi',
    'nav_active' => 'cash-transaction',
])

@section('right-menu')
  <li class="nav-item">
    <a class="btn plus-btn btn-primary" href="{{ url('cash-transaction/edit/0') }}" title="Baru">
      <i class="fa fa-plus"></i>
    </a>
    <a class="btn btn-default" href="{{ url('cash-transaction/transfer') }}" title="Transfer">
      <i class="fa fa-right-left"></i>
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
        <a class="dropdown-item"
          href="{{ url('cash-transaction/export?format=pdf') . '&' . http_build_query($filter) }}"><i
            class="fa fa-file-pdf text-danger mr-2"></i> Ekspor PDF</a>
        <a class="dropdown-item"
          href="{{ url('cash-transaction/export?format=excel') . '&' . http_build_query($filter) }}"><i
            class="fa fa-file-excel text-success mr-2"></i> Ekspor Excel</a>
      </div>
    </div>
  </li>
@endSection

@section('content')
  <form method="GET" action="?">
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
              <label class="col-form-label col-sm-4" for="account_id">Akun:</label>
              <div class="col-sm-8">
                <select class="form-control custom-select select2" id="account_id" name="account_id">
                  <option value="" {{ !$filter['account_id'] ? 'selected' : '' }}>Semua Akun</option>
                  @foreach ($accounts as $account)
                    <option value="{{ $account->id }}" {{ $filter['account_id'] == $account->id ? 'selected' : '' }}>
                      {{ $account->name }}
                    </option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="form-group row">
              <label class="col-form-label col-sm-4" for="type">Jenis Transaksi:</label>
              <div class="col-sm-8">
                <select class="form-control custom-select select2" id="type" name="type">
                  <option value="" {{ !$filter['type'] ? 'selected' : '' }}>Semua Jenis</option>
                  <option value="income" {{ $filter['type'] == 'income' ? 'selected' : '' }}>Pemasukan</option>
                  <option value="expense" {{ $filter['type'] == 'expense' ? 'selected' : '' }}>Pengeluaran</option>
                </select>
              </div>
            </div>
            <div class="form-group row">
              <label class="col-form-label col-sm-4" for="category_id">Kategori:</label>
              <div class="col-sm-8">
                <select class="form-control custom-select select2" id="category_id" name="category_id">
                  <option value="" {{ !$filter['category_id'] ? 'selected' : '' }}>Semua Kategori</option>
                  @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ $filter['category_id'] == $category->id ? 'selected' : '' }}>
                      {{ $category->type == 'income' ? 'Pemasukan' : 'Pengeluaran' }} : {{ $category->name }}
                    </option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="form-group row">
              <label class="col-form-label col-sm-4" for="period">Periode:</label>
              <div class="col-sm-8">
                <select class="form-control custom-select" name="period" id="period">
                  <option value="all">Semua</option>
                  <option value="today" {{ $filter['period'] == 'today' ? 'selected' : '' }}>Hari Ini</option>
                  <option value="yesterday" {{ $filter['period'] == 'yesterday' ? 'selected' : '' }}>Kemarin</option>
                  <option value="this_week" {{ $filter['period'] == 'this_week' ? 'selected' : '' }}>Minggu Ini</option>
                  <option value="prev_week" {{ $filter['period'] == 'prev_week' ? 'selected' : '' }}>Minggu Kemarin</option>
                  <option value="this_month" {{ $filter['period'] == 'this_month' ? 'selected' : '' }}>Bulan Ini</option>
                  <option value="prev_month" {{ $filter['period'] == 'prev_month' ? 'selected' : '' }}>Bulan Kemarin
                  </option>
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
        <div class="row">
          <div class="col-md-12">
            <div class="row mb-3">
              <div class="col-12 col-sm-8 col-md-9 d-flex align-items-center">
                <h5 class="m-0 my-2">Daftar Transaksi</h5>
              </div>
              <div class="col-12 col-sm-4 col-md-3 d-flex justify-content-end">
                <input class="form-control" id="search" name="search" type="text"
                  value="{{ $filter['search'] }}" autofocus placeholder="Cari">
              </div>
            </div>

            <div class="table-responsive">
              <table class="table-bordered table-striped table-sm table">
                <thead>
                  <tr>
                    <th>
                      <span class="d-block d-md-none">Rincian</span>
                      <span class="d-none d-md-block">ID</span>
                    </th>
                    <th class="d-none d-md-table-cell">Tanggal</th>
                    <th class="d-none d-md-table-cell">Akun</th>
                    <th class="d-none d-md-table-cell">Kategori</th>
                    <th class="d-none d-md-table-cell">Uraian</th>
                    <th class="d-none d-md-table-cell">Jumlah</th>
                    @if (Auth::user()->is_admin)
                      <th style="width:5%">Aksi</th>
                    @endif
                  </tr>
                </thead>
                <tbody>
                  @forelse ($items as $item)
                    <tr>
                      <td>
                        <span class="d-none d-md-block">{{ $item->idFormatted() }}</span>
                        <div class="d-block d-md-none">
                            <div><i class="fa fa-calendar mr-2"></i>#{{ $item->idFormatted() }} - <i class="fa fa-date mr-2"></i>{{ format_date($item->date) }}</div>
                            <div><i class="fa fa-wallet mr-2"></i>{{ $item->account->name }}</div>
                            <div><i class="fa fa-tag mr-2"></i>{{ optional($item->category)->name ?? 'Tanpa Kategori' }}</div>
                            <div><i class="fa fa-note-sticky mr-2"></i>{{ $item->description }}</div>
                            <div class="{{ $item->amount > 0 ? 'text-success' : 'text-danger' }}">
                                <i class="fa fa-money-bill mr-2"></i> {{ ($item->amount > 0 ? '+' : '') . 'Rp. ' . format_number($item->amount) }}
                            </div>
                        </div>
                      </td>
                      <td class="d-none d-md-table-cell text-nowrap">{{ format_date($item->date) }}</td>
                      <td class="d-none d-md-table-cell">{{ $item->account->name }}</td>
                      <td class="d-none d-md-table-cell">{{ $item->category ? $item->category->name : '-Tanpa Kategori-' }}
                      </td>
                      <td class="d-none d-md-table-cell">{{ $item->description }}</td>
                      <td class="d-none d-md-table-cell {{ $item->amount > 0 ? 'text-success' : 'text-danger' }} text-right">
                        {{ ($item->amount > 0 ? '+' : '') . format_number($item->amount) }}</td>
                      @if (Auth::user()->is_admin)
                        <td class="text-center">
                          <div class="btn-group">
                            <a class="btn btn-default btn-sm" href="{{ url("cash-transaction/edit/$item->id") }}"><i
                                class="fa fa-edit"></i></a>
                            <a class="btn btn-danger btn-sm" href="{{ url("cash-transaction/delete/$item->id") }}"
                              onclick="return confirm('Anda yakin akan menghapus rekaman ini?')"><i
                                class="fa fa-trash"></i></a>
                          </div>
                        </td>
                      @endif
                    </tr>
                  @empty
                    <tr class="empty">
                      <td colspan="7">Tidak ada rekaman untuk ditampilkan.</td>
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
