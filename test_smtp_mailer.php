<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/mailer.php';

echo "BharatBuy SMTP Mailer Test Utility\n";
echo "==================================\n";
echo "Loaded config environment variables:\n";
echo "SMTP Host: " . (getenv('SMTP_HOST') ?: 'Not Set') . "\n";
echo "SMTP Port: " . (getenv('SMTP_PORT') ?: 'Not Set') . "\n";
echo "SMTP Secure: " . (getenv('SMTP_SECURE') ?: 'Not Set') . "\n";
echo "SMTP User: " . (getenv('SMTP_USER') ?: 'Not Set') . "\n";
echo "SMTP From Email: " . (getenv('SMTP_FROM_EMAIL') ?: 'Not Set') . "\n";
echo "SMTP From Name: " . (getenv('SMTP_FROM_NAME') ?: 'Not Set') . "\n\n";

$to = 'paramshah647@gmail.com';
$name = 'Param Shah';
$productName = 'ASUS ROG Raikiri II Xbox Wireless Gaming Controller';
$oldPrice = 16995.77;
$newPrice = 15700.86;
$productUrl = 'http://localhost/BharatBuy/product-details.php?id=122';

echo "Sending test price drop alert email to $to ($name)...\n";
$start = microtime(true);
$result = sendPriceAlertEmail($to, $name, $productName, $oldPrice, $newPrice, $productUrl);
$duration = round((microtime(true) - $start) * 1000, 2);

if ($result) {
    echo "SUCCESS: Email sent successfully in {$duration} ms!\n";
} else {
    echo "WARNING/FAILURE: Email function returned false or fell back. Check logs.\n";
}

$previewFile = __DIR__ . '/mail_preview.html';
if (file_exists($previewFile)) {
    echo "Email HTML preview successfully updated at: $previewFile (" . filesize($previewFile) . " bytes)\n";
} else {
    echo "ERROR: mail_preview.html was not generated.\n";
}
