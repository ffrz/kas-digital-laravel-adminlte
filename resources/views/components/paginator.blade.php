<div class="row">
  <div class="col-md-12 d-flex justify-content-center">
    <p class="text-muted">Menampilkan {{ $items->count() }} rekaman dari total {{ $items->total() }} rekaman.</p>
  </div>
  <div class="col-md-12 d-flex justify-content-center">
    {{ $items->withQueryString()->onEachSide(1)->links('components.bootstrap-paginator') }}
  </div>
</div>
