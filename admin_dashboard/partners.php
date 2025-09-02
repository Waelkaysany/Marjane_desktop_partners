<?php
require_once 'config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: index.php');
    exit;
}

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $partnerId = (int)($_POST['partner_id'] ?? 0);
    $newStatus = $_POST['new_status'] ?? '';
    
    $validStatuses = ['active', 'inactive', 'pending'];
    if (in_array($newStatus, $validStatuses)) {
        try {
            $pdo = getMainDbConnection();
            $stmt = $pdo->prepare("UPDATE partners SET status = ?, updated_at = NOW() WHERE id = ?");
            if ($stmt->execute([$newStatus, $partnerId])) {
                $_SESSION['success'] = "Partner status updated successfully to '{$newStatus}'.";
            } else {
                $_SESSION['error'] = 'Failed to update partner status.';
            }
        } catch (Exception $e) {
            $_SESSION['error'] = 'An error occurred while updating the status.';
        }
    } else {
        $_SESSION['error'] = 'Invalid status selected.';
    }
    
    header('Location: ' . $_SERVER['REQUEST_URI']);
    exit;
}

// Fetch partners data
try {
    $pdo = getMainDbConnection();
    $stmt = $pdo->query("
        SELECT 
            id, username, full_name, email, phone, company, role, 
            status, created_at, last_login, notes
        FROM partners 
        ORDER BY created_at DESC
    ");
    $partners = $stmt->fetchAll();
} catch (Exception $e) {
    $partners = [];
    $_SESSION['error'] = 'Failed to fetch partners data.';
}

// Get statistics
try {
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM partners");
    $totalPartners = $stmt->fetch()['total'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as active FROM partners WHERE status = 'active'");
    $activePartners = $stmt->fetch()['active'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as pending FROM partners WHERE status = 'pending'");
    $pendingPartners = $stmt->fetch()['pending'];
} catch (Exception $e) {
    $totalPartners = $activePartners = $pendingPartners = 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Partners Management - Admin Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/admin.css" rel="stylesheet">
    

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
                        <a href="partners.php" class="nav-link active">
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
            <a href="partners.php" class="navbar-brand">Partners Management</a>
            <div class="d-flex align-items-center">
                <div class="user-info me-3">
                    <p class="user-name"><?php echo htmlspecialchars($_SESSION['admin_full_name'] ?? 'Admin'); ?></p>
                    <p class="user-role"><?php echo ucfirst($_SESSION['admin_role'] ?? 'Admin'); ?></p>
                </div>
                <a href="logout.php" class="btn-logout">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </div>
        </nav>
                <div class="p-4">

                    <!-- Messages -->
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($_SESSION['success']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php unset($_SESSION['success']); ?>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo htmlspecialchars($_SESSION['error']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>

                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="card-title"><?php echo $totalPartners; ?></h4>
                                            <p class="card-text">Total Partners</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="bi bi-people fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="card-title"><?php echo $activePartners; ?></h4>
                                            <p class="card-text">Active Partners</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="bi bi-check-circle fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="card-title"><?php echo $pendingPartners; ?></h4>
                                            <p class="card-text">Pending Approval</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="bi bi-clock fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Partners Table -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">All Partners</h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($partners)): ?>
                                <div class="text-center py-4">
                                    <i class="bi bi-people fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No partners found.</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Username</th>
                                                <th>Full Name</th>
                                                <th>Email</th>
                                                <th>Company</th>
                                                <th>Role</th>
                                                <th>Status</th>
                                                <th>Created</th>
                                                <th>Last Login</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($partners as $partner): ?>
                                                <tr>
                                                    <td><?php echo $partner['id']; ?></td>
                                                    <td>
                                                        <strong><?php echo htmlspecialchars($partner['username']); ?></strong>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($partner['full_name']); ?></td>
                                                    <td><?php echo htmlspecialchars($partner['email']); ?></td>
                                                    <td><?php echo htmlspecialchars($partner['company'] ?? '-'); ?></td>
                                                    <td>
                                                        <span class="badge bg-<?php echo $partner['role'] === 'admin' ? 'danger' : 'info'; ?>">
                                                            <?php echo ucfirst($partner['role']); ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $statusClass = match($partner['status']) {
                                                            'active' => 'success',
                                                            'inactive' => 'secondary',
                                                            'pending' => 'warning',
                                                            default => 'secondary'
                                                        };
                                                        ?>
                                                        <span class="badge bg-<?php echo $statusClass; ?> status-badge">
                                                            <?php echo ucfirst($partner['status'] ?? 'unknown'); ?>
                                                        </span>
                                                    </td>
                                                    <td><?php echo date('M j, Y', strtotime($partner['created_at'])); ?></td>
                                                    <td>
                                                        <?php 
                                                        if ($partner['last_login']) {
                                                            echo date('M j, Y H:i', strtotime($partner['last_login']));
                                                        } else {
                                                            echo '<span class="text-muted">Never</span>';
                                                        }
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-sm btn-outline-primary" 
                                                                onclick="openStatusModal(<?php echo $partner['id']; ?>, '<?php echo $partner['status'] ?? ''; ?>', '<?php echo htmlspecialchars($partner['full_name']); ?>')">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </main>

    <!-- Status Update Modal -->
    <div class="modal fade" id="statusModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Partner Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="partner_id" id="partnerId">
                        
                        <p>Update status for partner: <strong id="partnerName"></strong></p>
                        
                        <div class="mb-3">
                            <label for="newStatus" class="form-label">New Status</label>
                            <select class="form-select" name="new_status" id="newStatus" required>
                                <option value="">Select Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="pending">Pending</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function openStatusModal(partnerId, currentStatus, partnerName) {
            document.getElementById('partnerId').value = partnerId;
            document.getElementById('partnerName').textContent = partnerName;
            document.getElementById('newStatus').value = currentStatus;
            
            const modal = new bootstrap.Modal(document.getElementById('statusModal'));
            modal.show();
        }
    </script>
</body>
</html>
