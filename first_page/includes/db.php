<?php
// includes/db.php
$DB_HOST = getenv('DB_HOST') ?: 'localhost';
$DB_NAME = 'marjanpartner';
$DB_USER = getenv('DB_USER') ?: 'root';
$DB_PASS = getenv('DB_PASS') ?: '';
$DB_CHARSET = 'utf8mb4';

$dsn = "mysql:host={$DB_HOST};dbname={$DB_NAME};charset={$DB_CHARSET}";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, $options);
} catch (Throwable $e) {
    http_response_code(500);
    echo 'DB connection failed. Update credentials in includes/db.php';
    exit;
}

// Cart database (cartpartner) connection
function getCartDbConnection() {
    global $DB_HOST, $DB_USER, $DB_PASS, $DB_CHARSET;
    
    $CART_DBNAME = getenv('CART_DBNAME') ?: 'cartpartner';
    $cartDsn = "mysql:host={$DB_HOST};dbname={$CART_DBNAME};charset={$DB_CHARSET}";
    
    try {
        $pdoCart = new PDO($cartDsn, $DB_USER, $DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
        return $pdoCart;
    } catch (Throwable $e) {
        error_log("Cart DB connection failed: " . $e->getMessage());
        throw new Exception('Cart database connection failed');
    }
}
