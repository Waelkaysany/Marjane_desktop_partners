// Recommended products data with complete HTML structure - Updated to use local images
const recommendedProducts = [
  {
    id: 1,
    name: "COUSCOUS DARI",
    image: "data:image/jpeg;base64,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",
    quantity: "1 piece",
    price: 1,
    originalPrice: 1.55,
    rating: 5,
    description: "MOYEN 1KG",
    tag: "discount",
    tagText: "-33%"
  },
  {
    id: 2,
    name: "PACK SIDI ALI",
    image: "http://almaa.ma/597-large_default/sidi-ali-pack-05l-x12.jpg",
    quantity: "1 pack",
    price: 1.95,
    originalPrice: 2.25,
    rating: 5,
    description: "33CL x 12",
    tag: "discount",
    tagText: "-17%"
  },
  {
    id: 3,
    name: "TONIK CLASSIC",
    image: "https://bsahashop.com/cdn/shop/files/D7432352-57D8-40C9-94EE-E49BEE66B52B.jpg?v=1711812555&width=2048",
    quantity: "1 piece",
    price: 1.55,
    originalPrice: 1.95,
    rating: 0,
    description: "PACK GAUFRETTES",
    tag: "discount",
    tagText: "-34%"
  },
  {
    id: 4,
    name: "DALAA WOOLY",
    image: "https://cdn.youcan.shop/stores/2ab36242f98b84d05de34a2100837952/products/KL6gsod7m1oXeiinGDdqAK6kgVPjpRMT6USy25K9_lg.jpeg",
    quantity: "1 serving",
    price: 0.90,
    originalPrice: 1.20,
    rating: 0,
    description: "SACHET DE MOUCHOIRS",
    tag: "discount",
    tagText: "-43%"
  },
  {
    id: 5,
    name: "LESIEUR",
    image: "https://storage.googleapis.com/sales-img-ma-live/web/cache/sylius_shop_product_original/4c/84/ece24a4167a9b3568856530454f5.jpg",
    quantity: "1 bunch",
    price: 1.49,
    originalPrice: 2.99,
    rating: 4,
    description: "HUILE 5L",
    tag: "discount",
    tagText: "-50%"
  },
  {
    id: 6,
    name: "PACK PROMO SALIM",
    image: "https://cdn.youcan.shop/stores/2ab36242f98b84d05de34a2100837952/products/ixaYpLVJ5MTpbrJArMBZ9MmgzWDLqjVKfohdaBOR_lg.jpeg",
    quantity: "1 cup",
    price: 3,
    originalPrice: 3.60,
    rating: 5,
    description: "LAIT UHT 1/2L x 6",
    tag: "discount",
    tagText: "-33%"
  },
  {
    id: 7,
    name: "THON JOLY",
    image: "https://liya.ma/wp-content/uploads/2024/04/Design-sans-titre-165.png",
    quantity: "1 jar",
    price: 3.1,
    originalPrice: 3.50,
    rating: 4,
    description: "3 x 85GR",
    tag: "discount",
    tagText: "-25%"
  },
  {
    id: 8,
    name: "COCA COLA",
    image: "http://almaa.ma/394-large_default/Coca-cola-1L.jpg",
    quantity: "1 bowl",
    price: 0.55,
    originalPrice: 0.75,
    rating: 5,
    description: "GOUT ORIGINAL 1L",
    tag: "discount",
    tagText: "-31%"
  },
  {
    id: 9,
    name: "",
    image: "https://images.unsplash.com/photo-1541519227354-08fa5d50c44d?w=400&h=400&fit=crop&crop=center",
    quantity: "1 slice",
    price: 7.99,
    originalPrice: 7.5,
    rating: 4,
    description: "Healthy fats",
    tag: "discount",
    tagText: "-33%"
  },
  {
    id: 10,
    name: "GHAYT",
    image: "https://ghayt.ma/boutique//images/banner-extra-large-bottle.png.webp",
    quantity: "1 bowl",
    price:1,
    originalPrice: 1.3,
    rating: 5,
    description: "EAU DE TABLE 1L",
    tag: "discount",
    tagText: "-30%"
  },
  {
    id: 11,
    name: "BAROUD",
    image: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcT2pyVESkolbcKUQ3slggcSARB26Yjlq6uh0Q&s",
    quantity: "1 pack",
    price: 1.9,
    originalPrice: 2.2,
    rating: 4,
    description: "THE VERT BAROUD 200G",
    tag: "discount",
    tagText: "-30%"
  },
  {
    id: 12,
    name: "JAVEL ACE",
    image: "https://www.mymarket.ma/cdn/shop/products/8121FF98-996B-494F-AFB5-0527285564BC.png?v=1657019305&width=375",
    quantity: "1 pack",
    price: 1.3,
    originalPrice: 1.7,
    rating: 5,
    description: "LEMON 2.5L",
    tag: "discount",
    tagText: "-36%"
  },
  {
    id: 13,
    name: "LARENA",
    image: "https://storage.googleapis.com/sales-img-ma-live/web/cache/sylius_large/ea/ce/b2cbca2d13ac159c5faba8c1be26.jpg",
    quantity: "1 tub",
    price: 2.19,
    originalPrice: 2.55,
    rating: 4,
    description: "PREP SUCRE+GLUCOSE 1KG",
    tag: "discount",
    tagText: "-33%"
  },
  {
    id: 14,
    name: "EL BARAKA",
    image: "https://storage.googleapis.com/sales-img-ma-live/web/cache/sylius_large/48/60/fa6ec1e737345f12223a8b31e30f.jpg",
    quantity: "1 can",
    price: 1.3,
    originalPrice: 1.7,
    rating: 3,
    description: "CONFITURE d'ORANGE 37CL",
    tag: "discount",
    tagText: "-33%"
  },
  {
    id: 15,
    name: "AL ITKANE",
    image: "https://api.allonaya.ma/assets/files/Media/aeT6x2BSRqvxKDkrH/thumbnail/1639472092.png",
    quantity: "1 pack",
    price: 5,
    originalPrice: 5.50,
    rating: 5,
    description: "FINOT DE BLE DUR 5KG",
    tag: "discount",
    tagText: "-20%"
  },
  {
    id: 16,
    name: "PRESS'UP ORANGE",
    image: "https://api.allonaya.ma/assets/files/Media/yYTDsP7HNMvP7PAgm/thumbnail/48.png",
    quantity: "1 box",
    price: 0.45,
    originalPrice: 0.6,
    rating: 4,
    description: "NECTAR FRAIS D'ORANGE",
    tag: "discount",
    tagText: "-33%"
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
  
  // Add to cart using the cart system - works with both recommendation and all products
  if (window.cart) {
    // First try to find in recommended products
    let product = recommendedProducts.find(p => p.id === productId);
    
    // If not found in recommended products, try all products
    if (!product && window.getProductById) {
      product = window.getProductById(productId);
    }
    
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
      if (window.showAddToCartSuccess) {
        window.showAddToCartSuccess(product.name, count);
      } else {
        showNotification(`Added ${count} item(s) to cart!`);
      }
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


