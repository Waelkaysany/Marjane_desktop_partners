<?php
/**
 * Admin Profile Management
 * Allows admins to view and update their profile information
 */

require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

$message = '';
$error = '';

// Get current admin data
try {
    $mainPdo = getMainDbConnection();
    $stmt = $mainPdo->prepare("SELECT id, username, full_name, email, role, status, created_at FROM admin_users WHERE username = ?");
    $stmt->execute([$_SESSION['admin_username']]);
    $adminData = $stmt->fetch();
    
    if (!$adminData) {
        header('Location: logout.php');
        exit;
    }
} catch (Exception $e) {
    error_log("Profile data fetch error: " . $e->getMessage());
    $error = "Failed to load profile data";
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'update_profile') {
        $fullName = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        
        if (empty($fullName)) {
            $error = "Full name is required";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Please enter a valid email address";
        } else {
            try {
                $stmt = $mainPdo->prepare("UPDATE admin_users SET full_name = ?, email = ? WHERE username = ?");
                $stmt->execute([$fullName, $email, $_SESSION['admin_username']]);
                
                $message = "Profile updated successfully!";
                
                // Refresh admin data
                $stmt = $mainPdo->prepare("SELECT id, username, full_name, email, role, status, created_at FROM admin_users WHERE username = ?");
                $stmt->execute([$_SESSION['admin_username']]);
                $adminData = $stmt->fetch();
                
            } catch (Exception $e) {
                error_log("Profile update error: " . $e->getMessage());
                $error = "Failed to update profile";
            }
        }
    } elseif ($action === 'change_password') {
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            $error = "All password fields are required";
        } elseif ($newPassword !== $confirmPassword) {
            $error = "New passwords do not match";
        } elseif (strlen($newPassword) < 6) {
            $error = "New password must be at least 6 characters long";
        } else {
            try {
                // Verify current password
                $stmt = $mainPdo->prepare("SELECT password_hash FROM admin_users WHERE username = ?");
                $stmt->execute([$_SESSION['admin_username']]);
                $currentHash = $stmt->fetchColumn();
                
                if (!password_verify($currentPassword, $currentHash)) {
                    $error = "Current password is incorrect";
                } else {
                    // Update password
                    $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
                    $stmt = $mainPdo->prepare("UPDATE admin_users SET password_hash = ? WHERE username = ?");
                    $stmt->execute([$newHash, $_SESSION['admin_username']]);
                    
                    $message = "Password changed successfully!";
                }
            } catch (Exception $e) {
                error_log("Password change error: " . $e->getMessage());
                $error = "Failed to change password";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Admin Panel</title>
    
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
        
        .profile-card {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .profile-header {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .profile-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #3498db;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
            margin-right: 20px;
        }
        
        .profile-info h3 {
            margin: 0 0 5px 0;
            color: #2c3e50;
        }
        
        .profile-info p {
            margin: 0;
            color: #6c757d;
        }
        
        .form-section {
            margin-bottom: 30px;
        }
        
        .form-section h4 {
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
        
        .btn-danger {
            background: #e74c3c;
            border: none;
            padding: 10px 25px;
            border-radius: 5px;
        }
        
        .btn-danger:hover {
            background: #c0392b;
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
                        <a href="profile.php" class="nav-link active">
                            <i class="bi bi-person-circle"></i> Profile
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="settings.php" class="nav-link">
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
                <p class="user-name"><?php echo htmlspecialchars($adminData['full_name'] ?? 'Admin'); ?></p>
                <p class="user-role"><?php echo ucfirst($adminData['role'] ?? 'admin'); ?></p>
            </div>
        </nav>
        
        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="bi bi-person-circle"></i> Profile Management</h1>
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
                <!-- Profile Information -->
                <div class="profile-card">
                    <div class="profile-header">
                        <div class="profile-avatar">
                            <i class="bi bi-person"></i>
                        </div>
                        <div class="profile-info">
                            <h3><?php echo htmlspecialchars($adminData['full_name'] ?? 'Administrator'); ?></h3>
                            <p><i class="bi bi-envelope"></i> <?php echo htmlspecialchars($adminData['email'] ?? 'No email set'); ?></p>
                            <p><i class="bi bi-shield"></i> <?php echo ucfirst($adminData['role'] ?? 'admin'); ?> • <?php echo ucfirst($adminData['status'] ?? 'active'); ?></p>
                        </div>
                    </div>
                    
                    <!-- Update Profile Form -->
                    <div class="form-section">
                        <h4><i class="bi bi-pencil"></i> Update Profile Information</h4>
                        <form method="POST" action="">
                            <input type="hidden" name="action" value="update_profile">
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="username" value="<?php echo htmlspecialchars($adminData['username'] ?? ''); ?>" readonly>
                                    <div class="form-text">Username cannot be changed</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="full_name" class="form-label">Full Name *</label>
                                    <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo htmlspecialchars($adminData['full_name'] ?? ''); ?>" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address *</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($adminData['email'] ?? ''); ?>" required>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="role" class="form-label">Role</label>
                                    <input type="text" class="form-control" id="role" value="<?php echo ucfirst($adminData['role'] ?? 'admin'); ?>" readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="created_at" class="form-label">Member Since</label>
                                    <input type="text" class="form-control" id="created_at" value="<?php echo date('M d, Y', strtotime($adminData['created_at'] ?? 'now')); ?>" readonly>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Update Profile
                            </button>
                        </form>
                    </div>
                    
                    <!-- Change Password Form -->
                    <div class="form-section">
                        <h4><i class="bi bi-lock"></i> Change Password</h4>
                        <form method="POST" action="">
                            <input type="hidden" name="action" value="change_password">
                            
                            <div class="mb-3">
                                <label for="current_password" class="form-label">Current Password *</label>
                                <input type="password" class="form-control" id="current_password" name="current_password" required>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="new_password" class="form-label">New Password *</label>
                                    <input type="password" class="form-control" id="new_password" name="new_password" required>
                                    <div class="form-text">Minimum 6 characters</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="confirm_password" class="form-label">Confirm New Password *</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-key"></i> Change Password
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <!-- Account Status -->
                <div class="profile-card">
                    <h4><i class="bi bi-info-circle"></i> Account Status</h4>
                    <div class="mb-3">
                        <strong>Status:</strong>
                        <span class="badge bg-<?php echo ($adminData['status'] ?? 'active') === 'active' ? 'success' : 'warning'; ?>">
                            <?php echo ucfirst($adminData['status'] ?? 'active'); ?>
                        </span>
                    </div>
                    <div class="mb-3">
                        <strong>Last Login:</strong><br>
                        <small class="text-muted"><?php echo date('M d, Y H:i', strtotime($_SESSION['admin_last_login'] ?? 'now')); ?></small>
                    </div>
                    <div class="mb-3">
                        <strong>Session ID:</strong><br>
                        <small class="text-muted"><?php echo substr(session_id(), 0, 8) . '...'; ?></small>
                    </div>
                </div>
                
                <!-- Quick Actions -->
                <div class="profile-card">
                    <h4><i class="bi bi-lightning"></i> Quick Actions</h4>
                    <div class="d-grid gap-2">
                        <a href="dashboard.php" class="btn btn-outline-primary">
                            <i class="bi bi-speedometer2"></i> Back to Dashboard
                        </a>
                        <a href="settings.php" class="btn btn-outline-secondary">
                            <i class="bi bi-gear"></i> System Settings
                        </a>
                        <a href="logout.php" class="btn btn-outline-danger">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
