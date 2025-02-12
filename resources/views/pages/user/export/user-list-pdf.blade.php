<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar Pengguna</title>
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
  <h2 style="margin:0;text-align:center;">Daftar Pengguna</h2>
  <div style="text-align:center;">
    <small>Dibuat oleh <b>{{ Auth::user()->username }}</b> pada {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y H:i:s') }} - {{ env('APP_NAME') }} v{{ env('APP_VERSION') }}</small>
</div>
  <table>
    <thead>
      <tr>
        <th>No</th>
        <th>Username</th>
        <th>Nama Lengkap</th>
        <th>Hak Akses</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($users as $index => $user)
        <tr>
          <td align="right">{{ $index + 1 }}</td>
          <td>{{ $user->username }}</td>
          <td>{{ $user->fullname }}</td>
          <td>{{ $user->is_admin ? 'Admin' : 'User' }}</td>
          <td>{{ $user->is_active ? 'Aktif' : 'Non Aktif' }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</body>

</html>
