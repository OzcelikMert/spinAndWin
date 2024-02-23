<div class="card">
    <div class="card-body">
        <h5 class="card-title"><?= isset($formId) ? "" : "Yeni Ekle" ?></h5>
        <div class="row">
            <div class="col-md-12">
                <form id="<?= isset($formId) ? $formId : "addForm" ?>" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-12">
                            <label for="itemText" class="form-label">İsim*</label>
                            <input type="text" class="form-control" name="itemText" required>
                        </div>
                        <div class="col-md-6">
                            <label for="itemProbability" class="form-label">Olasılık*</label>
                            <input type="number" class="form-control" name="itemProbability" step="any" min="0" value="1" required>
                        </div>
                        <div class="col-md-6">
                            <label for="itemQty" class="form-label">Stok Miktarı*</label>
                            <input type="number" class="form-control" name="itemQty" min="0" value="1" required>
                        </div>
                        <div class="col-md-12">
                            <label for="itemImage" class="form-label">Resim</label>
                            <input class="form-control" type="file" id="itemImage" name="itemImage" accept="image/*">
                        </div>
                        <div class="col-md-12 mt-4">
                            <button type="submit" class="btn btn-success w-100"><?= isset($formId) ? "Güncelle" : "Ekle" ?></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>