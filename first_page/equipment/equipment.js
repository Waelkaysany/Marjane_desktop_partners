/**
 * Equipment Page JavaScript
 * Handles all interactive functionality for the equipment page
 */

class EquipmentPage {
  constructor() {
    this.currentSlide = 0;
    this.totalSlides = 3;
    this.init();
  }

  /**
   * Initialize the equipment page
   */
  init() {
    this.setupEventListeners();
    this.initializeCart();
    this.setupPagination();
    this.setupLogoInteractions();
    this.setupSmoothAnimations();
  }

  /**
   * Setup all event listeners
   */
  setupEventListeners() {
    // Learn more button
    const learnMoreBtn = document.querySelector('.learn-more-btn');
    if (learnMoreBtn) {
      learnMoreBtn.addEventListener('click', (e) => {
        e.preventDefault();
        this.handleLearnMore();
      });
    }

    // Pagination dots
    const paginationDots = document.querySelectorAll('.pagination-dot');
    paginationDots.forEach((dot, index) => {
      dot.addEventListener('click', () => {
        this.goToSlide(index);
      });
    });

    // Logo interactions
    const logoImage = document.querySelector('.logo-image');
    if (logoImage) {
      logoImage.addEventListener('mouseenter', () => {
        this.handleLogoHover();
      });
    }

    // Keyboard navigation
    document.addEventListener('keydown', (e) => {
      this.handleKeyboardNavigation(e);
    });
  }

  /**
   * Initialize cart functionality
   */
  initializeCart() {
    // Initialize cart if not already done
    if (!window.cart) {
      window.cart = new Cart();
    }
    
    // Update cart icon after a short delay
    setTimeout(() => {
      if (window.cart) {
        window.cart.updateCartIcon();
        console.log('Cart initialized with items:', window.cart.getItems());
      }
    }, 100);
  }

  /**
   * Setup pagination functionality
   */
  setupPagination() {
    const dots = document.querySelectorAll('.pagination-dot');
    
    // Add hover effects to pagination dots
    dots.forEach(dot => {
      dot.addEventListener('mouseenter', () => {
        dot.style.transform = 'scale(1.2)';
      });
      
      dot.addEventListener('mouseleave', () => {
        dot.style.transform = 'scale(1)';
      });
    });
  }

  /**
   * Setup logo interactions
   */
  setupLogoInteractions() {
    const logoContainer = document.querySelector('.logo-design');
    const logoImage = document.querySelector('.logo-image');
    
    if (logoContainer && logoImage) {
      // Add click effect
      logoContainer.addEventListener('click', () => {
        this.handleLogoClick();
      });

      // Add loading animation
      logoImage.addEventListener('load', () => {
        logoContainer.style.opacity = '1';
        logoContainer.style.transform = 'scale(1)';
      });
    }
  }

  /**
   * Setup smooth animations
   */
  setupSmoothAnimations() {
    // Animate elements on page load
    const observerOptions = {
      threshold: 0.1,
      rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.style.opacity = '1';
          entry.target.style.transform = 'translateY(0)';
        }
      });
    }, observerOptions);

    // Observe elements for animation
    const animatedElements = document.querySelectorAll('.hero-heading, .hero-description, .learn-more-btn');
    animatedElements.forEach(el => {
      el.style.opacity = '0';
      el.style.transform = 'translateY(30px)';
      el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
      observer.observe(el);
    });
  }

  /**
   * Handle learn more button click
   */
  handleLearnMore() {
    console.log('Learn more clicked');
    
    // Add click animation
    const btn = document.querySelector('.learn-more-btn');
    btn.style.transform = 'scale(0.95)';
    
    setTimeout(() => {
      btn.style.transform = 'scale(1)';
    }, 150);

    // You can add navigation logic here
    // window.location.href = '/learn-more';
  }

  /**
   * Handle pagination navigation
   */
  goToSlide(slideIndex) {
    if (slideIndex < 0 || slideIndex >= this.totalSlides) return;
    
    this.currentSlide = slideIndex;
    
    // Update pagination dots
    const dots = document.querySelectorAll('.pagination-dot');
    dots.forEach((dot, index) => {
      if (index === slideIndex) {
        dot.classList.add('active');
      } else {
        dot.classList.remove('active');
      }
    });

    // Add slide transition effect
    this.animateSlideTransition();
  }

  /**
   * Animate slide transition
   */
  animateSlideTransition() {
    const heroContent = document.querySelector('.equipment-left');
    
    // Fade out
    heroContent.style.opacity = '0.5';
    heroContent.style.transform = 'translateX(-20px)';
    
    setTimeout(() => {
      // Fade in
      heroContent.style.opacity = '1';
      heroContent.style.transform = 'translateX(0)';
    }, 300);
  }

  /**
   * Handle logo hover effects
   */
  handleLogoHover() {
    const logoContainer = document.querySelector('.logo-design');
    
    // Add subtle glow effect
    logoContainer.style.filter = 'brightness(1.1)';
    
    setTimeout(() => {
      logoContainer.style.filter = 'brightness(1)';
    }, 200);
  }

  /**
   * Handle logo click
   */
  handleLogoClick() {
    console.log('Logo clicked');
    
    // Add click animation
    const logoContainer = document.querySelector('.logo-design');
    logoContainer.style.transform = 'scale(0.95)';
    
    setTimeout(() => {
      logoContainer.style.transform = 'scale(1)';
    }, 150);
  }

  /**
   * Handle keyboard navigation
   */
  handleKeyboardNavigation(e) {
    switch(e.key) {
      case 'ArrowLeft':
        e.preventDefault();
        this.goToSlide(Math.max(0, this.currentSlide - 1));
        break;
      case 'ArrowRight':
        e.preventDefault();
        this.goToSlide(Math.min(this.totalSlides - 1, this.currentSlide + 1));
        break;
      case 'Enter':
        if (document.activeElement.classList.contains('learn-more-btn')) {
          this.handleLearnMore();
        }
        break;
    }
  }

  /**
   * Update page content dynamically
   */
  updateContent(content) {
    const heroHeading = document.querySelector('.hero-heading');
    const heroDescription = document.querySelector('.hero-description');
    
    if (content.heading && heroHeading) {
      heroHeading.innerHTML = content.heading;
    }
    
    if (content.description && heroDescription) {
      heroDescription.textContent = content.description;
    }
  }

  /**
   * Get current page state
   */
  getPageState() {
    return {
      currentSlide: this.currentSlide,
      totalSlides: this.totalSlides
    };
  }
}

// Utility functions
const EquipmentUtils = {
  /**
   * Debounce function for performance optimization
   */
  debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
      const later = () => {
        clearTimeout(timeout);
        func(...args);
      };
      clearTimeout(timeout);
      timeout = setTimeout(later, wait);
    };
  },

  /**
   * Smooth scroll to element
   */
  smoothScrollTo(element) {
    element.scrollIntoView({
      behavior: 'smooth',
      block: 'start'
    });
  },

  /**
   * Add loading state to button
   */
  setButtonLoading(button, isLoading) {
    if (isLoading) {
      button.disabled = true;
      button.innerHTML = '<span class="loading-spinner"></span> Loading...';
    } else {
      button.disabled = false;
      button.innerHTML = 'LEARN MORE <span class="arrow">→</span>';
    }
  }
};

// Initialize equipment page when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
  window.equipmentPage = new EquipmentPage();
  
  // Add loading spinner styles
  const style = document.createElement('style');
  style.textContent = `
    .loading-spinner {
      display: inline-block;
      width: 16px;
      height: 16px;
      border: 2px solid #ffffff;
      border-radius: 50%;
      border-top-color: transparent;
      animation: spin 1s linear infinite;
      margin-right: 8px;
    }
    
    @keyframes spin {
      to {
        transform: rotate(360deg);
      }
    }
  `;
  document.head.appendChild(style);
});

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
  module.exports = { EquipmentPage, EquipmentUtils };
}

// ... existing code ...

// Equipment request functions
function sendRequest(equipmentId, equipmentName, equipmentType, price) {
  // Check if user is authenticated
  if (!window.csrfToken) {
    alert('Please log in to submit equipment requests.');
    return;
  }

  // Get the button that was clicked
  const button = event.target.closest('.request-btn');
  if (!button) return;

  // Show loading state
  const originalText = button.innerHTML;
  button.disabled = true;
  button.innerHTML = '<span class="loading-spinner"></span> Sending...';

  // Prepare request data
  const formData = new FormData();
  formData.append('equipment_id', equipmentId);
  formData.append('equipment_name', equipmentName);
  formData.append('equipment_type', equipmentType);
  formData.append('price', price);
  formData.append('csrf', window.csrfToken);

  // Send request to server
  fetch('process_equipment_request.php', {
    method: 'POST',
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      // Show success message
      const pageId = getCurrentPageId();
      const successMessage = document.getElementById(`successMessage${pageId}`);
      if (successMessage) {
        successMessage.style.display = 'flex';
        // Update reference number if available
        const refElement = successMessage.querySelector('.reference-number');
        if (refElement && data.reference) {
          refElement.textContent = `Ref: ${data.reference}`;
        }
      }
    } else {
      alert('Error: ' + (data.message || 'Failed to submit request'));
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('Failed to submit request. Please try again.');
  })
  .finally(() => {
    // Restore button state
    button.disabled = false;
    button.innerHTML = originalText;
  });
}

// Helper function to get current page ID
function getCurrentPageId() {
  const activePage = document.querySelector('.page.active');
  if (activePage) {
    return activePage.id.replace('page', '');
  }
  return '1'; // Default fallback
}

// Close message functions
function closeMessage1() { document.getElementById('successMessage1').style.display = 'none'; }
function closeMessage2() { document.getElementById('successMessage2').style.display = 'none'; }
function closeMessage3() { document.getElementById('successMessage3').style.display = 'none'; }
function closeMessage4() { document.getElementById('successMessage4').style.display = 'none'; }

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
  module.exports = { EquipmentPage, EquipmentUtils };
}