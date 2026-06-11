<?php

require_once __DIR__ . '/../../includes/auth.php';
requireRole('pharmacist');
require_once __DIR__ . '/../../config/database.php';

$medicineName = trim($_GET['medicine'] ?? '');

if ($medicineName !== '') {
    $stmt = $pdo->prepare('DELETE FROM medicines WHERE medicine_name = ?');
    $stmt->execute([$medicineName]);
}

redirect('pharmacist/medicines/index.php');
