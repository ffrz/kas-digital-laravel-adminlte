@extends('layouts.default', [
    'title' => 'Laporan Pemasukan dan Pengeluaran',
    'nav_active' => 'report',
])

@section('content')
  <form method="GET" action="?">
    <section class="content">
      <div class="card">
        <div class="card-body">
          <div class="container-fluid">
            <div class="row">
              <div clas="col-12">
                <h5>Laporan Pemasukan dan Pengeluaran</h5>
              </div>
            </div>
            <div class="form-group row">
              <label class="col-form-label col-sm-4" for="period">Periode:</label>
              <div class="col-sm-8">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">
                      <i class="far fa-calendar-alt"></i>
                    </span>
                  </div>
                  <input class="form-control float-right" id="report-period" type="text" name="period">
                </div>
                @error('period')
                  <span class="text-danger">
                    {{ $message }}
                  </span>
                @enderror
              </div>
            </div>
            <div class="form-group row">
                <label class="col-form-label col-sm-4" for="format">Format Dokumen:</label>
                <div class="col-sm-8">
                  <select class="form-control custom-select select2" id="format" name="format">
                    <option value="html">HTML (WEB)</option>
                    <option value="pdf">PDF</option>
                  </select>
                </div>
              </div>
            <div class="form-group row mt-4">
              <button class="btn btn-sm btn-primary" type="submit" title="Cetak Laporan">
                <i class="fa fa-print mr-2"></i> Cetak Laporan
              </button>
            </div>
          </div>
        </div>
    </section>
  </form>
@endsection

@section('footscript')
  <script>
    $(document).ready(function() {
      $('#report-period').daterangepicker({
        locale: {
          format: DATE_FORMAT
        }
      });
    });
  </script>
@endsection
