<div class="family-member-container mb-3 border p-3 rounded">
    <h5>Maklumat Ahli Keluarga <span class="family-number"></span></h5>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label>Nama Ahli Keluarga</label>
                <input type="text" name="family_name[]" class="form-control" required>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label>No. Kad Pengenalan</label>
                <input type="text" name="family_ic[]" class="form-control" pattern="\d{6}-\d{2}-\d{4}" placeholder="000000-00-0000" required>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label>Hubungan</label>
                <select name="family_relationship[]" class="form-control" required>
                    <option value="">Pilih Hubungan</option>
                    <option value="Spouse">Pasangan</option>
                    <option value="Child">Anak</option>
                    <option value="Parent">Ibu/Bapa</option>
                    <option value="Sibling">Adik-beradik</option>
                </select>
            </div>
        </div>
        <div class="col-md-1">
            <button type="button" class="btn btn-danger remove-family-member mt-4">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    </div>
</div> 