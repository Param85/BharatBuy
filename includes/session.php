<?php
require_once __DIR__ . '/config.php';

function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function redirectIfLoggedOut() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}

/**
 * Returns the current session's CSRF token, generating one if not already set.
 */
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validates a submitted CSRF token using timing-safe comparison.
 */
function verify_csrf_token(?string $token): bool {
    if (empty($token) || empty($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Enforces CSRF token validation on state-changing requests.
 * Fails closed with 403 Forbidden if invalid.
 */
function validate_csrf_or_exit(): void {
    $submittedToken = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
    if (!verify_csrf_token($submittedToken)) {
        http_response_code(403);
        $isAjax = (
            (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') ||
            (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) ||
            (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false)
        );
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'CSRF verification failed. Request rejected.']);
        } else {
            echo "<h1>403 Forbidden</h1><p>CSRF verification failed. Please refresh the page and try again.</p>";
        }
        exit();
    }
}

/**
 * Regenerates the session identifier upon privilege transition to prevent session fixation.
 */
function regenerate_session_id(): void {
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_regenerate_id(true);
    }
}

/**
 * Clears and destroys the user session and invalidates the session cookie.
 */
function destroy_user_session(): void {
    if (session_status() === PHP_SESSION_ACTIVE) {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        session_destroy();
    }
}

/**
 * Validates whether a URL is a safe external HTTP/HTTPS web address or relative link,
 * explicitly disallowing javascript:, data:, and vbscript: protocols.
 */
function is_safe_url(?string $url): bool {
    if (empty($url) || $url === '#') {
        return true;
    }
    $trimmed = trim($url);
    if (preg_match('/^(javascript|data|vbscript|file):/i', $trimmed)) {
        return false;
    }
    // Allow relative URLs starting with / or alphanumeric
    if (strpos($trimmed, '/') === 0 || strpos($trimmed, '?') === 0 || preg_match('/^[a-zA-Z0-9_\-\.]+\.php/i', $trimmed)) {
        return true;
    }
    // For absolute URLs, require http or https scheme
    $scheme = parse_url($trimmed, PHP_URL_SCHEME);
    return in_array(strtolower($scheme ?: ''), ['http', 'https'], true);
}

// ==========================================================================
// 14. UTM Tracking Attribution Engine
// ==========================================================================
if (!empty($_GET['utm_source']) || !empty($_GET['utm_campaign']) || !empty($_GET['utm_medium'])) {
    $utmKeys = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'];
    $capturedUtm = [];
    foreach ($utmKeys as $key) {
        if (!empty($_GET[$key])) {
            $capturedUtm[$key] = substr(strip_tags($_GET[$key]), 0, 120);
        }
    }
    if (!empty($capturedUtm)) {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION['utm_data'] = array_merge($_SESSION['utm_data'] ?? [], $capturedUtm);
        }
    }
}

function get_utm_data(): array {
    return $_SESSION['utm_data'] ?? [];
}


