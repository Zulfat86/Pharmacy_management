<?php

require_once __DIR__ . '/../../includes/auth.php';
requireRole('pharmacist');
require_once __DIR__ . '/../../config/database.php';

$pageTitle = 'Edit Medicine';
$error = '';
$medicineName = trim($_GET['medicine'] ?? '');

$stmt = $pdo->prepare('SELECT medicine_name, expiry_date FROM medicines WHERE medicine_name = ?');
$stmt->execute([$medicineName]);
$medicine = $stmt->fetch();

if (!$medicine) {
    redirect('pharmacist/medicines/index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['medicine_name'] ?? '');
    $expiryDate = $_POST['expiry_date'] ?? '';

    if ($name === '' || $expiryDate === '') {
        $error = 'Medicine name and expiry date are required.';
    } else {
        $update = $pdo->prepare(
            'UPDATE medicines SET medicine_name = ?, expiry_date = ? WHERE medicine_name = ?'
        );
        $update->execute([$name, $expiryDate, $medicineName]);
        redirect('pharmacist/medicines/index.php');
    }

    $medicine['medicine_name'] = $name;
    $medicine['expiry_date'] = $expiryDate;
}

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="page-header">
    <h1 class="h3 mb-1">Edit Medicine</h1>
    <p class="text-muted mb-0">Update medicine information.</p>
</div>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card table-card">
            <div class="card-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="post" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="medicine_name" class="form-label">Medicine Name</label>
                        <input type="text" class="form-control" id="medicine_name" name="medicine_name"
                               value="<?= htmlspecialchars($medicine['medicine_name']) ?>" required>
                    </div>
                    <div class="mb-4">
                        <label for="expiry_date" class="form-label">Expiry Date</label>
                        <input type="date" class="form-control" id="expiry_date" name="expiry_date"
                               value="<?= htmlspecialchars($medicine['expiry_date']) ?>" required>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Update Medicine</button>
                        <a href="<?= url('pharmacist/medicines/index.php') ?>" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
