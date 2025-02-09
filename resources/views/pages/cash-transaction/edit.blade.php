@php
  $title = ($item->id ? 'Edit' : 'Tambah') . ' Transaksi';
@endphp

@extends('layouts.default', [
    'title' => $title,
    'menu_active' => 'finance',
    'nav_active' => 'cash-transaction',
    'form_action' => url('cash-transaction/edit/' . (int) $item->id),
])

@section('content')
  <div class="row">
    <div class="col-md-6">
      <div class="card card-primary">
        <div class="card-body">
          <div class="form-group">
            <label for="account_id">Akun / Rekening</label>
            <select class="custom-select select2 @error('account_id') is-invalid @enderror" id="account_id"
              name="account_id">
              <option value="" {{ !$item->account_id ? 'selected' : '' }}>-- Pilih Akun --</option>
              @foreach ($accounts as $account)
                <option value="{{ $account->id }}"
                  {{ old('account_id', $item->account_id) == $account->id ? 'selected' : '' }}>
                  {{ $account->name }}
                </option>
              @endforeach
            </select>
            @error('account_id')
              <span class="text-danger">
                {{ $message }}
              </span>
            @enderror
          </div>
          <div class="form-group">
            <label class="col-form-label" for="date">Tanggal:</label>
            <input class="form-control @error('date') is-invalid @enderror" id="date" name="date" type="date"
              value="{{ old('date', $item->date) }}" autofocus>
            @error('date')
              <span class="text-danger">
                {{ $message }}
              </span>
            @enderror
          </div>
          <div class="form-group">
            <label class="col-form-label" for="type">Jenis Transaksi</label>
            <div class="form-group clearfix">
              <div class="icheck-primary d-inline mr-2">
                <input id="radioPrimary1" name="type" type="radio" value="income"
                  {{ old('type', $item->type) == 'income' ? 'checked' : '' }}>
                <label for="radioPrimary1">Pemasukan
                </label>
              </div>
              <div class="icheck-primary d-inline mr-2">
                <input id="radioPrimary2" name="type" type="radio" value="expense"
                  {{ old('type', $item->type) == 'expense' ? 'checked' : '' }}>
                <label for="radioPrimary2">Pengeluaran
                </label>
              </div>
            </div>
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
              <option value="" {{ !$item->category_id ? 'selected' : '' }}>-- Pilih Kategori --</option>
              @foreach ($categories as $category)
                <option value="{{ $category->id }}"
                  {{ old('category_id', $item->category_id) == $category->id ? 'selected' : '' }}>
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
              type="text" value="{{ old('description', $item->description) }}" autofocus placeholder="Deskripsi">
            @error('description')
              <span class="text-danger">
                {{ $message }}
              </span>
            @enderror
          </div>
          <div class="form-group">
            <label for="amount">Jumlah</label>
            <input class="form-control col-md-5 @error('amount') is-invalid @enderror text-right" id="amount"
              name="amount" type="text" value="{{ old('amount', format_number(abs($item->amount))) }}"
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
              rows="4">{{ old('notes', $item->notes) }}</textarea>
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
