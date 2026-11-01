<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/PHPMailer/Exception.php';
require_once __DIR__ . '/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/SMTP.php';

/**
 * Generates the responsive HTML email markup for price drop alerts.
 */
function getPriceAlertEmailHtml($userName, $productName, $oldPrice, $newPrice, $productUrl) {
    $savings = $oldPrice - $newPrice;
    $savingsPercent = ($oldPrice > 0) ? round(($savings / $oldPrice) * 100) : 0;

    return "
    <html>
    <head>
        <meta charset='UTF-8'>
        <title>Price Drop Alert - BharatBuy</title>
        <link href='https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Poppins:wght@600;700;800&display=swap' rel='stylesheet'>
        <style>
            body { font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #E8E9DF; padding: 24px 12px; color: #2E3D28; margin: 0; }
            .container { max-width: 580px; margin: 0 auto; background-color: #FFFFFF; border-radius: 16px; border: 1px solid #D5D8C8; overflow: hidden; box-shadow: 0 4px 16px rgba(46, 61, 40, 0.08); }
            .header { background: #2E3D28; color: #E8E9DF; padding: 32px 24px; text-align: center; }
            .header h1 { margin: 0; font-family: 'Poppins', sans-serif; font-size: 22px; font-weight: 800; letter-spacing: -0.01em; color: #E8E9DF; }
            .content { padding: 32px 28px; }
            .greeting { font-size: 16px; font-weight: 700; margin-bottom: 8px; color: #2E3D28; }
            .intro { font-size: 14.5px; color: #586552; line-height: 1.6; margin-bottom: 24px; }
            .deal-card { background-color: #E8E9DF; border: 1.5px solid #D5D8C8; border-radius: 12px; padding: 22px 18px; text-align: center; margin-bottom: 24px; }
            .product-title { font-size: 17px; font-weight: 700; color: #2E3D28; margin-bottom: 14px; line-height: 1.4; }
            .price-comparison { display: flex; justify-content: center; align-items: baseline; gap: 16px; margin-bottom: 12px; }
            .price-old { font-size: 16px; text-decoration: line-through; color: #8C9EAF; font-weight: 600; margin-right: 12px; }
            .price-new { font-size: 26px; color: #2E3D28; font-weight: 800; }
            .savings-badge { display: inline-block; background-color: #A9B887; color: #2E3D28; padding: 6px 14px; border-radius: 6px; font-size: 12px; font-weight: 700; margin-top: 8px; border: 1px solid #96A673; }
            .btn { display: inline-block; background-color: #2E3D28; color: #FFFFFF !important; text-decoration: none; padding: 13px 32px; border-radius: 10px; font-weight: 700; font-size: 15px; margin-top: 18px; border: 1.5px solid #202B1C; box-shadow: 0 4px 12px rgba(46, 61, 40, 0.25); }
            .footer { background-color: #E8E9DF; padding: 20px 24px; text-align: center; font-size: 12px; color: #586552; border-top: 1px solid #D5D8C8; line-height: 1.5; }
            .footer p { margin: 4px 0; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>BharatBuy Price Drop Alert</h1>
            </div>
            <div class='content'>
                <div class='greeting'>Namaste " . htmlspecialchars($userName) . ",</div>
                <div class='intro'>Great news! A product you are actively tracking on BharatBuy has dropped in price. Here are the updated details:</div>
                
                <div class='deal-card'>
                    <div class='product-title'>" . htmlspecialchars($productName) . "</div>
                    <div class='price-comparison'>
                        <span class='price-old'>Was: ₹" . number_format($oldPrice, 2) . "</span>
                        <span class='price-new'>Now: ₹" . number_format($newPrice, 2) . "</span>
                    </div>
                    <span class='savings-badge'>SAVE " . $savingsPercent . "% (₹" . number_format($savings, 2) . ")</span>
                    <br>
                    <a href='" . htmlspecialchars($productUrl) . "' class='btn'>Go to Deal Store &rarr;</a>
                </div>
            </div>
            <div class='footer'>
                <p>You received this email because you subscribed to price drop alerts on BharatBuy.</p>
                <p>&copy; " . date('Y') . " BharatBuy. All rights reserved.</p>
            </div>
        </div>
    </body>
    </html>
    ";
}

/**
 * Sends a premium HTML email price alert notification to the user via PHPMailer SMTP.
 */
function sendPriceAlertEmail($toEmail, $userName, $productName, $oldPrice, $newPrice, $productUrl) {
    $subject = "Price Drop Alert: " . $productName;
    $message = getPriceAlertEmailHtml($userName, $productName, $oldPrice, $newPrice, $productUrl);

    // Save live email preview for debugging and demonstration
    @file_put_contents(__DIR__ . '/../mail_preview.html', $message);

    $smtpHost = $_ENV['SMTP_HOST'] ?? $_SERVER['SMTP_HOST'] ?? getenv('SMTP_HOST');
    $smtpUser = $_ENV['SMTP_USER'] ?? $_SERVER['SMTP_USER'] ?? getenv('SMTP_USER');
    $smtpPass = $_ENV['SMTP_PASS'] ?? $_SERVER['SMTP_PASS'] ?? getenv('SMTP_PASS');
    
    if ($smtpHost && $smtpHost !== 'your_smtp_username' && $smtpUser && $smtpUser !== 'your_smtp_username') {
        try {
            $mail = new PHPMailer(true);
            $mail->CharSet = 'UTF-8';
            $mail->isSMTP();
            $mail->Host = $smtpHost;
            $mail->SMTPAuth = true;
            $mail->Username = $smtpUser;
            $mail->Password = $smtpPass;
            
            $secure = strtolower(($_ENV['SMTP_SECURE'] ?? $_SERVER['SMTP_SECURE'] ?? getenv('SMTP_SECURE')) ?: 'tls');
            if ($secure === 'ssl') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            } else {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            }
            $mail->Port = (int)(($_ENV['SMTP_PORT'] ?? $_SERVER['SMTP_PORT'] ?? getenv('SMTP_PORT')) ?: 587);

            $fromEmail = ($_ENV['SMTP_FROM_EMAIL'] ?? $_SERVER['SMTP_FROM_EMAIL'] ?? getenv('SMTP_FROM_EMAIL')) ?: 'alerts@bharatbuy.com';
            $fromName = ($_ENV['SMTP_FROM_NAME'] ?? $_SERVER['SMTP_FROM_NAME'] ?? getenv('SMTP_FROM_NAME')) ?: 'BharatBuy Alerts';
            $mail->setFrom($fromEmail, $fromName);
            $mail->addAddress($toEmail, $userName);

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $message;

            return $mail->send();
        } catch (Exception $e) {
            error_log("PHPMailer Error sending email: " . $e->getMessage());
            // fall back to standard mail if PHPMailer fails
        }
    }

    // Fallback: Send using standard PHP mail (configured on local server/Sendmail/SMTP)
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: BharatBuy Alerts <noreply@localhost>" . "\r\n";
    return @mail($toEmail, $subject, $message, $headers);
}

