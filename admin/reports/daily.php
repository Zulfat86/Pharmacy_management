<?php

require_once __DIR__ . '/../../includes/auth.php';
requireRole('admin');
require_once __DIR__ . '/../../config/database.php';

$pageTitle = 'Daily Sales Report';
$selectedDate = $_GET['date'] ?? date('Y-m-d');

$stmt = $pdo->prepare(
    'SELECT medicine_name,
            SUM(quantity_sold) AS quantity_sold,
            SUM(amount_received) AS amount_received
     FROM sales
     WHERE sold_date = ?
     GROUP BY medicine_name
     ORDER BY medicine_name ASC'
);
$stmt->execute([$selectedDate]);
$rows = $stmt->fetchAll();

$revenueStmt = $pdo->prepare(
    'SELECT COALESCE(SUM(amount_received), 0) FROM sales WHERE sold_date = ?'
);
$revenueStmt->execute([$selectedDate]);
$totalRevenue = (float) $revenueStmt->fetchColumn();

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h1 class="h3 mb-1">Daily Sales Report</h1>
        <p class="text-muted mb-0">Sales summary for <?= formatDate($selectedDate) ?></p>
    </div>
    <form method="get" class="d-flex gap-2">
        <input type="date" name="date" class="form-control" value="<?= htmlspecialchars($selectedDate) ?>">
        <button type="submit" class="btn btn-primary">View</button>
    </form>
</div>

<div class="card stat-card mb-4">
    <div class="card-body d-flex justify-content-between align-items-center">
        <span class="text-muted">Total Daily Revenue</span>
        <span class="fs-4 fw-bold text-success"><?= formatCurrency($totalRevenue) ?></span>
    </div>
</div>

<div class="card table-card">
    <div class="card-body p-0">
        <?php if ($rows): ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                    <tr>
                        <th>Medicine</th>
                        <th>Quantity Sold</th>
                        <th>Amount Received</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['medicine_name']) ?></td>
                            <td><?= (int) $row['quantity_sold'] ?></td>
                            <td><?= formatCurrency((float) $row['amount_received']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted p-4 mb-0">No sales recorded for this date.</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
