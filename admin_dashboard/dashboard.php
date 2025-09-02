<?php
/**
 * Admin Dashboard - Main Dashboard
 * Overview and navigation to all administration sections
 */

require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

// Get statistics from all databases
try {
    // Partners statistics
    $mainPdo = getMainDbConnection();
    $stmt = $mainPdo->query("SELECT COUNT(*) FROM partners WHERE status = 'active'");
    $activePartners = $stmt->fetchColumn();
    
    $stmt = $mainPdo->query("SELECT COUNT(*) FROM partners WHERE status = 'pending'");
    $pendingPartners = $stmt->fetchColumn();
    
    // Orders statistics
    $cartPdo = getCartDbConnection();
    $stmt = $cartPdo->query("SELECT COUNT(*) FROM orders");
    $totalOrders = $stmt->fetchColumn();
    
    $stmt = $cartPdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'");
    $pendingOrders = $stmt->fetchColumn();
    
    $stmt = $cartPdo->query("SELECT SUM(total_amount) FROM orders WHERE status IN ('paid', 'completed')");
    $totalRevenue = $stmt->fetchColumn() ?: 0;
    
    // Plan requests statistics
    $planPdo = getPlanDbConnection();
    $stmt = $planPdo->query("SELECT COUNT(*) FROM plan_requests");
    $totalPlanRequests = $stmt->fetchColumn();
    
    $stmt = $planPdo->query("SELECT COUNT(*) FROM plan_requests WHERE status = 'pending'");
    $pendingPlanRequests = $stmt->fetchColumn();
    
    // Equipment requests statistics
    $equipmentPdo = getEquipmentDbConnection();
    $stmt = $equipmentPdo->query("SELECT COUNT(*) FROM requests");
    $totalEquipmentRequests = $stmt->fetchColumn();
    
    $stmt = $equipmentPdo->query("SELECT COUNT(*) FROM requests WHERE status = 'pending'");
    $pendingEquipmentRequests = $stmt->fetchColumn();
    
} catch (Exception $e) {
    error_log("Dashboard statistics error: " . $e->getMessage());
    $activePartners = $pendingPartners = $totalOrders = $pendingOrders = $totalRevenue = 0;
    $totalPlanRequests = $pendingPlanRequests = $totalEquipmentRequests = $pendingEquipmentRequests = 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Admin Panel</title>
    
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
        
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-icon {
            font-size: 2rem;
            margin-bottom: 15px;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .quick-actions {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
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
                        <a href="dashboard.php" class="nav-link active">
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
            <a href="dashboard.php" class="navbar-brand">Dashboard Overview</a>
            <div class="d-flex align-items-center">
                <div class="user-info me-3">
                    <p class="user-name"><?php echo htmlspecialchars($_SESSION['admin_full_name']); ?></p>
                    <p class="user-role"><?php echo ucfirst($_SESSION['admin_role']); ?></p>
                </div>
                <a href="logout.php" class="btn-logout">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </div>
        </nav>
        
        <!-- Statistics Overview -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon text-primary">
                        <i class="bi bi-people"></i>
                    </div>
                    <div class="stat-number text-primary"><?php echo number_format($activePartners); ?></div>
                    <div class="stat-label">Active Partners</div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon text-success">
                        <i class="bi bi-cart"></i>
                    </div>
                    <div class="stat-number text-success"><?php echo number_format($totalOrders); ?></div>
                    <div class="stat-label">Total Orders</div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon text-info">
                        <i class="bi bi-file-text"></i>
                    </div>
                    <div class="stat-number text-info"><?php echo number_format($totalPlanRequests); ?></div>
                    <div class="stat-label">Plan Requests</div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon text-warning">
                        <i class="bi bi-tools"></i>
                    </div>
                    <div class="stat-number text-warning"><?php echo number_format($totalEquipmentRequests); ?></div>
                    <div class="stat-label">Equipment Requests</div>
                </div>
            </div>
        </div>
        
        <!-- Additional Statistics -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon text-warning">
                        <i class="bi bi-clock"></i>
                    </div>
                    <div class="stat-number text-warning"><?php echo number_format($pendingOrders); ?></div>
                    <div class="stat-label">Pending Orders</div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon text-success">
                        <i class="bi bi-currency-dollar"></i>
                    </div>
                    <div class="stat-number text-success"><?php echo formatCurrency($totalRevenue); ?></div>
                    <div class="stat-label">Total Revenue</div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon text-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                    <div class="stat-number text-warning"><?php echo number_format($pendingPlanRequests); ?></div>
                    <div class="stat-label">Pending Plans</div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon text-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                    <div class="stat-number text-warning"><?php echo number_format($pendingEquipmentRequests); ?></div>
                    <div class="stat-label">Pending Equipment</div>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="row">
            <div class="col-md-6">
                <div class="quick-actions">
                    <h5><i class="bi bi-lightning"></i> Quick Actions</h5>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <a href="partners.php" class="btn btn-outline-primary w-100">
                                <i class="bi bi-person-plus"></i> Add Partner
                            </a>
                        </div>
                        <div class="col-6 mb-3">
                            <a href="orders.php" class="btn btn-outline-success w-100">
                                <i class="bi bi-cart-plus"></i> View Orders
                            </a>
                        </div>
                        <div class="col-6 mb-3">
                            <a href="plans.php" class="btn btn-outline-info w-100">
                                <i class="bi bi-file-earmark-text"></i> Plan Requests
                            </a>
                        </div>
                        <div class="col-6 mb-3">
                            <a href="equipment.php" class="btn btn-outline-warning w-100">
                                <i class="bi bi-tools"></i> Equipment
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="quick-actions">
                    <h5><i class="bi bi-graph-up"></i> Recent Activity</h5>
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span>New Orders</span>
                            <span class="badge bg-primary rounded-pill"><?php echo $pendingOrders; ?></span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Pending Plans</span>
                            <span class="badge bg-warning rounded-pill"><?php echo $pendingPlanRequests; ?></span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Equipment Requests</span>
                            <span class="badge bg-info rounded-pill"><?php echo $pendingEquipmentRequests; ?></span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Active Partners</span>
                            <span class="badge bg-success rounded-pill"><?php echo $activePartners; ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
