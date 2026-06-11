<?php

require_once __DIR__ . '/../includes/auth.php';
requireRole('admin');
require_once __DIR__ . '/../config/database.php';

$pageTitle = 'All Sales Records';
$searchDate = $_GET['date'] ?? '';

$sql = 'SELECT medicine_name, quantity_sold, amount_received, sold_date, pharmacist, created_at FROM sales WHERE 1=1';
$params = [];

if ($searchDate !== '') {
    $sql .= ' AND sold_date = ?';
    $params[] = $searchDate;
}

$sql .= ' ORDER BY created_at DESC';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$sales = $stmt->fetchAll();

$totalAmount = array_sum(array_column($sales, 'amount_received'));

require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h1 class="h3 mb-1">All Sales Records</h1>
        <p class="text-muted mb-0">View every sales transaction in the system.</p>
    </div>
</div>

<form method="get" class="row g-2 mb-4">
    <div class="col-md-4">
        <input type="date" name="date" class="form-control" value="<?= htmlspecialchars($searchDate) ?>">
    </div>
    <div class="col-md-4 d-flex gap-2">
        <button type="submit" class="btn btn-outline-primary">Filter by Date</button>
        <?php if ($searchDate !== ''): ?>
            <a href="<?= url('admin/sales.php') ?>" class="btn btn-outline-secondary">Clear</a>
        <?php endif; ?>
    </div>
</form>

<?php if ($sales): ?>
    <div class="alert alert-light border mb-3">
        Total amount: <strong><?= formatCurrency((float) $totalAmount) ?></strong>
        (<?= count($sales) ?> transaction<?= count($sales) !== 1 ? 's' : '' ?>)
    </div>
<?php endif; ?>

<div class="card table-card">
    <div class="card-body p-0">
        <?php if ($sales): ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                    <tr>
                        <th>Medicine</th>
                        <th>Quantity Sold</th>
                        <th>Amount Received</th>
                        <th>Date Sold</th>
                        <th>Pharmacist</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($sales as $sale): ?>
                        <tr>
                            <td><?= htmlspecialchars($sale['medicine_name']) ?></td>
                            <td><?= (int) $sale['quantity_sold'] ?></td>
                            <td><?= formatCurrency((float) $sale['amount_received']) ?></td>
                            <td><?= formatDate($sale['sold_date']) ?></td>
                            <td><?= htmlspecialchars($sale['pharmacist']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted p-4 mb-0">No sales records found.</p>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
