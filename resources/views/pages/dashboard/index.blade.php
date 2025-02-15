@extends('layouts.default', [
    'title' => 'Dashboard',
    'nav_active' => 'dashboard',
])

@section('right-menu')
  <li class="nav-item">
    <button class="btn btn-default position-relative" data-toggle="modal" data-target="#filter-dialog" title="Saring">
      <i class="fa fa-filter"></i>
      <span class="badge badge-warning position-absolute start-100 translate-middle top-0">!</span>
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
            @include ('pages.dashboard.small-box', [
                'bg_color' => 'bg-blue',
                'label' => 'Total Saldo Aktual',
                'data' => 'Rp. ' . format_number($data['total_balance']),
                'icon' => 'fa-hand-holding-dollar',
                'url' => url('cash-account?active=1'),
            ])
          </div>
          <div class="col-12 col-md-4">
            @include ('pages.dashboard.small-box', [
                'bg_color' => 'bg-info',
                'label' => 'Akun Kas Aktif',
                'data' => $data['active_cash_account_count'] . ' Akun Kas',
                'icon' => 'fa-wallet',
                'url' => url('cash-account?active=1'),
            ])
          </div>
          <div class="col-12 col-md-4">
            @include ('pages.dashboard.small-box', [
                'bg_color' => 'bg-info',
                'label' => 'Pengguna Aktif',
                'data' => $data['active_user_count'] . ' Pengguna',
                'icon' => 'fa-user',
                'url' => url('user?active=1'),
            ])
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            @include('pages.dashboard.chart-card', [
                'title' => 'Distribusi Akun Kas',
                'element_id' => 'account-balance-distribution-chart',
            ])
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
            @include ('pages.dashboard.small-box', [
                'bg_color' => 'bg-green',
                'label' => 'Total Pemasukan',
                'data' => 'Rp. ' . format_number($data['total_income']),
                'icon' => 'fa-arrow-right-to-bracket',
                'url' => url('cash-account?active=1'),
            ])
          </div>
          <div class="col-12 col-md-4">
            @include ('pages.dashboard.small-box', [
                'bg_color' => 'bg-red',
                'label' => 'Total Pengeluaran',
                'data' => 'Rp. ' . format_number($data['total_expense']),
                'icon' => 'fa-arrow-right-from-bracket',
                'url' => url('cash-account?active=1'),
            ])
          </div>
          <div class="col-12 col-md-4">
            @include ('pages.dashboard.small-box', [
                'bg_color' => 'bg-warning',
                'label' => 'Arus Kas Bersih',
                'data' => 'Rp. ' . format_number($data['cash_balance']),
                'icon' => 'fa-money-bill-wave',
                'url' => url('cash-account?active=1'),
            ])
          </div>
        </div>

        <div class="row">
          <div class="col-12 col-md-4">
            @include('pages.dashboard.chart-card', [
                'title' => 'Pemasukan vs Pengeluaran',
                'element_id' => 'income-vs-expense-chart',
            ])
          </div>
          <div class="col-12 col-md-4">
            @include('pages.dashboard.chart-card', [
                'title' => 'Pemasukan per Kategori',
                'element_id' => 'income-by-category-chart',
            ])
          </div>
          <div class="col-12 col-md-4">
            @include('pages.dashboard.chart-card', [
                'title' => 'Pengeluaran per Kategori',
                'element_id' => 'expense-by-category-chart',
            ])
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
            @include('pages.dashboard.table-card', [
                'title' => '5 Pengeluaran Terakhir',
                'items' => $data['recent_transactions'],
            ])
          </div>
        </div>

        <div class="row">
          <div class="col-lg-6 col-12">
            @include('pages.dashboard.table-card', [
                'title' => 'Top 5 Pemasukan',
                'items' => $data['top_incomes'],
            ])
          </div>
          <div class="col-lg-6 col-12">
            @include('pages.dashboard.table-card', [
                'title' => 'Top 5 Pengeluaran',
                'items' => $data['top_expenses'],
            ])
          </div>
        </div>
      @else
        <div class="row">
          <div clas="col-12">
            <h5 class="m-2 mb-3">Akun belum dibuat, silahkan <a href="{{ url('cash-account/edit/0') }}">buat akun</a>
              terlebih dahulu.</h5>
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
