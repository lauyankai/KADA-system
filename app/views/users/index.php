<?php require_once '../app/views/layouts/header.php'; ?>

<div class="container mt-4">
    <h2>Senarai Pengguna</h2>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?php 
                echo $_SESSION['success'];
                unset($_SESSION['success']);
            ?>
        </div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>No. KP</th>
                    <th>Jantina</th>
                    <th>Jawatan</th>
                    <th>Gaji Bulanan</th>
                    <th>Tindakan</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pendingregistermembers as $member): ?>
                    <tr>
                        <td><?= htmlspecialchars($member['name']) ?></td>
                        <td><?= htmlspecialchars($member['ic_no']) ?></td>
                        <td><?= htmlspecialchars($member['gender']) ?></td>
                        <td><?= htmlspecialchars($member['position']) ?></td>
                        <td>RM <?= number_format($member['monthly_salary'], 2) ?></td>
                        <td>
                            <a href="/users/edit/<?= $member['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                            <a href="/users/delete/<?= $member['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?>
