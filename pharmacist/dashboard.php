<?php

require_once __DIR__ . '/../includes/auth.php';
requireRole('pharmacist');
require_once __DIR__ . '/../config/database.php';

$pageTitle = 'Pharmacist Dashboard';
$user = currentUser();

$totalMedicines = (int) $pdo->query('SELECT COUNT(*) FROM medicines')->fetchColumn();

$todaySalesStmt = $pdo->prepare(
    'SELECT COUNT(*) FROM sales WHERE sold_date = CURDATE() AND pharmacist = ?'
);
$todaySalesStmt->execute([$user['username']]);
$todaySalesCount = (int) $todaySalesStmt->fetchColumn();

$todayRevenueStmt = $pdo->prepare(
    'SELECT COALESCE(SUM(amount_received), 0) FROM sales WHERE sold_date = CURDATE() AND pharmacist = ?'
);
$todayRevenueStmt->execute([$user['username']]);
$todayRevenue = (float) $todayRevenueStmt->fetchColumn();

$recentSales = $pdo->prepare(
    'SELECT medicine_name, quantity_sold, amount_received, sold_date
     FROM sales
     WHERE pharmacist = ?
     ORDER BY created_at DESC
     LIMIT 5'
);
$recentSales->execute([$user['username']]);
$recentSalesRows = $recentSales->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h1 class="h3 mb-1">Pharmacist Dashboard</h1>
    <p class="text-muted mb-0">Welcome, <?= htmlspecialchars($user['username']) ?>. Manage medicines and record sales.</p>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="card-body">
                <div class="text-muted small">Total Medicines</div>
                <div class="stat-value text-primary"><?= $totalMedicines ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="card-body">
                <div class="text-muted small">My Sales Today</div>
                <div class="stat-value text-info"><?= $todaySalesCount ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="card-body">
                <div class="text-muted small">My Revenue Today</div>
                <div class="stat-value text-success"><?= formatCurrency($todayRevenue) ?></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <a href="<?= url('pharmacist/medicines/add.php') ?>" class="btn btn-outline-primary w-100 py-3">
            <i class="bi bi-plus-circle me-1"></i> Add Medicine
        </a>
    </div>
    <div class="col-md-4">
        <a href="<?= url('pharmacist/sales/record.php') ?>" class="btn btn-primary w-100 py-3">
            <i class="bi bi-cart-plus me-1"></i> Record Sale
        </a>
    </div>
    <div class="col-md-4">
        <a href="<?= url('pharmacist/medicines/index.php') ?>" class="btn btn-outline-secondary w-100 py-3">
            <i class="bi bi-search me-1"></i> Search Medicines
        </a>
    </div>
</div>

<div class="card table-card">
    <div class="card-header bg-white fw-semibold">Recent Sales</div>
    <div class="card-body p-0">
        <?php if ($recentSalesRows): ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                    <tr>
                        <th>Medicine</th>
                        <th>Quantity</th>
                        <th>Amount</th>
                        <th>Date</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($recentSalesRows as $sale): ?>
                        <tr>
                            <td><?= htmlspecialchars($sale['medicine_name']) ?></td>
                            <td><?= (int) $sale['quantity_sold'] ?></td>
                            <td><?= formatCurrency((float) $sale['amount_received']) ?></td>
                            <td><?= formatDate($sale['sold_date']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted p-4 mb-0">No sales recorded yet.</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
