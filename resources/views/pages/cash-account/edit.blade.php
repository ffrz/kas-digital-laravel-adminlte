@php
  $title = ($item->id ? 'Edit' : 'Tambah') . ' Akun';
@endphp

@extends('layouts.default', [
    'title' => $title,
    'nav_active' => 'cash-account',
    'form_action' => url('cash-account/edit/' . (int) $item->id),
])

@section('content')
  <div class="row">
    <div class="col-md-6">
      <div class="card card-primary">
        <div class="card-body">
          <div class="form-group">
            <label for="type">Jenis Akun</label>
            <select class="custom-select form-control" id="type" name="type">
              <option value="cash" {{ $item->type == 'cash' ? 'selected' : '' }}>Tunai</option>
              <option value="bank" {{ $item->type == 'bank' ? 'selected' : '' }}>Bank</option>
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
            <label for="bank_account_number">No Rekening</label>
            <input type="text" class="form-control @error('bank_account_number') is-invalid @enderror" autofocus id="bank_account_number"
              placeholder="Masukkan nomor rekening" name="bank_account_number" value="{{ old('bank_account_number', $item->bank_account_number) }}">
            @error('bank_account_number')
              <span class="text-danger">
                {{ $message }}
              </span>
            @enderror
          </div>
          <div class="form-group" id="account-name-container" style="display: none">
            <label for="bank_account_name">Atas Nama</label>
            <input type="text" class="form-control @error('bank_account_name') is-invalid @enderror" autofocus id="bank_account_name"
              placeholder="Masukkan Nama Pemilik Rekening" name="bank_account_name" value="{{ old('bank_account_name', $item->bank_account_name) }}">
            @error('bank_account_name')
              <span class="text-danger">
                {{ $message }}
              </span>
            @enderror
          </div>
          <div class="form-group">
            <label for="balance">Saldo</label>
            <input type="text" class="form-control @error('balance') is-invalid @enderror text-right" autofocus
              id="balance" placeholder="" name="balance" value="{{ old('balance', format_number($item->balance)) }}">
            @error('balance')
              <span class="text-danger">
                {{ $message }}
              </span>
            @enderror
          </div>
          <div class="form-group">
            <div class="icheck-primary d-inline">
              <input type="checkbox" class="custom-control-input" id="active" name="active" value="1"
                {{ old('active', $item->active) ? 'checked="checked"' : '' }}>
              <label for="active" title="Akun aktif dapat login">Aktif</label>
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
          <div class="mt-4">
            <button type="submit" class="btn btn-primary mr-1"><i class="fas fa-save mr-1"></i> Simpan</button>
            <a onclick="return confirm('Batalkan perubahan?')" class="btn btn-default"
              href="{{ url('cash-account/') }}"><i class="fas fa-cancel mr-1"></i>Batal</a>
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
        if (val == 'bank') {
          $('#bank-name-container').show();
          $('#account-number-container').show();
          $('#account-name-container').show();
        } else {
          $('#bank-name-container').hide();
          $('#account-number-container').hide();
          $('#account-name-container').hide();
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
