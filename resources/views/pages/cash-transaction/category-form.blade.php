<form id="category-form" method="POST" action="{{ url('ajax/add-cash-transaction-category') }}">
  @csrf
  <div class="modal fade" id="category-dialog">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Tambah Kategori</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="name">Nama Kategori</label>
            <input type="text" class="form-control" autofocus id="name" placeholder="Masukkan nama kategori" name="name" required>
          </div>
          <div class="form-group">
            <label for="description">Deskripsi</label>
            <input type="text" class="form-control" id="description" placeholder="Deskripsi" name="description">
          </div>
        </div>
        <div class="modal-footer justify-content-center">
          <button type="submit" class="btn btn-primary"><i class="fas fa-check mr-1"></i> Simpan</button>
          <button id="cancel_button" type="button" class="btn btn-default"><i class="fa fa-xmark mr-1"></i> Batal</button>
        </div>
      </div>
    </div>
  </div>
</form>
