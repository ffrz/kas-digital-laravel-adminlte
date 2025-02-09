@extends('layouts.default', [
    'title' => 'Pengguna',
    'menu_active' => 'system',
    'nav_active' => 'user',
])

@section('right-menu')
  <li class="nav-item">
    <a href="{{ url('user/edit/0') }}" class="btn plus-btn btn-primary" title="Baru"><i class="fa fa-plus"></i></a>
    <button class="btn btn-default" data-toggle="modal" data-target="#filter-dialog" title="Saring"><i
        class="fa fa-filter"></i>
      @if ($filter_active)
        <span class="badge badge-warning">!</span>
      @endif
    </button>
  </li>
@endsection

@section('content')
  <form action="?" method="GET">
    <div class="card card-light">
      <div class="card-body">
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
                      <option value="-1">Semua</option>
                      <option value="1" {{ $filter['type'] == 1 ? 'selected' : '' }}>Administrator</option>
                      <option value="0" {{ $filter['type'] == 0 ? 'selected' : '' }}>Pengguna Biasa</option>
                    </select>
                  </div>
                </div>
                <div class="form-group row">
                  <label class="col-form-label col-sm-4" for="status">Status:</label>
                  <div class="col-sm-8">
                    <select class="form-control custom-select" name="status" id="status"
                      onchange="this.form.submit();">
                      <option value="-1">Semua</option>
                      <option value="1" {{ $filter['status'] == 1 ? 'selected' : '' }}>Aktif</option>
                      <option value="0" {{ $filter['status'] == 0 ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="modal-footer justify-content-center">
                <button class="btn btn-primary" type="submit"><i class="fas fa-check mr-2"></i> Terapkan</button>
                <button class="btn btn-default" name="action" type="submit" value="reset"><i
                    class="fa fa-filter-circle-xmark"></i> Reset Filter</button>
              </div>
            </div>
          </div>
        </div>
        <div class="row mb-3">
          <div class="col-12 col-sm-8 col-md-9">
            <h5>Daftar Pengguna</h5>
          </div>
          <div class="col-12 col-sm-4 col-md-3 d-flex justify-content-end">
            <input class="form-control" id="search" name="search" type="text" value="{{ $filter['search'] }}"
              autofocus placeholder="Cari">
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="table-responsive">
              <table class="table-bordered table-striped table-sm table" style="width:100%">
                <thead>
                  <tr>
                    <th>Username</th>
                    <th class="d-none d-md-table-cell">Nama Lengkap</th>
                    <th class="d-none d-md-table-cell">Status</th>
                    <th class="text-center" style="width:1%">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse ($items as $item)
                    <tr>
                      <td>
                        {{ $item->username }}
                        @if ($item->is_admin)
                          <span class="badge badge-warning">Administrator</span>
                        @endif
                        <div class="d-md-none">
                          <i class="fa fa-user fa-xs text-gray"></i> {{ $item->fullname }}
                          <br><span
                            class="badge bg-{{ $item->is_active ? 'success' : 'danger' }}">{{ $item->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                        </div>
                      </td>
                      <td class="d-none d-md-table-cell">{{ $item->fullname }}</td>
                      <td class="d-none d-md-table-cell">{{ $item->is_active ? 'Aktif' : 'Nonaktif' }}</td>
                      <td class="text-right align-middle">
                        <div class="btn-group">
                          <a href="{{ url("user/edit/$item->id") }}" class="btn btn-default btn-sm"><i
                              class="fa fa-edit"></i></a>
                          <a href="{{ url("user/delete/$item->id") }}" class="btn btn-danger btn-sm"><i
                              class="fa fa-trash"></i></a>
                        </div>
                      </td>
                    </tr>
                  @empty
                    <tr class="empty">
                      <td colspan="5">Belum ada rekaman</td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
        @include('components.paginator', ['items' => $items])
      </div>
    </div>
  </form>
@endsection
