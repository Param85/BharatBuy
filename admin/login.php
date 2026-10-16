<?php
/**
 * Admin Login Page
 * Validates credentials AND checks is_admin flag.
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/session.php';

// If already logged in as admin, redirect to admin dashboard
if (isLoggedIn() && isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true) {
    header("Location: index.php");
    exit();
}

$error = '';

// Rate Limiting for Admin Authentication: max 5 failed attempts in 5 minutes
if (!isset($_SESSION['admin_login_attempts'])) {
    $_SESSION['admin_login_attempts'] = ['count' => 0, 'last_attempt' => time()];
}
if (time() - $_SESSION['admin_login_attempts']['last_attempt'] > 300) {
    $_SESSION['admin_login_attempts'] = ['count' => 0, 'last_attempt' => time()];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_SESSION['admin_login_attempts']['count'] >= 5) {
        $remaining = max(1, ceil((300 - (time() - $_SESSION['admin_login_attempts']['last_attempt'])) / 60));
        $error = "Too many failed admin login attempts. Please wait {$remaining} minute(s).";
    } elseif (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
        $error = "Security validation failed. Please refresh and try again.";
    } else {
        $email = trim($_POST['email'] ?? '');
        $pass = $_POST['pass'] ?? '';

        if (empty($email) || empty($pass)) {
            $error = "All fields are required.";
        } else {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($pass, $user['password'])) {
                if ((int)$user['is_admin'] === 1) {
                    unset($_SESSION['admin_login_attempts']);
                    regenerate_session_id();

                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['full_name'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['is_admin'] = true;

                    header("Location: index.php");
                    exit();
                } else {
                    $_SESSION['admin_login_attempts']['count']++;
                    $_SESSION['admin_login_attempts']['last_attempt'] = time();
                    $error = "Access denied. This account does not have administrator privileges.";
                }
            } else {
                $_SESSION['admin_login_attempts']['count']++;
                $_SESSION['admin_login_attempts']['last_attempt'] = time();
                $error = "Invalid email or password.";
            }
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - <?php echo APP_NAME; ?></title>
    <link rel="icon" type="image/svg+xml" href="../assets/images/favicon.svg?v=3">
    <link rel="icon" type="image/png" sizes="32x32" href="../assets/images/favicon-32x32.png?v=3">
    <link rel="icon" type="image/png" sizes="16x16" href="../assets/images/favicon-16x16.png?v=3">
    <link rel="apple-touch-icon" sizes="180x180" href="../assets/images/apple-touch-icon.png?v=3">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css?v=<?= time(); ?>">
</head>
<body>
    <div class="admin-login-wrapper">
        <div class="admin-login-card">
            <div class="login-logo">
                <img src="../assets/images/logo.png?v=3" alt="BharatBuy">
                <span>Admin Panel</span>
            </div>

            <h2>Welcome Back</h2>
            <p class="login-subtitle">Sign in to the administrator console</p>

            <?php if ($error): ?>
                <div class="admin-login-error">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="login.php">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
                <div class="admin-form-group">
                    <label for="admin-email">Email Address</label>
                    <input type="email" name="email" id="admin-email" class="admin-form-input" placeholder="admin@bharatbuy.com" required value="<?= htmlspecialchars($email ?? ''); ?>">
                </div>

                <div class="admin-form-group">
                    <label for="admin-pass">Password</label>
                    <input type="password" name="pass" id="admin-pass" class="admin-form-input" placeholder="Enter your password" required>
                </div>

                <button type="submit" class="admin-btn admin-btn-primary" style="width: 100%; padding: 0.75rem; font-size: 0.95rem; margin-top: 0.5rem;">
                    <i class="fa-solid fa-right-to-bracket"></i> Sign In
                </button>
            </form>

            <div style="text-align: center; margin-top: 1.5rem;">
                <a href="../index.php" style="font-size: 0.85rem; color: var(--admin-text-muted);">
                    <i class="fa-solid fa-arrow-left"></i> Back to Store
                </a>
            </div>
        </div>
    </div>
</body>
</html>
