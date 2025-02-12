<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar Transaksi</title>
  <style>
    body {
      font-family: sans-serif;
      font-size: 10pt;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }

    th,
    td {
      border: 1px solid black;
      padding: 2px 5px;
    }

    th {
      background-color: #f2f2f2;
      text-align: center;
    }
  </style>
</head>

<body>
  <h2 style="margin:0;text-align:center;">Daftar Transaksi</h2>
  <div style="text-align:center;">
    <small>Dibuat oleh <b>{{ Auth::user()->username }}</b> pada
      {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y H:i:s') }} - {{ env('APP_NAME') }}
      v{{ env('APP_VERSION') }}</small>
  </div>
  <table>
    <thead>
      <tr>
        <th>No</th>
        <th>Tanggal</th>
        <th>Jenis</th>
        <th>Akun</th>
        <th>Kategori</th>
        <th>Deskripsi</th>
        <th>Jumlah (Rp.)</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($items as $index => $item)
        <tr>
          <td align="right">{{ $index + 1 }}</td>
          <td>{{ format_date($item->date) }}</td>
          <td>{{ $item->amount > 0 ? 'Pemasukan' : 'Pengeluaran' }}</td>
          <td>{{ $item->account->name }}</td>
          <td>{{ $item->category->name }}</td>
          <td>{{ $item->description }}</td>
          <td align="right">{{ format_number($item->amount) }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</body>

</html>
