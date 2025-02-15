@extends('layouts.default', [
    'title' => 'Dashboard',
    'nav_active' => 'dashboard',
])

@section('right-menu')
  <li class="nav-item">
    <button class="btn btn-default position-relative" data-toggle="modal" data-target="#filter-dialog" title="Saring">
      <i class="fa fa-filter"></i>
      @if ($filter_active)
        <span class="badge badge-warning position-absolute start-100 translate-middle top-0">!</span>
      @endif
    </button>
  </li>
@endSection

@section('content')
  <form method="GET" action="?">
    <div class="modal fade" id="filter-dialog">
      <div class="modal-dialog modal-md">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Penyaringan</h4>
            <button class="close" data-dismiss="modal" type="button" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="form-group row">
              <label class="col-form-label col-sm-4" for="account_id">Akun:</label>
              <div class="col-sm-8">
                <select class="form-control custom-select select2" id="account_id" name="account_id">
                  <option value="all" {{ !$filter['account_id'] ? 'selected' : '' }}>Semua Akun</option>
                  @foreach ($accounts as $account)
                    <option value="{{ $account->id }}" {{ $filter['account_id'] == $account->id ? 'selected' : '' }}>
                      {{ $account->name }}
                    </option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="form-group row">
              <label class="col-form-label col-sm-4" for="period">Periode:</label>
              <div class="col-sm-8">
                <select class="form-control custom-select" name="period" id="period">
                  <option value="today" {{ $filter['period'] == 'today' ? 'selected' : '' }}>Hari Ini</option>
                  <option value="yesterday" {{ $filter['period'] == 'yesterday' ? 'selected' : '' }}>Kemarin</option>
                  <option value="this_week" {{ $filter['period'] == 'this_week' ? 'selected' : '' }}>Minggu Ini</option>
                  <option value="prev_week" {{ $filter['period'] == 'prev_week' ? 'selected' : '' }}>Minggu Kemarin
                  </option>
                  <option value="this_month" {{ $filter['period'] == 'this_month' ? 'selected' : '' }}>Bulan Ini</option>
                  <option value="prev_month" {{ $filter['period'] == 'prev_month' ? 'selected' : '' }}>Bulan Kemarin
                  </option>
                </select>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button class="btn btn-primary" type="submit"><i class="fas fa-check mr-2"></i> Terapkan</button>
            <button class="btn btn-default" name="action" type="submit" value="reset"><i
                class="fa fa-filter-circle-xmark"></i> Reset Penyaringan</button>
          </div>
        </div>
      </div>
    </div>
  </form>

  <section class="content">
    <div class="container-fluid">
      @if (!empty($data['active_cash_account_count']))
        <div class="row">
          <div clas="col-12">
            <h5 class="m-2 mb-3">Ringkasan Aktual</h5>
          </div>
        </div>
        <div class="row">
          <div class="col-12 col-md-4">
            <div class="small-box bg-blue">
              <div class="inner">
                <h4><sup style="font-size: 20px">Rp. </sup>{{ format_number($data['total_balance']) }}</h4>
                <p>Total Saldo Aktual</p>
              </div>
              <div class="icon">
                <i class="fa fa-hand-holding-dollar"></i>
              </div>
              <a href="cash-account?active=1" class="small-box-footer"><i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <div class="col-12 col-md-4">
            <div class="small-box bg-info">
              <div class="inner">
                <h4>{{ $data['active_cash_account_count'] }} Akun Kas</h4>
                <p>Akun Kas Aktif</p>
              </div>
              <div class="icon">
                <i class="fas fa-wallet"></i>
              </div>
              <a href="cash-account?active=1" class="small-box-footer">
                <i class="fas fa-arrow-circle-right"></i>
              </a>
            </div>
          </div>
          <div class="col-12 col-md-4">
            <div class="small-box bg-info">
              <div class="inner">
                <h4>{{ $data['active_user_count'] }} Pengguna</h4>
                <p>Pengguna Aktif</p>
              </div>
              <div class="icon">
                <i class="fas fa-user"></i>
              </div>
              <a href="user?active=1" class="small-box-footer">
                <i class="fas fa-arrow-circle-right"></i>
              </a>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Distribusi Akun Kas</h3>
                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                  </button>
                </div>
              </div>
              <div class="card-body">
                <canvas id="account-balance-distribution-chart"
                  style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div clas="col-12">
            <h5 class="m-2 mb-3">Ringkasan {{ $data['selected_period'] }} untuk {{ $data['selected_account_name'] }}
            </h5>
          </div>
        </div>
        <div class="row">
          <div class="col-12 col-md-4">
            <div class="small-box bg-green">
              <div class="inner">
                <h4><sup style="font-size: 20px">Rp. </sup>{{ format_number($data['total_income']) }}</h4>
                <p>Total Pemasukan</p>
              </div>
              <div class="icon">
                <i class="fa fa-arrow-right-to-bracket"></i>
              </div>
              <a href="cash-account?active=1" class="small-box-footer"><i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <div class="col-12 col-md-4">
            <div class="small-box bg-red">
              <div class="inner">
                <h4><sup style="font-size: 20px">Rp. </sup>{{ format_number($data['total_expense']) }}</h4>
                <p>Total Pengeluaran</p>
              </div>
              <div class="icon">
                <i class="fa fa-arrow-right-from-bracket"></i>
              </div>
              <a href="cash-account?active=1" class="small-box-footer"><i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <div class="col-12 col-md-4">
            <div class="small-box bg-warning">
              <div class="inner">
                <h4><sup style="font-size: 20px">Rp. </sup>{{ format_number($data['cash_balance']) }}</h4>
                <p>Arus Kas Bersih</p>
              </div>
              <div class="icon">
                <i class="fa fa-money-bill-wave"></i>
              </div>
              <a href="cash-account?active=1" class="small-box-footer"><i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-12 col-md-4">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Pemasukan vs Pengeluaran</h3>
                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                  </button>
                </div>
              </div>
              <div class="card-body">
                <canvas id="income-vs-expense-chart"
                  style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
              </div>
            </div>
          </div>
          <div class="col-12 col-md-4">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Pemasukan per Kategori</h3>
                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                  </button>
                </div>
              </div>
              <div class="card-body">
                <canvas id="income-by-category-chart"
                  style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
              </div>
            </div>
          </div>
          <div class="col-12 col-md-4">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Pengeluaran per Kategori</h3>
                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                  </button>
                </div>
              </div>
              <div class="card-body">
                <canvas id="expense-by-category-chart"
                  style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header border-0">
                <div class="d-flex justify-content-between">
                  <h3 class="card-title">Arus Kas</h3>
                  <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                      <i class="fas fa-minus"></i>
                    </button>
                  </div>
                </div>
              </div>
              <div class="card-body">
                <div class="position-relative mb-4">
                  <canvas id="cashflow-chart" height="200"></canvas>
                </div>
                <div class="d-flex justify-content-end flex-row">
                  <span class="mr-2">
                    <i class="fas fa-square text-success"></i> Pemasukan
                  </span>
                  <span>
                    <i class="fas fa-square text-danger"></i> Pengeluaran
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header border-0">
                <h3 class="card-title">5 Transaksi Terakhir</h3>
                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                  </button>
                </div>
              </div>
              <div class="card-body py-0">
                <div class="table-responsive p-0">
                  <table class="table-bordered table-striped table-sm table">
                    <thead>
                      <tr>
                        <th style="width:1%">ID</th>
                        <th style="width:1%">Tanggal</th>
                        <th>Akun</th>
                        <th>Kategori</th>
                        <th>Uraian</th>
                        <th>Jumlah</th>
                      </tr>
                    </thead>
                    <tbody>
                      @forelse ($data['recent_transactions'] as $item)
                        <tr>
                          <td class="text-nowrap text-right">{{ $item->idFormatted() }}</td>
                          <td class="text-nowrap">{{ format_date($item->date) }}</td>
                          <td>{{ $item->account->name }}</td>
                          <td>{{ $item->category ? $item->category->name : '-Tanpa Kategori-' }}</td>
                          <td>{{ $item->description }}</td>
                          <td class="{{ $item->amount > 0 ? 'text-success' : 'text-danger' }} text-right">
                            {{ ($item->amount > 0 ? '+' : '') . format_number($item->amount) }}</td>
                        </tr>
                      @empty
                        <tr class="empty">
                          <td colspan="6">Tidak ada rekaman untuk ditampilkan.</td>
                        </tr>
                      @endforelse
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-lg-6 col-12">
            <div class="card">
              <div class="card-header border-0">
                <h3 class="card-title">Top 5 Pemasukan</h3>
                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                  </button>
                </div>
              </div>
              <div class="card-body py-0">
                <div class="table-responsive p-0">
                  <table class="table-bordered table-striped table-sm table">
                    <thead>
                      <tr>
                        <th style="width:1%">ID</th>
                        <th style="width:1%">Tanggal</th>
                        <th>Akun</th>
                        <th>Kategori</th>
                        <th>Uraian</th>
                        <th>Jumlah</th>
                      </tr>
                    </thead>
                    <tbody>
                      @forelse ($data['top_incomes'] as $item)
                        <tr>
                          <td class="text-nowrap text-right">{{ $item->idFormatted() }}</td>
                          <td class="text-nowrap">{{ format_date($item->date) }}</td>
                          <td>{{ $item->account->name }}</td>
                          <td>{{ $item->category ? $item->category->name : '-Tanpa Kategori-' }}</td>
                          <td>{{ $item->description }}</td>
                          <td class="{{ $item->amount > 0 ? 'text-success' : 'text-danger' }} text-right">
                            {{ ($item->amount > 0 ? '+' : '') . format_number($item->amount) }}</td>
                        </tr>
                      @empty
                        <tr class="empty">
                          <td colspan="6">Tidak ada rekaman untuk ditampilkan.</td>
                        </tr>
                      @endforelse
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-6 col-12">
            <div class="card">
              <div class="card-header border-0">
                <h3 class="card-title">Top 5 Pengeluaran</h3>
                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                  </button>
                </div>
              </div>
              <div class="card-body py-0">
                <div class="table-responsive p-0">
                  <table class="table-bordered table-striped table-sm table">
                    <thead>
                      <tr>
                        <th style="width:1%">ID</th>
                        <th style="width:1%">Tanggal</th>
                        <th>Akun</th>
                        <th>Kategori</th>
                        <th>Uraian</th>
                        <th>Jumlah</th>
                      </tr>
                    </thead>
                    <tbody>
                      @forelse ($data['top_expenses'] as $item)
                        <tr>
                          <td class="text-nowrap text-right">{{ $item->idFormatted() }}</td>
                          <td class="text-nowrap">{{ format_date($item->date) }}</td>
                          <td>{{ $item->account->name }}</td>
                          <td>{{ $item->category ? $item->category->name : '-Tanpa Kategori-' }}</td>
                          <td>{{ $item->description }}</td>
                          <td class="{{ $item->amount > 0 ? 'text-success' : 'text-danger' }} text-right">
                            {{ ($item->amount > 0 ? '+' : '') . format_number($item->amount) }}</td>
                        </tr>
                      @empty
                        <tr class="empty">
                          <td colspan="6">Tidak ada rekaman untuk ditampilkan.</td>
                        </tr>
                      @endforelse
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      @else
        <div class="row">
          <div clas="col-12">
            <h5 class="m-2 mb-3">Akun belum dibuat, silahkan <a href="{{ url('cash-account/edit/0') }}">buat akun</a> terlebih dahulu.</h5>
          </div>
        </div>
      @endif
    </div>
  </section>
@endsection
@section('footscript')
  <script src="{{ url('plugins/chart.js/Chart.min.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
  <script>
    /* global Chart:false */

    $(function() {
      'use strict'

      var ticksStyle = {
        fontColor: '#495057',
        fontStyle: 'bold'
      }

      var mode = 'index'
      var intersect = true

      var $cashflowChart = $('#cashflow-chart')
      // eslint-disable-next-line no-unused-vars
      var cashflowChart = new Chart($cashflowChart, {
        data: {
          labels: {!! json_encode($cashflow_chart_data['labels']) !!},
          datasets: [{
              type: 'line',
              data: {!! json_encode($cashflow_chart_data['incomes']) !!},
              backgroundColor: 'transparent',
              borderColor: '#00aa00',
              pointBorderColor: '#00dd00',
              pointBackgroundColor: '#00dd00',
              fill: false
              // pointHoverBackgroundColor: '#007bff',
              // pointHoverBorderColor    : '#007bff'
            },
            {
              type: 'line',
              data: {!! json_encode($cashflow_chart_data['expenses']) !!},
              backgroundColor: 'tansparent',
              borderColor: '#dd0000',
              pointBorderColor: '#ff0000',
              pointBackgroundColor: '#ff0000',
              fill: false
              // pointHoverBackgroundColor: '#ced4da',
              // pointHoverBorderColor    : '#ced4da'
            }
          ]
        },
        options: {
          maintainAspectRatio: false,
          tooltips: {
            mode: mode,
            intersect: intersect,
            callbacks: {
              label: function(tooltipItem, data) {
                let dataset = data.datasets[tooltipItem.datasetIndex];
                let value = dataset.data[tooltipItem.index];
                return formatRupiah(value);
              }
            }
          },
          hover: {
            mode: mode,
            intersect: intersect
          },
          legend: {
            display: false
          },
          scales: {
            yAxes: [{
              // display: false,
              gridLines: {
                display: true,
                lineWidth: '4px',
                color: 'rgba(0, 0, 0, .2)',
                zeroLineColor: 'transparent'
              },
              ticks: $.extend({
                beginAtZero: true,
                suggestedMax: 200,
                callback: function(value, index, values) {
                  return formatRupiah(value);
                }
              }, ticksStyle)
            }],
            xAxes: [{
              display: true,
              gridLines: {
                display: false
              },
              ticks: ticksStyle
            }]
          }
        }
      })

      var createDonutChart = (elementId, data) => {
        var el = $(elementId).get(0).getContext('2d');
        return new Chart(el, {
          type: 'doughnut',
          data: data,
          options: {
            maintainAspectRatio: false,
            responsive: true,
            tooltips: {
              callbacks: {
                label: function(tooltipItem, data) {
                  var dataset = data.datasets[tooltipItem.datasetIndex];
                  var value = dataset.data[tooltipItem.index];
                  return formatRupiah(value);
                }
              }
            }
          },
          plugins: {
            datalabels: {
              color: '#fff',
              anchor: 'center',
              align: 'center',
              font: {
                weight: 'bold',
                size: 14
              },
              formatter: function(value, context) {
                return formatRupiah(value);
              }
            }
          }
        })
      }

      let data = {!! json_encode($income_vs_expense_chart_data) !!};
      createDonutChart('#income-vs-expense-chart', {
        labels: data.labels,
        datasets: [{
          data: data.data,
          backgroundColor: ['#00aa00', '#dd0000'],
        }]
      });

      data = {!! json_encode($account_balance_distribution_chart_data) !!};
      createDonutChart('#account-balance-distribution-chart', {
        labels: data.labels,
        datasets: [{
          data: data.data,
          backgroundColor: generateUniqueColors(data.labels.length),
        }]
      });

      data = {!! json_encode($income_by_category_chart_data) !!};
      createDonutChart('#income-by-category-chart', {
        labels: data.labels,
        datasets: [{
          data: data.data,
          backgroundColor: generateUniqueColors(data.labels.length),
        }]
      });

      data = {!! json_encode($expense_by_category_chart_data) !!};
      createDonutChart('#expense-by-category-chart', {
        labels: data.labels,
        datasets: [{
          data: data.data,
          backgroundColor: generateUniqueColors(data.labels.length),
        }]
      });
    })

    // lgtm [js/unused-local-variable]
  </script>
@endSection
