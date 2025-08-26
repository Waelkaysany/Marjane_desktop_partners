// Recommended products data with complete HTML structure - Updated to use local images
const recommendedProducts = [
  {
    id: 1,
    name: "COUSCOUS DARI",
    image: "../img.video/our.recomdation.png",
    quantity: "1 piece",
    price: 1,
    originalPrice: 1.55,
    rating: 5,
    description: "MOYEN 1KG",
    tag: "discount",
    tagText: "-33%",
    category: "food"
  },
  {
    id: 2,
    name: "PACK SIDI ALI",
    image: "../img.video/raf-kbir.png",
    quantity: "1 pack",
    price: 1.95,
    originalPrice: 2.25,
    rating: 5,
    description: "33CL x 12",
    tag: "discount",
    tagText: "-17%",
    category: "beverages"
  },
  {
    id: 3,
    name: "TONIK CLASSIC",
    image: "../img.video/talaja.png",
    quantity: "1 piece",
    price: 1.55,
    originalPrice: 1.95,
    rating: 4,
    description: "PACK GAUFRETTES",
    tag: "discount",
    tagText: "-21%",
    category: "food"
  },
  {
    id: 4,
    name: "DALAA WOOLY",
    image: "../img.video/talaja (2).png",
    quantity: "1 serving",
    price: 0.90,
    originalPrice: 1.20,
    rating: 3,
    description: "SACHET DE MOUCHOIRS",
    tag: "discount",
    tagText: "-25%",
    category: "household"
  },
  {
    id: 5,
    name: "LESIEUR",
    image: "../img.video/awakhir.png",
    quantity: "1 bunch",
    price: 1.49,
    originalPrice: 2.99,
    rating: 4,
    description: "HUILE 5L",
    tag: "discount",
    tagText: "-50%",
    category: "food"
  },
  {
    id: 6,
    name: "PACK PROMO SALIM",
    image: "../img.video/image-removebg-preview (3).png",
    quantity: "1 cup",
    price: 3,
    originalPrice: 3.60,
    rating: 5,
    description: "LAIT UHT 1/2L x 6",
    tag: "discount",
    tagText: "-17%",
    category: "beverages"
  },
  {
    id: 7,
    name: "THON JOLY",
    image: "../img.video/Capture.PNG",
    quantity: "1 jar",
    price: 3.1,
    originalPrice: 3.50,
    rating: 4,
    description: "3 x 85GR",
    tag: "discount",
    tagText: "-11%",
    category: "food"
  },
  {
    id: 8,
    name: "COCA COLA",
    image: "../img.video/a4a11e4c-0af0-4601-ae4c-072c6cae0ab2.png",
    quantity: "1 bottle",
    price: 0.55,
    originalPrice: 0.75,
    rating: 5,
    description: "GOUT ORIGINAL 1L",
    tag: "discount",
    tagText: "-27%",
    category: "beverages"
  },
  {
    id: 9,
    name: "SOAP LUX",
    image: "../img.video/our.recomdation.png",
    quantity: "1 pack",
    price: 2.25,
    originalPrice: 2.75,
    rating: 4,
    description: "PREMIUM SOAP",
    tag: "discount",
    tagText: "-18%",
    category: "personal"
  },
  {
    id: 10,
    name: "SHAMPOO HEAD",
    image: "../img.video/raf-kbir.png",
    quantity: "1 piece",
    price: 1.75,
    originalPrice: 2.10,
    rating: 3,
    description: "ORGANIC SHAMPOO",
    tag: "discount",
    tagText: "-17%",
    category: "personal"
  },
  {
    id: 11,
    name: "CLEANING KIT",
    image: "../img.video/talaja.png",
    quantity: "1 set",
    price: 4.50,
    originalPrice: 5.25,
    rating: 5,
    description: "COMPLETE CLEANING SET",
    tag: "discount",
    tagText: "-14%",
    category: "household"
  },
  {
    id: 12,
    name: "DISH SOAP",
    image: "../img.video/talaja (2).png",
    quantity: "1 box",
    price: 2.80,
    originalPrice: 3.20,
    rating: 4,
    description: "FRESH DISH SOAP",
    tag: "discount",
    tagText: "-13%",
    category: "household"
  },
  {
    id: 13,
    name: "TOOTHPASTE",
    image: "../img.video/awakhir.png",
    quantity: "1 bag",
    price: 1.20,
    originalPrice: 1.45,
    rating: 3,
    description: "NATURAL TOOTHPASTE",
    tag: "discount",
    tagText: "-17%",
    category: "personal"
  },
  {
    id: 14,
    name: "CANNED TUNA",
    image: "../img.video/image-removebg-preview (3).png",
    quantity: "1 can",
    price: 0.85,
    originalPrice: 1.05,
    rating: 4,
    description: "PREMIUM CANNED TUNA",
    tag: "discount",
    tagText: "-19%",
    category: "food"
  },
  {
    id: 15,
    name: "ENERGY DRINK",
    image: "../img.video/Capture.PNG",
    quantity: "1 bottle",
    price: 1.95,
    originalPrice: 2.30,
    rating: 5,
    description: "ORGANIC ENERGY DRINK",
    tag: "discount",
    tagText: "-15%",
    category: "beverages"
  },
  {
    id: 16,
    name: "LAUNDRY DETERGENT",
    image: "../img.video/a4a11e4c-0af0-4601-ae4c-072c6cae0ab2.png",
    quantity: "1 pack",
    price: 3.75,
    originalPrice: 4.50,
    rating: 4,
    description: "DELUXE LAUNDRY DETERGENT",
    tag: "discount",
    tagText: "-17%",
    category: "household"
  }
];

// Make recommendedProducts available globally
window.recommendedProducts = recommendedProducts;

// Function to generate star rating HTML
function generateStars(rating) {
  let starsHTML = '';
  
  if (rating === 0) {
    // No rating - empty stars
    for (let i = 0; i < 5; i++) {
      starsHTML += '<span class="star empty">☆</span>';
    }
    return { stars: starsHTML, text: "No reviews" };
  } else {
    // Has rating - filled stars
    for (let i = 0; i < 5; i++) {
      starsHTML += '<span class="star filled">★</span>';
    }
    return { stars: starsHTML, text: `(${rating})` };
  }
}

// Function to generate complete product card HTML
function generateProductCard(product) {
  const [major, minor] = Number(product.price).toFixed(2).split('.');
  const ratingData = generateStars(product.rating);
  const pricePrefix = product.pricePrefix || '';
  
  return `
    <div class="product-card" data-product-id="${product.id}">
      <div class="product-image">
        <img src="${product.image}" alt="${product.name}">
        ${product.tag ? `<div class="product-tag ${product.tag}">${product.tagText}</div>` : ''}
      </div>
      <div class="product-info">
        <div class="product-price">
          <span class="current-price">${pricePrefix}$${major}.${minor}</span>
          ${product.originalPrice ? `<span class="original-price">$${product.originalPrice.toFixed(2)}</span>` : ''}
        </div>
        <h3 class="product-name">${product.name}</h3>
        <div class="product-rating">
          <div class="stars">
            ${ratingData.stars}
          </div>
          <span class="rating-text">${ratingData.text}</span>
        </div>
        <p class="product-description">${product.description}</p>
        <div class="action-container">
          <div class="quantity-selector" id="qty-${product.id}">
            <div class="qty-controls">
              <button class="qty-btn minus" onclick="decreaseQuantity(${product.id})" title="Decrease quantity">−</button>
              <span class="qty-count" id="count-${product.id}">1</span>
              <button class="qty-btn plus" onclick="increaseQuantity(${product.id})" title="Increase quantity">+</button>
              <button class="qty-add-btn" onclick="addToCartFromSelector(${product.id})" title="Add to cart">Add</button>
            </div>
            <button class="buy-now-btn" onclick="showQuantityControls(${product.id})" title="Buy now">
              Buy now
            </button>
          </div>
        </div>
      </div>
    </div>
  `;
}

// Function to render all products
function renderProducts() {
  const productGrid = document.getElementById('productGrid');
  if (productGrid) {
    const productsHTML = recommendedProducts.map(product => generateProductCard(product)).join('');
    productGrid.innerHTML = productsHTML;
    
    // Initialize quantity selectors for all products
    recommendedProducts.forEach(product => {
      initializeQuantitySelector(product.id);
    });
  }
}

// Quantity selector functions
function initializeQuantitySelector(productId) {
  const selector = document.getElementById(`qty-${productId}`);
  
  if (!selector) {
    console.error('Quantity selector not found for product:', productId);
    return;
  }
  
  // Initialize quantity to 1
  const countEl = document.getElementById(`count-${productId}`);
  if (countEl) {
    countEl.textContent = '1';
  }
}

function increaseQuantity(productId) {
  const countEl = document.getElementById(`count-${productId}`);
  if (!countEl) {
    console.error('Count element not found for product:', productId);
    return;
  }
  
  let count = parseInt(countEl.textContent);
  countEl.textContent = count + 1;
}

function decreaseQuantity(productId) {
  const countEl = document.getElementById(`count-${productId}`);
  if (!countEl) {
    console.error('Count element not found for product:', productId);
    return;
  }
  
  let count = parseInt(countEl.textContent);
  
  if (count > 1) {
    countEl.textContent = count - 1;
  }
}

function resetQuantity(productId) {
  const countEl = document.getElementById(`count-${productId}`);
  
  if (countEl) {
    countEl.textContent = '1';
  }
}

function showQuantityControls(productId) {
  const selector = document.getElementById(`qty-${productId}`);
  if (selector) {
    selector.classList.add('active');
  }
}

function addToCartFromSelector(productId) {
  const countEl = document.getElementById(`count-${productId}`);
  if (!countEl) {
    console.error('Count element not found for product:', productId);
    return;
  }
  
  const count = parseInt(countEl.textContent);
  
  // Add to cart using the cart system
  if (window.cart) {
    const product = recommendedProducts.find(p => p.id === productId);
    if (product) {
      window.cart.addItem(product, count);
      console.log(`Added ${count} x ${product.name} to cart`);
      
      // Reset quantity to 1
      resetQuantity(productId);
      
      // Hide quantity controls and show add to cart button again
      const selector = document.getElementById(`qty-${productId}`);
      if (selector) {
        selector.classList.remove('active');
      }
      
      // Show success message
      showNotification(`Added ${count} item(s) to cart!`);
    } else {
      console.error('Product not found:', productId);
    }
  } else {
    console.error('Cart not initialized');
  }
}

function showNotification(message) {
  // Create notification element
  const notification = document.createElement('div');
  notification.className = 'notification';
  notification.textContent = message;
  notification.style.cssText = `
    position: fixed;
    top: 20px;
    right: 20px;
    background: #28a745;
    color: white;
    padding: 12px 20px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    z-index: 1000;
    font-family: 'Inter', sans-serif;
    font-weight: 500;
    transform: translateX(100%);
    transition: transform 0.3s ease;
  `;
  
  document.body.appendChild(notification);
  
  // Animate in
  setTimeout(() => {
    notification.style.transform = 'translateX(0)';
  }, 100);
  
  // Remove after 3 seconds
  setTimeout(() => {
    notification.style.transform = 'translateX(100%)';
    setTimeout(() => {
      document.body.removeChild(notification);
    }, 300);
  }, 3000);
}


