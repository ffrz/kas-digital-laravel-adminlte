@php
  use App\Models\AclResource;
@endphp

@extends('layouts.default', [
    'title' => 'Akun / Rekening',
    'menu_active' => 'finance',
    'nav_active' => 'cash-account',
])

@section('right-menu')
  <li class="nav-item">
    <a class="btn plus-btn btn-primary" href="{{ url('cash-account/edit/0') }}" title="Baru">
      <i class="fa fa-plus"></i>
    </a>
    <button class="btn btn-default position-relative" data-toggle="modal" data-target="#filter-dialog" title="Saring">
      <i class="fa fa-filter"></i>
      @if ($filter_active)
        <span class="badge badge-warning position-absolute start-100 translate-middle top-0">!</span>
      @endif
    </button>
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
              <label class="col-form-label col-sm-4" for="type">Jenis Akun:</label>
              <div class="col-sm-8">
                <select class="form-control custom-select" name="type" id="type">
                  <option value="all">Semua</option>
                  <option value="cash" {{ $filter['type'] == 'cash' ? 'selected' : '' }}>Tunai</option>
                  <option value="bank" {{ $filter['type'] == 'bank' ? 'selected' : '' }}>Bank</option>
                </select>
              </div>
            </div>
            <div class="form-group row">
              <label class="col-form-label col-sm-4" for="active">Status:</label>
              <div class="col-sm-8">
                <select class="form-control custom-select" name="active" id="active">
                  <option value="all">Semua</option>
                  <option value="1" {{ $filter['active'] == 1 ? 'selected' : '' }}>Aktif</option>
                  <option value="0" {{ $filter['active'] == 0 ? 'selected' : '' }}>Tidak Aktif</option>
                </select>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-primary" type="submit"><i class="fas fa-check mr-2"></i> Terapkan</button>
            <button class="btn btn-default" name="action" type="submit" value="reset"><i
                class="fa fa-filter-circle-xmark"></i> Reset Penyaringan</button>
          </div>
        </div>
      </div>
    </div>

    <div class="card card-light">
      <div class="card-body">
        <div class="row mb-3">
          <div class="col-12 col-sm-8 col-md-9 d-flex align-items-center">
            <h5 class="m-0">Daftar Kas dan Rekening</h5>
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
                    <th style="width:30%">
                      <span class="d-block d-md-none">Akun</span>
                      <span class="d-none d-md-block">Nama Akun</span>
                    </th>
                    <th class="d-none d-md-table-cell">Rincian</th>
                    <th class="d-none d-md-table-cell">Saldo (Rp.)</th>
                    <th style="width:1%">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse ($items as $item)
                    <tr class="{{ !$item->active ? 'bg-red' : '' }}">
                      <td>
                        <div>{{ $item->name }}</div>
                        <div class="d-md-none">
                          @if ($item->type == 'bank')
                            {{ $item->bank }}<br>
                            a.n. {{ $item->bank_account_name }}<br>
                            {{ $item->bank_account_number }}<br>
                          @endif
                          Rp. {{ format_number($item->balance) }}
                        </div>
                      </td>
                      <td class="d-none d-md-table-cell">
                        @if ($item->type == 'bank')
                          {{ $item->bank }} a.n {{ $item->bank_account_name }}
                          - {{ $item->bank_account_number }}
                        @endif
                      </td>
                      <td class="d-none d-md-table-cell text-right">{{ format_number($item->balance) }}</td>
                      <td class="text-center align-middle">
                        <div class="btn-group">
                          <a class="btn btn-default btn-sm" href="{{ url("cash-account/edit/$item->id") }}"><i
                              class="fa fa-edit"></i></a>
                          <a class="btn btn-danger btn-sm" href="{{ url("cash-account/delete/$item->id") }}"
                            onclick="return confirm('Anda yakin akan menghapus rekaman ini?')"><i
                              class="fa fa-trash"></i></a>
                        </div>
                      </td>
                    </tr>
                  @empty
                    <tr class="empty">
                      <td colspan="4">Tidak ada rekaman untuk ditampilkan.</td>
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
