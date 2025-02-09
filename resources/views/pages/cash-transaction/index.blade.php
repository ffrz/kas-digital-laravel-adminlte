@php
  use App\Models\AclResource;
@endphp

@extends('layouts.default', [
    'title' => 'Transaksi Keuangan',
    'menu_active' => 'finance',
    'nav_active' => 'cash-transaction',
])

@section('right-menu')
  <li class="nav-item">
    <a class="btn plus-btn btn-primary mr-2" href="{{ url('cash-transaction/edit/0') }}" title="Baru"><i
        class="fa fa-plus"></i></a>
  </li>
@endSection

@section('content')
  <div class="card card-light">
    <div class="card-body">
      <div class="row">
        <div class="col-md-12">
          <form action="?" method="GET">
            <div class="row mb-3">
              <div class="col">
                <div class="form-inline">
                  <label class="mr-2" for="account_id">Akun:</label>
                  <select class="form-control custom-select select2" id="account_id" name="account_id" onchange="this.form.submit()">
                    <option value="" {{ !$filter['account_id'] ? 'selected' : '' }}>Semua Akun</option>
                    @foreach ($accounts as $account)
                      <option value="{{ $account->id }}" {{ $filter['account_id'] == $account->id ? 'selected' : '' }}>
                        {{ $account->name }}
                      </option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="col d-flex justify-content-end">
                <div class="form-inline">
                  <label class="mr-2" for="search">Cari:</label>
                  <input type="text" class="form-control" name="search" id="search" value="{{ $filter['search'] }}"
                    placeholder="Cari transaksi">
                </div>
              </div>
            </div>
          </form>

          <div class="table-responsive">
            <table class="table-bordered table-striped table-sm table">
              <thead>
                <tr>
                  <th style="width:1%">ID</th>
                  <th style="width:1%">Tanggal</th>
                  <th>Akun</th>
                  <th>Kategori</th>
                  <th>Uraian</th>
                  <th>Jumlah</th>
                  <th style="width:5%">Aksi</th>
                </tr>
              </thead>
              <tbody>
                @forelse ($items as $item)
                  <tr>
                    <td class="text-nowrap text-right">{{ $item->idFormatted() }}</td>
                    <td class="text-nowrap">{{ format_date($item->date) }}</td>
                    <td>{{ $item->account->name }}</td>
                    <td>{{ $item->category ? $item->category->name : '-Tanpa Kategori-' }}</td>
                    <td>{{ $item->description }}</td>
                    <td class="{{ $item->amount > 0 ? 'text-success' : 'text-danger' }} text-right">
                      {{ ($item->amount > 0 ? '+' : '') . format_number($item->amount) }}</td>
                    <td class="text-center">
                      <div class="btn-group">
                        <a class="btn btn-default btn-sm" href="{{ url("cash-transaction/edit/$item->id") }}"><i
                            class="fa fa-edit"></i></a>
                        <a class="btn btn-danger btn-sm" href="{{ url("cash-transaction/delete/$item->id") }}"
                          onclick="return confirm('Anda yakin akan menghapus rekaman ini?')"><i
                            class="fa fa-trash"></i></a>
                      </div>
                    </td>
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
@endSection
