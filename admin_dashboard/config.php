<?php
/**
 * Admin Dashboard Configuration
 * Central configuration for managing first_page project data
 */

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Database Names from first_page project
define('MAIN_DB_NAME', 'marjanpartner');
define('CART_DB_NAME', 'cartpartner');
define('PLAN_DB_NAME', 'planpartner');
define('EQUIPMENT_DB_NAME', 'equipementpartner');

// Session Configuration
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
    session_start();
}

// Security Configuration
define('CSRF_TOKEN_NAME', 'csrf_token');
define('LOGIN_ATTEMPTS_LIMIT', 5);
define('LOGIN_LOCKOUT_TIME', 300); // 5 minutes

// Application Configuration
define('ITEMS_PER_PAGE', 20);
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif']);

// Global PDO connections
$mainPdo = null;
$cartPdo = null;
$planPdo = null;
$equipmentPdo = null;

/**
 * Get main database connection (marjanpartner)
 */
function getMainDbConnection() {
    global $mainPdo;
    
    if ($mainPdo === null) {
        try {
            $mainPdo = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . MAIN_DB_NAME . ";charset=" . DB_CHARSET,
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (Exception $e) {
            error_log("Main DB connection failed: " . $e->getMessage());
            throw new Exception('Main database connection failed');
        }
    }
    
    return $mainPdo;
}

/**
 * Get cart database connection (cartpartner)
 */
function getCartDbConnection() {
    global $cartPdo;
    
    if ($cartPdo === null) {
        try {
            $cartPdo = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . CART_DB_NAME . ";charset=" . DB_CHARSET,
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (Exception $e) {
            error_log("Cart DB connection failed: " . $e->getMessage());
            throw new Exception('Cart database connection failed');
        }
    }
    
    return $cartPdo;
}

/**
 * Get plan database connection (planpartner)
 */
function getPlanDbConnection() {
    global $planPdo;
    
    if ($planPdo === null) {
        try {
            $planPdo = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . PLAN_DB_NAME . ";charset=" . DB_CHARSET,
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (Exception $e) {
            error_log("Plan DB connection failed: " . $e->getMessage());
            throw new Exception('Plan database connection failed');
        }
    }
    
    return $planPdo;
}

/**
 * Get equipment database connection (equipementpartner)
 */
function getEquipmentDbConnection() {
    global $equipmentPdo;
    
    if ($equipmentPdo === null) {
        try {
            $equipmentPdo = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . EQUIPMENT_DB_NAME . ";charset=" . DB_CHARSET,
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (Exception $e) {
            error_log("Equipment DB connection failed: " . $e->getMessage());
            throw new Exception('Equipment database connection failed');
        }
    }
    
    return $equipmentPdo;
}

/**
 * Initialize all database connections
 */
function initializeDatabases() {
    try {
        // Test all connections
        getMainDbConnection();
        getCartDbConnection();
        getPlanDbConnection();
        getEquipmentDbConnection();
        
        return true;
    } catch (Exception $e) {
        error_log("Database initialization failed: " . $e->getMessage());
        return false;
    }
}

/**
 * Get base URL for admin dashboard
 */
function getBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $scriptName = $_SERVER['SCRIPT_NAME'];
    
    // Remove the current script name to get the directory
    $path = dirname($scriptName);
    
    // Ensure we end with a slash
    if (substr($path, -1) !== '/') {
        $path .= '/';
    }
    
    return $protocol . '://' . $host . $path;
}

/**
 * Generate CSRF token
 */
function generateCSRFToken() {
    if (empty($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

/**
 * Verify CSRF token
 */
function verifyCSRFToken($token) {
    return isset($_SESSION[CSRF_TOKEN_NAME]) && hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}

/**
 * Sanitize input
 */
function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Format date
 */
function formatDate($date, $format = 'Y-m-d H:i:s') {
    return date($format, strtotime($date));
}

/**
 * Format currency
 */
function formatCurrency($amount, $currency = 'MAD') {
    return number_format($amount, 2) . ' ' . $currency;
}

/**
 * Get status badge class
 */
function getStatusBadgeClass($status) {
    $classes = [
        'pending' => 'bg-warning',
        'processing' => 'bg-info',
        'paid' => 'bg-primary',
        'completed' => 'bg-success',
        'cancelled' => 'bg-danger',
        'approved' => 'bg-success',
        'rejected' => 'bg-danger',
        'active' => 'bg-success',
        'inactive' => 'bg-secondary',
        'suspended' => 'bg-warning',
        'deleted' => 'bg-danger'
    ];
    
    return $classes[$status] ?? 'bg-secondary';
}

/**
 * Get status display name
 */
function getStatusDisplayName($status) {
    $names = [
        'pending' => 'Pending',
        'processing' => 'Processing',
        'paid' => 'Paid',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        'active' => 'Active',
        'inactive' => 'Inactive',
        'suspended' => 'Suspended',
        'deleted' => 'Deleted'
    ];
    
    return $names[$status] ?? ucfirst($status);
}

/**
 * Get status badge HTML
 */
function getStatusBadge($status) {
    $class = getStatusBadgeClass($status);
    $displayName = getStatusDisplayName($status);
    return '<span class="badge ' . $class . '">' . $displayName . '</span>';
}
?>
