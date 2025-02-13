@php
  use App\Models\Setting;
@endphp

@extends('layouts.print-report', [
    'title' => 'Laporan Arus Kas',
])

@section('content')
  <h4 style="text-align:center;">LAPORAN ARUS KAS</h4>
  <h5 style="text-align:center;">Periode: {{ $period[0] . ' - ' . $period[1] }}</h5>
  <table class="report-table">
    <thead style="background:#08e;color:#fff;">
      <th>No</th>
      <th>Nama Akun</th>
      <th>Saldo Awal</th>
      <th>Pemasukan</th>
      <th>Pengeluaran</th>
      <th>Saldo Akhir</th>
    </thead>
    <tbody>
      @php
        $total_initial_balance = 0;
        $total_income = 0;
        $total_expense = 0;
        $total_final_balance = 0;
      @endphp
      @foreach ($items as $index => $account)
        <tr style="vertical-align:top;">
          <td style="text-align:right">{{ $index + 1 }}</td>
          <td>{{ $account->name }}</td>
          <td style="text-align:right">{{ format_number($account->initial_balance) }}</td>
          <td style="text-align:right">{{ format_number($account->income) }}</td>
          <td style="text-align:right">{{ format_number(abs($account->expense)) }}</td>
          <td style="text-align:right">{{ format_number($account->final_balance) }}</td>
        </tr>
        @php
          $total_initial_balance += $account->initial_balance;
          $total_income += $account->income;
          $total_expense += abs($account->expense);
          $total_final_balance += $account->final_balance;
        @endphp
      @endforeach
    </tbody>
    <tfoot style="background:#08e;color:#fff;">
      <tr>
        <th colspan="2" style="text-align:right">Total</th>
        <th style="text-align:right">{{ format_number($total_initial_balance) }}</th>
        <th style="text-align:right">{{ format_number($total_income) }}</th>
        <th style="text-align:right">{{ format_number($total_expense) }}</th>
        <th style="text-align:right">{{ format_number($total_final_balance) }}</th>
      </tr>
    </tfoot>
  </table>
  <p class="mt-3">Dibuat oleh: {{ Auth::user()->fullname }} pada
    {{ Carbon\Carbon::now()->translatedFormat('l, d F Y H:i:s') }} - {{ env('APP_NAME') }}
    v{{ env('APP_VERSION_STR') }}</p>
@endSection
