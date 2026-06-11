<?php

require_once __DIR__ . '/../includes/auth.php';
requireRole('admin');
require_once __DIR__ . '/../config/database.php';

$pageTitle = 'Expiry Monitoring';

$nearExpiry = $pdo->query(
    'SELECT medicine_name, expiry_date, created_at,
            DATEDIFF(expiry_date, CURDATE()) AS days_left
     FROM medicines
     WHERE expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
     ORDER BY expiry_date ASC'
)->fetchAll();

$expired = $pdo->query(
    'SELECT medicine_name, expiry_date, created_at,
            DATEDIFF(CURDATE(), expiry_date) AS days_expired
     FROM medicines
     WHERE expiry_date < CURDATE()
     ORDER BY expiry_date ASC'
)->fetchAll();

$valid = $pdo->query(
    'SELECT medicine_name, expiry_date, created_at
     FROM medicines
     WHERE expiry_date > DATE_ADD(CURDATE(), INTERVAL 30 DAY)
     ORDER BY expiry_date ASC'
)->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h1 class="h3 mb-1">Expiry Monitoring</h1>
    <p class="text-muted mb-0">Track expired medicines and items expiring within 30 days.</p>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card table-card">
            <div class="card-header bg-white fw-semibold text-danger">
                <i class="bi bi-exclamation-octagon me-1"></i> Already Expired
            </div>
            <div class="card-body p-0">
                <?php if ($expired): ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                            <tr>
                                <th>Medicine</th>
                                <th>Expiry Date</th>
                                <th>Days Expired</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($expired as $med): ?>
                                <tr>
                                    <td><?= htmlspecialchars($med['medicine_name']) ?></td>
                                    <td><?= formatDate($med['expiry_date']) ?></td>
                                    <td><span class="badge badge-expired"><?= (int) $med['days_expired'] ?> days</span></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted p-3 mb-0">No expired medicines.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card table-card">
            <div class="card-header bg-white fw-semibold text-warning">
                <i class="bi bi-exclamation-triangle me-1"></i> Expiring Within 30 Days
            </div>
            <div class="card-body p-0">
                <?php if ($nearExpiry): ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                            <tr>
                                <th>Medicine</th>
                                <th>Expiry Date</th>
                                <th>Days Left</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($nearExpiry as $med): ?>
                                <tr>
                                    <td><?= htmlspecialchars($med['medicine_name']) ?></td>
                                    <td><?= formatDate($med['expiry_date']) ?></td>
                                    <td><span class="badge badge-expiring"><?= (int) $med['days_left'] ?> days</span></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted p-3 mb-0">No medicines expiring within 30 days.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card table-card">
            <div class="card-header bg-white fw-semibold text-success">
                <i class="bi bi-check-circle me-1"></i> Valid Medicines
            </div>
            <div class="card-body p-0">
                <?php if ($valid): ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                            <tr>
                                <th>Medicine</th>
                                <th>Expiry Date</th>
                                <th>Date Added</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($valid as $med): ?>
                                <tr>
                                    <td><?= htmlspecialchars($med['medicine_name']) ?></td>
                                    <td><span class="badge badge-ok"><?= formatDate($med['expiry_date']) ?></span></td>
                                    <td><?= formatDate($med['created_at']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted p-3 mb-0">No medicines with expiry beyond 30 days.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
