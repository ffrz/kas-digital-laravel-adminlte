@php
  $title = ($item->id ? 'Edit' : 'Tambah') . ' Akun Kas / Rekening';
@endphp

@extends('layouts.default', [
    'title' => $title,
    'menu_active' => 'finance',
    'nav_active' => 'cash-account',
    'form_action' => url('cash-account/edit/' . (int) $item->id),
])

@section('right-menu')
  <li class="nav-item">
    <button type="submit" class="btn btn-primary mr-1"><i class="fas fa-save mr-1"></i> Simpan</button>
    <a onclick="return confirm('Batalkan perubahan?')" class="btn btn-default" href="{{ url('cash-account/') }}"><i
        class="fas fa-cancel mr-1"></i>Batal</a>
  </li>
@endSection

@section('content')
  <div class="row">
    <div class="col-md-4">
      <div class="card card-primary">
        <div class="card-body">
          <div class="form-group">
            <label for="type">Jenis Akun</label>
            <select class="custom-select form-control" id="type" name="type">
              <option value="0" {{ $item->type == 0 ? 'selected' : '' }}>Tunai</option>
              <option value="1" {{ $item->type == 1 ? 'selected' : '' }}>Bank</option>
            </select>
          </div>
          <div class="form-group">
            <label for="name">Nama Akun</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" autofocus id="name"
              placeholder="Masukkan nama akun" name="name" value="{{ old('name', $item->name) }}">
            @error('name')
              <span class="text-danger">
                {{ $message }}
              </span>
            @enderror
          </div>
          <div class="form-group" id="bank-name-container" style="display: none">
            <label for="bank">Bank</label>
            <input type="text" class="form-control @error('bank') is-invalid @enderror" id="bank"
              placeholder="Masukkan nama bank" name="bank" value="{{ old('bank', $item->bank) }}">
            @error('bank')
              <span class="text-danger">
                {{ $message }}
              </span>
            @enderror
          </div>
          <div class="form-group" id="account-number-container" style="display: none">
            <label for="number">No Rekening</label>
            <input type="text" class="form-control @error('number') is-invalid @enderror" autofocus id="number"
              placeholder="Masukkan nomor rekening" name="number" value="{{ old('number', $item->number) }}">
            @error('number')
              <span class="text-danger">
                {{ $message }}
              </span>
            @enderror
          </div>
          <div class="form-group">
            <label for="balance">Saldo</label>
            <input type="text" class="form-control text-right @error('balance') is-invalid @enderror" autofocus
              id="balance" placeholder="" name="balance" value="{{ old('balance', format_number($item->balance)) }}">
            @error('balance')
              <span class="text-danger">
                {{ $message }}
              </span>
            @enderror
          </div>
          <div class="form-group">
            <div class="custom-control custom-checkbox">
              <input type="checkbox" class="custom-control-input " id="active" name="active" value="1"
                {{ old('active', $item->active) ? 'checked="checked"' : '' }}>
              <label class="custom-control-label" for="active" title="Akun aktif dapat login">Aktif</label>
            </div>
            <div class="text-muted">Akun aktif dapat digunakan di transaksi.</div>
          </div>
          <div class="form-group">
            <label for="notes">Catatan</label>
            <textarea class="form-control @error('notes') is-invalid @enderror" name="notes" id="notes" cols="30"
              rows="4">{{ old('notes', $item->notes) }}</textarea>
            @error('notes')
              <span class="text-danger">
                {{ $message }}
              </span>
            @enderror
          </div>
        </div>
      </div>
    </div>
  </div>
@endSection
@section('footscript')
  <script>
    $(document).ready(function() {
      function on_type_changed() {
        let val = $('#type').val();
        if (val == 1) {
          $('#bank-name-container').show();
          $('#account-number-container').show();
        } else {
          $('#bank-name-container').hide();
          $('#account-number-container').hide();
        }
      }
      $('#type').change(function() {
        on_type_changed();
      });
      Inputmask("decimal", Object.assign({
        allowMinus: false
      }, INPUTMASK_OPTIONS)).mask("#balance");
      on_type_changed();
    });
  </script>
@endsection
