// Cart System - Complete implementation with localStorage persistence

class Cart {
  constructor() {
    this.items = this.loadFromStorage();
    this.discount = 0;
    this.promoCode = null;
    this.updateCartIcon();
  }

  // Load cart from localStorage
  loadFromStorage() {
    const savedCart = localStorage.getItem('shoppingCart');
    if (savedCart) {
      const cartData = JSON.parse(savedCart);
      // Handle both old format (just items array) and new format (with discount)
      if (Array.isArray(cartData)) {
        // Old format - just items
        this.discount = 0;
        this.promoCode = null;
        return cartData;
      } else {
        // New format - with discount and promo code
        this.discount = cartData.discount || 0;
        this.promoCode = cartData.promoCode || null;
        return cartData.items || [];
      }
    }
    this.discount = 0;
    this.promoCode = null;
    return [];
  }

  // Save cart to localStorage
  saveToStorage() {
    const cartData = {
      items: this.items,
      discount: this.discount,
      promoCode: this.promoCode
    };
    localStorage.setItem('shoppingCart', JSON.stringify(cartData));
    this.updateCartIcon();
  }

  // Add item to cart
  addItem(product, quantity = 1) {
    const existingItem = this.items.find(item => item.id === product.id);
    
    if (existingItem) {
      existingItem.quantity += quantity;
    } else {
      this.items.push({
        id: product.id,
        name: product.name,
        price: product.price,
        image: product.image,
        quantity: quantity
      });
    }
    
    this.saveToStorage();
    return this.items;
  }

  // Update item quantity
  updateQuantity(productId, quantity) {
    const item = this.items.find(item => item.id === productId);
    
    if (item) {
      if (quantity <= 0) {
        this.removeItem(productId);
      } else {
        item.quantity = quantity;
        this.saveToStorage();
      }
    } else if (quantity > 0) {
      // If item doesn't exist but quantity > 0, add it
      const product = window.recommendedProducts ? window.recommendedProducts.find(p => p.id === productId) : null;
      if (product) {
        this.addItem(product, quantity);
      }
    }
    
    return this.items;
  }

  // Remove item from cart
  removeItem(productId) {
    this.items = this.items.filter(item => item.id !== productId);
    this.saveToStorage();
    return this.items;
  }

  // Get cart total
  getTotal() {
    const total = this.items.reduce((total, item) => total + (item.price * item.quantity), 0);
    console.log('Cart total calculated:', total, 'Items:', this.items);
    return total;
  }

  // Get cart total with discount
  getTotalWithDiscount() {
    const subtotal = this.getTotal();
    return subtotal - this.discount;
  }

  // Apply promo code discount
  applyPromoCode(code, discountPercent) {
    const subtotal = this.getTotal();
    this.discount = subtotal * (discountPercent / 100);
    this.promoCode = code;
    this.saveToStorage();
    return this.discount;
  }

  // Remove promo code discount
  removePromoCode() {
    this.discount = 0;
    this.promoCode = null;
    this.saveToStorage();
  }

  // Get current discount
  getDiscount() {
    console.log('Current discount:', this.discount, 'Promo code:', this.promoCode);
    return this.discount;
  }

  // Get current promo code
  getPromoCode() {
    return this.promoCode;
  }

  // Get cart count
  getCount() {
    return this.items.reduce((count, item) => count + item.quantity, 0);
  }

  // Clear cart
  clearCart() {
    this.items = [];
    this.discount = 0;
    this.promoCode = null;
    this.saveToStorage();
    
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
    
    // Force update checkout form visibility
    const checkoutForm = document.getElementById('checkoutForm');
    if (checkoutForm) {
      checkoutForm.style.display = 'none';
    }
  }

  // Update cart icon with count
  updateCartIcon() {
    const cartCounter = document.querySelector('.cart-counter');
    const count = this.getCount();
    
    if (cartCounter) {
      if (count > 0) {
        cartCounter.textContent = count;
        cartCounter.style.display = 'flex';
      } else {
        cartCounter.style.display = 'none';
      }
    }
  }

  // Get all cart items
  getItems() {
    return this.items;
  }

  // Prepare order data for submission
  prepareOrderData() {
    return {
      items: this.items.map(item => ({
        product_name: item.name,
        product_sku: item.id.toString(),
        quantity: item.quantity,
        unit_price: item.price
      })),
      discount: this.discount
    };
  }

  // Submit order to server via AJAX
  async submitOrder() {
    try {
      const orderData = this.prepareOrderData();
      const checkoutBtn = document.getElementById('checkoutBtn');
      
      if (!checkoutBtn) {
        throw new Error('Checkout button not found');
      }
      
      // Disable button and show spinner
      this.disableCheckoutButton();
      
      // Prepare cart data for submission
      const cartData = {
        items: orderData.items,
        discount: orderData.discount
      };
      
      // Create form data
      const formData = new FormData();
      formData.append('action', 'checkout');
      formData.append('order_data', JSON.stringify(cartData));
      formData.append('csrf_token', this.getCSRFToken());
      
      // Send AJAX request to cart.php with checkout action
      const response = await fetch('cart.php', {
        method: 'POST',
        body: formData,
        headers: {
          'Accept': 'application/json'
        }
      });
      
      const result = await response.json();
      
      if (result.success) {
        // Order successful - clear cart and show modal
        this.clearCart();
        this.showOrderCompletedModal(result);
        this.enableCheckoutButton();
      } else {
        // Order failed
        throw new Error(result.error || 'Checkout failed');
      }
      
    } catch (error) {
      console.error('Failed to submit order:', error);
      this.enableCheckoutButton();
      alert('Failed to submit order: ' + error.message);
    }
  }

  // Disable checkout button and show spinner
  disableCheckoutButton() {
    const checkoutBtn = document.getElementById('checkoutBtn');
    if (checkoutBtn) {
      checkoutBtn.disabled = true;
      const btnText = checkoutBtn.querySelector('.btn-text');
      const btnSpinner = checkoutBtn.querySelector('.btn-spinner');
      
      if (btnText) btnText.textContent = 'Processing your order...';
      if (btnSpinner) btnSpinner.style.display = 'inline-block';
    }
  }

  // Enable checkout button and hide spinner
  enableCheckoutButton() {
    const checkoutBtn = document.getElementById('checkoutBtn');
    if (checkoutBtn) {
      checkoutBtn.disabled = false;
      const btnText = checkoutBtn.querySelector('.btn-text');
      const btnSpinner = checkoutBtn.querySelector('.btn-spinner');
      
      if (btnText) btnText.textContent = 'Proceed to Checkout';
      if (btnSpinner) btnSpinner.style.display = 'none';
    }
  }

  // Get CSRF token from the page
  getCSRFToken() {
    const tokenInput = document.querySelector('input[name="csrf_token"]');
    return tokenInput ? tokenInput.value : '';
  }

  // Show order completed modal
  showOrderCompletedModal(orderData) {
    // Get cart items for display (since server doesn't return them)
    const cartItems = this.items || [];
    
    // Create modal HTML
    const modalHTML = `
      <div id="orderCompletedModal" class="order-completed-modal" role="dialog" aria-labelledby="modalTitle" aria-describedby="modalDescription">
        <div class="modal-content">
          <div class="modal-header">
            <h2 id="modalTitle" class="modal-title">Order Detail</h2>
            <button type="button" class="modal-close" onclick="closeOrderModal()" aria-label="Close modal">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M18 6L6 18M6 6l12 12"/>
              </svg>
            </button>
          </div>
          
          <div class="modal-body" id="modalDescription">
            <!-- Order ID and Status -->
            <div class="order-header-section">
              <div class="order-id-row">
                <span class="order-id-label">Order ID</span>
                <span class="order-id-value">#${orderData.order_number || 'Processing...'}</span>
              </div>
              <div class="order-status">On Deliver</div>
            </div>
            
            <!-- Delivery Status Cards -->
            <div class="delivery-cards">
              <div class="delivery-status-card">
                <h3>Delivery Status</h3>
                <div class="delivery-message">Be patient, package on deliver!</div>
                <div class="progress-bar">
                  <div class="progress-fill"></div>
                </div>
                <div class="delivery-locations">
                  <span>MOROCCO,El jadida</span>
                  <span>Wail el kaysany</span>
                </div>
              </div>
              
              <div class="estimated-arrival">
                <h3>Estimated Arrival</h3>
                <div class="arrival-date">9 July 2025</div>
                <div class="delivery-time">Delivered in 5 Days</div>
              </div>
            </div>
            
            <!-- Timeline Section -->
            <div class="timeline-section">
              <h3 class="timeline-title">Timeline</h3>
              <div class="timeline-item">
                <div class="timeline-date">4 Jul (Now) 06:00</div>
                <div class="timeline-content">
                  <div class="timeline-message">Your package is packed by the courier</div>
                  <div class="timeline-location">Malang, East Java, Indonesia</div>
                </div>
                <div class="timeline-icon">✓</div>
              </div>
              <div class="timeline-item">
                <div class="timeline-date">2 Jul 06:00</div>
                <div class="timeline-content">
                  <div class="timeline-message">Shipment has been created</div>
                  <div class="timeline-location">Malang, Indonesia</div>
                </div>
                <div class="timeline-icon">✓</div>
              </div>
              <div class="timeline-item">
                <div class="timeline-date">1 Jul 06:00</div>
                <div class="timeline-content">
                  <div class="timeline-message">Order placed</div>
                  <div class="timeline-location">Nike Store</div>
                </div>
                <div class="timeline-icon">✓</div>
              </div>
            </div>
            
            <!-- Shipment Details -->
            <div class="shipment-details">
              <h3 class="shipment-title">Shipment</h3>
              <div class="shipment-info">
                <div class="shipment-row">
                  <span class="shipment-label">Courier:</span>
                  <span class="shipment-value">Doordash Indonesia</span>
                </div>
                <div class="shipment-row">
                  <span class="shipment-label">Origin:</span>
                  <span class="shipment-value">Surabaya, Lor kidul, East Java, Indonesia</span>
                </div>
                <div class="shipment-row">
                  <span class="shipment-label">Recipient:</span>
                  <span class="shipment-value">Emir</span>
                </div>
                <div class="shipment-row">
                  <span class="shipment-label">Delivery Address:</span>
                  <span class="shipment-value">Malang, East Java, Indonesia</span>
                </div>
                <div class="shipment-row">
                  <span class="shipment-label">Tracking No.:</span>
                  <div class="tracking-number">
                    <span class="shipment-value">871291892812</span>
                    <span class="copy-icon">📋</span>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Items Section -->
            <div class="items-section">
              <h3 class="items-title">Items ${cartItems.length}</h3>
              ${cartItems.length > 0 ? cartItems.map(item => `
                <div class="item-card">
                  <div class="item-image"></div>
                  <div class="item-details">
                    <div class="item-name">${item.name || 'Product'}</div>
                    <div class="item-price">$${(item.price || 0).toFixed(2)} x${item.quantity || 0}</div>
                    <div class="item-size">Size: 24</div>
                  </div>
                </div>
              `).join('') : `
                <div class="item-card">
                  <div class="item-image">👟</div>
                  <div class="item-details">
                    <div class="item-name">Order items processed</div>
                    <div class="item-price">Processing...</div>
                    <div class="item-size">Size: N/A</div>
                  </div>
                </div>
              `}
            </div>
          </div>
          
          <div class="modal-footer">
            <button type="button" class="btn btn-primary" onclick="window.location.href='home.php'">
              Continue Shopping
            </button>
            <button type="button" class="btn btn-secondary" onclick="downloadReceipt()">
              Download Receipt
            </button>
          </div>
        </div>
      </div>
    `;
    
    // Add modal to page
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // Focus trap and accessibility
    const modal = document.getElementById('orderCompletedModal');
    const closeBtn = modal.querySelector('.modal-close');
    
    // Focus management
    closeBtn.focus();
    
    // Keyboard handling
    modal.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        closeOrderModal();
      }
    });
    
    // Click outside to close
    modal.addEventListener('click', (e) => {
      if (e.target === modal) {
        closeOrderModal();
      }
    });
    
    // Announce to screen readers
    const announcement = document.createElement('div');
    announcement.setAttribute('aria-live', 'polite');
    announcement.setAttribute('aria-atomic', 'true');
    announcement.className = 'sr-only';
    announcement.textContent = `Order completed successfully. Order number: ${orderData.order_number}`;
    document.body.appendChild(announcement);
    
    // Remove announcement after a delay
    setTimeout(() => {
      if (announcement.parentNode) {
        announcement.parentNode.removeChild(announcement);
      }
    }, 3000);
  }
}

// Initialize cart
const cart = new Cart();

// Export Cart class and cart instance for use in other files
window.Cart = Cart;
window.cart = cart;

// Cart functions for external use
window.addToCart = function(productId, quantity = 1) {
  const product = window.recommendedProducts ? window.recommendedProducts.find(p => p.id === productId) : null;
  if (product) {
    cart.addItem(product, quantity);
    console.log(`Added ${quantity} x ${product.name} to cart`);
    return true;
  }
  return false;
};

window.updateCartQuantity = function(productId, quantity) {
  return cart.updateQuantity(productId, quantity);
};

window.removeFromCart = function(productId) {
  return cart.removeItem(productId);
};

window.getCartTotal = function() {
  return cart.getTotal();
};

window.getCartCount = function() {
  return cart.getCount();
};

window.clearCart = function() {
  cart.clearCart();
};

window.submitOrder = function() {
  if (cart.getCount() > 0) {
    cart.submitOrder();
  } else {
    alert('Your cart is empty. Please add items before checkout.');
  }
};

// Modal functions
window.closeOrderModal = function() {
  const modal = document.getElementById('orderCompletedModal');
  if (modal) {
    modal.remove();
  }
};

window.printReceipt = function() {
  window.print();
};

window.downloadReceipt = function() {
  // Simple receipt download - could be enhanced with proper PDF generation
  const modal = document.getElementById('orderCompletedModal');
  if (modal) {
    const orderData = {
      orderNumber: modal.querySelector('.info-row .value')?.textContent || 'Unknown',
      date: modal.querySelectorAll('.info-row .value')[1]?.textContent || new Date().toLocaleString(),
      items: Array.from(modal.querySelectorAll('.table-row')).slice(1).map(row => ({
        product: row.querySelector('.product-name')?.textContent || 'Product',
        quantity: row.querySelector('.quantity')?.textContent || '0',
        price: row.querySelector('.price')?.textContent || '$0.00',
        subtotal: row.querySelector('.subtotal')?.textContent || '$0.00'
      })),
      total: modal.querySelector('.total-amount')?.textContent || '$0.00'
    };
    
    const receiptText = `RECEIPT\n\nOrder: ${orderData.orderNumber}\nDate: ${orderData.date}\n\nItems:\n${orderData.items.map(item => `${item.product} x${item.quantity} - ${item.subtotal}`).join('\n')}\n\nTotal: ${orderData.total}`;
    
    const blob = new Blob([receiptText], { type: 'text/plain' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `receipt-${orderData.orderNumber}.txt`;
    a.click();
    URL.revokeObjectURL(url);
  }
};

// Initialize cart icon on page load
document.addEventListener('DOMContentLoaded', function() {
  cart.updateCartIcon();
});
