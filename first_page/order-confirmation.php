<?php
// Include required files
require_once 'includes/session.php';
require_once 'includes/OrderProcessor.php';

// Check if user is authenticated
require_auth();

// Get order ID from URL
$orderId = $_GET['order_id'] ?? null;

if (!$orderId) {
    header('Location: cart.php');
    exit;
}

// Get order details
$orderDetails = null;
$orderItems = [];
try {
    $orderProcessor = new OrderProcessor();
    $orderDetails = $orderProcessor->getOrderDetails($orderId);
    if ($orderDetails) {
        $orderItems = $orderProcessor->getOrderItems($orderId);
    }
} catch (Exception $e) {
    error_log("Failed to get order details: " . $e->getMessage());
    header('Location: cart.php');
    exit;
}

// Verify order belongs to current partner
if (!$orderDetails || $orderDetails['partner_id'] != $_SESSION['auth']['partner_id']) {
    header('Location: cart.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="cart.css">
    <title>Order Confirmation - MARJANE</title>
</head>
<body>
    <!-- Navigation Bar -->
    <nav>
        <div class="logo">
            <p>MARJANE</p>
        </div>
        <div class="menu">
            <ul>
                <li><a href="home.php">HOME</a></li>
                <li><a href="#">PROMOTIONS</a></li>
                <li><a href="#">PRODUCTS</a></li>
                <li><a href="equipment/equipment.php">EQUIPMENTS</a></li>
            </ul>
        </div>
        <div class="right-side">
            <button class="button">
                <span class="text">Discover</span>
                <span class="svg">
                    <svg xmlns="http://www.w3.org/2000/svg" width="50" height="20" viewBox="0 0 38 15" fill="none">
                        <path fill="white" d="M10 7.519l-.939-.344h0l.939.344zm14.386-1.205l-.981-.192.981.192zm1.276 5.509l.537.843.148-.094.107-.139-.792-.611zm4.819-4.304l-.385-.923h0l.385.923zm7.227.707a1 1 0 0 0 0-1.414L31.343.448a1 1 0 0 0-1.414 0 1 1 0 0 0 0 1.414l5.657 5.657-5.657 5.657a1 1 0 0 0 1.414 1.414l6.364-6.364zM1 7.519l.554.833.029-.019.094-.061.361-.23 1.277-.77c1.054-.609 2.397-1.32 3.629-1.787.617-.234 1.17-.392 1.623-.455.477-.066.707-.008.788.034.025.013.031.021.039.034a.56.56 0 0 1 .058.235c.029.327-.047.906-.39 1.842l1.878.689c.383-1.044.571-1.949.505-2.705-.072-.815-.45-1.493-1.16-1.865-.627-.329-1.358-.332-1.993-.244-.659.092-1.367.305-2.056.566-1.381.523-2.833 1.297-3.921 1.925l-1.341.808-.385.245-.104.068-.028.018c-.011.007-.011.007.543.84zm8.061-.344c-.198.54-.328 1.038-.36 1.484-.032.441.024.94.325 1.364.319.45.786.64 1.21.697.403.054.824-.001 1.21-.09.775-.179 1.694-.566 2.633-1.014l3.023-1.554c2.115-1.122 4.107-2.168 5.476-2.524.329-.086.573-.117.742-.115s.195.038.161.014c-.15-.105.085-.139-.076.685l1.963.384c.192-.98.152-2.083-.74-2.707-.405-.283-.868-.37-1.28-.376s-.849.069-1.274.179c-1.65.43-3.888 1.621-5.909 2.693l-2.948 1.517c-.92.439-1.673.743-2.221.87-.276.064-.429.065-.492.057-.043-.006.066.003.155.127.07.099.024.131.038-.063.014-.187.078-.49.243-.94l-1.878-.689zm14.343-1.053c-.361 1.844-.474 3.185-.413 4.161.059.95.294 1.72.811 2.215.567.544 1.242.546 1.664.459a2.34 2.34 0 0 0 .502-.167l.15-.076.049-.028.018-.011c.013-.008.013-.008-.524-.852l-.536-.844.019-.012c-.038.018-.064.027-.084.032-.037.008.053-.013.125.056.021.02-.151-.135-.198-.895-.046-.734.034-1.887.38-3.652l-1.963-.384zm2.257 5.701l.791.611.024-.031.08-.101.311-.377 1.093-1.213c.922-.954 2.005-1.894 2.904-2.27l-.771-1.846c-1.31.547-2.637 1.758-3.572 2.725l-1.184 1.314-.341.414-.093.117-.025.032c-.01.013-.01.013.781.624zm5.204-3.381c.989-.413 1.791-.42 2.697-.307.871.108 2.083.385 3.437.385v-2c-1.197 0-2.041-.226-3.19-.369-1.114-.139-2.297-.146-3.715.447l.771 1.846z"></path>
                    </svg>
                </span>
            </button>
            <a class="cart-link" href="cart.php" aria-label="Cart">
                <div class="cart-icon-container">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="9" cy="21" r="1"></circle>
                        <circle cx="20" cy="21" r="1"></circle>
                        <path d="M1 1h4l2.68 12.39a2 2 0 0 0 2 1.61h7.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                    </svg>
                    <div class="cart-counter">0</div>
                </div>
            </a>
        </div>
    </nav>

    <!-- Main Container -->
    <div class="cart-container">
        <!-- Breadcrumb Navigation -->
        <div class="breadcrumb">
            <a href="home.php">Home</a>
            <span class="separator">></span>
            <a href="cart.php">Cart</a>
            <span class="separator">></span>
            <span class="current">Order Confirmation</span>
        </div>

        <!-- Order Confirmation Content -->
        <div class="confirmation-content">
            <div class="confirmation-header">
                <div class="success-icon">✓</div>
                <h1>Order Completed Successfully!</h1>
                <p>Thank you for your order. We'll process it right away.</p>
            </div>
            
            <div class="order-details">
                <div class="detail-row">
                    <span class="label">Order Number:</span>
                    <span class="value"><?php echo htmlspecialchars($orderDetails['order_number']); ?></span>
                </div>
                <div class="detail-row">
                    <span class="label">Order ID:</span>
                    <span class="value">#<?php echo $orderDetails['id']; ?></span>
                </div>
                <div class="detail-row">
                    <span class="label">Order Date:</span>
                    <span class="value"><?php echo date('M j, Y \a\t g:i A', strtotime($orderDetails['created_at'])); ?></span>
                </div>
                <div class="detail-row">
                    <span class="label">Status:</span>
                    <span class="value status-<?php echo $orderDetails['status']; ?>"><?php echo ucfirst($orderDetails['status']); ?></span>
                </div>
                <div class="detail-row">
                    <span class="label">Total Amount:</span>
                    <span class="value"><?php echo number_format($orderDetails['total_amount'], 2); ?> <?php echo htmlspecialchars($orderDetails['currency']); ?></span>
                </div>
            </div>
            
            <div class="order-items">
                <h3>Order Items</h3>
                <div class="items-list">
                    <?php foreach ($orderItems as $item): ?>
                    <div class="order-item">
                        <div class="item-info">
                            <h4 class="item-name"><?php echo htmlspecialchars($item['product_name']); ?></h4>
                            <?php if ($item['product_sku']): ?>
                            <p class="item-sku">SKU: <?php echo htmlspecialchars($item['product_sku']); ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="item-details">
                            <span class="item-quantity">Qty: <?php echo $item['quantity']; ?></span>
                            <span class="item-price"><?php echo number_format($item['unit_price'], 2); ?> <?php echo htmlspecialchars($orderDetails['currency']); ?></span>
                            <span class="item-subtotal"><?php echo number_format($item['subtotal'], 2); ?> <?php echo htmlspecialchars($orderDetails['currency']); ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="next-steps">
                <h3>What's Next?</h3>
                <ul>
                    <li>We'll send you an email confirmation shortly</li>
                    <li>Your order will be processed within 24-48 hours</li>
                    <li>You'll receive tracking information once shipped</li>
                    <li>Contact us if you have any questions</li>
                </ul>
            </div>
            
            <div class="confirmation-actions">
                <a href="home.php" class="btn-continue-shopping">Continue Shopping</a>
                <button onclick="window.print()" class="btn-print-receipt">Print Receipt</button>
                <a href="cart.php" class="btn-view-orders">View My Orders</a>
            </div>
        </div>
    </div>

    <script>
        // Set focus to confirmation header for accessibility
        document.addEventListener('DOMContentLoaded', function() {
            const header = document.querySelector('.confirmation-header h1');
            if (header) {
                header.focus();
            }
        });
    </script>
</body>
</html>
