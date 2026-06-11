<?php

require_once __DIR__ . '/../../includes/auth.php';
requireRole('admin');
require_once __DIR__ . '/../../config/database.php';

$pageTitle = 'Weekly Sales Report';

$weekStart = $_GET['week_start'] ?? date('Y-m-d', strtotime('monday this week'));
$weekEnd = date('Y-m-d', strtotime($weekStart . ' +6 days'));

$stmt = $pdo->prepare(
    'SELECT medicine_name, SUM(quantity_sold) AS total_quantity, SUM(amount_received) AS total_amount
     FROM sales
     WHERE sold_date BETWEEN ? AND ?
     GROUP BY medicine_name
     ORDER BY total_quantity DESC'
);
$stmt->execute([$weekStart, $weekEnd]);
$rows = $stmt->fetchAll();

$revenueStmt = $pdo->prepare(
    'SELECT COALESCE(SUM(amount_received), 0) FROM sales WHERE sold_date BETWEEN ? AND ?'
);
$revenueStmt->execute([$weekStart, $weekEnd]);
$weeklyRevenue = (float) $revenueStmt->fetchColumn();

$mostSold = $rows[0] ?? null;
$isWeekend = in_array((int) date('N'), [6, 7], true);

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h1 class="h3 mb-1">Weekly Sales Report</h1>
        <p class="text-muted mb-0">
            Week of <?= formatDate($weekStart) ?> — <?= formatDate($weekEnd) ?>
        </p>
    </div>
    <form method="get" class="d-flex gap-2">
        <input type="date" name="week_start" class="form-control" value="<?= htmlspecialchars($weekStart) ?>">
        <button type="submit" class="btn btn-primary">View Week</button>
    </form>
</div>

<?php if ($isWeekend): ?>
    <div class="alert alert-info">
        <i class="bi bi-calendar-week me-1"></i>
        Weekend report: this summary covers the current week's sales performance.
    </div>
<?php endif; ?>

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card stat-card">
            <div class="card-body">
                <div class="text-muted small">Total Weekly Revenue</div>
                <div class="stat-value text-success"><?= formatCurrency($weeklyRevenue) ?></div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card stat-card">
            <div class="card-body">
                <div class="text-muted small">Most Sold Medicine</div>
                <?php if ($mostSold): ?>
                    <div class="stat-value text-primary fs-5">
                        <?= htmlspecialchars($mostSold['medicine_name']) ?>
                        (<?= (int) $mostSold['total_quantity'] ?> units)
                    </div>
                <?php else: ?>
                    <div class="text-muted">No sales this week</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="card table-card">
    <div class="card-header bg-white fw-semibold">Weekly Sales Summary</div>
    <div class="card-body p-0">
        <?php if ($rows): ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                    <tr>
                        <th>Medicine</th>
                        <th>Total Quantity Sold</th>
                        <th>Total Amount</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['medicine_name']) ?></td>
                            <td><?= (int) $row['total_quantity'] ?></td>
                            <td><?= formatCurrency((float) $row['total_amount']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted p-4 mb-0">No sales recorded for this week.</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
