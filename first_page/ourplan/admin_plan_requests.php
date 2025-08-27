<?php
// Start session
session_start();

// Database configuration - CHANGE THESE VALUES AS NEEDED
$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = 'planpartner';

// Simple admin authentication (you may want to enhance this)
$adminPassword = 'admin123'; // CHANGE THIS PASSWORD

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    if (isset($_POST['admin_password']) && $_POST['admin_password'] === $adminPassword) {
        $_SESSION['admin_logged_in'] = true;
    } elseif (isset($_SESSION['auth']['role']) && $_SESSION['auth']['role'] === 'admin') {
        // If user is already logged in as admin, allow access
        $_SESSION['admin_logged_in'] = true;
    } else {
        // Show login form
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Admin Login - Plan Requests</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 50px; background: #f5f5f5; }
                .login-container { max-width: 400px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                input[type="password"] { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 4px; }
                button { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
                button:hover { background: #0056b3; }
            </style>
        </head>
        <body>
            <div class="login-container">
                <h2>Admin Login</h2>
                <form method="POST">
                    <input type="password" name="admin_password" placeholder="Enter admin password" required>
                    <button type="submit">Login</button>
                </form>
            </div>
        </body>
        </html>
        <?php
        exit();
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    unset($_SESSION['admin_logged_in']);
    header('Location: admin_plan_requests.php');
    exit();
}

// Fetch plan requests from database
$planRequests = [];
try {
    $mysqli = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
    
    if ($mysqli->connect_errno) {
        $error = "Database connection failed: " . $mysqli->connect_error;
    } else {
        $query = "SELECT pr.*, 
                         CASE pr.planid 
                             WHEN 1 THEN 'Basic Partner' 
                             WHEN 2 THEN 'Business Partner' 
                             WHEN 3 THEN 'Elite Partner' 
                             ELSE 'Unknown' 
                         END as plan_name
                  FROM plan_requests pr 
                  ORDER BY pr.time DESC";
        
        $result = $mysqli->query($query);
        
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $planRequests[] = $row;
            }
            $result->free();
        } else {
            $error = "Query failed: " . $mysqli->error;
        }
        $mysqli->close();
    }
} catch (Exception $e) {
    $error = "Database error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Plan Requests</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .logout-btn { background: #dc3545; color: white; padding: 8px 16px; text-decoration: none; border-radius: 4px; }
        .logout-btn:hover { background: #c82333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f8f9fa; font-weight: bold; }
        .status-pending { color: #ffc107; font-weight: bold; }
        .status-approved { color: #28a745; font-weight: bold; }
        .status-rejected { color: #dc3545; font-weight: bold; }
        .error { color: #dc3545; background: #f8d7da; padding: 10px; border-radius: 4px; margin-bottom: 20px; }
        .stats { display: flex; gap: 20px; margin-bottom: 20px; }
        .stat-card { background: #e9ecef; padding: 15px; border-radius: 4px; text-align: center; flex: 1; }
        .stat-number { font-size: 24px; font-weight: bold; color: #007bff; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Plan Requests Admin Panel</h1>
            <a href="?logout=1" class="logout-btn">Logout</a>
        </div>

        <?php if (isset($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if (!empty($planRequests)): ?>
            <!-- Statistics -->
            <div class="stats">
                <div class="stat-card">
                    <div class="stat-number"><?= count($planRequests) ?></div>
                    <div>Total Requests</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= count(array_filter($planRequests, function($r) { return $r['status'] === 'pending'; })) ?></div>
                    <div>Pending</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= count(array_filter($planRequests, function($r) { return $r['status'] === 'approved'; })) ?></div>
                    <div>Approved</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= count(array_filter($planRequests, function($r) { return $r['status'] === 'rejected'; })) ?></div>
                    <div>Rejected</div>
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Partner ID</th>
                        <th>Plan</th>
                        <th>Status</th>
                        <th>Request Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($planRequests as $request): ?>
                        <tr>
                            <td><?= htmlspecialchars($request['id']) ?></td>
                            <td><?= htmlspecialchars($request['partnerid']) ?></td>
                            <td><?= htmlspecialchars($request['plan_name']) ?></td>
                            <td>
                                <span class="status-<?= $request['status'] ?>">
                                    <?= htmlspecialchars(ucfirst($request['status'])) ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($request['time']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No plan requests found.</p>
        <?php endif; ?>

        <div style="margin-top: 30px;">
            <a href="ourplan.php" style="color: #007bff; text-decoration: none;">← Back to Plans Page</a>
        </div>
    </div>
</body>
</html>
