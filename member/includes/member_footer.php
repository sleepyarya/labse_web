    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AOS JS -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <script>
        // Initialize AOS with reduced motion for stability
        AOS.init({
            duration: 600,
            once: true,
            easing: 'ease-out',
            delay: 100,
            disable: function() {
                // Disable AOS on mobile to prevent glitches
                return window.innerWidth < 768;
            }
        });
        
        // Fix flicker issues
        document.addEventListener('DOMContentLoaded', function() {
            // Force hardware acceleration
            const elements = document.querySelectorAll('.member-topbar, .card, .member-content');
            elements.forEach(el => {
                el.style.transform = 'translateZ(0)';
                el.style.backfaceVisibility = 'hidden';
            });
            
            // Prevent FOUC (Flash of Unstyled Content)
            document.body.style.visibility = 'visible';
        });
        
        // Mobile Sidebar Toggle
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarClose = document.getElementById('sidebarClose');
            const sidebar = document.getElementById('memberSidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            
            // Function to open sidebar
            function openSidebar() {
                if (sidebar) sidebar.classList.add('active');
                if (backdrop) backdrop.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
            
            // Function to close sidebar
            function closeSidebar() {
                if (sidebar) sidebar.classList.remove('active');
                if (backdrop) backdrop.classList.remove('active');
                document.body.style.overflow = '';
            }
            
            // Toggle button click
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', openSidebar);
            }
            
            // Close button click
            if (sidebarClose) {
                sidebarClose.addEventListener('click', closeSidebar);
            }
            
            // Backdrop click
            if (backdrop) {
                backdrop.addEventListener('click', closeSidebar);
            }
            
            // Close sidebar when clicking menu item on mobile
            if (sidebar) {
                const menuItems = sidebar.querySelectorAll('.menu-item');
                menuItems.forEach(item => {
                    item.addEventListener('click', function() {
                        if (window.innerWidth <= 768) {
                            closeSidebar();
                        }
                    });
                });
            }
            
            // Close sidebar on window resize if > 768px
            window.addEventListener('resize', function() {
                if (window.innerWidth > 768) {
                    closeSidebar();
                }
            });
        });
        
        // Legacy function for backward compatibility
        function toggleSidebar() {
            const sidebar = document.getElementById('memberSidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            if (sidebar) sidebar.classList.toggle('active');
            if (backdrop) backdrop.classList.toggle('active');
        }
    </script>
</body>
</html>
