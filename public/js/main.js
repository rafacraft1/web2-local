// ==========================================
// Inisialisasi AOS (Animate On Scroll)
// ==========================================
AOS.init({ once: true, offset: 50, duration: 800 });

// ==========================================
// Logika Navbar Sticky & Tombol Mengambang
// ==========================================
const navbar = document.querySelector('.glass-nav');
const btnBackToTop = document.getElementById('btnBackToTop');
const btnWhatsApp = document.getElementById('btnWhatsApp');

window.addEventListener('scroll', function () {
    // Navbar Glass Effect (Hanya dieksekusi jika elemen .glass-nav ditemukan)
    if (navbar) {
        if (window.scrollY > 50) {
            navbar.style.background = 'rgba(255, 255, 255, 0.95)';
            navbar.style.boxShadow = '0 4px 6px -1px rgba(0, 0, 0, 0.1)';
        } else {
            navbar.style.background = 'rgba(255, 255, 255, 0.85)';
            navbar.style.boxShadow = 'none';
        }
    }

    // Tampilkan/Sembunyikan Tombol Floating dengan Animasi dari style.css
    if (window.scrollY > 300) {
        if (btnBackToTop) btnBackToTop.classList.add('show');
        if (btnWhatsApp) btnWhatsApp.classList.add('move-up');
    } else {
        if (btnBackToTop) btnBackToTop.classList.remove('show');
        if (btnWhatsApp) btnWhatsApp.classList.remove('move-up');
    }
});

// Aksi klik untuk tombol Back to Top
if (btnBackToTop) {
    btnBackToTop.addEventListener('click', function () {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
}

// ==========================================
// Logika Navigasi Aktif Otomatis
// ==========================================
document.addEventListener("DOMContentLoaded", function () {
    // Ambil path URL saat ini
    const currentPath = window.location.pathname.split("/").pop() || '';
    const navLinks = document.querySelectorAll('.navbar-nav .nav-link');

    navLinks.forEach(link => {
        const linkPath = link.getAttribute('href');
        // Pengecekan agar menu "Beranda" tidak selalu aktif jika ada sub-path lain
        if (linkPath.includes(currentPath) && currentPath !== '') {
            link.classList.add('active');
        } else if (currentPath === '' && linkPath === '/') {
            // Khusus untuk halaman utama / base_url
            link.classList.add('active');
        } else {
            link.classList.remove('active');
        }
    });
});

// ==========================================
// Logika Animasi Angka Statistik (Counter)
// ==========================================
const counters = document.querySelectorAll('.counter-value');
const speed = 250;

const startCounters = () => {
    counters.forEach(counter => {
        const animate = () => {
            const target = +counter.getAttribute('data-target');
            const count = +counter.innerText.replace(/[^0-9]/g, ''); // Hapus karakter non-angka
            const suffix = counter.getAttribute('data-suffix') || '';
            const inc = target / speed;

            if (count < target) {
                let current = Math.ceil(count + inc);
                if (current > target) current = target;
                counter.innerText = current + suffix;
                setTimeout(animate, 50);
            } else {
                // Format angka ribuan (opsional)
                counter.innerText = (target >= 1000 ? (target / 1000) + 'K' : target) + suffix;
            }
        };
        animate();
    });
};

// Gunakan IntersectionObserver agar counter baru mulai saat di-scroll ke area tersebut
const statsSection = document.querySelector('.stats-section');
if (statsSection) {
    const statsObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                startCounters();
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });

    statsObserver.observe(statsSection);
}