<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/session.php';
require_auth();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style.css">
  <link rel="stylesheet" href="equipment.css">
  <title>Equipment - Zero-Loss Business Continuity</title>
</head>
<body>
  <nav>
    <div class="logo">
      <p>MARJANE</p>
    </div>
    <div class="menu">
      <ul>
        <li><a href="../home.php">PROMOTIONS</a></li>
        <li><a href="../product/">PRODUCTS</a></li>
        <li><a href="equipment.php">EQUIPMENTS</a></li>
      </ul>
    </div>
    <div class="right-side">
      <a href="../ourplan/ourplan.php" class="button" style="text-decoration: none; display: inline-flex; align-items: center;">
        <span class="text">Discover</span>
        <span class="svg">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            width="50"
            height="20"
            viewBox="0 0 38 15"
            fill="none"
          >
            <path
              fill="white"
              d="M10 7.519l-.939-.344h0l.939.344zm14.386-1.205l-.981-.192.981.192zm1.276 5.509l.537.843.148-.094.107-.139-.792-.611zm4.819-4.304l-.385-.923h0l.385.923zm7.227.707a1 1 0 0 0 0-1.414L31.343.448a1 1 0 0 0-1.414 0 1 1 0 0 0 0 1.414l5.657 5.657-5.657 5.657a1 1 0 0 0 1.414 1.414l6.364-6.364zM1 7.519l.554.833.029-.019.094-.061.361-.23 1.277-.77c1.054-.609 2.397-1.32 3.629-1.787.617-.234 1.17-.392 1.623-.455.477-.066.707-.008.788.034.025.013.031.021.039.034a.56.56 0 0 1 .058.235c.029.327-.047.906-.39 1.842l1.878.689c.383-1.044.571-1.949.505-2.705-.072-.815-.45-1.493-1.16-1.865-.627-.329-1.358-.332-1.993-.244-.659.092-1.367.305-2.056.566-1.381.523-2.833 1.297-3.921 1.925l-1.341.808-.385.245-.104.068-.028.018c-.011.007-.011.007.543.84zm8.061-.344c-.198.54-.328 1.038-.36 1.484-.032.441.024.94.325 1.364.319.45.786.64 1.21.697.403.054.824-.001 1.21-.09.775-.179 1.694-.566 2.633-1.014l3.023-1.554c2.115-1.122 4.107-2.168 5.476-2.524.329-.086.573-.117.742-.115s.195.038.161.014c-.15-.105.085-.139-.076.685l1.963.384c.192-.98.152-2.083-.74-2.707-.405-.283-.868-.37-1.28-.376s-.849.069-1.274.179c-1.65.43-3.888 1.621-5.909 2.693l-2.948 1.517c-.92.439-1.673.743-2.221.87-.276.064-.429.065-.492.057-.043-.006.066.003.155.127.07.099.024.131.038-.063.014-.187.078-.49.243-.94l-1.878-.689zm14.343-1.053c-.361 1.844-.474 3.185-.413 4.161.059.95.294 1.72.811 2.215.567.544 1.242.546 1.664.459a2.34 2.34 0 0 0 .502-.167l.15-.076.049-.028.018-.011c.013-.008.013-.008-.524-.852l-.536-.844.019-.012c-.038.018-.064.027-.084.032-.037.008.053-.013.125.056.021.02-.151-.135-.198-.895-.046-.734.034-1.887.38-3.652l-1.963-.384zm2.257 5.701l.791.611.024-.031.08-.101.311-.377 1.093-1.213c.922-.954 2.005-1.894 2.904-2.27l-.771-1.846c-1.31.547-2.637 1.758-3.572 2.725l-1.184 1.314-.341.414-.093.117-.025.032c-.01.013-.01.013.781.624zm5.204-3.381c.989-.413 1.791-.42 2.697-.307.871.108 2.083.385 3.437.385v-2c-1.197 0-2.041-.226-3.19-.369-1.114-.139-2.297-.146-3.715.447l.771 1.846z"
          ></path>
        </svg>
      </span>
    </a>
    <a class="cart-link" href="../cart.php" aria-label="Cart">
      <div class="cart-icon-container">
        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="9" cy="21" r="1"></circle>
          <circle cx="20" cy="21" r="1"></circle>
          <path d="M1 1h4l2.68 12.39a2 2 0 0 0 2 1.61h7.72a2 2 0 0 0 2-1.61L23 6H6"></path>
        </svg>
        <div class="cart-counter">0</div>
      </div>
    </a>
    <button class="param-btn" id="paramsBtn" aria-haspopup="true" aria-expanded="false" aria-controls="paramsMenu" title="Parameters">⚙️</button>
   </div>
  </nav>

  <div class="equipment-hero">
    <div class="equipment-left">
      <h1 class="hero-heading">
        ZERO-LOSS
        <span class="arrow">→</span>
        BUSINESS<br>
        CONTINUITY
      </h1>
      <p class="hero-description">
        Our ground-breaking development service that ensures the ultimate business connection without compromise.
      </p>
      <button class="learn-more-btn">
        LEARN MORE
        <span class="arrow">→</span>
      </button>
     
    </div>
    
                   <div class="equipment-right">
        <div class="city-model">
          <div class="city-container">
            <div class="logo-design">
              <img src="https://2.bp.blogspot.com/-tE6EV0UsmPM/Wr_kqbO7mbI/AAAAAAAAAuA/0HEkBCvQi9EkiZbEg0kE_9xE3ewcWzh_ACLcBGAs/s640/marjane.png" alt="Marjane Logo" class="logo-image">
            </div>
          </div>
        </div>
      </div>
  </div>
<div id="page2" class="page">
  <!-- Loading Animation -->
  <div class="loading-overlay">
    <div class="loader"></div>
  </div>

  <!-- Main Hero Section -->
  <section class="hero-container">
    <!-- Hero Image -->
    <img src="../../img.video/talaja (2).png" alt="Talaja" class="hero-image">
    
    <!-- Floating Background Elements -->
   

    <div class="content-wrapper">
      <!-- Main Heading -->
      <div class="main-heading anim-item">
        <h1>
          FrostLine Triple Glass Door Cooler
        </h1>
      </div>

      <!-- Description Text -->
      <div class="description anim-item">
        <p>
          A high-capacity, energy-efficient commercial display refrigerator with triple glass doors, adjustable shelving, and LED lighting, perfect for showcasing beverages and perishables in retail spaces.
        </p>
      </div>

      <!-- Price Display -->
      <div class="price-display anim-item">
        <div class="price-amount">
          <span class="currency">$</span>
          <span class="amount">299</span>
          <span class="period">/month</span>
        </div>
      </div>

      <!-- Request Button -->
      <div class="request-button-container anim-item">
        <button class="request-btn" onclick="sendRequest(1, 'FrostLine Triple Glass Door Cooler', 'refrigerator', '$299/month')">
          <span class="btn-text">Send Request</span>
          <span class="btn-icon">→</span>
        </button>
      </div>

      <!-- Success Message -->
      <div class="success-message" id="successMessage">
        <div class="message-content">
          <span class="check-icon">✓</span>
          <h3>Request Submitted Successfully</h3>
          <p>Thank you for your interest! We will call you as soon as possible.</p>
          <div class="message-footer">
            <span class="reference-number">Ref: #REQ-2024-001</span>
          </div>
          <button class="close-btn" onclick="closeMessage()">Close</button>
        </div>
      </div>
    </div>
  </section>
</div>

<!-- Page 3 - Same content as page 2 -->
<div id="page3" class="page">
  <!-- Loading Animation -->
  <div class="loading-overlay">
    <div class="loader"></div>
  </div>

  <!-- Main Hero Section -->
  <section class="hero-container">
    <!-- Hero Image -->
    <img src="../../img.video/raf-kbir.png" alt="Raf Kbir" class="hero-image anim-item">
    
    <!-- Floating Background Elements -->
    <div class="floating-elements">
      <div class="floating-icon">📦</div>
      <div class="floating-icon">🏪</div>
      <div class="floating-icon">🔧</div>
    </div>

    <div class="content-wrapper">
      <!-- Main Heading -->
      <div class="main-heading anim-item">
        <h1>
          Heavy-Duty<br>
          Shelf
        </h1>
      </div>

      <!-- Description Text -->
      <div class="description anim-item">
        <p>
          Durable, spacious, and perfect for retail or storage, this heavy-duty shelving unit 
          offers multiple tiers to organize and display products efficiently.
        </p>
      </div>

      <!-- Price Display -->
      <div class="price-display anim-item">
        <div class="price-amount">
          <span class="currency">$</span>
          <span class="amount">299</span>
          <span class="period">/month</span>
        </div>
      </div>

      <!-- Request Button -->
      <div class="request-button-container anim-item">
        <button class="request-btn" onclick="sendRequest(2, 'Heavy-Duty Shelf', 'shelf', '$299/month')">
          <span class="btn-text">Send Request</span>
          <span class="btn-icon">→</span>
        </button>
      </div>

      <!-- Success Message -->
      <div class="success-message" id="successMessage3">
        <div class="message-content">
          <span class="check-icon">✓</span>
          <h3>Request Submitted Successfully</h3>
          <p>Thank you for your interest! We will call you as soon as possible.</p>
          <div class="message-footer">
            <span class="reference-number">Ref: #REQ-2024-002</span>
          </div>
          <button class="close-btn" onclick="closeMessage3()">Close</button>
        </div>
      </div>
    </div>
  </section>
</div>

<!-- Page 4 - Similar to page 3 -->
<div id="page4" class="page">
  <!-- Loading Animation -->
  <div class="loading-overlay">
    <div class="loader"></div>
  </div>

  <!-- Main Hero Section -->
  <section class="hero-container">
    <!-- Hero Image -->
    <img src="../../img.video/awakhir.png" alt="Kachair" class="hero-image anim-item">
    
   

    <div class="content-wrapper">
      <!-- Main Heading -->
      <div class="main-heading anim-item">
        <h1>
          Smart Checkout Pro
        </h1>
      </div>

      <!-- Description Text -->
      <div class="description anim-item">
        <p>
          A modern, high-performance retail checkout counter designed for efficiency and style,
          featuring a sleek POS system, conveyor belt, and spacious bagging area for a seamless
          shopping experience.
        </p>
      </div>

      <!-- Price Display -->
      <div class="price-display anim-item">
        <div class="price-amount">
          <span class="currency">$</span>
          <span class="amount">299</span>
          <span class="period">/month</span>
        </div>
      </div>

      <!-- Request Button -->
      <div class="request-button-container anim-item">
        <button class="request-btn" onclick="sendRequest(3, 'Smart Checkout Pro', 'checkout', '$299/month')">
          <span class="btn-text">Request Quote</span>
          <span class="btn-icon">→</span>
        </button>
      </div>

      <!-- Success Message -->
      <div class="success-message" id="successMessage4">
        <div class="message-content">
          <span class="check-icon">✓</span>
          <h3>Quote Request Submitted</h3>
          <p>Thanks! We will contact you shortly with pricing and availability.</p>
          <div class="message-footer">
            <span class="reference-number">Ref: #REQ-2024-004</span>
          </div>
          <button class="close-btn" onclick="closeMessage4()">Close</button>
        </div>
      </div>
    </div>
  </section>
</div>

  <!-- CSRF Token for AJAX requests -->
  <script>
    window.csrfToken = '<?php echo csrf_token(); ?>';
  </script>
  
  <script src="../cart.js"></script>
  <script src="equipment.js"></script>
  
  <!-- GSAP (CDN option) - if not using a module bundler -->
  <script src="https://cdn.jsdelivr.net/npm/gsap@3/dist/gsap.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/gsap@3/dist/ScrollTrigger.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/gsap@3/dist/ScrollToPlugin.min.js"></script>
  
  <!-- Scroll + Reveal logic (non-module). If using a bundler, comment this tag and use module import inside assets/js/equipment-scroll.js as documented there. -->
  <script src="../assets/js/equipment-scroll.js"></script>
  
  <script>
    // Smooth loading animation
    document.addEventListener('DOMContentLoaded', function() {
      // Simulate app loading
      setTimeout(() => {
        document.querySelector('.loading-overlay').style.display = 'none';
      }, 2000);
    });

    // Parallax effect for floating elements
    document.addEventListener('mousemove', (e) => {
      const floatingIcons = document.querySelectorAll('.floating-icon');
      const mouseX = e.clientX / window.innerWidth;
      const mouseY = e.clientY / window.innerHeight;

      floatingIcons.forEach((icon, index) => {
        const speed = (index + 1) * 0.5;
        const x = mouseX * speed * 10;
        const y = mouseY * speed * 10;
        icon.style.transform = `translate(${x}px, ${y}px) rotate(${x}deg)`;
      });
    });

    // Note: overflow handling moved to GSAP ScrollTrigger logic (equipment-scroll.js)

    // Unified request function for all equipment
    function sendRequest(equipmentId, equipmentName, equipmentType, price) {
      // Show loading state
      const button = event.target.closest('.request-btn');
      const originalText = button.innerHTML;
      button.innerHTML = '<span class="btn-text">Sending...</span>';
      button.disabled = true;
      
      // Prepare form data
      const formData = new FormData();
      formData.append('equipment_id', equipmentId);
      formData.append('equipment_name', equipmentName);
      formData.append('equipment_type', equipmentType);
      formData.append('price', price);
      formData.append('csrf', window.csrfToken);
      
      // Make AJAX request
      fetch('process_equipment_request.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Show success message based on equipment ID
          let successMessage;
          switch(equipmentId) {
            case 1:
              successMessage = document.getElementById('successMessage');
              break;
            case 2:
              successMessage = document.getElementById('successMessage3');
              break;
            case 3:
              successMessage = document.getElementById('successMessage4');
              break;
          }
          
          if (successMessage) {
            // Update reference number
            const refElement = successMessage.querySelector('.reference-number');
            if (refElement) {
              refElement.textContent = 'Ref: ' + data.reference;
            }
            successMessage.classList.add('show');
          }
        } else {
          alert('Error: ' + (data.message || 'Failed to submit request'));
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Error: Failed to submit request. Please try again.');
      })
      .finally(() => {
        // Restore button state
        button.innerHTML = originalText;
        button.disabled = false;
      });
    }

    // Close message function
    function closeMessage() {
      const successMessage = document.getElementById('successMessage');
      successMessage.classList.remove('show');
    }

    // Close message function for page 3
    function closeMessage3() {
      const successMessage = document.getElementById('successMessage3');
      successMessage.classList.remove('show');
    }

    // Close message function for page 4
    function closeMessage4() {
      const successMessage = document.getElementById('successMessage4');
      successMessage.classList.remove('show');
    }

    // Close success message when clicking outside
    document.addEventListener('click', function(event) {
      const successMessages = document.querySelectorAll('.success-message');
      
      successMessages.forEach(message => {
        if (event.target === message) {
          message.classList.remove('show');
        }
      });
    });

    // Also close when pressing Escape key
    document.addEventListener('keydown', function(event) {
      if (event.key === 'Escape') {
        const successMessages = document.querySelectorAll('.success-message');
        successMessages.forEach(message => {
          message.classList.remove('show');
        });
      }
    });

  </script>
  
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
  
  <!-- Pagination Dots (optional enhancement) -->
  <div class="page-pagination" aria-hidden="true"></div>
  
  <!-- Parameters dropdown (DB mapped) -->
  <div class="params-menu" id="paramsMenu" role="dialog" aria-modal="true" aria-labelledby="paramsTitle" aria-hidden="true">
    <div class="params-header">
      <?php if (!empty($partner['avatar_url'])): ?>
        <img class="params-avatar" src="<?php echo htmlspecialchars($partner['avatar_url']); ?>" alt="Avatar">
      <?php else: ?>
        <div class="params-avatar" aria-hidden="true"><?php echo strtoupper(substr($partner['full_name'],0,1)); ?></div>
      <?php endif; ?>
      <div>
        <div id="paramsTitle" class="params-name"><?php echo htmlspecialchars($partner['full_name']); ?></div>
        <div class="params-meta">Member since <?php echo htmlspecialchars(date('M Y', strtotime($partner['created_at']))); ?></div>
      </div>
    </div>
    <div class="params-row"><span>Username</span><span><?php echo htmlspecialchars($partner['username']); ?></span></div>
    <div class="params-row"><span>Email</span><span><?php echo htmlspecialchars($partner['full_name']); ?></span></div>
    <div class="params-row"><span>Phone</span><span><?php echo htmlspecialchars($partner['phone']); ?></span></div>
    <div class="params-row"><span>Company</span><span><?php echo htmlspecialchars($partner['company']); ?></span></div>
    <div class="params-row"><span>Role</span><span><?php echo htmlspecialchars($partner['role']); ?></span></div>
    <?php if (!empty($partner['address'])): ?><div class="params-row"><span>Address</span><span><?php echo htmlspecialchars($partner['address']); ?></span></div><?php endif; ?>
    <div class="params-row"><span>Last Login</span><span><?php echo htmlspecialchars($partner['last_login']); ?></span></div>
    <?php if (($_SESSION['auth']['role'] ?? '') === 'admin' && !empty($partner['notes'])): ?><div class="params-row"><span>Notes</span><span><?php echo htmlspecialchars($partner['notes']); ?></span></div><?php endif; ?>
    <form action="../logout.php" method="post">
      <input type="hidden" name="csrf" value="<?php echo htmlspecialchars(csrf_token()); ?>">
      <button class="logout-btn" type="submit">Logout</button>
    </form>
  </div>
  
  <script src="../assets/js/profile.js" defer></script>
</body>
</html>
