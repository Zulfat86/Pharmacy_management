<?php

if (!isset($pageTitle)) {
    $pageTitle = APP_NAME;
}

$user = currentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - <?= APP_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= url('assets/css/style.css') ?>" rel="stylesheet">
</head>
<body>
<?php if ($user): ?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-semibold" href="<?= url($user['role'] === 'admin' ? 'admin/dashboard.php' : 'pharmacist/dashboard.php') ?>">
            <i class="bi bi-capsule-pill me-1"></i> Pharmacy Sales
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto">
                <?php if ($user['role'] === 'admin'): ?>
                    <li class="nav-item"><a class="nav-link" href="<?= url('admin/dashboard.php') ?>">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= url('admin/sales.php') ?>">All Sales</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= url('admin/reports/daily.php') ?>">Daily Report</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= url('admin/reports/weekly.php') ?>">Weekly Report</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= url('admin/expiry.php') ?>">Expiry Monitor</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= url('admin/users.php') ?>">Users</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="<?= url('pharmacist/dashboard.php') ?>">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= url('pharmacist/medicines/index.php') ?>">Medicines</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= url('pharmacist/sales/record.php') ?>">Record Sale</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= url('pharmacist/sales/history.php') ?>">Sales History</a></li>
                <?php endif; ?>
            </ul>
            <span class="navbar-text text-white-50 me-3">
                <i class="bi bi-person-circle me-1"></i>
                <?= htmlspecialchars($user['username']) ?> (<?= htmlspecialchars(ucfirst($user['role'])) ?>)
            </span>
            <a class="btn btn-outline-light btn-sm" href="<?= url('logout.php') ?>">Logout</a>
        </div>
    </div>
</nav>
<?php endif; ?>
<main class="container py-4">
