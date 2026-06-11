<?php

require_once __DIR__ . '/../includes/auth.php';
requireRole('admin');
require_once __DIR__ . '/../config/database.php';

$pageTitle = 'Admin Dashboard';

$totalMedicines = (int) $pdo->query('SELECT COUNT(*) FROM medicines')->fetchColumn();

$todayTransactions = (int) $pdo->query(
    'SELECT COUNT(*) FROM sales WHERE sold_date = CURDATE()'
)->fetchColumn();

$todayRevenue = (float) $pdo->query(
    'SELECT COALESCE(SUM(amount_received), 0) FROM sales WHERE sold_date = CURDATE()'
)->fetchColumn();

$weekStart = date('Y-m-d', strtotime('monday this week'));
$weekEnd = date('Y-m-d', strtotime('sunday this week'));

$weeklyRevenueStmt = $pdo->prepare(
    'SELECT COALESCE(SUM(amount_received), 0) FROM sales WHERE sold_date BETWEEN ? AND ?'
);
$weeklyRevenueStmt->execute([$weekStart, $weekEnd]);
$weeklyRevenue = (float) $weeklyRevenueStmt->fetchColumn();

$mostSoldStmt = $pdo->prepare(
    'SELECT medicine_name, SUM(quantity_sold) AS total_qty
     FROM sales
     WHERE sold_date BETWEEN ? AND ?
     GROUP BY medicine_name
     ORDER BY total_qty DESC
     LIMIT 1'
);
$mostSoldStmt->execute([$weekStart, $weekEnd]);
$mostSold = $mostSoldStmt->fetch();

$nearExpiry = $pdo->query(
    'SELECT medicine_name, expiry_date, created_at
     FROM medicines
     WHERE expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
     ORDER BY expiry_date ASC'
)->fetchAll();

$expired = $pdo->query(
    'SELECT medicine_name, expiry_date, created_at
     FROM medicines
     WHERE expiry_date < CURDATE()
     ORDER BY expiry_date ASC'
)->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h1 class="h3 mb-1">Administrator Dashboard</h1>
    <p class="text-muted mb-0">Overview of pharmacy performance and inventory alerts.</p>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6 col-xl-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="text-muted small">Total Medicines Recorded</div>
                <div class="stat-value text-primary"><?= $totalMedicines ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="text-muted small">Today's Sales Transactions</div>
                <div class="stat-value text-info"><?= $todayTransactions ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="text-muted small">Today's Revenue</div>
                <div class="stat-value text-success"><?= formatCurrency($todayRevenue) ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="text-muted small">Weekly Revenue</div>
                <div class="stat-value text-dark"><?= formatCurrency($weeklyRevenue) ?></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card table-card h-100">
            <div class="card-header bg-white fw-semibold">Most Sold Medicine (This Week)</div>
            <div class="card-body">
                <?php if ($mostSold): ?>
                    <p class="mb-1 fs-5 fw-semibold"><?= htmlspecialchars($mostSold['medicine_name']) ?></p>
                    <p class="text-muted mb-0"><?= (int) $mostSold['total_qty'] ?> units sold</p>
                <?php else: ?>
                    <p class="text-muted mb-0">No sales recorded this week.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card table-card h-100">
            <div class="card-header bg-white fw-semibold text-warning">Medicines Near Expiry (30 Days)</div>
            <div class="card-body p-0">
                <?php if ($nearExpiry): ?>
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead><tr><th>Medicine</th><th>Expiry</th></tr></thead>
                            <tbody>
                            <?php foreach ($nearExpiry as $med): ?>
                                <tr>
                                    <td><?= htmlspecialchars($med['medicine_name']) ?></td>
                                    <td><span class="badge badge-expiring"><?= formatDate($med['expiry_date']) ?></span></td>
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

    <div class="col-lg-4">
        <div class="card table-card h-100">
            <div class="card-header bg-white fw-semibold text-danger">Expired Medicines</div>
            <div class="card-body p-0">
                <?php if ($expired): ?>
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead><tr><th>Medicine</th><th>Expiry</th></tr></thead>
                            <tbody>
                            <?php foreach ($expired as $med): ?>
                                <tr>
                                    <td><?= htmlspecialchars($med['medicine_name']) ?></td>
                                    <td><span class="badge badge-expired"><?= formatDate($med['expiry_date']) ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted p-3 mb-0">No expired medicines found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
