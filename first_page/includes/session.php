<?php
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.use_strict_mode', 1);
    ini_set('session.cookie_httponly', 1);
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        ini_set('session.cookie_secure', 1);
    }
    session_name('mp_sess');
    session_start();
}

function session_login(array $user): void {
    session_regenerate_id(true);
    $_SESSION['auth'] = [
        'partner_id' => (int)$user['id'],
        'username'   => (string)$user['username'],
        'full_name'  => (string)$user['full_name'],
        'role'       => (string)$user['role'],
        'last_active'=> time(),
    ];
}

function session_logout(): void {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time()-42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
}

function require_auth(): void {
    if (empty($_SESSION['auth']['partner_id'])) {
        header('Location: login.php?m=login_required');
        exit;
    }
}

function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_verify(?string $t): bool { return $t && hash_equals($_SESSION['csrf_token'] ?? '', $t); }
