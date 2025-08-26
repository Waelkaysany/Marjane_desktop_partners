// All Products Data - Additional products for the product page
const allProducts = [
  // Fresh Produce Category
  {
    id: 101,
    name: "FRESH TOMATOES",
    image: "https://images.unsplash.com/photo-1546094096-0df4bcaaa337?w=400&h=400&fit=crop&crop=center",
    quantity: "1 kg",
    price: 2.5,
    originalPrice: 3.2,
    rating: 5,
    description: "ORGANIC RED TOMATOES",
    tag: "discount",
    tagText: "-22%",
    category: "fresh-produce"
  },
  {
    id: 102,
    name: "BANANAS",
    image: "https://images.unsplash.com/photo-1571771894821-ce9b6c11b08e?w=400&h=400&fit=crop&crop=center",
    quantity: "1 kg",
    price: 1.8,
    originalPrice: 2.4,
    rating: 4,
    description: "SWEET YELLOW BANANAS",
    tag: "discount",
    tagText: "-25%",
    category: "fresh-produce"
  },
  {
    id: 103,
    name: "FRESH APPLES",
    image: "https://images.unsplash.com/photo-1560806887-1e4cd0b6cbd6?w=400&h=400&fit=crop&crop=center",
    quantity: "1 kg",
    price: 3.2,
    originalPrice: 4.0,
    rating: 5,
    description: "CRISP RED APPLES",
    tag: "discount",
    tagText: "-20%",
    category: "fresh-produce"
  },
  {
    id: 104,
    name: "FRESH CARROTS",
    image: "https://images.unsplash.com/photo-1447175008436-170170753d52?w=400&h=400&fit=crop&crop=center",
    quantity: "1 kg",
    price: 1.5,
    originalPrice: 2.0,
    rating: 4,
    description: "ORANGE CARROTS",
    tag: "discount",
    tagText: "-25%",
    category: "fresh-produce"
  },
  {
    id: 105,
    name: "FRESH LETTUCE",
    image: "https://images.unsplash.com/photo-1622205313162-be1d5716a43e?w=400&h=400&fit=crop&crop=center",
    quantity: "1 piece",
    price: 1.2,
    originalPrice: 1.6,
    rating: 4,
    description: "CRISP GREEN LETTUCE",
    tag: "discount",
    tagText: "-25%",
    category: "fresh-produce"
  },
  {
    id: 106,
    name: "FRESH ONIONS",
    image: "https://images.unsplash.com/photo-1518977676601-b53f82aba655?w=400&h=400&fit=crop&crop=center",
    quantity: "1 kg",
    price: 1.8,
    originalPrice: 2.3,
    rating: 4,
    description: "WHITE ONIONS",
    tag: "discount",
    tagText: "-22%",
    category: "fresh-produce"
  },

  // Beverages Category
  {
    id: 201,
    name: "ORANGE JUICE",
    image: "https://images.unsplash.com/photo-1621506289937-a8e4df240d0b?w=400&h=400&fit=crop&crop=center",
    quantity: "1 liter",
    price: 2.8,
    originalPrice: 3.5,
    rating: 5,
    description: "100% NATURAL JUICE",
    tag: "discount",
    tagText: "-20%",
    category: "beverages"
  },
  {
    id: 202,
    name: "GREEN TEA",
    image: "https://images.unsplash.com/photo-1556682851c0c6a83c8c2b8b3?w=400&h=400&fit=crop&crop=center",
    quantity: "20 bags",
    price: 3.5,
    originalPrice: 4.2,
    rating: 4,
    description: "ANTIOXIDANT RICH TEA",
    tag: "discount",
    tagText: "-17%",
    category: "beverages"
  },
  {
    id: 203,
    name: "LEMONADE",
    image: "https://images.unsplash.com/photo-1621263764928-df1444c5e859?w=400&h=400&fit=crop&crop=center",
    quantity: "1 liter",
    price: 2.2,
    originalPrice: 2.8,
    rating: 4,
    description: "REFRESHING LEMONADE",
    tag: "discount",
    tagText: "-21%",
    category: "beverages"
  },
  {
    id: 204,
    name: "COFFEE BEANS",
    image: "https://images.unsplash.com/photo-1559056199-641a0ac8b55e?w=400&h=400&fit=crop&crop=center",
    quantity: "500g",
    price: 8.5,
    originalPrice: 10.0,
    rating: 5,
    description: "PREMIUM ARABICA BEANS",
    tag: "discount",
    tagText: "-15%",
    category: "beverages"
  },
  {
    id: 205,
    name: "MINERAL WATER",
    image: "https://images.unsplash.com/photo-1559827260-dc66d52bef19?w=400&h=400&fit=crop&crop=center",
    quantity: "6 bottles",
    price: 2.5,
    originalPrice: 3.0,
    rating: 4,
    description: "NATURAL MINERAL WATER",
    tag: "discount",
    tagText: "-17%",
    category: "beverages"
  },

  // Meat & Seafood Category
  {
    id: 301,
    name: "FRESH CHICKEN",
    image: "https://images.unsplash.com/photo-1604503468506-a8da13d82791?w=400&h=400&fit=crop&crop=center",
    quantity: "1 kg",
    price: 6.5,
    originalPrice: 7.8,
    rating: 5,
    description: "PREMIUM CHICKEN BREAST",
    tag: "discount",
    tagText: "-17%",
    category: "meat-seafood"
  },
  {
    id: 302,
    name: "SALMON FILLET",
    image: "https://images.unsplash.com/photo-1519708227418-c8fd9a32b7a2?w=400&h=400&fit=crop&crop=center",
    quantity: "500g",
    price: 12.5,
    originalPrice: 15.0,
    rating: 5,
    description: "FRESH ATLANTIC SALMON",
    tag: "discount",
    tagText: "-17%",
    category: "meat-seafood"
  },
  {
    id: 303,
    name: "BEEF STEAK",
    image: "https://images.unsplash.com/photo-1546833999-b9f581a1996d?w=400&h=400&fit=crop&crop=center",
    quantity: "300g",
    price: 9.8,
    originalPrice: 11.5,
    rating: 5,
    description: "PREMIUM BEEF STEAK",
    tag: "discount",
    tagText: "-15%",
    category: "meat-seafood"
  },
  {
    id: 304,
    name: "LAMB CHOPS",
    image: "https://images.unsplash.com/photo-1607623814075-e51df1bdc82f?w=400&h=400&fit=crop&crop=center",
    quantity: "400g",
    price: 11.2,
    originalPrice: 13.5,
    rating: 4,
    description: "FRESH LAMB CHOPS",
    tag: "discount",
    tagText: "-17%",
    category: "meat-seafood"
  },
  {
    id: 305,
    name: "TUNA FISH",
    image: "https://images.unsplash.com/photo-1559827260-dc66d52bef19?w=400&h=400&fit=crop&crop=center",
    quantity: "250g",
    price: 4.8,
    originalPrice: 5.5,
    rating: 4,
    description: "FRESH TUNA FILLET",
    tag: "discount",
    tagText: "-13%",
    category: "meat-seafood"
  },

  // Dairy Category
  {
    id: 401,
    name: "FRESH MILK",
    image: "https://images.unsplash.com/photo-1550583724-b2692b85b150?w=400&h=400&fit=crop&crop=center",
    quantity: "1 liter",
    price: 3.2,
    originalPrice: 3.8,
    rating: 5,
    description: "ORGANIC FRESH MILK",
    tag: "discount",
    tagText: "-16%",
    category: "dairy"
  },
  {
    id: 402,
    name: "GREEK YOGURT",
    image: "https://images.unsplash.com/photo-1488477181946-6428a0291777?w=400&h=400&fit=crop&crop=center",
    quantity: "500g",
    price: 4.5,
    originalPrice: 5.2,
    rating: 5,
    description: "CREAMY GREEK YOGURT",
    tag: "discount",
    tagText: "-13%",
    category: "dairy"
  },
  {
    id: 403,
    name: "CHEDDAR CHEESE",
    image: "https://images.unsplash.com/photo-1486297678162-eb2a19b0a32d?w=400&h=400&fit=crop&crop=center",
    quantity: "200g",
    price: 3.8,
    originalPrice: 4.5,
    rating: 4,
    description: "AGED CHEDDAR CHEESE",
    tag: "discount",
    tagText: "-16%",
    category: "dairy"
  },
  {
    id: 404,
    name: "ORGANIC EGGS",
    image: "https://images.unsplash.com/photo-1506976785307-8732e854ad03?w=400&h=400&fit=crop&crop=center",
    quantity: "12 pieces",
    price: 5.2,
    originalPrice: 6.0,
    rating: 5,
    description: "FARM FRESH EGGS",
    tag: "discount",
    tagText: "-13%",
    category: "dairy"
  },
  {
    id: 405,
    name: "BUTTER",
    image: "https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=400&h=400&fit=crop&crop=center",
    quantity: "250g",
    price: 2.8,
    originalPrice: 3.3,
    rating: 4,
    description: "PURE BUTTER",
    tag: "discount",
    tagText: "-15%",
    category: "dairy"
  },
  {
    id: 406,
    name: "COTTAGE CHEESE",
    image: "https://images.unsplash.com/photo-1486297678162-eb2a19b0a32d?w=400&h=400&fit=crop&crop=center",
    quantity: "300g",
    price: 3.5,
    originalPrice: 4.1,
    rating: 4,
    description: "LOW FAT COTTAGE CHEESE",
    tag: "discount",
    tagText: "-15%",
    category: "dairy"
  }
];

// Make allProducts available globally
window.allProducts = allProducts;

// Function to combine recommended products with all products
function getAllProducts() {
  // Combine recommended products with all products, avoiding duplicates by ID
  const combinedProducts = [...recommendedProducts];
  
  allProducts.forEach(product => {
    // Check if product with this ID already exists
    const existingProduct = combinedProducts.find(p => p.id === product.id);
    if (!existingProduct) {
      combinedProducts.push(product);
    }
  });
  
  return combinedProducts;
}

// Function to get products by category
function getProductsByCategory(category) {
  if (category === 'all') {
    return getAllProducts();
  }
  
  const allProductsList = getAllProducts();
  return allProductsList.filter(product => product.category === category);
}

// Function to search products
function searchProducts(query) {
  const allProductsList = getAllProducts();
  const searchTerm = query.toLowerCase();
  
  return allProductsList.filter(product => {
    return product.name.toLowerCase().includes(searchTerm) ||
           product.description.toLowerCase().includes(searchTerm) ||
           product.category.toLowerCase().includes(searchTerm);
  });
}

// Cart functionality for all products
function addToCartFromAllProducts(productId, quantity = 1) {
  const allProductsList = getAllProducts();
  const product = allProductsList.find(p => p.id === productId);
  
  if (product && window.cart) {
    window.cart.addItem(product, quantity);
    console.log(`Added ${quantity} x ${product.name} to cart from all products`);
    
    // Show success message
    if (window.showAddToCartSuccess) {
      window.showAddToCartSuccess(product.name, quantity);
    }
    
    // Update cart icon
    if (window.cart.updateCartIcon) {
      window.cart.updateCartIcon();
    }
    
    return true;
  }
  
  console.error('Product not found or cart not initialized:', productId);
  return false;
}

// Function to get product by ID from all products
function getProductById(productId) {
  const allProductsList = getAllProducts();
  return allProductsList.find(p => p.id === productId);
}

// Export functions for global use
window.getAllProducts = getAllProducts;
window.getProductsByCategory = getProductsByCategory;
window.searchProducts = searchProducts;
window.addToCartFromAllProducts = addToCartFromAllProducts;
window.getProductById = getProductById;
