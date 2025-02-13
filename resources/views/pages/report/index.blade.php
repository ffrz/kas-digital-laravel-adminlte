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
                <li><a href="{{ url('report/') }}">Laporan Rekapitulasi Pemasukan dan Pengeluaran</a></li>

                {{--
                    Laporan Arus Kas (Cash Flow) - Penting untuk Memantau Keuangan
                    Bisa disaring berdasarkan periode, kategori, akun
                    - Total pemasukan & pengeluaran dalam periode tertentu.
                    - Saldo awal dan saldo akhir periode tersebut.
                    - Bisa dikelompokkan berdasarkan kategori (opsional).
                --}}
                <li><a href="{{ url('report/') }}">Laporan Arus Kas</a></li>

                {{--
                    Laporan Pemasukan & Pengeluaran - Untuk Mengetahui Sumber Keuangan
                    Bisa disaring berdasarkan periode dan akun
                    Isi laporan:
                    - Total pemasukan dari berbagai sumber.
                    - Total pengeluaran berdasarkan kategori utama.
                --}}
                <li><a href="{{ url('report/') }}">Laporan Pemasukan & Pengeluaran</a></li>

                {{--
                    Laporan Saldo Akun - Untuk Melihat Uang di Setiap Akun
                    Isi laporan:
                    - Saldo akhir di setiap akun kas/bank.
                --}}
                <li><a href="{{ url('report/account-balance') }}">Laporan Saldo Akun</a></li>
              </ul>

              <h3>Laporan Rincian</h3>
              <ul>
                <li><a href="{{ url('report/detail') }}">Laporan Rincian Transaksi</a></li>
                <li><a href="{{ url('report/detail?type=income') }}">Laporan Rincian Pemasukan</a></li>
                <li><a href="{{ url('report/detail?type=expense') }}">Laporan Rincian Pengeluaran</a></li>
              </ul>

              <h3>Laporan Analitik dan Insight</h3>
              <ul>
                <li><a href="{{ url('report/') }}">Laporan Tren Keuangan</a></li>
                <li><a href="{{ url('report/') }}">Laporan Kategori Terbanyak</a></li>
                <li><a href="{{ url('report/') }}">Laporan Perbandingan Bulanan / Tahunan</a></li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
@endsection
