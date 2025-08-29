<?php
/**
 * Admin Dashboard Setup Script
 * Creates admin user and necessary database structure
 */

require_once 'config.php';

echo "<h2>Admin Dashboard Setup</h2>";

try {
    // Test database connections
    echo "<h3>Testing Database Connections...</h3>";
    
    $mainPdo = getMainDbConnection();
    echo "✅ Main database (marjanpartner) connected successfully<br>";
    
    $cartPdo = getCartDbConnection();
    echo "✅ Cart database (cartpartner) connected successfully<br>";
    
    $planPdo = getPlanDbConnection();
    echo "✅ Plan database (planpartner) connected successfully<br>";
    
    $equipmentPdo = getEquipmentDbConnection();
    echo "✅ Equipment database (equipementpartner) connected successfully<br>";
    
    // Create admin_users table if it doesn't exist
    echo "<h3>Setting up admin users table...</h3>";
    
    $sql = "CREATE TABLE IF NOT EXISTS admin_users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        password_hash VARCHAR(255) NOT NULL,
        full_name VARCHAR(100) NOT NULL,
        email VARCHAR(100),
        role ENUM('admin', 'moderator') DEFAULT 'admin',
        status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    
    $mainPdo->exec($sql);
    echo "✅ Admin users table created/verified<br>";
    
    // Check if admin user already exists
    $stmt = $mainPdo->prepare("SELECT id FROM admin_users WHERE username = ?");
    $stmt->execute(['admin']);
    $existingAdmin = $stmt->fetch();
    
    if (!$existingAdmin) {
        // Create default admin user
        $adminPassword = 'admin123'; // Change this password!
        $passwordHash = password_hash($adminPassword, PASSWORD_DEFAULT);
        
        $stmt = $mainPdo->prepare("INSERT INTO admin_users (username, password_hash, full_name, email, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute(['admin', $passwordHash, 'Administrator', 'admin@marjanpartners.com', 'admin']);
        
        echo "✅ Default admin user created<br>";
        echo "⚠️ <strong>Username: admin</strong><br>";
        echo "⚠️ <strong>Password: admin123</strong><br>";
        echo "⚠️ <strong>IMPORTANT: Change this password after first login!</strong><br>";
    } else {
        echo "✅ Admin user already exists<br>";
    }
    
    // Show database statistics
    echo "<h3>Database Statistics:</h3>";
    
    // Partners count
    $stmt = $mainPdo->query("SELECT COUNT(*) FROM partners");
    $partnersCount = $stmt->fetchColumn();
    echo "👥 Partners: {$partnersCount}<br>";
    
    // Orders count
    $stmt = $cartPdo->query("SELECT COUNT(*) FROM orders");
    $ordersCount = $stmt->fetchColumn();
    echo "🛒 Orders: {$ordersCount}<br>";
    
    // Plan requests count
    $stmt = $planPdo->query("SELECT COUNT(*) FROM plan_requests");
    $plansCount = $stmt->fetchColumn();
    echo "📋 Plan Requests: {$plansCount}<br>";
    
    // Equipment requests count
    $stmt = $equipmentPdo->query("SELECT COUNT(*) FROM requests");
    $equipmentCount = $stmt->fetchColumn();
    echo "🔧 Equipment Requests: {$equipmentCount}<br>";
    
    echo "<br><h3>✅ Setup Complete!</h3>";
    echo "<p>You can now <a href='index.php'>login to the admin dashboard</a>.</p>";
    echo "<p><strong>Default credentials:</strong><br>";
    echo "Username: <code>admin</code><br>";
    echo "Password: <code>admin123</code></p>";
    
} catch (Exception $e) {
    echo "<div style='color: red;'>❌ Setup failed: " . $e->getMessage() . "</div>";
    echo "<p>Please check your database configuration and try again.</p>";
}
?>

<style>
body {
    font-family: Arial, sans-serif;
    max-width: 800px;
    margin: 50px auto;
    padding: 20px;
    background: #f5f5f5;
}

h2, h3 {
    color: #333;
}

.alert {
    padding: 15px;
    margin: 15px 0;
    border-radius: 5px;
}

.alert-success {
    background: #d4edda;
    border: 1px solid #c3e6cb;
    color: #155724;
}

.alert-danger {
    background: #f8d7da;
    border: 1px solid #f5c6cb;
    color: #721c24;
}

code {
    background: #f8f9fa;
    padding: 2px 6px;
    border-radius: 3px;
    font-family: monospace;
}
</style>
