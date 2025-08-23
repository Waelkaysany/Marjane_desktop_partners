// Cart System - Complete implementation with localStorage persistence

class Cart {
  constructor() {
    this.items = this.loadFromStorage();
    this.updateCartIcon();
  }

  // Load cart from localStorage
  loadFromStorage() {
    const savedCart = localStorage.getItem('shoppingCart');
    return savedCart ? JSON.parse(savedCart) : [];
  }

  // Save cart to localStorage
  saveToStorage() {
    localStorage.setItem('shoppingCart', JSON.stringify(this.items));
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
    return this.items.reduce((total, item) => total + (item.price * item.quantity), 0);
  }

  // Get cart count
  getCount() {
    return this.items.reduce((count, item) => count + item.quantity, 0);
  }

  // Clear cart
  clearCart() {
    this.items = [];
    this.saveToStorage();
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

// Initialize cart icon on page load
document.addEventListener('DOMContentLoaded', function() {
  cart.updateCartIcon();
});
