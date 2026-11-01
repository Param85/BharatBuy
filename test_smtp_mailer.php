<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/mailer.php';

// Detect if running via command line (CLI)
if (php_sapi_name() === 'cli') {
    echo "==========================================\n";
    echo " BharatBuy SMTP Mailer Demo (CLI Runner)  \n";
    echo "==========================================\n";
    echo "SMTP Host:       " . (getenv('SMTP_HOST') ?: 'smtp.gmail.com') . "\n";
    echo "SMTP Port:       " . (getenv('SMTP_PORT') ?: '587') . "\n";
    echo "SMTP Encryption: " . (getenv('SMTP_SECURE') ?: 'tls') . "\n";
    echo "Authenticated As:" . (getenv('SMTP_USER') ?: 'paramshah647@gmail.com') . "\n\n";

    $to = 'paramshah647@gmail.com';
    $name = 'Param Shah';
    $productName = 'Apple iPhone 16 Pro (128GB) - Desert Titanium';
    $oldPrice = 119900.00;
    $newPrice = 109990.00;
    $productUrl = 'http://localhost/BharatBuy/product-details.php?id=1';

    echo "Dispatching live price alert to: {$to}...\n";
    $start = microtime(true);
    $result = sendPriceAlertEmail($to, $name, $productName, $oldPrice, $newPrice, $productUrl);
    $duration = round((microtime(true) - $start) * 1000, 2);

    if ($result) {
        echo ">>> SUCCESS: Email delivered via SMTP in {$duration} ms!\n";
    } else {
        echo ">>> NOTICE: Fallback or error occurred. Check error log.\n";
    }

    $previewFile = __DIR__ . '/mail_preview.html';
    if (file_exists($previewFile)) {
        echo "Live HTML preview updated at: {$previewFile}\n";
    }
    exit(0);
}

// Web Browser Interactive Demo Mode
$smtpHost = $_ENV['SMTP_HOST'] ?? $_SERVER['SMTP_HOST'] ?? getenv('SMTP_HOST') ?: 'smtp.gmail.com';
$smtpPort = $_ENV['SMTP_PORT'] ?? $_SERVER['SMTP_PORT'] ?? getenv('SMTP_PORT') ?: '587';
$smtpSecure = $_ENV['SMTP_SECURE'] ?? $_SERVER['SMTP_SECURE'] ?? getenv('SMTP_SECURE') ?: 'tls';
$smtpUser = $_ENV['SMTP_USER'] ?? $_SERVER['SMTP_USER'] ?? getenv('SMTP_USER') ?: 'paramshah647@gmail.com';

// Handle Form Submission
$statusMessage = null;
$statusType = null;
$durationMs = null;

$toEmail = $_POST['to_email'] ?? $smtpUser;
$userName = $_POST['user_name'] ?? 'Param Shah';
$productName = $_POST['product_name'] ?? 'Apple iPhone 16 Pro (128GB) - Desert Titanium';
$oldPrice = (float)($_POST['old_price'] ?? 119900.00);
$newPrice = (float)($_POST['new_price'] ?? 109990.00);
$productUrl = $_POST['product_url'] ?? 'http://localhost/BharatBuy/product-details.php?id=1';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_test_email'])) {
    $startTime = microtime(true);
    try {
        $sent = sendPriceAlertEmail($toEmail, $userName, $productName, $oldPrice, $newPrice, $productUrl);
        $durationMs = round((microtime(true) - $startTime) * 1000, 2);
        if ($sent) {
            $statusType = 'success';
            $statusMessage = "Email delivered successfully via SMTP ({$smtpHost}:{$smtpPort}) to {$toEmail} in {$durationMs} ms!";
        } else {
            $statusType = 'warning';
            $statusMessage = "PHPMailer failed to complete the handshake. Fallback mail() was invoked.";
        }
    } catch (\Exception $e) {
        $statusType = 'error';
        $statusMessage = "SMTP Exception: " . htmlspecialchars($e->getMessage());
    }
}

// Generate the live HTML markup preview for the demo
$previewHtml = getPriceAlertEmailHtml($userName, $productName, $oldPrice, $newPrice, $productUrl);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BharatBuy — SMTP Mailer Interactive Demo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Poppins:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --color-background: #E8E9DF;
            --color-surface: #FFFFFF;
            --color-brand: #2E3D28;
            --color-brand-hover: #202B1C;
            --color-accent: #2E3D28;
            --color-accent-hover: #202B1C;
            --color-text-primary: #2E3D28;
            --color-text-secondary: #586552;
            --color-border: #D5D8C8;
            --color-success: #198754;
            --color-error: #DC3545;
            --shadow: 0 4px 16px rgba(46, 61, 40, 0.08);
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--color-background);
            color: var(--color-text-primary);
            min-height: 100vh;
            padding: 2rem 1.5rem;
        }
        .demo-container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .demo-header {
            background: var(--color-brand);
            color: #FAF9F6;
            padding: 1.75rem 2rem;
            border-radius: 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow);
        }
        .demo-title h1 {
            font-family: 'Poppins', sans-serif;
            font-size: 1.5rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.65rem;
        }
        .demo-title p {
            font-size: 0.88rem;
            color: #E5E1D9;
            margin-top: 0.35rem;
        }
        .demo-back-btn {
            background: rgba(255, 255, 255, 0.12);
            color: #FAF9F6;
            text-decoration: none;
            padding: 0.55rem 1.1rem;
            border-radius: 8px;
            font-size: 0.88rem;
            font-weight: 600;
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.2s;
        }
        .demo-back-btn:hover {
            background: rgba(255, 255, 255, 0.22);
            color: #FFFFFF;
        }
        .grid-layout {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            align-items: start;
        }
        @media (max-width: 960px) {
            .grid-layout { grid-template-columns: 1fr; }
        }
        .card {
            background: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: 16px;
            padding: 1.75rem;
            box-shadow: var(--shadow);
        }
        .card-header {
            border-bottom: 1px solid var(--color-border);
            padding-bottom: 1rem;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .card-header h2 {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--color-brand);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .config-badge-row {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }
        .config-pill {
            background: #F0F5F2;
            border: 1px solid #D1DFD7;
            padding: 0.35rem 0.75rem;
            border-radius: 6px;
            font-size: 0.78rem;
            font-weight: 600;
            color: var(--color-brand);
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
        }
        .config-pill i { color: #198754; }
        .form-group {
            margin-bottom: 1.15rem;
        }
        .form-group label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--color-text-secondary);
            margin-bottom: 0.35rem;
        }
        .form-control {
            width: 100%;
            padding: 0.7rem 0.9rem;
            border: 1.5px solid var(--color-border);
            border-radius: 8px;
            font-family: inherit;
            font-size: 0.92rem;
            color: var(--color-text-primary);
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
            background: #FAF9F6;
        }
        .form-control:focus {
            border-color: var(--color-brand);
            box-shadow: 0 0 0 3px rgba(11, 60, 38, 0.12);
            background: #FFFFFF;
        }
        .btn-submit {
            width: 100%;
            padding: 0.9rem 1.5rem;
            background: var(--color-accent);
            color: #FFFFFF;
            border: 1.5px solid #C4825E;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(184, 115, 80, 0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 1.5rem;
        }
        .btn-submit:hover {
            background: var(--color-accent-hover);
            border-color: #B36F4D;
            color: #FFFFFF;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(184, 115, 80, 0.35);
        }
        .btn-submit:active { transform: translateY(0); }
        .alert-box {
            padding: 1rem 1.25rem;
            border-radius: 10px;
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.65rem;
            line-height: 1.45;
        }
        .alert-success {
            background: #E8F5E9;
            color: #1E4620;
            border: 1.5px solid #A5D6A7;
        }
        .alert-warning {
            background: #FFF8E1;
            color: #6D4C00;
            border: 1.5px solid #FFE082;
        }
        .alert-error {
            background: #FFEBEE;
            color: #B71C1C;
            border: 1.5px solid #FFCDD2;
        }
        .preview-wrapper {
            background: #FAF9F6;
            border: 1px solid var(--color-border);
            border-radius: 12px;
            padding: 1rem;
            overflow: hidden;
        }
        .preview-iframe {
            width: 100%;
            height: 520px;
            border: none;
            border-radius: 8px;
            background: #FFFFFF;
        }
    </style>
</head>
<body>
    <div class="demo-container">
        <!-- Top Banner -->
        <header class="demo-header">
            <div class="demo-title">
                <h1><i class="fa-solid fa-paper-plane" style="color: var(--color-accent);"></i> BharatBuy SMTP Mailer Demo</h1>
                <p>Real-time price drop notification dispatch engine powered by PHPMailer &amp; Google SMTP</p>
            </div>
            <div>
                <a href="index.php" class="demo-back-btn"><i class="fa-solid fa-arrow-left"></i> Return to Store</a>
            </div>
        </header>

        <?php if ($statusMessage): ?>
            <div class="alert-box alert-<?= $statusType; ?>">
                <i class="fa-solid <?= $statusType === 'success' ? 'fa-circle-check' : 'fa-triangle-exclamation'; ?> fa-lg"></i>
                <div>
                    <strong><?= $statusType === 'success' ? 'Delivery Confirmed!' : 'Notification Status'; ?>:</strong>
                    <?= htmlspecialchars($statusMessage); ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="grid-layout">
            <!-- Left Panel: Interactive SMTP Sender Form -->
            <div class="card">
                <div class="card-header">
                    <h2><i class="fa-solid fa-sliders"></i> Test Mailer Controls</h2>
                    <span style="font-size: 0.8rem; font-weight: 600; color: var(--color-text-secondary);"><i class="fa-solid fa-bolt" style="color: #198754;"></i> Online</span>
                </div>

                <!-- SMTP Server Credentials Overview -->
                <div class="config-badge-row">
                    <span class="config-pill"><i class="fa-solid fa-server"></i> <?= htmlspecialchars($smtpHost); ?></span>
                    <span class="config-pill"><i class="fa-solid fa-shield-halved"></i> Port <?= htmlspecialchars($smtpPort); ?> (<?= strtoupper($smtpSecure); ?>)</span>
                    <span class="config-pill"><i class="fa-solid fa-user-check"></i> <?= htmlspecialchars($smtpUser); ?></span>
                </div>

                <form method="POST" action="test_smtp_mailer.php">
                    <div class="form-group">
                        <label for="to_email">Recipient Email Address</label>
                        <input type="email" id="to_email" name="to_email" class="form-control" value="<?= htmlspecialchars($toEmail); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="user_name">Customer Full Name</label>
                        <input type="text" id="user_name" name="user_name" class="form-control" value="<?= htmlspecialchars($userName); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="product_name">Tracked Product Title</label>
                        <input type="text" id="product_name" name="product_name" class="form-control" value="<?= htmlspecialchars($productName); ?>" required>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label for="old_price">Original Price (₹)</label>
                            <input type="number" step="0.01" id="old_price" name="old_price" class="form-control" value="<?= htmlspecialchars((string)$oldPrice); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="new_price">New Dropped Price (₹)</label>
                            <input type="number" step="0.01" id="new_price" name="new_price" class="form-control" value="<?= htmlspecialchars((string)$newPrice); ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="product_url">Product Outbound Deal URL</label>
                        <input type="url" id="product_url" name="product_url" class="form-control" value="<?= htmlspecialchars($productUrl); ?>" required>
                    </div>

                    <button type="submit" name="send_test_email" value="1" class="btn-submit" id="submitBtn">
                        <i class="fa-solid fa-paper-plane"></i> Send Live SMTP Test Email
                    </button>
                </form>
            </div>

            <!-- Right Panel: Live HTML Email Template Preview -->
            <div class="card">
                <div class="card-header">
                    <h2><i class="fa-solid fa-envelope-open-text"></i> Live Template Preview</h2>
                    <a href="mail_preview.html" target="_blank" style="font-size: 0.82rem; font-weight: 600; color: var(--color-brand); text-decoration: none;">
                        Open Standalone <i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 0.75rem;"></i>
                    </a>
                </div>

                <p style="font-size: 0.85rem; color: var(--color-text-secondary); margin-bottom: 1rem;">
                    This preview shows the exact responsive HTML template rendered with the Royal Emerald &amp; Rose Gold heritage styling that customers receive in their inbox:
                </p>

                <div class="preview-wrapper">
                    <iframe class="preview-iframe" srcdoc="<?= htmlspecialchars($previewHtml, ENT_QUOTES, 'UTF-8'); ?>"></iframe>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Provide visual feedback during SMTP handshake
        const form = document.querySelector('form');
        const submitBtn = document.getElementById('submitBtn');
        if (form && submitBtn) {
            form.addEventListener('submit', function() {
                submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Connecting to SMTP server...';
                submitBtn.style.pointerEvents = 'none';
                submitBtn.style.opacity = '0.85';
            });
        }
    </script>
</body>
</html>
