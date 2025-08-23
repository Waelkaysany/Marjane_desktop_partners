<?php
require_once __DIR__ . '/includes/session.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_verify($_POST['csrf'] ?? null)) {
    session_logout();
}
header('Location: login.php?m=logged_out');
exit;
