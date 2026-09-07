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
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['is_admin'] = true;
                header("Location: index.php");
                exit();
            } else {
                $error = "Access denied. This account does not have administrator privileges.";
            }
        } else {
            $error = "Invalid email or password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — <?php echo APP_NAME; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
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
