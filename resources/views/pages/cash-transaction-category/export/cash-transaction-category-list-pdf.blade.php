<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar Kategori Transaksi</title>
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
  <h2 style="margin:0;text-align:center;">Daftar Kategori Transaksi</h2>
  <div style="text-align:center;">
    <small>Dibuat oleh <b>{{ Auth::user()->username }}</b> pada
      {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y H:i:s') }} - {{ env('APP_NAME') }}
      v{{ env('APP_VERSION') }}</small>
  </div>
  <table>
    <thead>
      <tr>
        <th>No</th>
        <th>Jenis</th>
        <th>Nama</th>
        <th>Deskripsi</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($categories as $index => $category)
        <tr>
          <td align="right">{{ $index + 1 }}</td>
          <td>{{ $category->type == 'income' ? 'Pemasukan' : 'Pengeluaran' }}</td>
          <td>{{ $category->name }}</td>
          <td>{{ $category->description }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</body>

</html>
