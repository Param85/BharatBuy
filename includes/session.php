<?php
require_once __DIR__ . '/config.php';
function isLoggedIn() { return isset($_SESSION['user_id']); }
function redirectIfLoggedOut() {
    if (!isLoggedIn()) { header("Location: login.php"); exit(); }
}
