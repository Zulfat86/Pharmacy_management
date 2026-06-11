<?php

require_once __DIR__ . '/../../includes/auth.php';
requireRole('pharmacist');
require_once __DIR__ . '/../../config/database.php';

$pageTitle = 'Record Sale';
$success = '';
$error = '';

$medicines = $pdo->query(
    'SELECT medicine_name FROM medicines ORDER BY medicine_name ASC'
)->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $medicineName = trim($_POST['medicine_name'] ?? '');
    $quantity = (int) ($_POST['quantity_sold'] ?? 0);
    $amount = (float) ($_POST['amount_received'] ?? 0);
    $soldDate = $_POST['sold_date'] ?? date('Y-m-d');

    if ($medicineName === '') {
        $error = 'Please select a medicine.';
    } elseif ($quantity <= 0) {
        $error = 'Quantity sold must be greater than zero.';
    } elseif ($amount < 0) {
        $error = 'Amount received cannot be negative.';
    } else {
        $check = $pdo->prepare('SELECT 1 FROM medicines WHERE medicine_name = ?');
        $check->execute([$medicineName]);

        if (!$check->fetch()) {
            $error = 'Selected medicine is not registered in the system.';
        } else {
            $stmt = $pdo->prepare(
                'INSERT INTO sales (medicine_name, quantity_sold, amount_received, sold_date, pharmacist)
                 VALUES (?, ?, ?, ?, ?)'
            );
            $stmt->execute([
                $medicineName,
                $quantity,
                $amount,
                $soldDate,
                $_SESSION['username'],
            ]);
            $success = 'Sale recorded successfully. Daily revenue has been updated.';
        }
    }
}

$todayRevenue = (float) $pdo->query(
    'SELECT COALESCE(SUM(amount_received), 0) FROM sales WHERE sold_date = CURDATE()'
)->fetchColumn();

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="page-header">
    <h1 class="h3 mb-1">Record Sale</h1>
    <p class="text-muted mb-0">Record a medicine sale transaction.</p>
</div>

<?php if ($success): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card table-card">
            <div class="card-body">
                <?php if (!$medicines): ?>
                    <div class="alert alert-warning mb-0">
                        No medicines available. <a href="<?= url('pharmacist/medicines/add.php') ?>">Add a medicine</a> first.
                    </div>
                <?php else: ?>
                    <form method="post" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label for="medicine_name" class="form-label">Medicine Name</label>
                            <select class="form-select" id="medicine_name" name="medicine_name" required>
                                <option value="">Select medicine...</option>
                                <?php foreach ($medicines as $med): ?>
                                    <option value="<?= htmlspecialchars($med['medicine_name']) ?>"
                                        <?= (($_POST['medicine_name'] ?? '') === $med['medicine_name']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($med['medicine_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="quantity_sold" class="form-label">Quantity Sold</label>
                            <input type="number" class="form-control" id="quantity_sold" name="quantity_sold"
                                   min="1" value="<?= htmlspecialchars($_POST['quantity_sold'] ?? '') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="amount_received" class="form-label">Amount Received (TZS)</label>
                            <input type="number" class="form-control" id="amount_received" name="amount_received"
                                   min="0" step="0.01" value="<?= htmlspecialchars($_POST['amount_received'] ?? '') ?>" required>
                        </div>
                        <div class="mb-4">
                            <label for="sold_date" class="form-label">Date Sold</label>
                            <input type="date" class="form-control" id="sold_date" name="sold_date"
                                   value="<?= htmlspecialchars($_POST['sold_date'] ?? date('Y-m-d')) ?>" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Transaction</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card stat-card">
            <div class="card-body">
                <div class="text-muted small">Total Daily Revenue (All Staff)</div>
                <div class="stat-value text-success"><?= formatCurrency($todayRevenue) ?></div>
                <p class="text-muted small mb-0 mt-2">
                    Pharmacist: <?= htmlspecialchars($_SESSION['username']) ?>
                </p>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
