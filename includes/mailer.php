<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/PHPMailer/Exception.php';
require_once __DIR__ . '/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/SMTP.php';

/**
 * Sends a premium HTML email price alert notification to the user.
 */
function sendPriceAlertEmail($toEmail, $userName, $productName, $oldPrice, $newPrice, $productUrl) {
    $subject = "Price Drop Alert: " . $productName;
    
    $savings = $oldPrice - $newPrice;
    $savingsPercent = round(($savings / $oldPrice) * 100);

    // Styling email template with BharatBuy Emerald & Champagne theme
    $message = "
    <html>
    <head>
        <meta charset='UTF-8'>
        <title>Price Drop Alert</title>
        <link href='https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap' rel='stylesheet'>
        <style>
            body { font-family: 'Outfit', 'Helvetica Neue', Helvetica, Arial, sans-serif; background-color: #f8fafc; padding: 20px; color: #0f172a; margin: 0; }
            .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.02); }
            .header { background: linear-gradient(135deg, #06433B 0%, #0A5A50 100%); color: #F8E7C9; padding: 30px; text-align: center; }
            .header h1 { margin: 0; font-size: 24px; font-weight: 800; }
            .content { padding: 30px; }
            .greeting { font-size: 16px; font-weight: 600; margin-bottom: 10px; color: #0f172a; }
            .intro { font-size: 15px; color: #64748b; line-height: 1.5; margin-bottom: 25px; }
            .deal-card { background-color: #f0fdf9; border: 1px solid #ccfbf1; border-radius: 10px; padding: 20px; text-align: center; margin-bottom: 25px; }
            .product-title { font-size: 18px; font-weight: 700; color: #06433B; margin-bottom: 15px; }
            .price-comparison { display: flex; justify-content: center; align-items: center; gap: 20px; margin-bottom: 10px; text-align: center; }
            .price-old { font-size: 18px; text-decoration: line-through; color: #94a3b8; font-weight: 600; margin-right: 15px; }
            .price-new { font-size: 28px; color: #06433B; font-weight: 800; }
            .savings-badge { display: inline-block; background-color: #06433B; color: #F8E7C9; padding: 5px 12px; border-radius: 6px; font-size: 12px; font-weight: 700; margin-top: 10px; }
            .btn { display: inline-block; background-color: #06433B; color: #F8E7C9; text-decoration: none; padding: 12px 30px; border-radius: 8px; font-weight: 700; font-size: 15px; margin-top: 15px; box-shadow: 0 4px 10px rgba(6, 67, 59, 0.2); }
            .footer { background-color: #f8fafc; padding: 20px; text-align: center; font-size: 12px; color: #64748b; border-top: 1px solid #e2e8f0; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>BharatBuy Alerts</h1>
            </div>
            <div class='content'>
                <div class='greeting'>Hello " . htmlspecialchars($userName) . ",</div>
                <div class='intro'>Good news! A product you are tracking has just dropped in price. Here is the deal we found:</div>
                
                <div class='deal-card'>
                    <div class='product-title'>" . htmlspecialchars($productName) . "</div>
                    <div class='price-comparison'>
                        <span class='price-old'>Was: ₹" . number_format($oldPrice, 2) . "</span>
                        <span class='price-new'>Now: ₹" . number_format($newPrice, 2) . "</span>
                    </div>
                    <span class='savings-badge'>SAVE " . $savingsPercent . "% (₹" . number_format($savings, 2) . ")</span>
                    <br>
                    <a href='" . htmlspecialchars($productUrl) . "' class='btn'>Buy Product Now</a>
                </div>
            </div>
            <div class='footer'>
                <p>You received this email because you subscribed to price alerts on BharatBuy.</p>
                <p>&copy; " . date('Y') . " BharatBuy. All rights reserved.</p>
            </div>
        </div>
    </body>
    </html>
    ";

    // Only write email preview if explicitly enabled in local debugging
    if (defined('APP_DEBUG') && APP_DEBUG === true && defined('ENABLE_EMAIL_PREVIEW') && ENABLE_EMAIL_PREVIEW === true) {
        @file_put_contents(__DIR__ . '/../mail_preview.html', $message);
    }

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

