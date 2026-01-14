<?php
// Use the same session configuration as the main application
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.use_strict_mode', 1);
    ini_set('session.cookie_httponly', 1);
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        ini_set('session.cookie_secure', 1);
    }
    session_name('mp_sess');
    session_start();
}

// Check if user is logged in - use same logic as main application
if (empty($_SESSION['auth']['partner_id'])) {
    // For testing purposes, allow access with a test parameter
    if (isset($_GET['test']) && $_GET['test'] === '1') {
        $partnerid = 1; // Use test partner ID
    } else {
        header('Location: ../login.php?m=login_required');
        exit;
    }
} else {
    $partnerid = (int)$_SESSION['auth']['partner_id'];
}

// Database configuration - CHANGE THESE VALUES AS NEEDED
$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = 'planpartner';

// Create or ensure CSRF token exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Initialize variables (partnerid already set above)
$message = '';
$messageType = '';

// Debug information (remove this in production)
if (isset($_GET['debug'])) {
    echo "<pre>Session Debug Info:\n";
    echo "Session ID: " . session_id() . "\n";
    echo "Session Data: " . print_r($_SESSION, true) . "\n";
    echo "Partner ID: " . $partnerid . "\n";
    echo "Auth Array: " . print_r($_SESSION['auth'] ?? 'Not set', true) . "\n";
    echo "</pre>";
    exit;
}

// Handle POST requests (form submissions)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $message = 'Invalid request method.';
        $messageType = 'error';
    }
    // Validate CSRF token
    elseif (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])) {
        $message = 'Security token validation failed. Please try again.';
        $messageType = 'error';
    }
    // Validate planid
    elseif (!isset($_POST['planid']) || !in_array((int)$_POST['planid'], [1, 2, 3])) {
        $message = 'Invalid plan selection. Please choose a valid plan.';
        $messageType = 'error';
    }
    // Check if user is logged in
    elseif ($partnerid === 0) {
        $message = 'Please log in to request a plan. <a href="../login.php" style="color: white; text-decoration: underline;">Click here to login</a>';
        $messageType = 'error';
    }
    else {
        // Database connection
        try {
            $mysqli = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
            
            if ($mysqli->connect_errno) {
                error_log("Database connection failed: " . $mysqli->connect_error);
                $message = 'Database connection error. Please try again later.';
                $messageType = 'error';
            } else {
                // Prepare and execute the insert statement
                $planid = (int)$_POST['planid'];
                $stmt = $mysqli->prepare("INSERT INTO plan_requests (partnerid, planid, status) VALUES (?, ?, 'pending')");
                
                if ($stmt) {
                    $stmt->bind_param("ii", $partnerid, $planid);
                    $stmt->execute();
                    
                    if ($stmt->affected_rows === 1) {
                        // Success - redirect to avoid duplicate submissions
                        $planNames = [1 => 'Basic Partner', 2 => 'Business Partner', 3 => 'Elite Partner'];
                        $planName = $planNames[$planid];
                        header("Location: ourplan.php?msg=success&plan=" . urlencode($planName));
                        exit();
                    } else {
                        $message = 'Failed to submit plan request. Please try again.';
                        $messageType = 'error';
                        error_log("Plan request insert failed for partnerid: $partnerid, planid: $planid");
                    }
                    $stmt->close();
                } else {
                    $message = 'Database error. Please try again later.';
                    $messageType = 'error';
                    error_log("Prepare statement failed: " . $mysqli->error);
                }
                $mysqli->close();
            }
        } catch (Exception $e) {
            error_log("Database exception: " . $e->getMessage());
            $message = 'An error occurred. Please try again later.';
            $messageType = 'error';
        }
    }
}

// Handle success message from redirect
if (isset($_GET['msg']) && $_GET['msg'] === 'success' && isset($_GET['plan'])) {
    $planName = htmlspecialchars($_GET['plan']);
    $message = "Your request for $planName was submitted successfully. Status: pending.";
    $messageType = 'success';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../style.css">
  <link rel="stylesheet" href="ourplan.css">
  <title>Our Plans - Zero-Loss Business Continuity</title>
  
  <!-- Block external scripts that cause errors -->
  <script>
    // Block MathJax and other external scripts
    window.MathJax = undefined;
    window.WebSocket = function() {
      console.log('WebSocket blocked to prevent errors');
      return {
        send: function() {},
        close: function() {},
        addEventListener: function() {},
        removeEventListener: function() {}
      };
    };
    
    // Block any script loading that might cause issues
    const originalCreateElement = document.createElement;
    document.createElement = function(tagName) {
      const element = originalCreateElement.call(document, tagName);
      if (tagName.toLowerCase() === 'script') {
        element.addEventListener('error', function(e) {
          e.preventDefault();
          console.log('Blocked script error:', e);
        });
      }
      return element;
    };
  </script>
</head>
<body>
    <nav>
    <div class="logo">
      <p>MARJANE</p>
    </div>
    <div class="menu">
      <ul>
        <li><a href="../home.php">PROMOTIONS</a></li>
        <li><a href="../product/">PRODUCTS</a></li>
        <li><a href="../equipment/equipment.php">EQUIPMENTS</a></li>
        <li><a href="ourplan.php">OUR PLANS</a></li>
      </ul>
    </div>
    <div class="right-side">
      <a href="../home.php" class="button" style="text-decoration: none; display: inline-flex; align-items: center;">
        <span class="text">Home</span>
        <span class="svg">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            width="50"
            height="20"
            viewBox="0 0 38 15"
            fill="none"
          >
            <path
              fill="white"
              d="M10 7.519l-.939-.344h0l.939.344zm14.386-1.205l-.981-.192.981.192zm1.276 5.509l.537.843.148-.094.107-.139-.792-.611zm4.819-4.304l-.385-.923h0l.385.923zm7.227.707a1 1 0 0 0 0-1.414L31.343.448a1 1 0 0 0-1.414 0 1 1 0 0 0 0 1.414l5.657 5.657-5.657 5.657a1 1 0 0 0 1.414 1.414l6.364-6.364zM1 7.519l.554.833.029-.019.094-.061.361-.23 1.277-.77c1.054-.609 2.397-1.32 3.629-1.787.617-.234 1.17-.392 1.623-.455.477-.066.707-.008.788.034.025.013.031.021.039.034a.56.56 0 0 1 .058.235c.029.327-.047.906-.39 1.842l1.878.689c.383-1.044.571-1.949.505-2.705-.072-.815-.45-1.493-1.16-1.865-.627-.329-1.358-.332-1.993-.244-.659.092-1.367.305-2.056.566-1.381.523-2.833 1.297-3.921 1.925l-1.341.808-.385.245-.104.068-.028.018c-.011.007-.011.007.543.84zm8.061-.344c-.198.54-.328 1.038-.36 1.484-.032.441.024.94.325 1.364.319.45.786.64 1.21.697.403.054.824-.001 1.21-.09.775-.179 1.694-.566 2.633-1.014l3.023-1.554c2.115-1.122 4.107-2.168 5.476-2.524.329-.086.573-.117.742-.115s.195.038.161.014c-.15-.105.085-.139-.076.685l1.963.384c.192-.98.152-2.083-.74-2.707-.405-.283-.868-.37-1.28-.376s-.849.069-1.274.179c-1.65.43-3.888 1.621-5.909 2.693l-2.948 1.517c-.92.439-1.673.743-2.221.87-.276.064-.429.065-.492.057-.043-.006.066.003.155.127.07.099.024.131.038-.063.014-.187.078-.49.243-.94l-1.878-.689zm14.343-1.053c-.361 1.844-.474 3.185-.413 4.161.059.95.294 1.72.811 2.215.567.544 1.242.546 1.664.459a2.34 2.34 0 0 0 .502-.167l.15-.076.049-.028.018-.011c.013-.008.013-.008-.524-.852l-.536-.844.019-.012c-.038.018-.064.027-.084.032-.037.008.053-.013.125.056.021.02-.151-.135-.198-.895-.046-.734.034-1.887.38-3.652l-1.963-.384zm2.257 5.701l.791.611.024-.031.08-.101.311-.377 1.093-1.213c.922-.954 2.005-1.894 2.904-2.27l-.771-1.846c-1.31.547-2.637 1.758-3.572 2.725l-1.184 1.314-.341.414-.093.117-.025.032c-.01.013-.01.013.781.624zm5.204-3.381c.989-.413 1.791-.42 2.697-.307.871.108 2.083.385 3.437.385v-2c-1.197 0-2.041-.226-3.19-.369-1.114-.139-2.297-.146-3.715.447l.771 1.846z"
            ></path>
          </svg>
        </span>
      </a>
      <a class="cart-link" href="../cart.php" aria-label="Cart">
        <div class="cart-icon-container">
          <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="9" cy="21" r="1"></circle>
            <circle cx="20" cy="21" r="1"></circle>
            <path d="M1 1h4l2.68 12.39a2 2 0 0 0 2 1.61h7.72a2 2 0 0 0 2-1.61L23 6H6"></path>
          </svg>
          <div class="cart-counter">0</div>
        </div>
      </a>
      <button class="param-btn" id="paramsBtn" aria-haspopup="true" aria-expanded="false" aria-controls="paramsMenu" title="Parameters">⚙️</button>
     </div>
  </nav>

  
  
  </div>

  <!-- Message Display -->
  <?php if ($message): ?>
    <div class="message-container" style="
      position: fixed;
      top: 20px;
      right: 20px;
      padding: 15px 20px;
      border-radius: 8px;
      color: white;
      font-weight: bold;
      z-index: 1000;
      max-width: 400px;
      background-color: <?= $messageType === 'success' ? '#10b981' : '#ef4444' ?>;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    ">
      <?= $message ?>
      <button onclick="this.parentElement.remove()" style="
        background: none;
        border: none;
        color: white;
        margin-left: 10px;
        cursor: pointer;
        font-size: 18px;
      ">&times;</button>
    </div>
  <?php endif; ?>

  <div class="pricing-container">
    <div class="pricing-header">
      <h1 class="main-heading">Your Plan, Your Way</h1>
      <p class="subtitle">Choose the perfect Marjan partner plan to grow your business and maximize your success.</p>
      
      <div class="billing-toggle">
        <button class="toggle-btn active" data-period="yearly">Yearly</button>
        <button class="toggle-btn" data-period="monthly">Monthly</button>
      </div>
    </div>

    <div class="pricing-cards">
      <!-- Basic Partner Plan -->
      <div class="pricing-card">
        <div class="card-header">
          <h3 class="plan-name">Basic Partner</h3>
          <p class="plan-description">Perfect for individuals starting their own Marjan mini-store.</p>
        </div>
        
        <div class="features">
          <div class="feature">
            <svg class="check-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="20,6 9,17 4,12"></polyline>
            </svg>
            <span>Up to 100 products</span>
          </div>
          <div class="feature">
            <svg class="check-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="20,6 9,17 4,12"></polyline>
            </svg>
            <span>Standard product categories</span>
          </div>
          <div class="feature">
            <svg class="check-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="20,6 9,17 4,12"></polyline>
            </svg>
            <span>Basic sales dashboard</span>
          </div>
          <div class="feature">
            <svg class="check-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="20,6 9,17 4,12"></polyline>
            </svg>
            <span>Transaction history overview</span>
          </div>
          <div class="feature">
            <svg class="check-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="20,6 9,17 4,12"></polyline>
            </svg>
            <span>Standard support</span>
          </div>
        </div>

        <div class="pricing">
          <div class="price">
            <span class="currency">MAD</span>
            <span class="amount" data-yearly="299" data-monthly="299">299</span>
            <span class="period">/ per month</span>
          </div>
          <p class="billing-info">billed yearly</p>
        </div>

        <form method="POST" style="margin: 0;">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
          <input type="hidden" name="planid" value="1">
          <button type="submit" class="plan-btn start-btn">CHOOSE BASIC</button>
        </form>
        <p class="free-trial">7 days free</p>
      </div>

      <!-- Business Partner Plan -->
      <div class="pricing-card featured">
        <div class="best-choice">🔥 Best Choice</div>
        <div class="card-header">
          <h3 class="plan-name">Business Partner</h3>
          <p class="plan-description">Ideal for small businesses who want to scale their Marjan store.</p>
        </div>
        
        <div class="features">
          <div class="feature">
            <svg class="check-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="20,6 9,17 4,12"></polyline>
            </svg>
            <span>Everything in Basic Partner</span>
          </div>
          <div class="feature">
            <svg class="check-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="20,6 9,17 4,12"></polyline>
            </svg>
            <span>Unlimited products</span>
          </div>
          <div class="feature">
            <svg class="check-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="20,6 9,17 4,12"></polyline>
            </svg>
            <span>Advanced sales & stock insights</span>
          </div>
          <div class="feature">
            <svg class="check-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="20,6 9,17 4,12"></polyline>
            </svg>
            <span>Promotions & discounts management</span>
          </div>
          <div class="feature">
            <svg class="check-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="20,6 9,17 4,12"></polyline>
            </svg>
            <span>Multi-payment support (Cash / Card / Online)</span>
          </div>
          <div class="feature">
            <svg class="check-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="20,6 9,17 4,12"></polyline>
            </svg>
            <span>Priority support 24/7</span>
          </div>
        </div>

        <div class="pricing">
          <div class="price">
            <span class="currency">MAD</span>
            <span class="amount" data-yearly="799" data-monthly="799">799</span>
            <span class="period">/ per month</span>
          </div>
          <p class="billing-info">billed yearly</p>
        </div>

        <form method="POST" style="margin: 0;">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
          <input type="hidden" name="planid" value="2">
          <button type="submit" class="plan-btn growth-btn">CHOOSE BUSINESS</button>
        </form>
        <p class="free-trial">7 days free</p>
      </div>

      <!-- Elite Partner Plan -->
      <div class="pricing-card">
        <div class="card-header">
          <h3 class="plan-name">Elite Partner</h3>
          <p class="plan-description">Tailored for enterprises & serious investors who want full control.</p>
        </div>
        
        <div class="features">
          <div class="feature">
            <svg class="check-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="20,6 9,17 4,12"></polyline>
            </svg>
            <span>Everything in Business Partner</span>
          </div>
          <div class="feature">
            <svg class="check-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="20,6 9,17 4,12"></polyline>
            </svg>
            <span>Dedicated account manager</span>
          </div>
          <div class="feature">
            <svg class="check-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="20,6 9,17 4,12"></polyline>
            </svg>
            <span>API access for custom integrations</span>
          </div>
          <div class="feature">
            <svg class="check-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="20,6 9,17 4,12"></polyline>
            </svg>
            <span>Multi-user permissions (manage your team)</span>
          </div>
          <div class="feature">
            <svg class="check-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="20,6 9,17 4,12"></polyline>
            </svg>
            <span>SLA-backed 24/7 support</span>
          </div>
          <div class="feature">
            <svg class="check-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="20,6 9,17 4,12"></polyline>
            </svg>
            <span>Compliance & audit reports</span>
          </div>
          <div class="feature">
            <svg class="check-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="20,6 9,17 4,12"></polyline>
            </svg>
            <span>Marketing & branding assistance</span>
          </div>
        </div>

        <div class="pricing">
          <div class="price">
            <span class="custom-price">Custom Pricing</span>
          </div>
        </div>

        <form method="POST" style="margin: 0;">
          <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
          <input type="hidden" name="planid" value="3">
          <button type="submit" class="plan-btn enterprise-btn">CHOOSE ELITE</button>
        </form>
        <p class="free-trial">Enterprise</p>
      </div>
    </div>
  </div>

  <script src="ourplan.js"></script>
  <script>
    // Auto-hide messages after 5 seconds
    setTimeout(function() {
      const messageContainer = document.querySelector('.message-container');
      if (messageContainer) {
        messageContainer.remove();
      }
    }, 5000);
  </script>
</body>
</html>
