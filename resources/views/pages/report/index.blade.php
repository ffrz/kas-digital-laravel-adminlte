@extends('layouts.default', [
    'title' => 'Laporan-laporan',
    'nav_active' => 'report',
])

@section('content')
  <section class="content">
    <div class="card">
      <div class="card-body">
        <div class="container-fluid">
          <div class="row">
            <div clas="col-12">
              <h3>Laporan Rekapitulasi</h3>
              <ul>
                <li><a href="{{ url('report/cash-flow') }}">Laporan Arus Kas</a></li>
                <li><a href="{{ url('report/income-expense') }}">Laporan Pemasukan & Pengeluaran</a></li>
              </ul>
              <h3>Laporan Rincian</h3>
              <ul>
                <li><a href="{{ url('report/detail') }}">Laporan Rincian Transaksi</a></li>
                <li><a href="{{ url('report/detail?type=income') }}">Laporan Rincian Pemasukan</a></li>
                <li><a href="{{ url('report/detail?type=expense') }}">Laporan Rincian Pengeluaran</a></li>
              </ul>
              {{-- <h3>Laporan Analitik dan Insight</h3>
              <ul>
                <li><a href="{{ url('report/') }}">Laporan Tren Keuangan</a></li>
                <li><a href="{{ url('report/') }}">Laporan Kategori Terbanyak</a></li>
                <li><a href="{{ url('report/') }}">Laporan Perbandingan Bulanan / Tahunan</a></li>
              </ul> --}}
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
@endsection
