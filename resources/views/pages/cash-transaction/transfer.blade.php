@php
  $title = 'Transfer Kas';
@endphp

@extends('layouts.default', [
    'title' => $title,
    'menu_active' => 'finance',
    'nav_active' => 'cash-transaction',
    'form_action' => url('cash-transaction/transfer'),
])

@section('content')
  <div class="row">
    <div class="col-md-6">
      <div class="card card-primary">
        <div class="card-body">
          <div class="form-group">
            <label class="col-form-label" for="date">Tanggal:</label>
            <input class="form-control @error('date') is-invalid @enderror" id="date" name="date" type="date"
              value="{{ old('date', $from->date) }}" autofocus>
            @error('date')
              <span class="text-danger">
                {{ $message }}
              </span>
            @enderror
          </div>
          <div class="form-group">
            <label for="from_account_id">Dari Akun / Rekening Asal</label>
            <select class="custom-select select2 @error('from_account_id') is-invalid @enderror" id="from_account_id"
              name="from_account_id">
              <option value="" {{ !$from->account_id ? 'selected' : '' }}>-- Pilih Akun --</option>
              @foreach ($accounts as $account)
                <option value="{{ $account->id }}"
                  {{ old('from_account_id', $from->account_id) == $account->id ? 'selected' : '' }}>
                  {{ $account->name }}
                </option>
              @endforeach
            </select>
            @error('from_account_id')
              <span class="text-danger">
                {{ $message }}
              </span>
            @enderror
          </div>
          <div class="form-group">
            <label for="to_account_id">Ke Akun / Rekening Tujuan</label>
            <select class="custom-select select2 @error('to_account_id') is-invalid @enderror" id="to_account_id"
              name="to_account_id">
              <option value="" {{ !$to->account_id ? 'selected' : '' }}>-- Pilih Akun --</option>
              @foreach ($accounts as $account)
                <option value="{{ $account->id }}"
                  {{ old('to_account_id', $to->account_id) == $account->id ? 'selected' : '' }}>
                  {{ $account->name }}
                </option>
              @endforeach
            </select>
            @error('to_account_id')
              <span class="text-danger">
                {{ $message }}
              </span>
            @enderror
          </div>
          <div class="form-group">
            <label for="category_id">Kategori Transaksi
              <button class="btn btn-sm btn-default plus-btn" data-toggle="modal" data-target="#category-dialog"
                type="button" title="Tambah">
                <i class="fa fa-plus"></i>
              </button>
            </label>
            <select class="custom-select select2 @error('category_id') is-invalid @enderror" id="category_id"
              name="category_id">
              <option value="" {{ !$from->category_id ? 'selected' : '' }}>-- Pilih Kategori --</option>
              @foreach ($categories as $category)
                <option value="{{ $category->id }}"
                  {{ old('category_id', $from->category_id) == $category->id ? 'selected' : '' }}>
                  {{ $category->name }}
                </option>
              @endforeach
            </select>
            @error('category_id')
              <span class="text-danger">
                {{ $message }}
              </span>
            @enderror
          </div>
          <div class="form-group">
            <label for="description">Deskripsi</label>
            <input class="form-control @error('description') is-invalid @enderror" id="description" name="description"
              type="text" value="{{ old('description', $from->description) }}" autofocus placeholder="Deskripsi">
            @error('description')
              <span class="text-danger">
                {{ $message }}
              </span>
            @enderror
          </div>
          <div class="form-group">
            <label for="amount">Jumlah</label>
            <input class="form-control col-md-5 @error('amount') is-invalid @enderror text-right" id="amount"
              name="amount" type="text" value="{{ old('amount', format_number(abs($from->amount))) }}"
              placeholder="Jumlah">
            @error('amount')
              <span class="text-danger">
                {{ $message }}
              </span>
            @enderror
          </div>
          <div class="form-group">
            <label for="notes">Keterangan</label>
            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" cols="30"
              rows="4">{{ old('notes', $from->notes) }}</textarea>
            @error('notes')
              <span class="text-danger">
                {{ $message }}
              </span>
            @enderror
          </div>
          <div class="mt-4">
            <button class="btn btn-primary mr-1" type="submit"><i class="fas fa-save mr-1"></i> Simpan</button>
            <a class="btn btn-default" href="{{ url('cash-transaction/') }}"
              onclick="return confirm('Batalkan perubahan?')"><i class="fas fa-cancel mr-1"></i>Batal</a>
          </div>
        </div>
      </div>
    </div>
  </div>
@endSection

@section('footscript')
  <script>
    $(document).ready(function() {
      Inputmask("decimal", Object.assign({
        allowMinus: true
      }, INPUTMASK_OPTIONS)).mask("#amount");
    });

    $('.select2').select2();

    $('#cancel_button').click(function() {
      $('#category-dialog').modal('hide');
    });

    $('#category-form').submit(function(e) {
      e.preventDefault();
      let frm = $(this);
      $.ajax({
        type: frm.attr('method'),
        url: frm.attr('action'),
        data: frm.serialize(),
        success: function(data) {
          let category = data.data;
          var newOption = new Option(category.name, category.id, true, true);
          $('#category_id').append(newOption).trigger('change');
          toastr["info"](data.message);
          frm.trigger("reset");
          $('#category-dialog').modal('hide');
        },
        error: function(data) {
          toastr["error"]('Terdapat kesalahan saat menambahkan kategori.');
        },
      });
    });
  </script>
@endSection

@section('modal')
  @include('pages.cash-transaction.category-form')
@endsection
