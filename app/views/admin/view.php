<?php require_once '../app/views/layouts/header.php'; ?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="bi bi-person-lines-fill me-2"></i>Maklumat Ahli</h4>
                </div>
                <div class="card-body">
                    <!-- Personal Information Section -->
                    <div class="section mb-4">
                        <h5 class="border-bottom pb-2 text-primary">
                            <i class="bi bi-person-badge me-2"></i>Maklumat Peribadi
                        </h5>
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-hover">
                                    <tr>
                                        <th width="150" class="text-muted">Nama</th>
                                        <td><?= htmlspecialchars($data['member']->name ?? '-'); ?></td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">No. KP</th>
                                        <td><?= htmlspecialchars($data['member']->ic_no ?? '-'); ?></td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">Jantina</th>
                                        <td><?= htmlspecialchars($data['member']->gender ?? '-'); ?></td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">Agama</th>
                                        <td><?= htmlspecialchars($data['member']->religion ?? '-'); ?></td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">E-mel</th>
                                        <td><?= htmlspecialchars($data['member']->email ?? '-'); ?></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-hover">
                                    <tr>
                                        <th width="150" class="text-muted">Bangsa</th>
                                        <td><?= htmlspecialchars($data['member']->race ?? '-'); ?></td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">Status</th>
                                        <td><?= htmlspecialchars($data['member']->marital_status ?? '-'); ?></td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">Jawatan</th>
                                        <td><?= htmlspecialchars($data['member']->position ?? '-'); ?></td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">Gred</th>
                                        <td><?= htmlspecialchars($data['member']->grade ?? '-'); ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information Section -->
                    <div class="section mb-4">
                        <h5 class="border-bottom pb-2 text-primary">
                            <i class="bi bi-house-door me-2"></i>Maklumat Perhubungan
                        </h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card bg-light mb-3">
                                    <div class="card-header">Alamat Rumah</div>
                                    <div class="card-body">
                                        <p class="card-text"><?= htmlspecialchars($data['member']->home_address ?? '-'); ?></p>
                                        <p class="card-text">
                                            Poskod: <?= htmlspecialchars($data['member']->home_postcode ?? '-'); ?><br>
                                            Negeri: <?= htmlspecialchars($data['member']->home_state ?? '-'); ?>
                                        </p>
                                        <p class="card-text">
                                            <i class="bi bi-telephone me-2"></i><?= htmlspecialchars($data['member']->home_phone ?? '-'); ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-light mb-3">
                                    <div class="card-header">Alamat Pejabat</div>
                                    <div class="card-body">
                                        <p class="card-text"><?= htmlspecialchars($data['member']->office_address ?? '-'); ?></p>
                                        <p class="card-text">
                                            Poskod: <?= htmlspecialchars($data['member']->office_postcode ?? '-'); ?>
                                        </p>
                                        <p class="card-text">
                                            <i class="bi bi-telephone me-2"></i><?= htmlspecialchars($data['member']->office_phone ?? '-'); ?><br>
                                            <i class="bi bi-printer me-2"></i><?= htmlspecialchars($data['member']->fax ?? '-'); ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Financial Information Section -->
                    <div class="section mb-4">
                        <h5 class="border-bottom pb-2 text-primary">
                            <i class="bi bi-cash-stack me-2"></i>Maklumat Kewangan
                        </h5>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <tr>
                                            <th width="200" class="text-muted">Gaji Bulanan</th>
                                            <td class="text-success fw-bold">RM <?= number_format($data['member']->monthly_salary ?? 0, 2); ?></td>
                                        </tr>
                                        <tr>
                                            <th class="text-muted">Yuran Pendaftaran</th>
                                            <td>RM <?= number_format($data['member']->registration_fee ?? 0, 2); ?></td>
                                        </tr>
                                        <tr>
                                            <th class="text-muted">Modal Syer</th>
                                            <td>RM <?= number_format($data['member']->share_capital ?? 0, 2); ?></td>
                                        </tr>
                                        <tr>
                                            <th class="text-muted">Yuran Modal</th>
                                            <td>RM <?= number_format($data['member']->fee_capital ?? 0, 2); ?></td>
                                        </tr>
                                        <tr>
                                            <th class="text-muted">Wang Deposit</th>
                                            <td>RM <?= number_format($data['member']->deposit_funds ?? 0, 2); ?></td>
                                        </tr>
                                        <tr>
                                            <th class="text-muted">Tabung Kebajikan</th>
                                            <td>RM <?= number_format($data['member']->welfare_fund ?? 0, 2); ?></td>
                                        </tr>
                                        <tr>
                                            <th class="text-muted">Simpanan Tetap</th>
                                            <td>RM <?= number_format($data['member']->fixed_deposit ?? 0, 2); ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Family Information Section -->
                    <div class="section">
                        <h5 class="border-bottom pb-2 text-primary">
                            <i class="bi bi-people me-2"></i>Maklumat Waris
                        </h5>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <p class="mb-1 text-muted">Hubungan</p>
                                                <h6><?= htmlspecialchars($data['member']->family_relationship ?? '-'); ?></h6>
                                            </div>
                                            <div class="col-md-4">
                                                <p class="mb-1 text-muted">Nama Waris</p>
                                                <h6><?= htmlspecialchars($data['member']->family_name ?? '-'); ?></h6>
                                            </div>
                                            <div class="col-md-4">
                                                <p class="mb-1 text-muted">No. KP Waris</p>
                                                <h6><?= htmlspecialchars($data['member']->family_ic ?? '-'); ?></h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 text-center">
                        <a href="/admin" class="btn btn-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../app/views/layouts/footer.php'; ?> 