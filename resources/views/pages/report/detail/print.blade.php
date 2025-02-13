@php
  use App\Models\Setting;
@endphp

@extends('layouts.print-report', [
    'title' => 'Laporan Rincian Transaksi' . ($type === 'income' ? 'Pemasukan' : ($type === 'expense' ? 'Pengeluaran' : '')),
])

@section('content')
  <h4 style="text-align:center">LAPORAN RINCIAN TRANSAKSI
    {{ $type === 'income' ? 'PEMASUKAN' : ($type === 'expense' ? 'PENGELUARAN' : '') }}</h4>
  @if ($account)
    <h5 style="text-align:center">{{ $account->name }}<h5>
  @endif
  <h5 style="text-align:center">Periode: {{ $period[0] . ' - ' . $period[1] }}</h5>
  <table class="report-table">
    <thead style="background:#08e;color:#fff;">
      <th>No</th>
      <th>Tanggal</th>
      @if (!$account)
        <th>Akun</th>
      @endif
      <th>Kategori</th>
      <th>Deskripsi</th>
      @if ($type == 'income' || $type == 'all')
        <th>Pemasukan</th>
      @endif
      @if ($type == 'expense' || $type == 'all')
        <th>Pengeluaran</th>
      @endif
    </thead>
    <tbody>
      @php
        $total_amount = 0;
        $total_income = 0;
        $total_expense = 0;
      @endphp
      @forelse ($items as $num => $item)
        <tr style="vertical-align:top;">
          <td style="text-align:right">{{ $num + 1 }}</td>
          <td style="text-align:right">{{ format_date($item->date) }}</td>
          @if (!$account)
            <td>{{ $item->account->name }}</td>
          @endif
          <td>{{ $item->category ? $item->category->name : '-' }}</td>
          <td>
            {{ $item->description }}
            @if ($item->notes)
              <p>{{ $item->notes }}</p>
            @endif
          </td>
          @if ($type !== 'all')
            <td style="text-align:right">{{ format_number(abs($item->amount)) }}</td>
          @else
            <td style="text-align:right">{{ $item->amount > 0 ? format_number(abs($item->amount)) : '-' }}</td>
            <td style="text-align:right">{{ $item->amount < 0 ? format_number(abs($item->amount)) : '-' }}</td>
          @endif
        </tr>
        @if ($type !== 'all')
          @php $total_amount += abs($item->amount) @endphp
        @else
          @php $total_income += $item->amount > 0 ? abs($item->amount) : 0 @endphp
          @php $total_expense += $item->amount < 0 ? abs($item->amount) : 0 @endphp
        @endif
      @empty
        <tr>
          <td colspan="{{ $account ? 5 : 6 }}" class="font-italic text-muted text-center">Tidak ada rekaman</td>
        </tr>
      @endforelse
    </tbody>
    <tfoot style="background:#08e;color:#fff;">
      <th colspan="{{ $account ? 4 : 5 }}" style="text-align:right">Jumlah</th>
      @if ($type !== 'all')
        <th style="text-align:right">{{ format_number($total_amount) }}</th>
      @else
        <th style="text-align:right">{{ format_number($total_income) }}</th>
        <th style="text-align:right">{{ format_number($total_expense) }}</th>
      @endif
    </tfoot>
  </table>
  <p class="mt-3">Dibuat oleh: {{ Auth::user()->fullname }} pada
    {{ Carbon\Carbon::now()->translatedFormat('l, d F Y H:i:s') }} - {{ env('APP_NAME') }}
    v{{ env('APP_VERSION_STR') }}</p>
@endSection
