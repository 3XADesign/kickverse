/* ============================================
   KICKVERSE - NAVBAR FUNCTIONALITY
   Mobile menu toggle & dropdown
   ============================================ */

document.addEventListener('DOMContentLoaded', () => {
    // Mobile Menu Toggle
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const navMenu = document.querySelector('.nav-menu');
    const navDropdownBtn = document.querySelector('.nav-dropdown-btn');
    const navDropdown = document.querySelector('.nav-dropdown');

    if (mobileMenuToggle && navMenu) {
        mobileMenuToggle.addEventListener('click', () => {
            mobileMenuToggle.classList.toggle('active');
            navMenu.classList.toggle('active');
            document.body.style.overflow = navMenu.classList.contains('active') ? 'hidden' : '';
        });

        // Close menu when clicking outside
        document.addEventListener('click', (e) => {
            if (!navMenu.contains(e.target) && !mobileMenuToggle.contains(e.target)) {
                mobileMenuToggle.classList.remove('active');
                navMenu.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    }

    // Mobile dropdown toggle
    if (navDropdownBtn && navDropdown) {
        navDropdownBtn.addEventListener('click', (e) => {
            if (window.innerWidth <= 768) {
                e.stopPropagation();
                navDropdown.classList.toggle('active');
            }
        });
    }
});
