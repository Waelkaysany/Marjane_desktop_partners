// ===== CART PAGE FUNCTIONALITY =====

// Initialize cart page
document.addEventListener('DOMContentLoaded', function() {
  console.log('Cart page loaded');
  
  // Wait for Cart class to be available
  const waitForCart = () => {
    if (typeof Cart !== 'undefined' && !window.cart) {
      window.cart = new Cart();
      console.log('Cart initialized on cart page');
      renderCart();
      setupEventListeners();
    } else if (window.cart) {
      console.log('Cart already initialized');
      renderCart();
      setupEventListeners();
    } else {
      console.log('Waiting for Cart class...');
      setTimeout(waitForCart, 100);
    }
  };
  
  waitForCart();
});

// Main cart rendering function
function renderCart() {
  const cartItems = document.getElementById('cartItems');
  const cartSummary = document.getElementById('cartSummary');
  const emptyCart = document.getElementById('emptyCart');
  
  if (!window.cart) {
    console.error('Cart not initialized');
    return;
  }
  
  const items = window.cart.getItems();
  console.log('Cart items:', items); // Debug log
  
  if (items.length === 0) {
    // Show empty cart
    if (cartItems) cartItems.style.display = 'none';
    if (cartSummary) cartSummary.style.display = 'none';
    if (emptyCart) emptyCart.style.display = 'flex';
  } else {
    // Show cart items
    if (cartItems) cartItems.style.display = 'block';
    if (cartSummary) cartSummary.style.display = 'block';
    if (emptyCart) emptyCart.style.display = 'none';
    
    // Render cart items with animation
    if (cartItems) {
      cartItems.innerHTML = items.map((item, index) => 
        generateCartItemHTML(item, index)
      ).join('');
    }
    
    // Update summary
    updateCartSummary();
  }
}

// Generate cart item HTML with modern styling
function generateCartItemHTML(item, index) {
  const [major, minor] = Number(item.price).toFixed(2).split('.');
  const total = (item.price * item.quantity).toFixed(2);
  const [totalMajor, totalMinor] = total.split('.');
  
  return `
    <div class="cart-item fade-in" data-item-id="${item.id}" style="animation-delay: ${index * 0.1}s">
      <div class="cart-item-image">
        <img src="${item.image}" alt="${item.name}" loading="lazy">
      </div>
      <div class="cart-item-details">
        <div class="cart-item-info">
          <h3 class="cart-item-name">${item.name}</h3>
          <p class="cart-item-specs">Size: Large, Color: White</p>
          <div class="cart-item-price">$${major}.${minor}</div>
        </div>
        <div class="cart-item-controls">
          <div class="cart-quantity-controls">
            <button class="cart-qty-btn" onclick="decreaseCartQuantity(${item.id})" 
                    ${item.quantity <= 1 ? 'disabled' : ''} title="Decrease quantity">
              −
            </button>
            <span class="cart-qty-count" id="cart-qty-${item.id}">${item.quantity}</span>
            <button class="cart-qty-btn" onclick="increaseCartQuantity(${item.id})" title="Increase quantity">
              +
            </button>
          </div>
          <button class="remove-item-btn" onclick="removeCartItem(${item.id})" title="Remove item">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
            </svg>
          </button>
        </div>
      </div>
    </div>
  `;
}

// Update cart summary with modern calculations
function updateCartSummary() {
  if (!window.cart) return;
  
  const subtotal = window.cart.getTotal();
  const shipping = 15; // Fixed delivery fee
  const discount = calculateDiscount(subtotal);
  const total = subtotal + shipping - discount;
  
  // Update summary elements
  const subtotalEl = document.getElementById('subtotal');
  const shippingEl = document.getElementById('shipping');
  const discountEl = document.getElementById('discount');
  const discountRow = document.getElementById('discountRow');
  const totalEl = document.getElementById('total');
  const checkoutBtn = document.getElementById('checkoutBtn');
  
  if (subtotalEl) subtotalEl.textContent = `$${subtotal.toFixed(2)}`;
  if (shippingEl) shippingEl.textContent = `$${shipping.toFixed(2)}`;
  
  if (discount > 0) {
    if (discountRow) discountRow.style.display = 'flex';
    if (discountEl) discountEl.textContent = `-$${discount.toFixed(2)}`;
  } else {
    if (discountRow) discountRow.style.display = 'none';
  }
  
  if (totalEl) totalEl.textContent = `$${total.toFixed(2)}`;
  
  // Enable/disable checkout button
  if (checkoutBtn) {
    checkoutBtn.disabled = subtotal === 0;
    if (subtotal === 0) {
      checkoutBtn.innerHTML = `
        <span>Cart is Empty</span>
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M5 12h14M12 5l7 7-7 7"/>
        </svg>
      `;
    } else {
      checkoutBtn.innerHTML = `
        <span>Go to Checkout</span>
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M5 12h14M12 5l7 7-7 7"/>
        </svg>
      `;
    }
  }
}

// Calculate discount based on total
function calculateDiscount(subtotal) {
  // 20% discount on orders over $50
  if (subtotal >= 50) {
    return subtotal * 0.2;
  }
  return 0;
}

// Increase cart quantity
function increaseCartQuantity(itemId) {
  if (!window.cart) return;
  
  const item = window.cart.getItems().find(item => item.id === itemId);
  if (item) {
    window.cart.updateQuantity(itemId, item.quantity + 1);
    renderCart();
    showNotification('Quantity updated', 'success');
  }
}

// Decrease cart quantity
function decreaseCartQuantity(itemId) {
  if (!window.cart) return;
  
  const item = window.cart.getItems().find(item => item.id === itemId);
  if (item && item.quantity > 1) {
    window.cart.updateQuantity(itemId, item.quantity - 1);
    renderCart();
    showNotification('Quantity updated', 'success');
  }
}

// Remove cart item
function removeCartItem(itemId) {
  if (!window.cart) return;
  
  const item = window.cart.getItems().find(item => item.id === itemId);
  if (item) {
    if (confirm(`Remove "${item.name}" from your cart?`)) {
      window.cart.removeItem(itemId);
      renderCart();
      showNotification(`${item.name} removed from cart`, 'success');
    }
  }
}

// Setup event listeners
function setupEventListeners() {
  
  // Checkout button
  const checkoutBtn = document.getElementById('checkoutBtn');
  if (checkoutBtn) {
    checkoutBtn.addEventListener('click', function() {
      if (!checkoutBtn.disabled) {
        // Call the submitOrder function from cart.js
        if (typeof window.submitOrder === 'function') {
          window.submitOrder();
        } else {
          showNotification('Checkout system not available', 'error');
        }
      }
    });
  }
  

  
  // Promo code functionality
  const promoCodeInput = document.getElementById('promoCode');
  const applyPromoBtn = document.getElementById('applyPromoBtn');
  const promoMessage = document.getElementById('promoMessage');
  
  if (promoCodeInput && applyPromoBtn) {
    // Apply promo on button click
    applyPromoBtn.addEventListener('click', function() {
      applyPromoCode();
    });
    
    // Apply promo on Enter key
    promoCodeInput.addEventListener('keypress', function(e) {
      if (e.key === 'Enter') {
        applyPromoCode();
      }
    });
  }
  
}

// Apply promo code
function applyPromoCode() {
  const promoCodeInput = document.getElementById('promoCode');
  const promoMessage = document.getElementById('promoMessage');
  
  if (!promoCodeInput || !promoMessage) return;
  
  const code = promoCodeInput.value.trim().toUpperCase();
  
  if (!code) {
    showPromoMessage('Please enter a promo code', 'error');
    return;
  }
  
  // Example promo codes
  const validCodes = {
    'SAVE10': { discount: 0.1, message: '10% off your order!' },
    'FREESHIP': { discount: 0, message: 'Free shipping applied!' },
    'WELCOME': { discount: 0.15, message: '15% welcome discount!' }
  };
  
  if (validCodes[code]) {
    const promo = validCodes[code];
    localStorage.setItem('activePromo', JSON.stringify({ code, ...promo }));
    showPromoMessage(promo.message, 'success');
    promoCodeInput.value = '';
    updateCartSummary(); // Recalculate with promo
  } else {
    showPromoMessage('Invalid promo code', 'error');
  }
}

// Show promo message
function showPromoMessage(message, type) {
  const promoMessage = document.getElementById('promoMessage');
  if (promoMessage) {
    promoMessage.textContent = message;
    promoMessage.className = `promo-message ${type}`;
    
    // Clear message after 3 seconds
    setTimeout(() => {
      promoMessage.textContent = '';
      promoMessage.className = 'promo-message';
    }, 3000);
  }
}



// Enhanced notification system
function showNotification(message, type = 'info') {
  // Remove existing notifications
  const existingNotifications = document.querySelectorAll('.notification');
  existingNotifications.forEach(notification => {
    notification.remove();
  });
  
  // Create notification element
  const notification = document.createElement('div');
  notification.className = `notification notification-${type}`;
  notification.innerHTML = `
    <div class="notification-content">
      <svg class="notification-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        ${getNotificationIcon(type)}
      </svg>
      <span class="notification-message">${message}</span>
      <button class="notification-close" onclick="this.parentElement.parentElement.remove()">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M18 6L6 18M6 6l12 12"/>
        </svg>
      </button>
    </div>
  `;
  
  // Add styles
  notification.style.cssText = `
    position: fixed;
    top: 20px;
    right: 20px;
    background: ${getNotificationColor(type)};
    color: white;
    padding: 16px 20px;
    border-radius: 12px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    z-index: 10000;
    font-family: 'Inter', sans-serif;
    font-weight: 500;
    transform: translateX(100%);
    transition: all 0.3s ease;
    max-width: 400px;
    border-left: 4px solid ${getNotificationBorderColor(type)};
  `;
  
  document.body.appendChild(notification);
  
  // Animate in
  setTimeout(() => {
    notification.style.transform = 'translateX(0)';
  }, 100);
  
  // Auto remove after 4 seconds
  setTimeout(() => {
    notification.style.transform = 'translateX(100%)';
    setTimeout(() => {
      if (notification.parentElement) {
        notification.remove();
      }
    }, 300);
  }, 4000);
}

// Get notification icon based on type
function getNotificationIcon(type) {
  switch (type) {
    case 'success':
      return '<path d="M9 12l2 2 4-4M21 12c-1 0-2-1-2-2s1-2 2-2 2 1 2 2-1 2-2 2z"/>';
    case 'error':
      return '<path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0zM12 9v4M12 17h.01"/>';
    case 'warning':
      return '<path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0zM12 9v4M12 17h.01"/>';
    default:
      return '<circle cx="12" cy="12" r="10M12 16v-4M12 8h.01"/>';
  }
}

// Get notification color based on type
function getNotificationColor(type) {
  switch (type) {
    case 'success':
      return '#059669';
    case 'error':
      return '#dc2626';
    case 'warning':
      return '#d97706';
    default:
      return '#3b82f6';
  }
}

// Get notification border color based on type
function getNotificationBorderColor(type) {
  switch (type) {
    case 'success':
      return '#047857';
    case 'error':
      return '#b91c1c';
    case 'warning':
      return '#b45309';
    default:
      return '#1d4ed8';
  }
}

// Make functions globally available
window.increaseCartQuantity = increaseCartQuantity;
window.decreaseCartQuantity = decreaseCartQuantity;
window.removeCartItem = removeCartItem;

// Test function to add sample items
window.addTestItems = function() {
  if (window.cart && window.recommendedProducts) {
    window.cart.addItem(window.recommendedProducts[0], 2);
    window.cart.addItem(window.recommendedProducts[1], 1);
    renderCart();
    showNotification('Test items added to cart', 'success');
    console.log('Test items added to cart');
  }
};

// Export for debugging
window.cartPage = {
  renderCart,
  updateCartSummary,
  showNotification,
  addTestItems
};

// Test function to add sample items to cart
window.addTestItemsToCart = function() {
  if (window.cart && window.recommendedProducts) {
    // Clear cart first
    window.cart.clearCart();
    
    // Add test items
    window.cart.addItem(window.recommendedProducts[0], 2);
    window.cart.addItem(window.recommendedProducts[1], 1);
    
    console.log('Test items added to cart:', window.cart.getItems());
    
    // Re-render cart
    renderCart();
    
    // Show notification
    showNotification('Test items added to cart!', 'success');
  } else {
    console.error('Cart or recommendedProducts not available');
  }
};
