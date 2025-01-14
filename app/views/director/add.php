<?php 
    $title = 'Tambah Pengarah';
    require_once '../app/views/layouts/header.php';
?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-body p-4">
                    <h2 class="card-title mb-4">Tambah Pengarah Baru</h2>

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger">
                            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                        </div>
                    <?php endif; ?>

                    <form action="/director/store" method="POST">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Username</label>
                                <input type="text" 
                                       name="username" 
                                       class="form-control"
                                       required
                                       pattern="^DIR.*"
                                       title="Username must start with 'DIR'"
                                       placeholder="e.g., DIR001">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Nama Penuh</label>
                                <input type="text" 
                                       name="name" 
                                       class="form-control"
                                       required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" 
                                       name="email" 
                                       class="form-control"
                                       required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">No. Telefon</label>
                                <input type="tel" 
                                       name="phone_number" 
                                       class="form-control"
                                       required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Jawatan</label>
                                <select name="position" class="form-select" required>
                                    <option value="">Pilih Jawatan</option>
                                    <?php foreach ($positions as $position): ?>
                                        <option value="<?= $position ?>"><?= $position ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Jabatan</label>
                                <select name="department" class="form-select" required>
                                    <option value="">Pilih Jabatan</option>
                                    <?php foreach ($departments as $department): ?>
                                        <option value="<?= $department ?>"><?= $department ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Kata Laluan</label>
                                <input type="password" 
                                       name="password" 
                                       class="form-control"
                                       required
                                       minlength="8">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Sahkan Kata Laluan</label>
                                <input type="password" 
                                       name="confirm_password" 
                                       class="form-control"
                                       required
                                       minlength="8"
                                       oninput="validatePassword(this)">
                            </div>

                            <div class="col-12">
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <a href="/admin" class="btn btn-outline-secondary">
                                        <i class="bi bi-x-circle me-2"></i>Batal
                                    </a>
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-person-plus me-2"></i>Tambah Pengarah
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function validatePassword(input) {
    const password = document.querySelector('input[name="password"]').value;
    if (input.value !== password) {
        input.setCustomValidity('Passwords do not match');
    } else {
        input.setCustomValidity('');
    }
}
</script>

<?php require_once '../app/views/layouts/footer.php'; ?> 