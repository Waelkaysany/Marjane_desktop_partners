<?php
// Include required files
require_once 'includes/session.php';
require_once 'includes/OrderProcessor.php';

// Check if user is authenticated
require_auth();

// Handle order submission
$orderResult = null;
$showOrderConfirmation = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'place_order') {
        // Verify CSRF token
        if (!csrf_verify($_POST['csrf_token'] ?? null)) {
            $orderResult = ['success' => false, 'error' => 'Invalid request token'];
        } else {
            try {
                $orderProcessor = new OrderProcessor();
                $orderData = json_decode($_POST['order_data'], true);
                
                if ($orderData) {
                    $orderResult = $orderProcessor->processOrder($orderData);
                    if ($orderResult['success']) {
                        $showOrderConfirmation = true;
                    }
                } else {
                    $orderResult = ['success' => false, 'error' => 'Invalid order data'];
                }
            } catch (Exception $e) {
                $orderResult = ['success' => false, 'error' => $e->getMessage()];
                error_log("Place order exception: " . $e->getMessage());
            }
        }
    } elseif ($_POST['action'] === 'checkout') {
        // Handle checkout action
        if (!csrf_verify($_POST['csrf_token'] ?? null)) {
            $orderResult = ['success' => false, 'error' => 'Invalid request token'];
        } else {
            try {
                $orderProcessor = new OrderProcessor();
                $orderData = json_decode($_POST['order_data'], true);
                
                if ($orderData) {
                    error_log("=== CHECKOUT PROCESS STARTED ===");
                    error_log("POST data received: " . print_r($_POST, true));
                    error_log("Order data decoded: " . print_r($orderData, true));
                    error_log("Session data: " . print_r($_SESSION, true));
                    
                    $orderResult = $orderProcessor->processOrder($orderData);
                    
                    error_log("Checkout result: " . print_r($orderResult, true));
                    
                    if ($orderResult['success']) {
                        // Check if this is an AJAX request
                        if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
                            // AJAX request - return JSON response
                            header('Content-Type: application/json');
                            echo json_encode([
                                'success' => true,
                                'order_id' => $orderResult['order_id'],
                                'order_number' => $orderResult['order_number'],
                                'total_amount' => $orderResult['total_amount'],
                                'message' => 'Order placed successfully!'
                            ]);
                            exit;
                        } else {
                            // Regular form submission - redirect
                            // Store order success in session for JavaScript to clear cart
                            $_SESSION['order_success'] = [
                                'order_id' => $orderResult['order_id'],
                                'order_number' => $orderResult['order_number'],
                                'timestamp' => time()
                            ];
                            
                            // Clear cart from session/localStorage (will be handled by JavaScript)
                            $_SESSION['cart_cleared'] = true;
                            
                            // Redirect to order confirmation page
                            header("Location: order-confirmation.php?order_id=" . $orderResult['order_id']);
                            exit;
                        }
                    }
                } else {
                    $orderResult = ['success' => false, 'error' => 'Invalid order data'];
                    error_log("Checkout failed - Invalid order data");
                }
            } catch (Exception $e) {
                $orderResult = ['success' => false, 'error' => $e->getMessage()];
                error_log("Checkout exception: " . $e->getMessage());
            }
            
            // Handle AJAX error responses
            if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
                header('Content-Type: application/json');
                echo json_encode($orderResult);
                exit;
            }
        }
    }
}
 
// Get order history for display
$orderHistory = [];
try {
    $orderProcessor = new OrderProcessor();
    $orderHistory = $orderProcessor->getOrderHistory();
} catch (Exception $e) {
    // Log error but don't show to user
    error_log("Failed to get order history: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="cart.css">
  <title>YOUR CART - MARJANE</title>
</head>
<!-- 
  Professional Promo Code System
  - Clean, modern design with smooth animations
  - Real-time input validation and visual feedback
  - Professional color scheme and typography
  - Mobile-responsive layout
  - Test codes: SAVE20, WELCOME15, FLASH25, LOYALTY10
-->
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

  <!-- Main Cart Container -->
  <div class="cart-container">
    <!-- Breadcrumb Navigation -->
    <div class="breadcrumb">
              <a href="home.php">Home</a>
      <span class="separator">></span>
      <span class="current">Cart</span>
    </div>

    <!-- Page Title -->
    <div class="page-title">
      <h1>YOUR CART</h1>
    </div>

    <!-- Cart Content -->
    <div class="cart-content">
      <!-- Left Column - Cart Items -->
      <div class="cart-items-section">
        <div class="cart-items" id="cartItems">
          <!-- Cart items will be dynamically loaded here -->
        </div>

        <!-- Empty Cart State -->
        <div class="empty-cart" id="emptyCart">
          <div class="empty-cart-content">
            <div class="empty-cart-icon">
              <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <circle cx="9" cy="21" r="1"></circle>
                <circle cx="20" cy="21" r="1"></circle>
                <path d="M1 1h4l2.68 12.39a2 2 0 0 0 2 1.61h7.72a2 2 0 0 0 2-1.61L23 6H6"></path>
              </svg>
            </div>
            <h2>Your cart is empty</h2>
            <p>Looks like you haven't added any items to your cart yet.</p>
            <a href="home.php" class="start-shopping-btn">
              Start Shopping
            </a>
            <div class="demo-buttons">
              <button onclick="addTestItemsToCart()" class="demo-btn primary">
                Add Demo Items
              </button>
              <button onclick="testAddToCart()" class="demo-btn secondary">
                Test Single Item
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Column - Order Summary -->
      <div class="order-summary-section">
        <div class="order-summary" id="cartSummary">
          <h2>Order Summary</h2>
          
          <!-- Summary Details -->
          <div class="summary-details">
            <div class="summary-row">
              <span>Subtotal</span>
              <span id="subtotal">$0.00</span>
            </div>
            <div class="summary-row">
              <span>Shipping</span>
              <span id="shipping">$15.00</span>
            </div>
            <div class="summary-row discount" id="discountRow" style="display: none;">
              <span id="discountLabel">Discount</span>
              <span id="discount">-$0.00</span>
            </div>
            <div class="summary-row total">
              <span>Total</span>
              <span id="total">$0.00</span>
            </div>
          </div>

          <!-- Promo Code Section -->
          <div class="promo-code-section">
            <div class="promo-header">
              <div class="promo-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M9 14l6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M9.5 8.5h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M14.5 13.5h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </div>
              <h3>Promo Code</h3>
            </div>
            
            <div class="promo-form">
              <div class="promo-input-wrapper">
                <input 
                  type="text" 
                  id="promoCodeInput" 
                  placeholder="Enter promo code" 
                  class="promo-input-field"
                  maxlength="20"
                >
                <button 
                  type="button" 
                  id="applyPromoButton" 
                  class="promo-apply-btn"
                  disabled
                >
                  <span class="btn-text">Apply</span>
                  <svg class="btn-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="m12 5 7 7-7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                </button>
              </div>
              
              <div id="promoStatusMessage" class="promo-status"></div>
              
              <!-- Remove Promo Code Button (hidden by default) -->
              <div id="removePromoSection" class="promo-remove-section" style="display: none;">
                <button 
                  type="button" 
                  id="removePromoButton" 
                  class="promo-remove-btn"
                  onclick="removePromoCode()"
                >
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                  Remove Promo Code
                </button>
              </div>
              
              <div class="promo-info">
                <p class="promo-tip">💡 Save money with exclusive promo codes</p>
              </div>
            </div>
          </div>



                     <!-- Checkout Button -->
           <div id="checkoutForm" style="display: none;">
             <input type="hidden" name="action" value="checkout">
             <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
             <input type="hidden" name="order_data" id="orderDataInput">
             <input type="hidden" name="clear_cart" value="true">
             <button type="button" class="checkout-btn" id="checkoutBtn" disabled>
               <span class="btn-text">Proceed to Checkout</span>
               <span class="btn-spinner" style="display: none;">
                 <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                   <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" stroke-dasharray="31.416" stroke-dashoffset="31.416">
                     <animate attributeName="stroke-dasharray" dur="2s" values="0 31.416;15.708 15.708;0 31.416" repeatCount="indefinite"/>
                     <animate attributeName="stroke-dashoffset" dur="2s" values="0;-15.708;-31.416" repeatCount="indefinite"/>
                   </circle>
                 </svg>
               </span>
               </button>
           </div>

          <!-- Continue Shopping Link -->
          <div class="continue-shopping">
            <a href="home.php">Continue Shopping</a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Order Confirmation Section -->
  <?php if ($showOrderConfirmation && $orderResult && $orderResult['success']): ?>
  <div class="order-confirmation" id="orderConfirmation">
    <div class="confirmation-content">
      <div class="confirmation-header">
        <div class="success-icon">✓</div>
        <h2>Order Placed Successfully!</h2>
        <p>Thank you for your order. We'll process it right away.</p>
      </div>
      
      <div class="order-details">
        <div class="detail-row">
          <span class="label">Order Number:</span>
          <span class="value"><?php echo htmlspecialchars($orderResult['order_number']); ?></span>
        </div>
        <div class="detail-row">
          <span class="label">Order ID:</span>
          <span class="value">#<?php echo $orderResult['order_id']; ?></span>
        </div>
        <div class="detail-row">
          <span class="label">Total Amount:</span>
          <span class="value"><?php echo number_format($orderResult['total_amount'], 2); ?> MAD</span>
        </div>
        <div class="detail-row">
          <span class="label">Status:</span>
          <span class="value status-pending">Pending</span>
        </div>
      </div>
      
      <div class="confirmation-actions">
        <a href="home.php" class="btn-continue-shopping">Continue Shopping</a>
        <button onclick="viewOrderHistory()" class="btn-view-orders">View My Orders</button>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <!-- Order Success Message -->
  <?php if (isset($_SESSION['order_success'])): ?>
  <div class="order-success-message" id="orderSuccessMessage">
    <div class="success-content">
      <div class="success-icon">✓</div>
      <div class="success-text">
        <h3>Order Placed Successfully!</h3>
        <p>Your order #<?php echo htmlspecialchars($_SESSION['order_success']['order_number']); ?> has been received.</p>
        <p>The cart has been cleared and your items are being processed.</p>
      </div>
      <button onclick="hideOrderSuccessMessage()" class="close-success-btn">×</button>
    </div>
  </div>
  <?php endif; ?>

  <!-- Order History Section -->
  <?php if (!empty($orderHistory)): ?>
  <div class="order-history-section" id="orderHistory" style="display: none;">
    <div class="history-header">
      <h2>Order History</h2>
      <button onclick="hideOrderHistory()" class="close-btn">×</button>
    </div>
    
    <div class="order-list">
      <?php foreach ($orderHistory as $order): ?>
      <div class="order-item">
        <div class="order-header">
          <div class="order-info">
            <span class="order-number"><?php echo htmlspecialchars($order['order_number']); ?></span>
            <span class="order-date"><?php echo date('M j, Y', strtotime($order['created_at'])); ?></span>
          </div>
          <div class="order-status status-<?php echo $order['status']; ?>">
            <?php echo ucfirst($order['status']); ?>
          </div>
        </div>
        <div class="order-summary">
          <span class="total-amount"><?php echo number_format($order['total_amount'], 2); ?> MAD</span>
          <span class="item-count"><?php echo $order['item_count']; ?> item(s)</span>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>

  <!-- Footer Section -->
  <footer class="footer">
    <div class="footer-content">
      <div class="footer-left">
        <div class="footer-logo">
          <h3>MARJANE</h3>
          <p class="footer-tagline">There is no risk-free shopping</p>
        </div>
        <div class="system-status">
          <div class="status-indicator">
            <span class="status-dot"></span>
            <span class="status-text">All systems operational</span>
          </div>
        </div>
      </div>
      
      <div class="footer-right">
        <div class="footer-links">
          <div class="link-column">
            <h4>Company</h4>
            <ul>
              <li><a href="#">About</a></li>
              <li><a href="#">Features</a></li>
              <li><a href="#">Pricing</a></li>
              <li><a href="#">Contact</a></li>
              <li><a href="#">Blog</a></li>
            </ul>
          </div>
          
          <div class="link-column">
            <h4>Support</h4>
            <ul>
              <li><a href="#">Documentation</a></li>
              <li><a href="#">FAQ</a></li>
              <li><a href="#">Support</a></li>
            </ul>
          </div>
          
          <div class="link-column">
            <h4>Social</h4>
            <ul>
              <li><a href="#">X (Twitter)</a></li>
              <li><a href="#">LinkedIn</a></li>
              <li><a href="#">YouTube</a></li>
            </ul>
          </div>
        </div>
        
        <div class="footer-bottom">
          <div class="copyright">
            <p>© 2024 MARJANE. All rights reserved.</p>
          </div>
          <div class="legal-links">
            <a href="#">Privacy Policy</a>
            <a href="#">Terms of Use</a>
          </div>
        </div>
      </div>
    </div>
  </footer>

     <script src="recommendation-product.js"></script>
   <script src="cart.js"></script>
   <script src="cart-page.js"></script>
  <script>
    // Debug: Check if cart is working
    document.addEventListener('DOMContentLoaded', function() {
      console.log('Cart.php loaded');
      console.log('Window.cart:', window.cart);
      console.log('Cart items:', window.cart ? window.cart.getItems() : 'No cart');
      
      // Test function to add items manually
      window.testAddToCart = function() {
        if (window.cart && window.recommendedProducts) {
          window.cart.addItem(window.recommendedProducts[0], 1);
          console.log('Test item added, cart now has:', window.cart.getItems());
          // Force re-render
          if (window.renderCart) {
            window.renderCart();
          }
        } else {
          console.error('Cart or products not available');
        }
      };
      
      // Test function to check cart state
      window.checkCartState = function() {
        console.log('=== CART STATE DEBUG ===');
        console.log('Cart instance:', window.cart);
        console.log('Cart class:', window.Cart);
        console.log('Cart items:', window.cart ? window.cart.getItems() : 'No items');
        console.log('Cart total:', window.cart ? window.cart.getTotal() : 'No total');
        console.log('Cart discount:', window.cart ? window.cart.getDiscount() : 'No discount');
        console.log('Cart promo code:', window.cart ? window.cart.getPromoCode() : 'No promo code');
        console.log('LocalStorage cart:', localStorage.getItem('shoppingCart'));
        console.log('=======================');
      };
      
      // Check cart state after a delay
      setTimeout(window.checkCartState, 1000);
      
      // Initialize cart display
      updateCartDisplay();
      updateCartSummary();
      updateCartIcon();
      
      // Force update checkout form visibility
      const checkoutForm = document.getElementById('checkoutForm');
      if (checkoutForm) {
        checkoutForm.style.display = window.cart && window.cart.getCount() > 0 ? 'block' : 'none';
      }
      
      // Professional Promo Code Functionality
      const promoCodeInput = document.getElementById('promoCodeInput');
      const applyPromoButton = document.getElementById('applyPromoButton');
      const promoStatusMessage = document.getElementById('promoStatusMessage');
      
      if (promoCodeInput && applyPromoButton && promoStatusMessage) {
        console.log('Professional promo code system initialized');
        
        // Enable/disable apply button based on input
        promoCodeInput.addEventListener('input', function() {
          const hasValue = this.value.trim().length > 0;
          applyPromoButton.disabled = !hasValue;
          
          if (hasValue) {
            applyPromoButton.classList.add('active');
          } else {
            applyPromoButton.classList.remove('active');
          }
        });
        
        // Handle promo code application
        applyPromoButton.addEventListener('click', function() {
          const code = promoCodeInput.value.trim().toUpperCase();
          
          if (code === '') {
            showPromoStatus('Please enter a promo code', 'error');
            return;
          }
          
          // Simulate API call for promo validation
          validatePromoCode(code);
        });
        
        // Enter key support
        promoCodeInput.addEventListener('keypress', function(e) {
          if (e.key === 'Enter' && !applyPromoButton.disabled) {
            applyPromoButton.click();
          }
        });
        
        // Promo code validation logic
        function validatePromoCode(code) {
          const promoSection = document.querySelector('.promo-code-section');
          
          // Show loading state
          applyPromoButton.disabled = true;
          applyPromoButton.classList.add('loading');
          applyPromoButton.innerHTML = '<span class="btn-text">Validating...</span>';
          promoSection.classList.add('loading');
          
          // Simulate API delay
          setTimeout(() => {
            const validCodes = {
              'SAVE20': { discount: 20, message: '20% discount applied successfully!' },
              'WELCOME15': { discount: 15, message: 'Welcome discount applied! 15% off your order.' },
              'FLASH25': { discount: 25, message: 'Flash sale! 25% discount applied.' },
              'LOYALTY10': { discount: 10, message: 'Loyalty discount applied! 10% off.' }
            };
            
            if (validCodes[code]) {
              const promo = validCodes[code];
              
              // Success state
              promoSection.classList.remove('loading');
              promoSection.classList.add('success');
              promoCodeInput.classList.add('success');
              showPromoStatus(promo.message, 'success');
              
              promoCodeInput.value = '';
              applyPromoButton.disabled = true;
              applyPromoButton.classList.remove('active', 'loading');
              
              // Apply discount to cart
              if (window.cart) {
                const discountAmount = window.cart.applyPromoCode(code, promo.discount);
                console.log(`Promo code ${code} applied with ${promo.discount}% discount: $${discountAmount.toFixed(2)}`);
                
                // Show remove promo button
                const removePromoSection = document.getElementById('removePromoSection');
                if (removePromoSection) {
                  removePromoSection.style.display = 'block';
                }
                
                // Update cart display
                if (typeof updateCartSummary === 'function') {
                  updateCartSummary();
                  console.log('Cart summary updated after promo code application');
                } else {
                  console.error('updateCartSummary function not available');
                  // Try to force a cart re-render
                  if (typeof renderCart === 'function') {
                    renderCart();
                    console.log('Cart re-rendered instead');
                  }
                }
                
                // Show discount amount in success message
                showPromoStatus(`${promo.message} Discount: $${discountAmount.toFixed(2)}`, 'success');
              } else {
                console.error('Cart not available');
                showPromoStatus('Error: Cart not available', 'error');
              }
              
              // Reset success state after 3 seconds
              setTimeout(() => {
                promoSection.classList.remove('success');
                promoCodeInput.classList.remove('success');
              }, 3000);
              
            } else {
              // Error state
              promoSection.classList.remove('loading');
              promoSection.classList.add('error');
              promoCodeInput.classList.add('error');
              showPromoStatus('Invalid promo code. Please try again.', 'error');
              applyPromoButton.disabled = false;
              applyPromoButton.classList.remove('loading');
              
              // Reset error state after 3 seconds
              setTimeout(() => {
                promoSection.classList.remove('error');
                promoCodeInput.classList.remove('error');
              }, 3000);
            }
            
            // Reset button state
            applyPromoButton.innerHTML = '<span class="btn-text">Apply</span><svg class="btn-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="m12 5 7 7-7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
          }, 800);
        }
        
        // Display promo status messages
        function showPromoStatus(message, type) {
          promoStatusMessage.textContent = message;
          promoStatusMessage.className = `promo-status ${type}`;
          promoStatusMessage.style.display = 'block';
          
          // Auto-hide after 6 seconds
          setTimeout(() => {
            promoStatusMessage.style.display = 'none';
          }, 6000);
        }
        
        // Test promo codes for development
        window.testPromoCode = function(code) {
          promoCodeInput.value = code;
          applyPromoButton.click();
        };

        // Test promo code functionality directly
        window.testPromoCodeDirect = function() {
          if (window.cart) {
            console.log('=== DIRECT PROMO CODE TEST ===');
            console.log('Cart items:', window.cart.getItems());
            console.log('Cart total:', window.cart.getTotal());
            console.log('Current discount:', window.cart.getDiscount());
            
            // Test applying a promo code directly
            const discountAmount = window.cart.applyPromoCode('SAVE20', 20);
            console.log('Applied SAVE20 promo code, discount amount:', discountAmount);
            
            // Force update cart summary
            if (typeof updateCartSummary === 'function') {
              updateCartSummary();
            }
            
            // Force refresh cart display
            if (typeof renderCart === 'function') {
              renderCart();
            }
            
            console.log('=== END DIRECT PROMO CODE TEST ===');
          } else {
            console.error('Cart not available');
          }
        };

        // Check cart summary elements
        window.checkCartSummaryElements = function() {
          console.log('=== CART SUMMARY ELEMENTS CHECK ===');
          const subtotalEl = document.getElementById('subtotal');
          const shippingEl = document.getElementById('shipping');
          const discountEl = document.getElementById('discount');
          const discountRow = document.getElementById('discountRow');
          const totalEl = document.getElementById('total');
          
          console.log('Cart Summary Elements:', {
            subtotalEl: subtotalEl,
            shippingEl: shippingEl,
            discountEl: discountEl,
            discountRow: discountRow,
            totalEl: totalEl
          });
          
          if (subtotalEl) console.log('Subtotal element found, current text:', subtotalEl.textContent);
          if (shippingEl) console.log('Shipping element found, current text:', shippingEl.textContent);
          if (discountEl) console.log('Discount element found, current text:', discountEl.textContent);
          if (totalEl) console.log('Total element found, current text:', totalEl.textContent);
          
          console.log('=== END CART SUMMARY ELEMENTS CHECK ===');
        };

        // Remove promo code
        window.removePromoCode = function() {
          if (window.cart) {
            window.cart.removePromoCode();
            console.log('Promo code removed');
            
            // Update cart display
            if (typeof updateCartSummary === 'function') {
              updateCartSummary();
            }
            
            // Reset promo section
            const promoSection = document.querySelector('.promo-code-section');
            promoSection.classList.remove('success', 'error');
            promoCodeInput.classList.remove('success', 'error');
            promoStatusMessage.style.display = 'none';
            
            // Hide remove promo button
            const removePromoSection = document.getElementById('removePromoSection');
            if (removePromoSection) {
              removePromoSection.style.display = 'none';
            }
            
            // Reset input and button
            promoCodeInput.value = '';
            applyPromoButton.disabled = true;
            applyPromoButton.classList.remove('active');
            
            showPromoStatus('Promo code removed', 'success');
          }
        };
        
        console.log('Professional promo code system ready');
        console.log('Available test codes: SAVE20 (20%), WELCOME15 (15%), FLASH25 (25%), LOYALTY10 (10%)');
        
        // Check if promo code is already applied
        if (window.cart && window.cart.getPromoCode()) {
          const removePromoSection = document.getElementById('removePromoSection');
          if (removePromoSection) {
            removePromoSection.style.display = 'block';
          }
          console.log('Promo code already applied:', window.cart.getPromoCode());
        }
        
      } else {
        console.error('Promo code elements not found!');
      }
    });

    // Order history functions
    window.viewOrderHistory = function() {
      const orderHistory = document.getElementById('orderHistory');
      if (orderHistory) {
        orderHistory.style.display = 'block';
      }
    };

    window.hideOrderHistory = function() {
      const orderHistory = document.getElementById('orderHistory');
      if (orderHistory) {
        orderHistory.style.display = 'none';
      }
    };

    // Cart display functions
    function updateCartDisplay() {
      const cartItems = document.getElementById('cartItems');
      const emptyCart = document.getElementById('emptyCart');
      
      if (!window.cart || window.cart.getCount() === 0) {
        // Show empty cart
        if (cartItems) cartItems.style.display = 'none';
        if (emptyCart) emptyCart.style.display = 'block';
        return;
      }
      
      // Hide empty cart and show items
      if (emptyCart) emptyCart.style.display = 'none';
      if (cartItems) cartItems.style.display = 'block';
      
      // Generate cart items HTML
      const itemsHTML = window.cart.items.map(item => `
        <div class="cart-item" data-product-id="${item.id}">
          <div class="item-image">
            <img src="${item.image}" alt="${item.name}">
          </div>
          <div class="item-details">
            <h3 class="item-name">${item.name}</h3>
            <p class="item-price">$${item.price.toFixed(2)}</p>
          </div>
          <div class="item-quantity">
            <button class="qty-btn minus" onclick="updateItemQuantity(${item.id}, ${item.quantity - 1})">−</button>
            <span class="qty-count">${item.quantity}</span>
            <button class="qty-btn plus" onclick="updateItemQuantity(${item.id}, ${item.quantity + 1})">+</button>
          </div>
          <div class="item-total">
            <span class="total-amount">$${(item.price * item.quantity).toFixed(2)}</span>
          </div>
          <div class="item-actions">
            <button class="remove-btn" onclick="removeCartItem(${item.id})" title="Remove item">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M18 6L6 18M6 6l12 12"/>
              </svg>
            </button>
          </div>
        </div>
      `).join('');
      
      if (cartItems) {
        cartItems.innerHTML = itemsHTML;
      }
    }

            // Function to update cart summary
        function updateCartSummary() {
          if (!window.cart) return;
          
          const subtotal = window.cart.getTotal();
          const shipping = subtotal > 0 ? 15.00 : 0;
          const discount = window.cart.getDiscount() || 0;
          const total = Math.max(0, subtotal + shipping - discount);
          
          console.log('Updating cart summary:', { subtotal, shipping, discount, total });
          
          // Update display
          const subtotalEl = document.getElementById('subtotal');
          const shippingEl = document.getElementById('shipping');
          const totalEl = document.getElementById('total');
          const discountEl = document.getElementById('discount');
          const discountRow = document.getElementById('discountRow');
          
          if (subtotalEl) subtotalEl.textContent = `$${subtotal.toFixed(2)}`;
          if (shippingEl) shippingEl.textContent = `$${shipping.toFixed(2)}`;
          if (totalEl) totalEl.textContent = `$${total.toFixed(2)}`;
          
          // Show/hide discount row
          if (discountRow && discountEl) {
            if (discount > 0) {
              discountRow.style.display = 'flex';
              discountEl.textContent = `-$${discount.toFixed(2)}`;
            } else {
              discountRow.style.display = 'none';
            }
          }
          
          // Update checkout button
          const checkoutBtn = document.getElementById('checkoutBtn');
          const checkoutForm = document.getElementById('checkoutForm');
          if (checkoutBtn && checkoutForm) {
            checkoutBtn.disabled = subtotal <= 0;
            checkoutForm.style.display = subtotal > 0 ? 'block' : 'none';
            
            // Update button text based on cart state
            const btnText = checkoutBtn.querySelector('.btn-text');
            if (btnText) {
              if (subtotal <= 0) {
                btnText.textContent = 'Cart is Empty';
              } else {
                btnText.textContent = 'Proceed to Checkout';
              }
            }
          }
        }

    // Function to update cart icon
    function updateCartIcon() {
      if (!window.cart) return;
      
      const cartCounter = document.querySelector('.cart-counter');
      if (cartCounter) {
        cartCounter.textContent = window.cart.getCount();
      }
    }

            // Function to update item quantity
        function updateItemQuantity(productId, newQuantity) {
          if (window.cart) {
            window.cart.updateQuantity(productId, newQuantity);
            updateCartDisplay();
            updateCartSummary();
            updateCartIcon();
            
            // Force update checkout form visibility
            const checkoutForm = document.getElementById('checkoutForm');
            if (checkoutForm) {
              checkoutForm.style.display = window.cart.getCount() > 0 ? 'block' : 'none';
            }
          }
        }

        // Function to remove cart item
        function removeCartItem(productId) {
          if (window.cart) {
            window.cart.removeItem(productId);
            updateCartDisplay();
            updateCartSummary();
            updateCartIcon();
            
            // Force update checkout form visibility
            const checkoutForm = document.getElementById('checkoutForm');
            if (checkoutForm) {
              checkoutForm.style.display = window.cart.getCount() > 0 ? 'block' : 'none';
            }
          }
        }

        // Add test items function
        window.addTestItemsToCart = function() {
          if (window.cart && window.recommendedProducts) {
            // Add a few test items
            window.cart.addItem(window.recommendedProducts[0], 2); // Couscous
            window.cart.addItem(window.recommendedProducts[1], 1); // Sidi Ali
            window.cart.addItem(window.recommendedProducts[2], 3); // Tonik
            console.log('Test items added to cart');
            
            // Update display
            updateCartDisplay();
            updateCartSummary();
            updateCartIcon();
            
            // Force update checkout form visibility
            const checkoutForm = document.getElementById('checkoutForm');
            if (checkoutForm) {
              checkoutForm.style.display = window.cart.getCount() > 0 ? 'block' : 'none';
            }
          } else {
            console.error('Cart or products not available');
          }
        };

        // Check if order was successful and clear cart
        function checkOrderSuccess() {
          <?php if (isset($_SESSION['order_success'])): ?>
          // Clear cart after successful order
          if (window.cart) {
            window.cart.clearCart();
            console.log('Cart cleared after successful order');
            
            // Update cart display
            if (typeof updateCartDisplay === 'function') {
              updateCartDisplay();
            }
            if (typeof updateCartSummary === 'function') {
              updateCartSummary();
            }
            if (typeof updateCartIcon === 'function') {
              updateCartIcon();
            }
          }
          
          // Remove order success from session
          <?php 
          unset($_SESSION['order_success']);
          endif; 
          ?>
          
          // Check if cart was cleared from session
          <?php if (isset($_SESSION['cart_cleared'])): ?>
          if (window.cart) {
            console.log('Clearing cart after successful checkout...');
            window.cart.clearCart();
            console.log('Cart cleared from session after checkout');
            
            // Update cart display
            if (typeof updateCartDisplay === 'function') {
              updateCartDisplay();
            }
            if (typeof updateCartSummary === 'function') {
              updateCartSummary();
            }
            if (typeof updateCartIcon === 'function') {
              updateCartIcon();
            }
            
            // Show success message
            alert('Order placed successfully! Your cart has been cleared.');
          }
          
          // Remove cart cleared flag from session
          <?php 
          unset($_SESSION['cart_cleared']);
          endif; 
          ?>
        }
        
        // Enhanced cart clearing after successful order
        window.clearCartAfterOrder = function() {
          if (window.cart) {
            console.log('Clearing cart after successful order...');
            window.cart.clearCart();
            
            // Update all cart displays
            updateCartDisplay();
            updateCartSummary();
            updateCartIcon();
            
            // Hide checkout form
            const checkoutForm = document.getElementById('checkoutForm');
            if (checkoutForm) {
              checkoutForm.style.display = 'none';
            }
            
            // Show empty cart state
            const cartItems = document.getElementById('cartItems');
            const emptyCart = document.getElementById('emptyCart');
            if (cartItems) cartItems.style.display = 'none';
            if (emptyCart) emptyCart.style.display = 'block';
            
            console.log('Cart successfully cleared and updated');
          }
        };
        
        // Check and clear cart after checkout redirect
        window.checkAndClearCartAfterCheckout = function() {
          const cartToClear = sessionStorage.getItem('cartToClear');
          const cartItemsCount = sessionStorage.getItem('cartItemsCount');
          
          if (cartToClear === 'true' && cartItemsCount && window.cart) {
            console.log('Cart marked for clearing after checkout. Items count:', cartItemsCount);
            
            // Clear the cart
            window.cart.clearCart();
            
            // Update all cart displays
            updateCartDisplay();
            updateCartSummary();
            updateCartIcon();
            
            // Hide checkout form
            const checkoutForm = document.getElementById('checkoutForm');
            if (checkoutForm) {
              checkoutForm.style.display = 'none';
            }
            
            // Show empty cart state
            const cartItems = document.getElementById('cartItems');
            const emptyCart = document.getElementById('emptyCart');
            if (cartItems) cartItems.style.display = 'none';
            if (emptyCart) emptyCart.style.display = 'block';
            
            // Clear sessionStorage flags
            sessionStorage.removeItem('cartToClear');
            sessionStorage.removeItem('cartItemsCount');
            
            // Show success message
            const successMessage = document.createElement('div');
            successMessage.className = 'checkout-success-message';
            successMessage.innerHTML = `
              <div class="success-content">
                <div class="success-icon">✓</div>
                <div class="success-text">
                  <h3>Order Placed Successfully!</h3>
                  <p>Your cart has been cleared and your items are being processed.</p>
                </div>
              </div>
            `;
            
            // Insert success message at the top of cart container
            const cartContainer = document.querySelector('.cart-container');
            if (cartContainer) {
              cartContainer.insertBefore(successMessage, cartContainer.firstChild);
              
              // Auto-remove success message after 5 seconds
              setTimeout(() => {
                if (successMessage.parentNode) {
                  successMessage.parentNode.removeChild(successMessage);
                }
              }, 5000);
            }
            
            console.log('Cart successfully cleared after checkout redirect');
          }
        };

        // Run order success check when page loads
        document.addEventListener('DOMContentLoaded', function() {
          checkOrderSuccess();
          
          // Check if cart should be cleared after checkout redirect
          checkAndClearCartAfterCheckout();
        });

        // Function to hide order success message
        window.hideOrderSuccessMessage = function() {
          const successMessage = document.getElementById('orderSuccessMessage');
          if (successMessage) {
            successMessage.style.display = 'none';
          }
        };

        // Auto-hide success message after 8 seconds
        <?php if (isset($_SESSION['order_success'])): ?>
        setTimeout(function() {
          hideOrderSuccessMessage();
        }, 8000);
        <?php endif; ?>

                 // Function to prepare order data for checkout
         window.prepareCheckout = function(event) {
           event.preventDefault();
           if (window.cart && window.cart.submitOrder) {
             window.cart.submitOrder();
           } else {
             alert('Checkout system not available. Please refresh the page.');
           }
         };

         // Checkout form is handled by cart.js submitOrder method
      </script>
</body>
</html>
