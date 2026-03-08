document.addEventListener("DOMContentLoaded", function () {
    const sidebar = document.getElementById('sidebar');
    const sidebarCollapse = document.getElementById('sidebarCollapse');
    const sidebarClose = document.getElementById('sidebarClose');
    const sidebarOverlay = document.getElementById('sidebarOverlay');

    // Toggle sidebar dari tombol hamburger menu
    if (sidebarCollapse) {
        sidebarCollapse.addEventListener('click', function () {
            sidebar.classList.toggle('toggled');
            if (window.innerWidth <= 768) {
                sidebarOverlay.classList.toggle('active');
            }
        });
    }

    // Tutup sidebar dari tombol silang (khusus mobile)
    if (sidebarClose) {
        sidebarClose.addEventListener('click', function () {
            sidebar.classList.remove('toggled');
            sidebarOverlay.classList.remove('active');
        });
    }

    // Tutup sidebar saat area gelap (overlay) diklik
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', function () {
            sidebar.classList.remove('toggled');
            sidebarOverlay.classList.remove('active');
        });
    }

    // Handle perbaikan tampilan saat jendela browser di-resize
    window.addEventListener('resize', function () {
        if (window.innerWidth > 768) {
            sidebarOverlay.classList.remove('active');
        }
    });
});