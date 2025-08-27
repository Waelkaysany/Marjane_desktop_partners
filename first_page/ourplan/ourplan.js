// Pricing Page JavaScript - Completely Isolated Version

(function() {
    'use strict';
    
    // Wait for DOM to be ready
    function ready(fn) {
        if (document.readyState !== 'loading') {
            fn();
        } else {
            document.addEventListener('DOMContentLoaded', fn);
        }
    }
    
    ready(function() {
        console.log('Pricing page loaded successfully - isolated version');
        
        // Billing toggle functionality
        var toggleBtns = document.querySelectorAll('.toggle-btn');
        var amounts = document.querySelectorAll('.amount');
        var billingInfos = document.querySelectorAll('.billing-info');
        
        // Pricing data
        var pricingData = {
            basic: { yearly: 299, monthly: 299 },
            business: { yearly: 799, monthly: 799 },
            elite: { yearly: 'Custom', monthly: 'Custom' }
        };
        
        // Handle billing toggle
        for (var i = 0; i < toggleBtns.length; i++) {
            toggleBtns[i].addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                var period = this.getAttribute('data-period');
                
                // Update active state
                for (var j = 0; j < toggleBtns.length; j++) {
                    toggleBtns[j].classList.remove('active');
                }
                this.classList.add('active');
                
                // Update prices
                updatePrices(period);
            });
        }
        
        function updatePrices(period) {
            for (var i = 0; i < amounts.length; i++) {
                var amount = amounts[i];
                var card = amount.closest('.pricing-card');
                var planName = card.querySelector('.plan-name').textContent.toLowerCase();
                
                if (planName === 'basic partner') {
                    amount.textContent = pricingData.basic[period];
                } else if (planName === 'business partner') {
                    amount.textContent = pricingData.business[period];
                } else if (planName === 'elite partner') {
                    amount.textContent = pricingData.elite[period];
                }
            }
            
            // Update billing info
            for (var i = 0; i < billingInfos.length; i++) {
                billingInfos[i].textContent = period === 'yearly' ? 'billed yearly' : 'billed monthly';
            }
        }
        
        // Plan button click handlers - Simple and direct
        var planButtons = document.querySelectorAll('.plan-btn');
        
        for (var i = 0; i < planButtons.length; i++) {
            planButtons[i].addEventListener('click', function(e) {
                // Add click animation
                this.style.transform = 'scale(0.95)';
                setTimeout(function() {
                    this.style.transform = '';
                }.bind(this), 150);
                
                console.log('Plan button clicked - form will submit naturally');
                
                // Let the form submit naturally - don't prevent default
            });
        }
        
        // Add hover effects for cards
        var pricingCards = document.querySelectorAll('.pricing-card');
        
        for (var i = 0; i < pricingCards.length; i++) {
            var card = pricingCards[i];
            
            card.addEventListener('mouseenter', function() {
                if (this.classList.contains('featured')) {
                    this.style.transform = 'scale(1.08)';
                } else {
                    this.style.transform = 'translateY(-8px)';
                }
            });
            
            card.addEventListener('mouseleave', function() {
                if (this.classList.contains('featured')) {
                    this.style.transform = 'scale(1.05)';
                } else {
                    this.style.transform = 'translateY(0)';
                }
            });
        }
        
        // Initialize with yearly pricing
        updatePrices('yearly');
        
        // Block all external errors
        window.addEventListener('error', function(e) {
            if (e.filename && (
                e.filename.includes('ws://') || 
                e.filename.includes('reload.js') || 
                e.filename.includes('mj.') ||
                e.filename.includes('MathJax')
            )) {
                e.preventDefault();
                console.log('Blocked external script error:', e.message);
                return false;
            }
        });
        
        // Test form functionality
        console.log('Forms found:', document.querySelectorAll('form').length);
        console.log('Plan buttons found:', planButtons.length);
        
        // Add a simple test to verify forms work
        var forms = document.querySelectorAll('form');
        for (var i = 0; i < forms.length; i++) {
            forms[i].addEventListener('submit', function(e) {
                console.log('Form submitting:', this.querySelector('input[name="plan_id"]').value);
            });
        }
    });
})();
