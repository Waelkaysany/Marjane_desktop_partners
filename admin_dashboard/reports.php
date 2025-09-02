<?php
/**
 * Admin Reports Dashboard
 * Comprehensive reports and analytics for all system data
 */

require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

$reportType = $_GET['type'] ?? 'overview';
$dateRange = $_GET['range'] ?? '30';
$startDate = $_GET['start'] ?? date('Y-m-d', strtotime("-{$dateRange} days"));
$endDate = $_GET['end'] ?? date('Y-m-d');

$reports = [];
$error = '';

try {
    $mainPdo = getMainDbConnection();
    $cartPdo = getCartDbConnection();
    $planPdo = getPlanDbConnection();
    $equipmentPdo = getEquipmentDbConnection();
    
    // Overview Report
    if ($reportType === 'overview') {
        // Partners Statistics
        $stmt = $mainPdo->query("SELECT 
            COUNT(*) as total_partners,
            COUNT(CASE WHEN status = 'active' THEN 1 END) as active_partners,
            COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_partners,
            COUNT(CASE WHEN status = 'suspended' THEN 1 END) as suspended_partners
            FROM partners");
        $reports['partners'] = $stmt->fetch();
        
        // Orders Statistics
        $stmt = $cartPdo->query("SELECT 
            COUNT(*) as total_orders,
            COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_orders,
            COUNT(CASE WHEN status = 'paid' THEN 1 END) as paid_orders,
            COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed_orders,
            COUNT(CASE WHEN status = 'cancelled' THEN 1 END) as cancelled_orders,
            SUM(CASE WHEN status IN ('paid', 'completed') THEN total_amount ELSE 0 END) as total_revenue
            FROM orders");
        $reports['orders'] = $stmt->fetch();
        
        // Plan Requests Statistics
        $stmt = $planPdo->query("SELECT 
            COUNT(*) as total_requests,
            COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_requests,
            COUNT(CASE WHEN status = 'approved' THEN 1 END) as approved_requests,
            COUNT(CASE WHEN status = 'rejected' THEN 1 END) as rejected_requests
            FROM plan_requests");
        $reports['plan_requests'] = $stmt->fetch();
        
        // Equipment Requests Statistics
        $stmt = $equipmentPdo->query("SELECT 
            COUNT(*) as total_requests,
            COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_requests,
            COUNT(CASE WHEN status = 'approved' THEN 1 END) as approved_requests,
            COUNT(CASE WHEN status = 'rejected' THEN 1 END) as rejected_requests
            FROM requests");
        $reports['equipment_requests'] = $stmt->fetch();
    }
    
    // Partners Report
    elseif ($reportType === 'partners') {
        $stmt = $mainPdo->prepare("SELECT 
            id, username, full_name, email, phone, status, created_at,
            (SELECT COUNT(*) FROM cartpartner.orders WHERE partner_id = partners.id) as total_orders,
            (SELECT SUM(total_amount) FROM cartpartner.orders WHERE partner_id = partners.id AND status IN ('paid', 'completed')) as total_revenue
            FROM partners 
            ORDER BY created_at DESC");
        $stmt->execute();
        $reports['partners_list'] = $stmt->fetchAll();
    }
    
    // Orders Report
    elseif ($reportType === 'orders') {
        $stmt = $cartPdo->prepare("SELECT 
            o.*, p.full_name as partner_name, p.username as partner_username
            FROM orders o
            LEFT JOIN marjanpartner.partners p ON o.partner_id = p.id
            WHERE DATE(o.created_at) BETWEEN ? AND ?
            ORDER BY o.created_at DESC");
        $stmt->execute([$startDate, $endDate]);
        $reports['orders_list'] = $stmt->fetchAll();
        
        // Orders by status
        $stmt = $cartPdo->prepare("SELECT 
            status, COUNT(*) as count, SUM(total_amount) as total_amount
            FROM orders 
            WHERE DATE(created_at) BETWEEN ? AND ?
            GROUP BY status");
        $stmt->execute([$startDate, $endDate]);
        $reports['orders_by_status'] = $stmt->fetchAll();
    }
    
    // Revenue Report
    elseif ($reportType === 'revenue') {
        // Daily revenue for the last 30 days
        $stmt = $cartPdo->prepare("SELECT 
            DATE(created_at) as date,
            COUNT(*) as orders_count,
            SUM(total_amount) as daily_revenue
            FROM orders 
            WHERE status IN ('paid', 'completed') 
            AND DATE(created_at) BETWEEN ? AND ?
            GROUP BY DATE(created_at)
            ORDER BY date");
        $stmt->execute([$startDate, $endDate]);
        $reports['daily_revenue'] = $stmt->fetchAll();
        
        // Monthly revenue
        $stmt = $cartPdo->prepare("SELECT 
            DATE_FORMAT(created_at, '%Y-%m') as month,
            COUNT(*) as orders_count,
            SUM(total_amount) as monthly_revenue
            FROM orders 
            WHERE status IN ('paid', 'completed') 
            AND DATE(created_at) BETWEEN ? AND ?
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ORDER BY month");
        $stmt->execute([$startDate, $endDate]);
        $reports['monthly_revenue'] = $stmt->fetchAll();
    }
    
    // Plan Requests Report
    elseif ($reportType === 'plan_requests') {
        $stmt = $planPdo->prepare("SELECT 
            pr.*, p.full_name as partner_name, p.username as partner_username
            FROM plan_requests pr
            LEFT JOIN marjanpartner.partners p ON pr.partner_id = p.id
            WHERE DATE(pr.created_at) BETWEEN ? AND ?
            ORDER BY pr.created_at DESC");
        $stmt->execute([$startDate, $endDate]);
        $reports['plan_requests_list'] = $stmt->fetchAll();
    }
    
    // Equipment Requests Report
    elseif ($reportType === 'equipment_requests') {
        $stmt = $equipmentPdo->prepare("SELECT 
            r.*, p.full_name as partner_name, p.username as partner_username
            FROM requests r
            LEFT JOIN marjanpartner.partners p ON r.partner_id = p.id
            WHERE DATE(r.created_at) BETWEEN ? AND ?
            ORDER BY r.created_at DESC");
        $stmt->execute([$startDate, $endDate]);
        $reports['equipment_requests_list'] = $stmt->fetchAll();
    }
    
} catch (Exception $e) {
    error_log("Reports error: " . $e->getMessage());
    $error = "Failed to generate reports";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Admin Panel</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
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
        
        .report-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
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
        
        .chart-container {
            position: relative;
            height: 400px;
            margin: 20px 0;
        }
        
        .report-filters {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
        }
        
        .btn-primary {
            background: #3498db;
            border: none;
        }
        
        .btn-primary:hover {
            background: #2980b9;
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
                        <a href="reports.php" class="nav-link active">
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
            <h1><i class="bi bi-graph-up"></i> Reports & Analytics</h1>
            <div>
                <button class="btn btn-primary" onclick="window.print()">
                    <i class="bi bi-printer"></i> Print Report
                </button>
                <button class="btn btn-success" onclick="exportToCSV()">
                    <i class="bi bi-download"></i> Export CSV
                </button>
            </div>
        </div>
        
        <!-- Error Message -->
        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle"></i> <?php echo htmlspecialchars($error); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <!-- Report Filters -->
        <div class="report-filters">
            <div class="row">
                <div class="col-md-3">
                    <label for="reportType" class="form-label">Report Type</label>
                    <select class="form-select" id="reportType" onchange="changeReport()">
                        <option value="overview" <?php echo $reportType === 'overview' ? 'selected' : ''; ?>>Overview</option>
                        <option value="partners" <?php echo $reportType === 'partners' ? 'selected' : ''; ?>>Partners</option>
                        <option value="orders" <?php echo $reportType === 'orders' ? 'selected' : ''; ?>>Orders</option>
                        <option value="revenue" <?php echo $reportType === 'revenue' ? 'selected' : ''; ?>>Revenue</option>
                        <option value="plan_requests" <?php echo $reportType === 'plan_requests' ? 'selected' : ''; ?>>Plan Requests</option>
                        <option value="equipment_requests" <?php echo $reportType === 'equipment_requests' ? 'selected' : ''; ?>>Equipment Requests</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="dateRange" class="form-label">Date Range</label>
                    <select class="form-select" id="dateRange" onchange="changeDateRange()">
                        <option value="7" <?php echo $dateRange === '7' ? 'selected' : ''; ?>>Last 7 days</option>
                        <option value="30" <?php echo $dateRange === '30' ? 'selected' : ''; ?>>Last 30 days</option>
                        <option value="90" <?php echo $dateRange === '90' ? 'selected' : ''; ?>>Last 90 days</option>
                        <option value="365" <?php echo $dateRange === '365' ? 'selected' : ''; ?>>Last year</option>
                        <option value="custom" <?php echo $dateRange === 'custom' ? 'selected' : ''; ?>>Custom range</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="startDate" class="form-label">Start Date</label>
                    <input type="date" class="form-control" id="startDate" value="<?php echo $startDate; ?>" onchange="updateCustomRange()">
                </div>
                <div class="col-md-3">
                    <label for="endDate" class="form-label">End Date</label>
                    <input type="date" class="form-control" id="endDate" value="<?php echo $endDate; ?>" onchange="updateCustomRange()">
                </div>
            </div>
        </div>
        
        <!-- Overview Report -->
        <?php if ($reportType === 'overview'): ?>
            <div class="row">
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-number text-primary"><?php echo number_format($reports['partners']['total_partners'] ?? 0); ?></div>
                        <div class="stat-label">Total Partners</div>
                        <small class="text-muted">
                            <?php echo number_format($reports['partners']['active_partners'] ?? 0); ?> active
                        </small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-number text-success"><?php echo number_format($reports['orders']['total_orders'] ?? 0); ?></div>
                        <div class="stat-label">Total Orders</div>
                        <small class="text-muted">
                            $<?php echo number_format($reports['orders']['total_revenue'] ?? 0, 2); ?> revenue
                        </small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-number text-warning"><?php echo number_format($reports['plan_requests']['total_requests'] ?? 0); ?></div>
                        <div class="stat-label">Plan Requests</div>
                        <small class="text-muted">
                            <?php echo number_format($reports['plan_requests']['pending_requests'] ?? 0); ?> pending
                        </small>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-number text-info"><?php echo number_format($reports['equipment_requests']['total_requests'] ?? 0); ?></div>
                        <div class="stat-label">Equipment Requests</div>
                        <small class="text-muted">
                            <?php echo number_format($reports['equipment_requests']['pending_requests'] ?? 0); ?> pending
                        </small>
                    </div>
                </div>
            </div>
            
            <!-- Charts -->
            <div class="row">
                <div class="col-md-6">
                    <div class="report-card">
                        <h4><i class="bi bi-pie-chart"></i> Orders by Status</h4>
                        <div class="chart-container">
                            <canvas id="ordersChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="report-card">
                        <h4><i class="bi bi-bar-chart"></i> Partners by Status</h4>
                        <div class="chart-container">
                            <canvas id="partnersChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Partners Report -->
        <?php if ($reportType === 'partners'): ?>
            <div class="report-card">
                <h4><i class="bi bi-people"></i> Partners Report</h4>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Full Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Status</th>
                                <th>Total Orders</th>
                                <th>Total Revenue</th>
                                <th>Joined</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reports['partners_list'] ?? [] as $partner): ?>
                                <tr>
                                    <td><?php echo $partner['id']; ?></td>
                                    <td><?php echo htmlspecialchars($partner['username']); ?></td>
                                    <td><?php echo htmlspecialchars($partner['full_name']); ?></td>
                                    <td><?php echo htmlspecialchars($partner['email']); ?></td>
                                    <td><?php echo htmlspecialchars($partner['phone'] ?? 'N/A'); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $partner['status'] === 'active' ? 'success' : ($partner['status'] === 'pending' ? 'warning' : 'danger'); ?>">
                                            <?php echo ucfirst($partner['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo number_format($partner['total_orders'] ?? 0); ?></td>
                                    <td>$<?php echo number_format($partner['total_revenue'] ?? 0, 2); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($partner['created_at'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Orders Report -->
        <?php if ($reportType === 'orders'): ?>
            <div class="report-card">
                <h4><i class="bi bi-cart"></i> Orders Report (<?php echo date('M d, Y', strtotime($startDate)); ?> - <?php echo date('M d, Y', strtotime($endDate)); ?>)</h4>
                
                <!-- Orders by Status Summary -->
                <div class="row mb-4">
                    <?php foreach ($reports['orders_by_status'] ?? [] as $status): ?>
                        <div class="col-md-3">
                            <div class="stat-card">
                                <div class="stat-number"><?php echo number_format($status['count']); ?></div>
                                <div class="stat-label"><?php echo ucfirst($status['status']); ?> Orders</div>
                                <small class="text-muted">$<?php echo number_format($status['total_amount'] ?? 0, 2); ?></small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Partner</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reports['orders_list'] ?? [] as $order): ?>
                                <tr>
                                    <td><?php echo $order['id']; ?></td>
                                    <td><?php echo htmlspecialchars($order['partner_name'] ?? $order['partner_username'] ?? 'Unknown'); ?></td>
                                    <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $order['status'] === 'completed' ? 'success' : ($order['status'] === 'paid' ? 'primary' : ($order['status'] === 'pending' ? 'warning' : 'danger')); ?>">
                                            <?php echo ucfirst($order['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Revenue Report -->
        <?php if ($reportType === 'revenue'): ?>
            <div class="report-card">
                <h4><i class="bi bi-currency-dollar"></i> Revenue Report (<?php echo date('M d, Y', strtotime($startDate)); ?> - <?php echo date('M d, Y', strtotime($endDate)); ?>)</h4>
                
                <div class="chart-container">
                    <canvas id="revenueChart"></canvas>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Orders</th>
                                <th>Revenue</th>
                                <th>Average Order Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reports['daily_revenue'] ?? [] as $revenue): ?>
                                <tr>
                                    <td><?php echo date('M d, Y', strtotime($revenue['date'])); ?></td>
                                    <td><?php echo number_format($revenue['orders_count']); ?></td>
                                    <td>$<?php echo number_format($revenue['daily_revenue'], 2); ?></td>
                                    <td>$<?php echo number_format($revenue['daily_revenue'] / $revenue['orders_count'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Plan Requests Report -->
        <?php if ($reportType === 'plan_requests'): ?>
            <div class="report-card">
                <h4><i class="bi bi-file-text"></i> Plan Requests Report (<?php echo date('M d, Y', strtotime($startDate)); ?> - <?php echo date('M d, Y', strtotime($endDate)); ?>)</h4>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Partner</th>
                                <th>Plan Type</th>
                                <th>Status</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reports['plan_requests_list'] ?? [] as $request): ?>
                                <tr>
                                    <td><?php echo $request['id']; ?></td>
                                    <td><?php echo htmlspecialchars($request['partner_name'] ?? $request['partner_username'] ?? 'Unknown'); ?></td>
                                    <td><?php echo htmlspecialchars($request['plan_type'] ?? 'N/A'); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $request['status'] === 'approved' ? 'success' : ($request['status'] === 'pending' ? 'warning' : 'danger'); ?>">
                                            <?php echo ucfirst($request['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M d, Y H:i', strtotime($request['created_at'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Equipment Requests Report -->
        <?php if ($reportType === 'equipment_requests'): ?>
            <div class="report-card">
                <h4><i class="bi bi-tools"></i> Equipment Requests Report (<?php echo date('M d, Y', strtotime($startDate)); ?> - <?php echo date('M d, Y', strtotime($endDate)); ?>)</h4>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Partner</th>
                                <th>Equipment</th>
                                <th>Status</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reports['equipment_requests_list'] ?? [] as $request): ?>
                                <tr>
                                    <td><?php echo $request['id']; ?></td>
                                    <td><?php echo htmlspecialchars($request['partner_name'] ?? $request['partner_username'] ?? 'Unknown'); ?></td>
                                    <td><?php echo htmlspecialchars($request['equipment_name'] ?? 'N/A'); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $request['status'] === 'approved' ? 'success' : ($request['status'] === 'pending' ? 'warning' : 'danger'); ?>">
                                            <?php echo ucfirst($request['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M d, Y H:i', strtotime($request['created_at'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </main>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Report navigation
        function changeReport() {
            const reportType = document.getElementById('reportType').value;
            const url = new URL(window.location);
            url.searchParams.set('type', reportType);
            window.location.href = url.toString();
        }
        
        function changeDateRange() {
            const dateRange = document.getElementById('dateRange').value;
            if (dateRange === 'custom') {
                document.getElementById('startDate').disabled = false;
                document.getElementById('endDate').disabled = false;
            } else {
                const url = new URL(window.location);
                url.searchParams.set('range', dateRange);
                window.location.href = url.toString();
            }
        }
        
        function updateCustomRange() {
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;
            const url = new URL(window.location);
            url.searchParams.set('start', startDate);
            url.searchParams.set('end', endDate);
            url.searchParams.set('range', 'custom');
            window.location.href = url.toString();
        }
        
        // Export to CSV
        function exportToCSV() {
            const table = document.querySelector('table');
            if (!table) return;
            
            let csv = [];
            const rows = table.querySelectorAll('tr');
            
            for (let row of rows) {
                let cols = row.querySelectorAll('td, th');
                let rowArray = [];
                for (let col of cols) {
                    rowArray.push('"' + col.innerText.replace(/"/g, '""') + '"');
                }
                csv.push(rowArray.join(','));
            }
            
            const csvContent = csv.join('\n');
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);
            link.setAttribute('href', url);
            link.setAttribute('download', 'report_<?php echo $reportType; ?>_<?php echo date('Y-m-d'); ?>.csv');
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
        
        // Charts for overview report
        <?php if ($reportType === 'overview'): ?>
        // Orders Chart
        const ordersCtx = document.getElementById('ordersChart').getContext('2d');
        new Chart(ordersCtx, {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'Paid', 'Completed', 'Cancelled'],
                datasets: [{
                    data: [
                        <?php echo $reports['orders']['pending_orders'] ?? 0; ?>,
                        <?php echo $reports['orders']['paid_orders'] ?? 0; ?>,
                        <?php echo $reports['orders']['completed_orders'] ?? 0; ?>,
                        <?php echo $reports['orders']['cancelled_orders'] ?? 0; ?>
                    ],
                    backgroundColor: ['#ffc107', '#007bff', '#28a745', '#dc3545']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
        
        // Partners Chart
        const partnersCtx = document.getElementById('partnersChart').getContext('2d');
        new Chart(partnersCtx, {
            type: 'bar',
            data: {
                labels: ['Active', 'Pending', 'Suspended'],
                datasets: [{
                    label: 'Partners',
                    data: [
                        <?php echo $reports['partners']['active_partners'] ?? 0; ?>,
                        <?php echo $reports['partners']['pending_partners'] ?? 0; ?>,
                        <?php echo $reports['partners']['suspended_partners'] ?? 0; ?>
                    ],
                    backgroundColor: ['#28a745', '#ffc107', '#dc3545']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        <?php endif; ?>
        
        // Revenue Chart
        <?php if ($reportType === 'revenue'): ?>
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: [<?php echo implode(',', array_map(function($item) { return '"' . date('M d', strtotime($item['date'])) . '"'; }, $reports['daily_revenue'] ?? [])); ?>],
                datasets: [{
                    label: 'Daily Revenue',
                    data: [<?php echo implode(',', array_map(function($item) { return $item['daily_revenue']; }, $reports['daily_revenue'] ?? [])); ?>],
                    borderColor: '#28a745',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
        <?php endif; ?>
    </script>
</body>
</html>
