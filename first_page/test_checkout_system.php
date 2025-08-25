<?php
// test_checkout_system.php - Test script for checkout system
require_once 'includes/session.php';
require_once 'includes/db.php';

echo "<h1>Checkout System Test</h1>\n";

// Test 1: Database connections
echo "<h2>Test 1: Database Connections</h2>\n";
try {
    // Test main database
    $pdo = new PDO("mysql:host=localhost;dbname=marjanpartner;charset=utf8mb4", "root", "", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    echo "✓ Main database (marjanpartner) connection successful<br>\n";
    
    // Test cart database
    $pdoCart = getCartDbConnection();
    echo "✓ Cart database (cartpartner) connection successful<br>\n";
    
} catch (Exception $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "<br>\n";
    exit;
}

// Test 2: Check if test partner exists
echo "<h2>Test 2: Test Partner Account</h2>\n";
try {
    $stmt = $pdo->prepare("SELECT id, username, full_name FROM partners WHERE username = ?");
    $stmt->execute(['wail']);
    $partner = $stmt->fetch();
    
    if ($partner) {
        echo "✓ Test partner found: {$partner['username']} ({$partner['full_name']}) - ID: {$partner['id']}<br>\n";
        $testPartnerId = $partner['id'];
    } else {
        echo "✗ Test partner 'wail' not found<br>\n";
        echo "Creating test partner account...<br>\n";
        
        // Create test partner if it doesn't exist
        $stmt = $pdo->prepare("INSERT INTO partners (username, password, full_name, email, phone, role) VALUES (?, ?, ?, ?, ?, ?)");
        $hashedPassword = password_hash('wailelkaysany', PASSWORD_DEFAULT);
        $stmt->execute(['wail', $hashedPassword, 'Wail El Kaysani', 'wail@example.com', '+1234567890', 'partner']);
        
        $testPartnerId = $pdo->lastInsertId();
        echo "✓ Test partner created with ID: $testPartnerId<br>\n";
    }
    
} catch (Exception $e) {
    echo "✗ Partner check failed: " . $e->getMessage() . "<br>\n";
    exit;
}

// Test 3: Check cartpartner database structure
echo "<h2>Test 3: Cart Database Structure</h2>\n";
try {
    // Check if orders table exists
    $stmt = $pdoCart->query("SHOW TABLES LIKE 'orders'");
    if ($stmt->rowCount() > 0) {
        echo "✓ Orders table exists<br>\n";
    } else {
        echo "✗ Orders table not found<br>\n";
        echo "Please run create_cartpartner_db.sql first<br>\n";
        exit;
    }
    
    // Check if cart_items table exists
    $stmt = $pdoCart->query("SHOW TABLES LIKE 'cart_items'");
    if ($stmt->rowCount() > 0) {
        echo "✓ Cart items table exists<br>\n";
    } else {
        echo "✗ Cart items table not found<br>\n";
        echo "Please run create_cartpartner_db.sql first<br>\n";
        exit;
    }
    
} catch (Exception $e) {
    echo "✗ Database structure check failed: " . $e->getMessage() . "<br>\n";
    exit;
}

// Test 4: Test CSRF token generation
echo "<h2>Test 4: CSRF Token</h2>\n";
try {
    $token = csrf_token();
    if ($token && strlen($token) > 0) {
        echo "✓ CSRF token generated: " . substr($token, 0, 16) . "...<br>\n";
    } else {
        echo "✗ CSRF token generation failed<br>\n";
    }
} catch (Exception $e) {
    echo "✗ CSRF token test failed: " . $e->getMessage() . "<br>\n";
}

// Test 5: Test order number generation
echo "<h2>Test 5: Order Number Generation</h2>\n";
try {
    $orderNumber = 'MP-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
    echo "✓ Sample order number generated: $orderNumber<br>\n";
} catch (Exception $e) {
    echo "✗ Order number generation failed: " . $e->getMessage() . "<br>\n";
}

// Test 6: Test session functions
echo "<h2>Test 6: Session Functions</h2>\n";
try {
    if (function_exists('require_auth')) {
        echo "✓ require_auth function exists<br>\n";
    } else {
        echo "✗ require_auth function not found<br>\n";
    }
    
    if (function_exists('csrf_verify')) {
        echo "✓ csrf_verify function exists<br>\n";
    } else {
        echo "✗ csrf_verify function not found<br>\n";
    }
    
} catch (Exception $e) {
    echo "✗ Session functions test failed: " . $e->getMessage() . "<br>\n";
}

// Test 7: File existence check
echo "<h2>Test 7: File Existence</h2>\n";
$requiredFiles = [
    'checkout.php',
    'cart.js',
    'cart.css',
    'order-confirmation-simple.php',
    'includes/db.php',
    'includes/session.php'
];

foreach ($requiredFiles as $file) {
    if (file_exists($file)) {
        echo "✓ $file exists<br>\n";
    } else {
        echo "✗ $file not found<br>\n";
    }
}

echo "<h2>Test Summary</h2>\n";
echo "<p>If all tests passed, your checkout system is ready for testing!</p>\n";
echo "<p><strong>Next steps:</strong></p>\n";
echo "<ol>\n";
echo "<li>Login with username: <strong>wail</strong> and password: <strong>wailelkaysany</strong></li>\n";
echo "<li>Add items to cart</li>\n";
echo "<li>Navigate to cart.php</li>\n";
echo "<li>Click 'Proceed to Checkout'</li>\n";
echo "<li>Verify the Order Completed modal appears</li>\n";
echo "</ol>\n";

echo "<p><strong>Test partner details:</strong></p>\n";
echo "<ul>\n";
echo "<li>Username: wail</li>\n";
echo "<li>Password: wailelkaysany</li>\n";
echo "<li>Partner ID: $testPartnerId</li>\n";
echo "</ul>\n";
?>


