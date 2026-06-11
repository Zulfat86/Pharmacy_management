<?php

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';

if (isLoggedIn()) {
    redirect($_SESSION['role'] === 'admin' ? 'admin/dashboard.php' : 'pharmacist/dashboard.php');
}

$pageTitle = 'Login';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Please enter both username and password.';
    } else {
        $stmt = $pdo->prepare('SELECT username, password, role FROM users WHERE username = ?');
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            loginUser($user);
            redirect($user['role'] === 'admin' ? 'admin/dashboard.php' : 'pharmacist/dashboard.php');
        }

        $error = 'Access denied. Invalid username or password.';
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-5 col-lg-4">
        <div class="card shadow-sm border-0 login-card">
            <div class="card-body p-4">
                <div class="text-center mb-4">
                    <div class="login-illustration mb-3">
                        <img src="<?= url('assets/images/login-hero.svg') ?>" alt="Pharmacy login illustration">
                    </div>
                    <h1 class="h4 mb-1">Welcome Back</h1>
                    <p class="text-muted small">Sign in to access the pharmacy system</p>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger py-2"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="post" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username"
                               value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required autofocus>
                    </div>
                    <div class="mb-4">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </form>

                <p class="text-muted small text-center mt-4 mb-0">
                    Default admin: <strong>admin</strong> / <strong>admin123</strong>
                </p>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
