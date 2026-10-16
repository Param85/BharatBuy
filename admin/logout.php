<?php
/**
 * Admin Logout
 */
require_once __DIR__ . '/../includes/session.php';
destroy_user_session();
header("Location: login.php");
exit();

