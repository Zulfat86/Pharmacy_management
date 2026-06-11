<?php

require_once __DIR__ . '/includes/auth.php';

if (isLoggedIn()) {
    redirect($_SESSION['role'] === 'admin' ? 'admin/dashboard.php' : 'pharmacist/dashboard.php');
}

redirect('login.php');
