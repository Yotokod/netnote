import './bootstrap';

// NetNote App JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    
    if (mobileMenuBtn && mobileMenu) {
        mobileMenuBtn.addEventListener('click', function() {
            mobileMenu.classList.toggle('hidden');
        });
    }

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'start' 
                });
            }
        });
    });

    // Add loading states to buttons
    document.querySelectorAll('button[type="submit"], .btn-loading').forEach(button => {
        button.addEventListener('click', function() {
            if (!this.disabled) {
                this.classList.add('opacity-75');
                this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>' + this.textContent;
            }
        });
    });
});