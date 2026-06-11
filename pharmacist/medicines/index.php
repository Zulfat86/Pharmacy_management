<?php

require_once __DIR__ . '/../../includes/auth.php';
requireRole('pharmacist');
require_once __DIR__ . '/../../config/database.php';

$pageTitle = 'Medicines';
$search = trim($_GET['search'] ?? '');

if ($search !== '') {
    $stmt = $pdo->prepare(
        'SELECT medicine_name, expiry_date, created_at
         FROM medicines
         WHERE medicine_name LIKE ?
         ORDER BY medicine_name ASC'
    );
    $stmt->execute(['%' . $search . '%']);
} else {
    $stmt = $pdo->query(
        'SELECT medicine_name, expiry_date, created_at
         FROM medicines
         ORDER BY medicine_name ASC'
    );
}

$medicines = $stmt->fetchAll();

require_once __DIR__ . '/../../includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h1 class="h3 mb-1">Medicine Management</h1>
        <p class="text-muted mb-0">Add, edit, delete, and search medicines.</p>
    </div>
    <a href="<?= url('pharmacist/medicines/add.php') ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Add Medicine
    </a>
</div>

<form method="get" class="row g-2 mb-4">
    <div class="col-md-8">
        <input type="text" name="search" class="form-control"
               placeholder="Search by medicine name..."
               value="<?= htmlspecialchars($search) ?>">
    </div>
    <div class="col-md-4 d-flex gap-2">
        <button type="submit" class="btn btn-outline-primary">Search</button>
        <?php if ($search !== ''): ?>
            <a href="<?= url('pharmacist/medicines/index.php') ?>" class="btn btn-outline-secondary">Clear</a>
        <?php endif; ?>
    </div>
</form>

<div class="card table-card">
    <div class="card-body p-0">
        <?php if ($medicines): ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                    <tr>
                        <th>Medicine Name</th>
                        <th>Expiry Date</th>
                        <th>Date Added</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($medicines as $med): ?>
                        <?php
                        $daysLeft = (int) ((strtotime($med['expiry_date']) - time()) / 86400);
                        if ($daysLeft < 0) {
                            $status = '<span class="badge badge-expired">Expired</span>';
                        } elseif ($daysLeft <= 30) {
                            $status = '<span class="badge badge-expiring">Expiring Soon</span>';
                        } else {
                            $status = '<span class="badge badge-ok">Valid</span>';
                        }
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($med['medicine_name']) ?></td>
                            <td><?= formatDate($med['expiry_date']) ?></td>
                            <td><?= formatDate($med['created_at']) ?></td>
                            <td><?= $status ?></td>
                            <td class="text-end">
                                <a href="<?= url('pharmacist/medicines/edit.php?medicine=' . urlencode($med['medicine_name'])) ?>"
                                   class="btn btn-sm btn-outline-primary">Edit</a>
                                <a href="<?= url('pharmacist/medicines/delete.php?medicine=' . urlencode($med['medicine_name'])) ?>"
                                   class="btn btn-sm btn-outline-danger"
                                   onclick="return confirm('Delete this medicine?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted p-4 mb-0">
                <?= $search !== '' ? 'No medicines match your search.' : 'No medicines registered yet.' ?>
            </p>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
