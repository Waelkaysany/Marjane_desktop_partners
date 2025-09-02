<?php
/**
 * Admin Settings Page
 * System configuration and settings management
 */

require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

$message = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'update_settings') {
        // This is a placeholder for actual settings update logic
        // In a real application, you would save these to a database or config file
        $message = "Settings updated successfully!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Admin Panel</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        .admin-sidebar {
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            width: 250px;
            background: #2c3e50;
            color: white;
            overflow-y: auto;
            z-index: 1000;
        }
        
        .admin-main {
            margin-left: 250px;
            padding: 20px;
            background: #f8f9fa;
            min-height: 100vh;
        }
        
        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid #34495e;
            text-align: center;
        }
        
        .sidebar-brand {
            color: white;
            text-decoration: none;
            font-size: 1.5rem;
            font-weight: bold;
        }
        
        .nav-section {
            margin: 20px 0;
        }
        
        .nav-section-title {
            padding: 10px 20px;
            font-size: 0.8rem;
            text-transform: uppercase;
            color: #bdc3c7;
            font-weight: bold;
        }
        
        .nav-link {
            color: #ecf0f1;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .nav-link:hover {
            background: #34495e;
            color: white;
        }
        
        .nav-link.active {
            background: #3498db;
            color: white;
        }
        
        .nav-link i {
            margin-right: 10px;
            width: 20px;
        }
        
        .admin-navbar {
            background: white;
            padding: 15px 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .navbar-brand {
            font-size: 1.5rem;
            font-weight: bold;
            color: #2c3e50;
            text-decoration: none;
        }
        
        .user-info {
            text-align: right;
        }
        
        .user-name {
            margin: 0;
            font-weight: bold;
            color: #2c3e50;
        }
        
        .user-role {
            margin: 0;
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .btn-logout {
            color: #e74c3c;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        
        .btn-logout:hover {
            background: #e74c3c;
            color: white;
        }
        
        .settings-card {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .settings-section {
            margin-bottom: 30px;
        }
        
        .settings-section h4 {
            color: #2c3e50;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #3498db;
        }
        
        .alert {
            border-radius: 8px;
            border: none;
        }
        
        .btn-primary {
            background: #3498db;
            border: none;
            padding: 10px 25px;
            border-radius: 5px;
        }
        
        .btn-primary:hover {
            background: #2980b9;
        }
        
        .form-switch {
            padding-left: 2.5em;
        }
        
        .form-check-input:checked {
            background-color: #3498db;
            border-color: #3498db;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <nav class="admin-sidebar">
        <div class="sidebar-header">
            <a href="dashboard.php" class="sidebar-brand">
                <i class="bi bi-shield-check"></i> Admin Panel
            </a>
        </div>
        
        <div class="sidebar-nav">
            <div class="nav-section">
                <div class="nav-section-title">Main Navigation</div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a href="dashboard.php" class="nav-link">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="partners.php" class="nav-link">
                            <i class="bi bi-people"></i> Partners
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="orders.php" class="nav-link">
                            <i class="bi bi-cart"></i> Orders
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="plan_requests.php" class="nav-link">
                            <i class="bi bi-file-text"></i> Plan Requests
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="equipment_requests.php" class="nav-link">
                            <i class="bi bi-tools"></i> Equipment Requests
                        </a>
                    </li>
                </ul>
            </div>
            
            <div class="nav-section">
                <div class="nav-section-title">Analytics</div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a href="reports.php" class="nav-link">
                            <i class="bi bi-graph-up"></i> Reports
                        </a>
                    </li>
                </ul>
            </div>
            
            <div class="nav-section">
                <div class="nav-section-title">System</div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a href="profile.php" class="nav-link">
                            <i class="bi bi-person-circle"></i> Profile
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="settings.php" class="nav-link active">
                            <i class="bi bi-gear"></i> Settings
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="logout.php" class="nav-link text-danger">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <main class="admin-main">
        <!-- Top Navbar -->
        <nav class="admin-navbar">
            <div>
                <a href="dashboard.php" class="navbar-brand">
                    <i class="bi bi-shield-check"></i> Admin Panel
                </a>
            </div>
            <div class="user-info">
                <p class="user-name"><?php echo htmlspecialchars($_SESSION['admin_full_name'] ?? 'Admin'); ?></p>
                <p class="user-role"><?php echo ucfirst($_SESSION['admin_role'] ?? 'admin'); ?></p>
            </div>
        </nav>
        
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="bi bi-gear"></i> System Settings</h1>
        </div>
        
        <!-- Messages -->
        <?php if ($message): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> <?php echo htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle"></i> <?php echo htmlspecialchars($error); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-lg-8">
                <!-- General Settings -->
                <div class="settings-card">
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="update_settings">
                        
                        <div class="settings-section">
                            <h4><i class="bi bi-gear"></i> General Settings</h4>
                            
                            <div class="mb-3">
                                <label for="site_name" class="form-label">Site Name</label>
                                <input type="text" class="form-control" id="site_name" name="site_name" value="Marjan Partners Admin Panel">
                            </div>
                            
                            <div class="mb-3">
                                <label for="admin_email" class="form-label">Admin Email</label>
                                <input type="email" class="form-control" id="admin_email" name="admin_email" value="admin@marjanpartners.com">
                            </div>
                            
                            <div class="mb-3">
                                <label for="timezone" class="form-label">Timezone</label>
                                <select class="form-select" id="timezone" name="timezone">
                                    <option value="UTC">UTC</option>
                                    <option value="America/New_York">Eastern Time</option>
                                    <option value="America/Chicago">Central Time</option>
                                    <option value="America/Denver">Mountain Time</option>
                                    <option value="America/Los_Angeles">Pacific Time</option>
                                    <option value="Europe/London">London</option>
                                    <option value="Europe/Paris">Paris</option>
                                    <option value="Asia/Tokyo">Tokyo</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="settings-section">
                            <h4><i class="bi bi-shield"></i> Security Settings</h4>
                            
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="two_factor" name="two_factor">
                                    <label class="form-check-label" for="two_factor">
                                        Enable Two-Factor Authentication
                                    </label>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="session_timeout" name="session_timeout" checked>
                                    <label class="form-check-label" for="session_timeout">
                                        Enable Session Timeout (30 minutes)
                                    </label>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="login_attempts" name="login_attempts" checked>
                                    <label class="form-check-label" for="login_attempts">
                                        Limit Login Attempts (5 attempts)
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="settings-section">
                            <h4><i class="bi bi-bell"></i> Notification Settings</h4>
                            
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="email_notifications" name="email_notifications" checked>
                                    <label class="form-check-label" for="email_notifications">
                                        Email Notifications
                                    </label>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="new_partner_notifications" name="new_partner_notifications" checked>
                                    <label class="form-check-label" for="new_partner_notifications">
                                        New Partner Registration Notifications
                                    </label>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="order_notifications" name="order_notifications" checked>
                                    <label class="form-check-label" for="order_notifications">
                                        New Order Notifications
                                    </label>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="request_notifications" name="request_notifications" checked>
                                    <label class="form-check-label" for="request_notifications">
                                        Plan & Equipment Request Notifications
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="settings-section">
                            <h4><i class="bi bi-database"></i> Database Settings</h4>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="backup_frequency" class="form-label">Backup Frequency</label>
                                    <select class="form-select" id="backup_frequency" name="backup_frequency">
                                        <option value="daily">Daily</option>
                                        <option value="weekly" selected>Weekly</option>
                                        <option value="monthly">Monthly</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="backup_retention" class="form-label">Backup Retention (days)</label>
                                    <input type="number" class="form-control" id="backup_retention" name="backup_retention" value="30" min="1" max="365">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="auto_backup" name="auto_backup" checked>
                                    <label class="form-check-label" for="auto_backup">
                                        Enable Automatic Backups
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Save Settings
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="col-lg-4">
                <!-- System Information -->
                <div class="settings-card">
                    <h4><i class="bi bi-info-circle"></i> System Information</h4>
                    
                    <div class="mb-3">
                        <strong>PHP Version:</strong><br>
                        <small class="text-muted"><?php echo PHP_VERSION; ?></small>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Server Software:</strong><br>
                        <small class="text-muted"><?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?></small>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Database:</strong><br>
                        <small class="text-muted">MySQL</small>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Upload Max Size:</strong><br>
                        <small class="text-muted"><?php echo ini_get('upload_max_filesize'); ?></small>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Memory Limit:</strong><br>
                        <small class="text-muted"><?php echo ini_get('memory_limit'); ?></small>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Max Execution Time:</strong><br>
                        <small class="text-muted"><?php echo ini_get('max_execution_time'); ?> seconds</small>
                    </div>
                </div>
                
                <!-- Quick Actions -->
                <div class="settings-card">
                    <h4><i class="bi bi-lightning"></i> Quick Actions</h4>
                    
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary" onclick="clearCache()">
                            <i class="bi bi-trash"></i> Clear Cache
                        </button>
                        <button class="btn btn-outline-warning" onclick="testEmail()">
                            <i class="bi bi-envelope"></i> Test Email
                        </button>
                        <button class="btn btn-outline-info" onclick="backupDatabase()">
                            <i class="bi bi-download"></i> Backup Database
                        </button>
                        <button class="btn btn-outline-secondary" onclick="viewLogs()">
                            <i class="bi bi-file-text"></i> View Logs
                        </button>
                    </div>
                </div>
                
                <!-- Maintenance Mode -->
                <div class="settings-card">
                    <h4><i class="bi bi-tools"></i> Maintenance Mode</h4>
                    
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="maintenance_mode" name="maintenance_mode">
                            <label class="form-check-label" for="maintenance_mode">
                                Enable Maintenance Mode
                            </label>
                        </div>
                        <small class="text-muted">When enabled, only admins can access the system</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="maintenance_message" class="form-label">Maintenance Message</label>
                        <textarea class="form-control" id="maintenance_message" name="maintenance_message" rows="3" placeholder="System is under maintenance. Please check back later."></textarea>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Quick action functions
        function clearCache() {
            if (confirm('Are you sure you want to clear the cache?')) {
                alert('Cache cleared successfully!');
            }
        }
        
        function testEmail() {
            if (confirm('Send a test email to admin@marjanpartners.com?')) {
                alert('Test email sent!');
            }
        }
        
        function backupDatabase() {
            if (confirm('Create a database backup?')) {
                alert('Database backup started. You will receive an email when complete.');
            }
        }
        
        function viewLogs() {
            alert('Log viewer functionality would be implemented here.');
        }
    </script>
</body>
</html>
