<?php 
    $title = 'Pengurusan Pinjaman';
    require_once '../app/views/layouts/header.php';
    
    // Verify admin access
    if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
        header('Location: /auth/login');
        exit;
    }
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Pengurusan Pinjaman</h5>
                </div>
                <div class="card-body">
                    <form action="/admin/process-loan/<?= $loan['id'] ?>" method="POST">
                        <!-- Date Received -->
                        <div class="mb-3">
                            <label class="form-label required">Tarikh Permohonan Diterima</label>
                            <input type="date" name="date_received" class="form-control" required>
                        </div>

                        <!-- Financial Details -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jumlah Syer dan Yuran Terkumpul (RM)</label>
                                <input type="number" name="total_shares" class="form-control" 
                                       step="0.01" min="0" max="99999.99">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jumlah Baki Pinjaman (RM)</label>
                                <input type="number" name="loan_balance" class="form-control" 
                                       step="0.01" min="0" max="99999.99" readonly
                                       value="<?= $loan['amount'] ?? 0 ?>">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Balik Pulih Kenderaan (RM)</label>
                                <input type="number" name="vehicle_repair" class="form-control" 
                                       step="0.01" min="0" max="99999.99">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Karnival Muslim Istimewa (RM)</label>
                                <input type="number" name="carnival" class="form-control" 
                                       step="0.01" min="0" max="99999.99">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Lain-lain (Nyatakan)</label>
                            <input type="text" name="others_description" class="form-control mb-2" 
                                   placeholder="Keterangan">
                            <input type="number" name="others_amount" class="form-control" 
                                   step="0.01" min="0" max="99999.99" 
                                   placeholder="Jumlah (RM)">
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Jumlah Potongan Koperasi (RM)</label>
                            <input type="number" name="total_deduction" class="form-control" 
                                   step="0.01" min="0" max="99999.99">
                        </div>
<!-- Decision -->
<div class="card bg-light mb-4">
                            <div class="card-body">
                                <h6 class="card-title">Keputusan Mesyuarat Jawatan Kuasa Perniagaan</h6>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="decision" 
                                           value="approved" id="approved" required>
                                    <label class="form-check-label" for="approved">
                                        Diluluskan
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="decision" 
                                           value="rejected" id="rejected" required>
                                    <label class="form-check-label" for="rejected">
                                        Tidak Diluluskan
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                Simpan Keputusan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?>    