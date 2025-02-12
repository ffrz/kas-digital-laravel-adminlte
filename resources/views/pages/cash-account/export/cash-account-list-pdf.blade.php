<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar Akun Kas</title>
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
  <h2 style="margin:0;text-align:center;">Daftar Akun Kas</h2>
  <div style="text-align:center;">
    <small>Dibuat oleh <b>{{ Auth::user()->username }}</b> pada
      {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y H:i:s') }} - {{ env('APP_NAME') }}
      v{{ env('APP_VERSION') }}</small>
  </div>
  <table>
    <thead>
      <tr>
        <th>No</th>
        <th>Nama Akun</th>
        <th>Jenis</th>
        <th>Rincian</th>
        <th>Status</th>
        <th>Saldo (Rp.)</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($accounts as $index => $account)
        <tr>
          <td align="right">{{ $index + 1 }}</td>
          <td>{{ $account->name }}</td>
          <td>{{ $account->type == 'cash' ? 'Kas' : 'Bank' }}</td>
          <td>
            @if ($account->type == 'bank')
              {{ $account->bank }} a.n {{ $account->bank_account_name }}
              <br>{{ $account->bank_account_number }}
            @endif
          </td>
          <td>{{ $account->active ? 'Aktif' : 'Non Aktif' }}</td>
          <td align="right">Rp. {{ format_number($account->balance) }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</body>

</html>
