// All Products Data - Additional products for the product page
const allProducts = [
  // Fresh Produce Category
  {
    id: 101,
    name: "FRESH TOMATOES",
    image: "https://res.cloudinary.com/dcphm6bor/image/upload/q_75,f_auto,w_1920/v1751382220/product/a820bb11f69fe199bdf9fd131b9ca362.webp",
    quantity: "1 kg",
    price: 0.25,
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
    image: "https://www.bobtailfruit.co.uk/media/mageplaza/blog/post/4/2/42e9as7nataai4a6jcufwg.jpeg",
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
    image: "https://www.makaro.id/wp-content/uploads/2020/03/wangsan-fuji.jpg",
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
    image: "https://res.cloudinary.com/dcphm6bor/image/upload/q_75,f_auto,w_1920/v1751384253/product/b28ff07f7228de6c76050425bf4ebef9.webp",
    quantity: "1 kg",
    price: 0.99,
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
    image: "https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRrxnMoGZzf9PHTtXoNWmLmlXziXT_U77O47g&s",
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
    image: "https://cdn.mafrservices.com/sys-master-root/h69/h59/12922526498846/16856_main.jpg?im=Resize=1700",
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
    image: "https://www.mymarket.ma/cdn/shop/products/43.jpg?v=1654260744",
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
    image: "https://www.mymarket.ma/cdn/shop/products/THE-A-LA-MENTHE-MAROC-12.5_1024x1024_6d4e84d1-1430-4bcf-af69-1c7806ea9547.png?v=1680875854&width=375",
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
    name: "MINERAL WATER",
    image: "https://api.allonaya.ma/assets/files/Media/TQduLsNHmThirPPec/large/Sidi-Ali-pack-2L.jpg",
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
    name: "COFFEE ",
    image: "https://www.mymarket.ma/cdn/shop/products/10279.jpg?v=1590786040",
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
    name: "PEPSI MAX",
    image: "https://entreprise.warakpro.ma/2338-large_default/pepsi-max-sans-sucre-1l-paquet-de-6.jpg",
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
    image: "https://rawabihypermarket.com/uploads/product_images/featured_image/927611.jpg",
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
    name: "FISH PACK ",
    image: "https://miro.medium.com/1*Sbt7ndbX76ul1ZDB9uCChg.jpeg",
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
    image: "https://www.telegraph.co.uk/content/dam/food-and-drink/2024/02/14/TELEMMGLPICT000366482974_17079159370030_trans_NvBQzQNjv4BqqVzuuqpFlyLIwiB6NTmJwfSVWeZ_vEN7c6bHu2jJnT8.jpeg?imwidth=350",
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
    image: "https://groceries.morrisons.com/images-v3/4b85987b-1398-4173-a0c1-3546047c9d74/e676e902-b056-4dcd-b86f-e18ea4605422/300x300.jpg",
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
    image: "https://api.allonaya.ma/assets/files/Media/7LTSPtDFe3T397erS/image/Thon-entier-a-lhuile-vegetale-3x-80g-MARIO.jpg",
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
    image: "https://api.allonaya.ma/assets/files/Media/6ejCRRb8whEmJKxsj/large/jgfufufgu.jpg",
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
    image: "https://www.mymarket.ma/cdn/shop/products/8410128121006.png?v=1647958151",
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
    image: "https://www.mymarket.ma/cdn/shop/products/b70m.png?v=1654510369",
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
    image: "https://cdn.mafrservices.com/pim-content/UAE/media/product/2214048/1744952403/2214048_main.jpg",
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
    image: "https://e-xportmorocco.com/storage/produits/thumbnail-1645523506.png",
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
    image: "https://cdn.dsmcdn.com/mnresize/1200/1800/ty572/product/media/images/20221019/10/197041555/360533077/1/1_org_zoom.jpg",
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
