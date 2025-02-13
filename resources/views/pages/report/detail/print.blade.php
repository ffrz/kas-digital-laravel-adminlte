@php
  use App\Models\Setting;
@endphp

@extends('layouts.print-report', [
    'title' => 'Laporan Rincian Transaksi' . ($type === 'income' ? 'Pemasukan' : ($type === 'expense' ? 'Pengeluaran' : '')),
])

@section('content')
  <h4 class="text-center">LAPORAN RINCIAN TRANSAKSI
    {{ $type === 'income' ? 'PEMASUKAN' : ($type === 'expense' ? 'PENGELUARAN' : '') }}</h4>
  @if ($account)
    <h5 class="text-center">{{ $account->name }}<h5>
  @endif
  <h6 class="text-center">Periode: {{ $period[0] . ' - ' . $period[1] }}</h6>

  <table class="report-table">
    <thead style="background:#08e;color:#fff;">
      <th>No</th>
      <th>Tanggal</th>
      @if (!$account)
        <th>Akun</th>
      @endif
      <th>Kategori</th>
      <th>Deskripsi</th>
      @if ($type !== 'income')
        <th>Pemasukan</th>
      @endif
      @if ($type !== 'expense')
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
          <td align=right>{{ $num + 1 }}</td>
          <td align=right>{{ format_date($item->date) }}</td>
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
            <td align=right>{{ format_number(abs($item->amount)) }}</td>
          @else
            <td align=right>{{ $item->amount > 0 ? format_number(abs($item->amount)) : '-' }}</td>
            <td align=right>{{ $item->amount < 0 ? format_number(abs($item->amount)) : '-' }}</td>
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
      <th colspan="{{ $account ? 4 : 5 }}" align=right>Total</th>
      @if ($type !== 'all')
        <th align=right>{{ format_number($total_amount) }}</th>
      @else
        <th align=right>{{ format_number($total_income) }}</th>
        <th align=right>{{ format_number($total_expense) }}</th>
      @endif
    </tfoot>
  </table>
  <p class="mt-3">Dibuat oleh: {{ Auth::user()->fullname }} pada
    {{ Carbon\Carbon::now()->translatedFormat('l, d F Y H:i:s') }} - {{ env('APP_NAME') }}
    v{{ env('APP_VERSION_STR') }}</p>
@endSection
