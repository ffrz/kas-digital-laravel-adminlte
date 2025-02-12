@extends('layouts.default', [
    'title' => 'Laporan-laporan',
    'nav_active' => 'report',
])


@section('content')
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div clas="col-12">
          <ul>
            <li><a href="#">Laporan 1</a></li>
            <li><a href="#">Laporan 2</a></li>
            <li><a href="#">Laporan 3</a></li>
          </ul>
        </div>
      </div>
    </div>
  </section>
@endsection
