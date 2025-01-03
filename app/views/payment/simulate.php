<?php 
    $title = 'Simulasi Pembayaran';
    require_once '../app/views/layouts/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body p-5">
                    <h4 class="text-center mb-4">Simulasi Pembayaran</h4>
                    
                    <form action="/payment/callback" method="POST">
                        <input type="hidden" name="payment_id" value="<?= $paymentId ?>">
                        
                        <div class="mb-4 text-center">
                            <div class="btn-group" role="group">
                                <input type="radio" class="btn-check" name="status" id="success" value="success" checked>
                                <label class="btn btn-outline-success" for="success">Berjaya</label>

                                <input type="radio" class="btn-check" name="status" id="failed" value="failed">
                                <label class="btn btn-outline-danger" for="failed">Gagal</label>
                            </div>
                        </div>

                        <input type="hidden" name="reference" value="<?= 'SIM' . time() ?>">
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                Selesai Pembayaran
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?> 