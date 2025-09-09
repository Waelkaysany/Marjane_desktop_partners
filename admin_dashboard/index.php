<?php
/**
 * Admin Dashboard - Main Entry Point
 * Central administration panel for first_page project
 */

require_once 'config.php';

// Check if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$success = '';

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } else {
        try {
            $pdo = getMainDbConnection();
            
            // Check admin credentials
            $stmt = $pdo->prepare("SELECT id, username, password_hash, full_name, role FROM admin_users WHERE username = ? AND status = 'active'");
            $stmt->execute([$username]);
            $admin = $stmt->fetch();
            
            if ($admin && password_verify($password, $admin['password_hash'])) {
                // Login successful
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                $_SESSION['admin_full_name'] = $admin['full_name'];
                $_SESSION['admin_role'] = $admin['role'];
                
                // Log successful login
                error_log("Admin login successful: {$username}");
                
                header('Location: dashboard.php');
                exit;
            } else {
                $error = 'Invalid username or password.';
                error_log("Failed admin login attempt: {$username}");
            }
        } catch (Exception $e) {
            $error = 'Database connection error. Please try again.';
            error_log("Admin login database error: " . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Marjan Partners</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        /* Page background */
        body {
            background: #0b0f0e;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            color: #e6ece9;
        }

        /* Split panel container (keeps existing HTML) */
        .login-container {
            position: relative;
            overflow: hidden;
            width: 100%;
            max-width: 1100px;
            min-height: 560px;
            border-radius: 20px;
            border: 1px solid rgba(255,255,255,0.06);
            background: radial-gradient(1200px 600px at -20% 120%, rgba(40,60,55,0.25), transparent 60%),
                        radial-gradient(1000px 400px at 120% -10%, rgba(35,50,48,0.2), transparent 50%),
                        linear-gradient(160deg, #0f1513 0%, #0b0f0e 100%);
            box-shadow: 0 10px 30px rgba(0,0,0,0.35);
            display: grid;
            grid-template-columns: 1.05fr 1fr;
        }

        /* Subtle grid/mesh texture */
        .login-container::before {
            content: "";
            position: absolute;
            inset: 0;
            background: repeating-linear-gradient(
                0deg,
                rgba(255,255,255,0.02) 0px,
                rgba(255,255,255,0.02) 1px,
                transparent 1px,
                transparent 18px
            ), repeating-linear-gradient(
                90deg,
                rgba(255,255,255,0.02) 0px,
                rgba(255,255,255,0.02) 1px,
                transparent 1px,
                transparent 18px
            );
            pointer-events: none;
        }

        /* Right visual panel */
        .login-container .visual {
            position: relative;
            isolation: isolate;
        }

        .login-container .visual::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                radial-gradient(120% 120% at 80% 20%, rgba(80, 180, 130, 0.16), transparent 60%),
                radial-gradient(60% 60% at 60% 60%, rgba(120, 200, 160, 0.08), transparent 70%),
                linear-gradient(165deg, #0f1513 0%, #0b0f0e 100%);
            border-top-left-radius: 55% 45%;
            border-bottom-left-radius: 0;
            border-left: 1px solid rgba(255,255,255,0.06);
            box-shadow: inset 0 0 120px rgba(0,0,0,0.35);
        }

        /* Left content column */
        .login-container .content {
            display: flex;
            flex-direction: column;
            padding: 2.5rem;
        }

        /* Motivational quote card */
        .motto-card {
            position: absolute;
            right: 32px;
            top: 50%;
            transform: translateY(-56%);
            max-width: 480px;
            color: #eaf8f1;
            background: linear-gradient(180deg, rgba(255,255,255,0.06), rgba(255,255,255,0.03));
            border: none;
            border-radius: 14px;
            padding: 22px 24px;
            box-shadow: 0 10px 28px rgba(0,0,0,0.35);
            backdrop-filter: blur(8px);
            z-index: 2;
        }
        .motto-card .kicker { font-size: 12px; letter-spacing: 2.4px; text-transform: uppercase; opacity: 0.78; }
        .motto-card .headline { margin: 8px 0 4px; font-size: 26px; font-weight: 800; line-height: 1.25; }
        .motto-card .sub { font-size: 14px; opacity: 0.92; }

        /* Left content area */
        .login-header {
            background: transparent;
            color: #e6ece9;
            padding: 0 0 1rem 0;
            text-align: left;
        }

        .login-header h2 {
            font-weight: 700;
            letter-spacing: 0.2px;
            margin-bottom: 0.25rem;
        }

        .login-header p {
            opacity: 0.7;
            margin-bottom: 0;
        }

        .login-body {
            padding: 0;
            max-width: 420px;
        }

        .form-label {
            color: #cfe4db;
            font-weight: 500;
        }

        .form-control {
            background: #111816;
            color: #e6ece9;
            border-radius: 12px;
            border: 1px solid rgba(255,255,255,0.08);
            padding: 12px 14px;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .form-control::placeholder {
            color: #7a9188;
        }

        .form-control:focus {
            color: #e6ece9;
            background: #101614;
            border-color: #5bd08f;
            box-shadow: 0 0 0 3px rgba(91, 208, 143, 0.15);
        }

        .btn-login {
            background: linear-gradient(180deg, #6bd59a 0%, #4ebf83 100%);
            color: #0b0f0e;
            border: none;
            border-radius: 12px;
            padding: 12px;
            font-weight: 700;
            letter-spacing: 0.2px;
            transition: transform 0.2s ease, box-shadow 0.2s ease, opacity 0.2s ease;
        }

        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 18px rgba(91, 208, 143, 0.25);
            opacity: 0.95;
        }

        .project-info {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.06);
            color: #ffffff;
            border-radius: 12px;
            padding: 1rem;
            margin-top: 1rem;
            text-align: left;
        }

        .project-info ul li { margin-bottom: 2px; }
        .project-info .text-muted { color: #ffffff !important; opacity: 1; }
        /* Responsive */
        @media (max-width: 992px) {
            .login-container {
                grid-template-columns: 1fr;
            }
            .login-container .visual { display: none; }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="content">
            <div class="login-header">
                <h2 class="mb-1"><i class="bi bi-shield-check"></i> Admin Dashboard</h2>
                <p class="mb-0">Marjan Partners Administration</p>
            </div>
            
            <div class="login-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger" role="alert">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success" role="alert">
                        <i class="bi bi-check-circle-fill"></i>
                        <?php echo htmlspecialchars($success); ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" 
                               value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-login w-100 mb-2">
                        <i class="bi bi-box-arrow-in-right"></i> Login
                    </button>
                </form>

                <div class="project-info">
                    <h6 class="mb-1"><i class="bi bi-info-circle"></i> Project Overview</h6>
                    <ul class="small text-muted mb-0">
                        <li>Partners & Users</li>
                        <li>Orders & Cart System</li>
                        <li>Plan Requests</li>
                        <li>Equipment Requests</li>
                        <li>Analytics & Reports</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="visual" aria-hidden="true">
            <div class="motto-card">
                <div class="kicker">Keep pushing</div>
                <div class="headline">Small steps today become giant leaps tomorrow.</div>
                <div class="sub">Stay curious, ship confidently, and let progress compound.</div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
