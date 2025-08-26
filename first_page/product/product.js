// Product Page JavaScript

// Filter state
let currentFilters = {
  category: 'all',
  maxPrice: 5,
  ratings: [3, 4, 5],
  discounts: ['low', 'medium', 'high']
};

// Wait for cart.js to load and initialize
document.addEventListener('DOMContentLoaded', function() {
  console.log('Product page script loaded');
  
  // Initialize cart first
  if (!window.cart) {
    window.cart = new Cart();
  }
  
  // Initialize filters
  initializeFilters();
  
  // Simple call to render all products
  renderProducts();
  
  // Initialize cart icon after products are rendered
  setTimeout(() => {
    if (window.cart) {
      window.cart.updateCartIcon();
      console.log('Cart initialized with items:', window.cart.getItems());
    }
  }, 100);
  
  // Initialize parameters menu functionality
  initializeParamsMenu();
});

// Initialize filter functionality
function initializeFilters() {
  // Category filter buttons
  const categoryButtons = document.querySelectorAll('.filter-category');
  categoryButtons.forEach(button => {
    button.addEventListener('click', function() {
      // Remove active class from all buttons
      categoryButtons.forEach(btn => btn.classList.remove('active'));
      // Add active class to clicked button
      this.classList.add('active');
      
      // Update filter state
      currentFilters.category = this.dataset.category;
      
      // Apply filters
      applyFilters();
    });
  });
  
  // Filter dropdown toggle
  const filterBtn = document.getElementById('filterBtn');
  const filterDropdown = document.getElementById('filterDropdown');
  
  if (filterBtn && filterDropdown) {
    filterBtn.addEventListener('click', function() {
      filterDropdown.classList.toggle('show');
      filterBtn.classList.toggle('active');
    });
  }
  
  // Price range slider
  const priceRange = document.getElementById('priceRange');
  const priceValue = document.getElementById('priceValue');
  
  if (priceRange && priceValue) {
    priceRange.addEventListener('input', function() {
      const value = parseFloat(this.value);
      priceValue.textContent = value.toFixed(2);
      currentFilters.maxPrice = value;
    });
  }
  
  // Rating checkboxes
  const ratingCheckboxes = document.querySelectorAll('.rating-option input');
  ratingCheckboxes.forEach(checkbox => {
    checkbox.addEventListener('change', function() {
      updateRatingFilters();
    });
  });
  
  // Discount checkboxes
  const discountCheckboxes = document.querySelectorAll('.discount-option input');
  discountCheckboxes.forEach(checkbox => {
    checkbox.addEventListener('change', function() {
      updateDiscountFilters();
    });
  });
  
  // Clear filters button
  const clearFiltersBtn = document.getElementById('clearFilters');
  if (clearFiltersBtn) {
    clearFiltersBtn.addEventListener('click', clearAllFilters);
  }
  
  // Apply filters button
  const applyFiltersBtn = document.getElementById('applyFilters');
  if (applyFiltersBtn) {
    applyFiltersBtn.addEventListener('click', function() {
      applyFilters();
      // Close dropdown
      if (filterDropdown) {
        filterDropdown.classList.remove('show');
        if (filterBtn) filterBtn.classList.remove('active');
      }
    });
  }
}

// Update rating filters based on checkboxes
function updateRatingFilters() {
  const ratingCheckboxes = document.querySelectorAll('.rating-option input:checked');
  currentFilters.ratings = Array.from(ratingCheckboxes).map(cb => parseInt(cb.value));
}

// Update discount filters based on checkboxes
function updateDiscountFilters() {
  const discountCheckboxes = document.querySelectorAll('.discount-option input:checked');
  currentFilters.discounts = Array.from(discountCheckboxes).map(cb => cb.value);
}

// Clear all filters
function clearAllFilters() {
  // Reset filter state
  currentFilters = {
    category: 'all',
    maxPrice: 5,
    ratings: [3, 4, 5],
    discounts: ['low', 'medium', 'high']
  };
  
  // Reset UI
  const categoryButtons = document.querySelectorAll('.filter-category');
  categoryButtons.forEach(btn => btn.classList.remove('active'));
  document.querySelector('[data-category="all"]').classList.add('active');
  
  // Reset price range
  const priceRange = document.getElementById('priceRange');
  const priceValue = document.getElementById('priceValue');
  if (priceRange && priceValue) {
    priceRange.value = 5;
    priceValue.textContent = '5.00';
  }
  
  // Reset checkboxes
  const ratingCheckboxes = document.querySelectorAll('.rating-option input');
  const discountCheckboxes = document.querySelectorAll('.discount-option input');
  
  ratingCheckboxes.forEach(cb => cb.checked = true);
  discountCheckboxes.forEach(cb => cb.checked = true);
  
  // Apply filters
  applyFilters();
}

// Apply all filters
function applyFilters() {
  const filteredProducts = recommendedProducts.filter(product => {
    // Category filter
    if (currentFilters.category !== 'all' && product.category !== currentFilters.category) {
      return false;
    }
    
    // Price filter
    if (product.price > currentFilters.maxPrice) {
      return false;
    }
    
    // Rating filter
    if (!currentFilters.ratings.includes(product.rating)) {
      return false;
    }
    
    // Discount filter
    const discountPercent = ((product.originalPrice - product.price) / product.originalPrice) * 100;
    let discountLevel = 'low';
    if (discountPercent >= 30) discountLevel = 'high';
    else if (discountPercent >= 15) discountLevel = 'medium';
    
    if (!currentFilters.discounts.includes(discountLevel)) {
      return false;
    }
    
    return true;
  });
  
  // Render filtered products
  renderFilteredProducts(filteredProducts);
  
  // Update results counter
  updateResultsCounter(filteredProducts.length);
}

// Render filtered products
function renderFilteredProducts(products) {
  const productGrid = document.getElementById('productGrid');
  if (productGrid) {
    const productsHTML = products.map(product => generateProductCard(product)).join('');
    productGrid.innerHTML = productsHTML;
    
    // Initialize quantity selectors for filtered products
    products.forEach(product => {
      initializeQuantitySelector(product.id);
    });
  }
}

// Update results counter
function updateResultsCounter(count) {
  const resultsCounter = document.getElementById('resultsCount');
  if (resultsCounter) {
    const text = count === 1 ? '1 product' : `${count} products`;
    resultsCounter.textContent = text;
  }
}

// Initialize parameters menu
function initializeParamsMenu() {
  const paramsBtn = document.getElementById('paramsBtn');
  const paramsMenu = document.getElementById('paramsMenu');
  
  if (paramsBtn && paramsMenu) {
    paramsBtn.addEventListener('click', function() {
      const isExpanded = paramsBtn.getAttribute('aria-expanded') === 'true';
      
      if (isExpanded) {
        paramsBtn.setAttribute('aria-expanded', 'false');
        paramsMenu.classList.remove('show');
        paramsMenu.setAttribute('aria-hidden', 'true');
      } else {
        paramsBtn.setAttribute('aria-expanded', 'true');
        paramsMenu.classList.add('show');
        paramsMenu.setAttribute('aria-hidden', 'false');
      }
    });
    
    // Close menu when clicking outside
    document.addEventListener('click', function(event) {
      if (!paramsBtn.contains(event.target) && !paramsMenu.contains(event.target)) {
        paramsBtn.setAttribute('aria-expanded', 'false');
        paramsMenu.classList.remove('show');
        paramsMenu.setAttribute('aria-hidden', 'true');
      }
    });
    
    // Close menu on escape key
    document.addEventListener('keydown', function(event) {
      if (event.key === 'Escape') {
        paramsBtn.setAttribute('aria-expanded', 'false');
        paramsMenu.classList.remove('show');
        paramsMenu.setAttribute('aria-hidden', 'true');
      }
    });
  }
}

// Product page specific functions
function showAddToCartSuccess(productName, quantity) {
  // Create a temporary success message
  const successMsg = document.createElement('div');
  successMsg.className = 'add-to-cart-success';
  successMsg.innerHTML = `
    <div class="success-content">
      <span class="success-icon">✅</span>
      <span class="success-text">Added ${quantity} x ${productName} to cart!</span>
    </div>
  `;
  
  // Style the success message
  successMsg.style.cssText = `
    position: fixed;
    top: 20px;
    right: 20px;
    background: #28a745;
    color: white;
    padding: 1rem 1.5rem;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    z-index: 1000;
    animation: slideInRight 0.3s ease-out;
  `;
  
  // Add to page
  document.body.appendChild(successMsg);
  
  // Remove after 3 seconds
  setTimeout(() => {
    if (successMsg.parentNode) {
      successMsg.parentNode.removeChild(successMsg);
    }
  }, 3000);
}

// Add CSS for success message animation
const style = document.createElement('style');
style.textContent = `
  @keyframes slideInRight {
    from {
      transform: translateX(100%);
      opacity: 0;
    }
    to {
      transform: translateX(0);
      opacity: 1;
    }
  }
  
  .add-to-cart-success {
    font-family: Arial, sans-serif;
    font-weight: 500;
  }
  
  .success-content {
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  
  .success-icon {
    font-size: 1.2rem;
  }
  
  .success-text {
    font-size: 0.9rem;
  }
`;
document.head.appendChild(style);

// Export functions for global use
window.showAddToCartSuccess = showAddToCartSuccess;
