<?php
require_once 'config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: index.php');
    exit;
}

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $requestId = (int)($_POST['request_id'] ?? 0);
    $newStatus = $_POST['new_status'] ?? '';
    
    $validStatuses = ['pending', 'approved', 'rejected', 'completed'];
    if (in_array($newStatus, $validStatuses)) {
        try {
            $pdo = getPlanDbConnection();
            $stmt = $pdo->prepare("UPDATE plan_requests SET status = ? WHERE id = ?");
            if ($stmt->execute([$newStatus, $requestId])) {
                $_SESSION['success'] = "Plan request status updated successfully to '{$newStatus}'.";
            } else {
                $_SESSION['error'] = 'Failed to update plan request status.';
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

// Fetch plan requests data
try {
    $pdo = getPlanDbConnection();
    $stmt = $pdo->query("
        SELECT 
            pr.id, pr.partnerid as partner_id, pr.planid, pr.status, pr.time as created_at,
            CASE pr.planid 
                WHEN 1 THEN 'Basic Partner' 
                WHEN 2 THEN 'Business Partner' 
                WHEN 3 THEN 'Elite Partner' 
                ELSE 'Unknown' 
            END as plan_type,
            p.username, p.full_name, p.email, p.phone
        FROM plan_requests pr
        LEFT JOIN marjanpartner.partners p ON pr.partnerid = p.id
        ORDER BY pr.time DESC
    ");
    $planRequests = $stmt->fetchAll();
} catch (Exception $e) {
    $planRequests = [];
    $_SESSION['error'] = 'Failed to fetch plan requests data.';
}

// Get statistics
try {
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM plan_requests");
    $totalRequests = $stmt->fetch()['total'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as pending FROM plan_requests WHERE status = 'pending'");
    $pendingRequests = $stmt->fetch()['pending'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as approved FROM plan_requests WHERE status = 'approved'");
    $approvedRequests = $stmt->fetch()['approved'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as rejected FROM plan_requests WHERE status = 'rejected'");
    $rejectedRequests = $stmt->fetch()['rejected'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as completed FROM plan_requests WHERE status = 'completed'");
    $completedRequests = $stmt->fetch()['completed'];
} catch (Exception $e) {
    $totalRequests = $pendingRequests = $approvedRequests = $rejectedRequests = $completedRequests = 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plan Requests Management - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
                        <a href="plan_requests.php" class="nav-link active">
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
            <a href="plan_requests.php" class="navbar-brand">Plan Requests Management</a>
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
                        <div class="col-md-2">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="card-title"><?php echo $totalRequests; ?></h4>
                                            <p class="card-text">Total</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="bi bi-file-text fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="card-title"><?php echo $pendingRequests; ?></h4>
                                            <p class="card-text">Pending</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="bi bi-clock fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="card-title"><?php echo $approvedRequests; ?></h4>
                                            <p class="card-text">Approved</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="bi bi-check-circle fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-danger text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="card-title"><?php echo $rejectedRequests; ?></h4>
                                            <p class="card-text">Rejected</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="bi bi-x-circle fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="card-title"><?php echo $completedRequests; ?></h4>
                                            <p class="card-text">Completed</p>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="bi bi-flag-checkered fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Plan Requests Table -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">All Plan Requests</h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($planRequests)): ?>
                                <div class="text-center py-4">
                                    <i class="bi bi-file-text fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No plan requests found.</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Partner</th>
                                                <th>Plan Type</th>
                                                <th>Status</th>
                                                <th>Contact</th>
                                                <th>Created</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($planRequests as $request): ?>
                                                <tr>
                                                    <td><?php echo $request['id']; ?></td>
                                                    <td>
                                                        <div>
                                                            <strong><?php echo htmlspecialchars($request['full_name'] ?? 'Unknown'); ?></strong>
                                                            <br>
                                                            <small class="text-muted">
                                                                <?php echo htmlspecialchars($request['username'] ?? 'N/A'); ?>
                                                            </small>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-info">
                                                            <?php echo htmlspecialchars(ucfirst($request['plan_type'] ?? 'Unknown')); ?>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $statusClass = match($request['status']) {
                                                            'pending' => 'warning',
                                                            'approved' => 'success',
                                                            'rejected' => 'danger',
                                                            'completed' => 'info',
                                                            default => 'secondary'
                                                        };
                                                        ?>
                                                        <span class="badge bg-<?php echo $statusClass; ?> status-badge">
                                                            <?php echo ucfirst($request['status'] ?? 'unknown'); ?>
                                                        </span>
                                                    </td>

                                                    <td>
                                                        <div>
                                                            <small>
                                                                <i class="bi bi-envelope me-1"></i>
                                                                <?php echo htmlspecialchars($request['email'] ?? 'N/A'); ?>
                                                            </small>
                                                            <?php if ($request['phone']): ?>
                                                                <br>
                                                                <small>
                                                                    <i class="bi bi-telephone me-1"></i>
                                                                    <?php echo htmlspecialchars($request['phone']); ?>
                                                                </small>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>
                                                    <td><?php echo date('M j, Y', strtotime($request['created_at'])); ?></td>
                                                    <td>
                                                        <button class="btn btn-sm btn-outline-primary" 
                                                                onclick="openStatusModal(<?php echo $request['id']; ?>, '<?php echo $request['status'] ?? ''; ?>', '<?php echo htmlspecialchars($request['full_name'] ?? 'Unknown'); ?>')">
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

    <!-- Status Update Modal -->
    <div class="modal fade" id="statusModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Plan Request Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="request_id" id="requestId">
                        
                        <p>Update status for plan request from: <strong id="partnerName"></strong></p>
                        
                        <div class="mb-3">
                            <label for="newStatus" class="form-label">New Status</label>
                            <select class="form-select" name="new_status" id="newStatus" required>
                                <option value="">Select Status</option>
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                                <option value="completed">Completed</option>
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



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function openStatusModal(requestId, currentStatus, partnerName) {
            document.getElementById('requestId').value = requestId;
            document.getElementById('partnerName').textContent = partnerName;
            document.getElementById('newStatus').value = currentStatus;
            
            const modal = new bootstrap.Modal(document.getElementById('statusModal'));
            modal.show();
        }


    </script>
</body>
</html>
