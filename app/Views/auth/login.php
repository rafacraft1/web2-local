<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'SYS-LOGIN // SMK KREATIF' ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@500;600;700&family=Share+Tech+Mono&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        :root {
            /* Warna latar diterangkan menjadi Deep Navy */
            --bg-base: #0b1121; 
            --neon-cyan: #00e5ff;
            --neon-purple: #9d4edd;
            /* Latar panel lebih solid/tidak terlalu tembus pandang */
            --panel-bg: rgba(15, 23, 42, 0.85); 
            /* Warna teks dibuat jauh lebih terang (putih & abu muda) */
            --text-main: #f8fafc;
            --text-muted: #94a3b8; 
            --text-label: #cbd5e1;
        }

        body {
            font-family: 'Rajdhani', sans-serif;
            background-color: var(--bg-base);
            background-image: 
                linear-gradient(rgba(0, 229, 255, 0.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0, 229, 255, 0.05) 1px, transparent 1px);
            background-size: 40px 40px;
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            color: var(--text-main);
            overflow: hidden;
            position: relative;
        }

        .cyber-glow {
            position: absolute;
            width: 50vw;
            height: 50vw;
            background: radial-gradient(circle, rgba(157, 78, 221, 0.15) 0%, rgba(0, 229, 255, 0) 60%);
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: -1;
            pointer-events: none;
        }

        .futuristic-card {
            width: 100%;
            max-width: 420px;
            background: var(--panel-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(0, 229, 255, 0.3);
            border-top: 3px solid var(--neon-cyan);
            border-bottom: 3px solid var(--neon-purple);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5), 0 0 20px rgba(0, 229, 255, 0.1);
            padding: 2.5rem 2rem;
            position: relative;
            z-index: 1;
            clip-path: polygon(0 0, 100% 0, 100% calc(100% - 25px), calc(100% - 25px) 100%, 0 100%);
        }

        .hud-corner {
            position: absolute;
            width: 20px;
            height: 20px;
            border: 2px solid var(--neon-cyan);
        }
        .hud-corner.top-left { top: 12px; left: 12px; border-right: none; border-bottom: none; }
        .hud-corner.top-right { top: 12px; right: 12px; border-left: none; border-bottom: none; }

        .login-brand-text {
            font-family: 'Share Tech Mono', monospace;
            font-size: 2rem;
            color: #ffffff;
            letter-spacing: 2px;
            margin-bottom: 0.2rem;
        }
        
        .brand-accent {
            color: var(--neon-cyan);
            text-shadow: 0 0 8px rgba(0, 229, 255, 0.6);
        }

        /* Label lebih terang dan tajam */
        .form-label {
            font-family: 'Share Tech Mono', monospace;
            color: var(--text-label);
            font-size: 0.85rem;
            letter-spacing: 1px;
            margin-bottom: 0.4rem;
        }

        /* Input Form lebih jelas */
        .input-group {
            background: rgba(255, 255, 255, 0.03); /* Sedikit keabu-abuan agar terlihat batasnya */
            border: 1px solid rgba(255, 255, 255, 0.15);
            transition: all 0.3s ease;
        }

        .input-group-text, .form-control, .btn-outline-secondary {
            background: transparent !important;
            border: none !important;
            color: #ffffff !important; /* Teks input warna putih terang */
            font-family: 'Share Tech Mono', monospace;
            font-size: 1rem;
            border-radius: 0;
            padding: 0.75rem 1rem;
        }

        .input-group-text i {
            color: var(--neon-cyan);
        }

        .form-control::placeholder {
            color: #64748b; /* Placeholder abu-abu jelas */
        }

        /* Fokus Input */
        .input-group:focus-within {
            border-color: var(--neon-cyan);
            box-shadow: 0 0 10px rgba(0, 229, 255, 0.2);
            background: rgba(0, 229, 255, 0.05);
        }

        .btn-outline-secondary:hover i {
            color: #ffffff !important;
        }

        /* Checkbox */
        .form-check-input {
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid var(--text-muted);
            border-radius: 2px;
            cursor: pointer;
        }
        .form-check-input:checked {
            background-color: var(--neon-cyan);
            border-color: var(--neon-cyan);
        }
        
        .form-check-label {
            font-family: 'Share Tech Mono', monospace;
            font-size: 0.85rem;
            color: var(--text-muted);
            cursor: pointer;
        }

        /* Tombol Utama */
        .btn-cyber {
            font-family: 'Share Tech Mono', monospace;
            background: var(--neon-cyan);
            border: none;
            color: #000000; /* Teks hitam di atas cyan terang (sangat kontras) */
            padding: 0.8rem 1rem;
            font-weight: bold;
            font-size: 1.1rem;
            letter-spacing: 1px;
            border-radius: 2px;
            transition: all 0.3s ease;
        }

        .btn-cyber:hover {
            background: #ffffff;
            color: #000000;
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.4);
        }

        /* Tautan (Link) */
        .link-sys {
            font-family: 'Share Tech Mono', monospace;
            color: var(--neon-cyan);
            text-decoration: none;
            transition: all 0.2s;
        }
        .link-sys:hover {
            color: #ffffff;
            text-decoration: underline;
        }

        /* Alert Box */
        .alert {
            background-color: rgba(0, 0, 0, 0.5);
            border-radius: 2px;
            font-family: 'Share Tech Mono', monospace;
            color: #ffffff;
        }
        .alert-danger { border-left: 4px solid #ef4444; border-color: rgba(239, 68, 68, 0.3); }
        .alert-success { border-left: 4px solid #10b981; border-color: rgba(16, 185, 129, 0.3); }
        .btn-close { filter: invert(1) grayscale(100%) brightness(200%); }

        .scanline-divider {
            height: 1px;
            background: rgba(255, 255, 255, 0.1);
            margin: 1.5rem 0;
        }

        .sys-indicator {
            font-family: 'Share Tech Mono', monospace;
            font-size: 0.75rem;
            color: var(--text-muted);
            text-align: center;
            letter-spacing: 2px;
            margin-top: 1.5rem;
        }
    </style>
</head>

<body>

    <div class="cyber-glow"></div>

    <div class="futuristic-card">
        <div class="hud-corner top-left"></div>
        <div class="hud-corner top-right"></div>
        
        <div class="text-center mb-3 mt-2">
            <div class="login-brand-text">SYS.<span class="brand-accent">KREATIF</span></div>
            <p class="mb-0" style="font-family: 'Share Tech Mono', monospace; font-size: 0.9rem; color: var(--text-muted); letter-spacing: 2px;">SECURE LOGIN PANEL</p>
        </div>

        <div class="scanline-divider"></div>

        <?php if (session()->getFlashdata('error')) : ?>
            <div class="alert alert-danger alert-dismissible fade show py-2 px-3" role="alert" style="font-size: 0.9rem;">
                <i class="bi bi-exclamation-triangle-fill me-2 text-danger"></i>
                <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close pb-2" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('success')) : ?>
            <div class="alert alert-success alert-dismissible fade show py-2 px-3" role="alert" style="font-size: 0.9rem;">
                <i class="bi bi-check-circle-fill me-2 text-success"></i>
                <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close pb-2" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('panel/login/process') ?>" method="POST">
            <?= csrf_field() ?>

            <div class="mb-4">
                <label for="username" class="form-label">ID PENGGUNA / EMAIL</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                    <input type="text" class="form-control" id="username" name="username" value="<?= old('username') ?>" placeholder="Masukkan username" required autofocus autocomplete="off">
                </div>
            </div>

            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label for="password" class="form-label mb-0">KATA SANDI</label>
                    <a href="#" class="link-sys" style="font-size: 0.8rem;">Lupa Sandi?</a>
                </div>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-shield-lock"></i></span>
                    <input type="password" class="form-control" id="password" name="password" placeholder="••••••••" required>
                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                        <i class="bi bi-eye-slash" id="toggleIcon"></i>
                    </button>
                </div>
            </div>

            <div class="mb-4 form-check">
                <input type="checkbox" class="form-check-input mt-1" id="remember" name="remember">
                <label class="form-check-label user-select-none ms-1" for="remember">
                    Simpan Sesi Login
                </label>
            </div>

            <div class="d-grid gap-2 mb-4 mt-2">
                <button type="submit" class="btn btn-cyber">
                    OTORISASI MASUK <i class="bi bi-arrow-right-short ms-1 fs-5 align-middle"></i>
                </button>
            </div>

            <div class="text-center">
                <a href="<?= base_url() ?>" class="link-sys" style="font-size: 0.85rem; color: var(--text-muted);">
                    <i class="bi bi-arrow-left-short me-1"></i> KEMBALI KE BERANDA
                </a>
            </div>
            
            <div class="sys-indicator">
                CONNECTION SECURED // V.1.0
            </div>
        </form>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');
        const toggleIcon = document.querySelector('#toggleIcon');

        togglePassword.addEventListener('click', function () {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);

            toggleIcon.classList.toggle('bi-eye');
            toggleIcon.classList.toggle('bi-eye-slash');
        });
    </script>
</body>

</html>