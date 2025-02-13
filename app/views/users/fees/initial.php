<?php 
    $title = 'Bayaran Keahlian';
    require_once '../app/views/layouts/header.php';
?>

<div class="container mt-4">
    <div class="card">
        <div class="card-header">
            <h4>Yuran Keahlian</h4>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <p>Sila sahkan pembayaran yuran berikut untuk mengaktifkan keahlian anda:</p>
            </div>

            <table class="table">
                <thead>
                    <tr>
                        <th>Perkara</th>
                        <th class="text-end">Jumlah (RM)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Yuran Pendaftaran</td>
                        <td class="text-end">20.00</td>
                    </tr>
                    <tr>
                        <td>Modal Saham</td>
                        <td class="text-end">100.00</td>
                    </tr>
                    <tr>
                        <td>Yuran Bulanan</td>
                        <td class="text-end">10.00</td>
                    </tr>
                    <tr>
                        <td>Tabung Kebajikan</td>
                        <td class="text-end">10.00</td>
                    </tr>
                    <tr class="table-primary">
                        <th>Jumlah</th>
                        <th class="text-end">140.00</th>
                    </tr>
                </tbody>
            </table>

            <form action="/users/fees/confirm" method="POST" class="mt-4">
                <div class="alert alert-warning">
                    <p><strong>Nota:</strong> Dengan menekan butang di bawah, anda mengesahkan bahawa pembayaran telah dibuat.</p>
                </div>

                <button type="submit" class="btn btn-primary">Sahkan Pembayaran</button>
            </form>
        </div>
    </div>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?> 