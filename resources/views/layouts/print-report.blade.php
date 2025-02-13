@php use App\Models\Setting; @endphp
<!DOCTYPE html>
<html class="page-a4" lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $title }}</title>
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback" rel="stylesheet">
  <link href="/plugins/fontawesome-free/css/all.min.css" rel="stylesheet">
  <link href="/dist/css/adminlte.min.css?v=3.2.0" rel="stylesheet">
  <link href="/assets/css/report.css" rel="stylesheet">
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
      text-align: center;
    }

    h1, h2, h3, h4, h5, h6 { margin: 0; }
  </style>
  @vite([])
</head>

<body>
  <div class="wrapper">
    <section class="report">
      <div class="page">
        @yield('content')
      </div>
    </section>
  </div>
  <script>
    //window.addEventListener("load", window.print());
  </script>
</body>

</html>
