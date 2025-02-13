@php
  use App\Models\Setting;
  $labels = [
      'incomes' => 'Pemasukan',
      'expenses' => 'Pengeluaran',
  ];
@endphp

@extends('layouts.print-report', [
    'title' => 'Laporan Pemasukan dan Pengeluaran',
])

@section('content')
  <h4 class="text-center">LAPORAN PEMASUKAN DAN PENGELUARAN</h4>
  <h6 class="text-center">Periode: {{ $period[0] . ' - ' . $period[1] }}</h6>

  @php
    $final_balance = 0;
  @endphp

  @foreach ($items as $key => $subitems)
    <h5 style="margin-top:20px;margin-bottom:5px;">{{ $labels[$key] }}</h5>
    <table class="report-table">
      <thead style="background:#08e;color:#fff;">
        <th>Nama Akun</th>
        <th>Kategori</th>
        <th>Jumlah</th>
      </thead>
      <tbody>
        @php $total = 0; @endphp
        @foreach ($subitems as $data)
          <tr style="vertical-align:top;">
            <td>{{ $data['account_name'] }}</td>
            <td>{{ $data['category_name'] }}</td>
            <td style="text-align:right">{{ format_number($data['total']) }}</td>
          </tr>
          @php
            $total += $data['total'];
          @endphp
        @endforeach
      </tbody>
      <tfoot style="background:#08e;color:#fff;">
        <tr>
          <th colspan="2" style="text-align:right">Jumlah</th>
          <th style="text-align:right">{{ format_number($total) }}</th>
        </tr>
      </tfoot>
    </table>
    @php $final_balance += $total @endphp
  @endforeach
  <h5 style="margin-top:20px;">Selisih Pengeluaran dan Pemasukan: Rp. {{ format_number($final_balance) }}</h5>
  <p class="mt-3">Dibuat oleh: {{ Auth::user()->fullname }} pada
    {{ Carbon\Carbon::now()->translatedFormat('l, d F Y H:i:s') }} - {{ env('APP_NAME') }}
    v{{ env('APP_VERSION_STR') }}</p>
@endSection
