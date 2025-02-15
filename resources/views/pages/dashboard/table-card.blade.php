<div class="card">
  <div class="card-header border-0">
    <h3 class="card-title">{{ $title }}</h3>
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
            <th>
              <span class="d-block d-md-none">Rincian</span>
              <span class="d-none d-md-block">ID</span>
            </th>
            <th class="d-none d-md-table-cell">Tanggal</th>
            <th class="d-none d-md-table-cell">Akun</th>
            <th class="d-none d-md-table-cell">Kategori</th>
            <th class="d-none d-md-table-cell">Uraian</th>
            <th>Jumlah</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($items as $item)
            <tr>
              <td>
                <span class="d-none d-md-block">{{ $item->idFormatted() }}</span>
                <div class="d-block d-md-none">
                  <div><i class="fa fa-calendar mr-2"></i>#{{ $item->idFormatted() }} - <i
                      class="fa fa-date mr-2"></i>{{ format_date($item->date) }}</div>
                  <div><i class="fa fa-wallet mr-2"></i>{{ $item->account->name }}</div>
                  <div><i class="fa fa-tag mr-2"></i>{{ optional($item->category)->name ?? 'Tanpa Kategori' }}
                  </div>
                  <div><i class="fa fa-note-sticky mr-2"></i>{{ $item->description }}</div>
                </div>
              </td>
              <td class="d-none d-md-table-cell text-nowrap">{{ format_date($item->date) }}</td>
              <td class="d-none d-md-table-cell">{{ $item->account->name }}</td>
              <td class="d-none d-md-table-cell">
                {{ $item->category ? $item->category->name : '-Tanpa Kategori-' }}
              </td>
              <td class="d-none d-md-table-cell">{{ $item->description }}</td>
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
