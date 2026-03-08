<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Sistem Dalam Pemeliharaan | <?= esc($settings['nama_web'] ?? 'Akses Dibatasi') ?></title>

    <?php if (!empty($settings['logo_image'])): ?>
        <link rel="icon" href="<?= base_url('uploads/logo/' . esc($settings['logo_image'], 'url')) ?>" type="image/webp">
    <?php else: ?>
        <link rel="icon" href="<?= base_url('favicon.ico') ?>" type="image/x-icon">
    <?php endif; ?>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg-color: #07090f;
            --grid-color: rgba(14, 165, 233, 0.07);
            --card-bg: rgba(15, 23, 42, 0.6);
            --card-border: rgba(56, 189, 248, 0.2);
            --accent-cyan: #0ea5e9;
            --accent-glow: #38bdf8;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image:
                linear-gradient(var(--grid-color) 1px, transparent 1px),
                linear-gradient(90deg, var(--grid-color) 1px, transparent 1px);
            background-size: 40px 40px;
            background-position: center center;
            z-index: 0;
            animation: panGrid 20s linear infinite;
        }

        .glow-background {
            position: absolute;
            width: 60vw;
            height: 60vw;
            max-width: 600px;
            max-height: 600px;
            background: radial-gradient(circle, rgba(14, 165, 233, 0.15) 0%, rgba(7, 9, 15, 0) 70%);
            border-radius: 50%;
            z-index: 1;
            animation: pulseGlow 4s ease-in-out infinite alternate;
        }

        .maintenance-card {
            position: relative;
            z-index: 2;
            background: var(--card-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--card-border);
            border-radius: 24px;
            padding: 50px 40px;
            max-width: 560px;
            width: 90%;
            text-align: center;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5),
                inset 0 1px 0 rgba(255, 255, 255, 0.05);
        }

        .system-icon {
            position: relative;
            width: 110px;
            height: 110px;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .system-icon::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            border: 2px dashed var(--accent-cyan);
            border-radius: 50%;
            animation: spin 10s linear infinite;
            opacity: 0.5;
        }

        .system-icon::after {
            content: '';
            position: absolute;
            width: 125%;
            height: 125%;
            border: 1px solid rgba(56, 189, 248, 0.3);
            border-radius: 50%;
            animation: spinReverse 15s linear infinite;
        }

        .system-icon img {
            width: 60px;
            height: 60px;
            object-fit: contain;
            filter: drop-shadow(0 0 15px rgba(255, 255, 255, 0.3));
            animation: float 3s ease-in-out infinite;
            z-index: 10;
        }

        .system-icon svg {
            width: 44px;
            height: 44px;
            color: var(--accent-glow);
            filter: drop-shadow(0 0 10px var(--accent-glow));
            animation: float 3s ease-in-out infinite;
            z-index: 10;
        }

        /* Styling Baru: Nama Website (Brand) */
        .brand-name {
            font-size: 1.1rem;
            font-weight: 800;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: var(--accent-glow);
            margin-bottom: 25px;
            text-shadow: 0 0 10px rgba(56, 189, 248, 0.4);
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(14, 165, 233, 0.1);
            border: 1px solid rgba(14, 165, 233, 0.2);
            color: var(--accent-glow);
            padding: 6px 16px;
            border-radius: 50px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.8rem;
            letter-spacing: 1px;
            margin-bottom: 24px;
            text-transform: uppercase;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            background-color: var(--accent-glow);
            border-radius: 50%;
            box-shadow: 0 0 8px var(--accent-glow);
            animation: blink 1.5s infinite;
        }

        h1 {
            font-size: 2rem;
            font-weight: 800;
            letter-spacing: -0.5px;
            margin-bottom: 16px;
            background: linear-gradient(135deg, #fff 0%, #94a3b8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        p {
            font-size: 1.05rem;
            line-height: 1.6;
            color: var(--text-muted);
            margin-bottom: 35px;
            font-weight: 300;
        }

        .btn-check {
            display: inline-block;
            background: transparent;
            color: var(--text-main);
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.9rem;
            text-decoration: none;
            padding: 12px 28px;
            border: 1px solid var(--accent-cyan);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            box-shadow: 0 0 15px rgba(14, 165, 233, 0.1);
        }

        .btn-check:hover {
            background: var(--accent-cyan);
            color: #000;
            box-shadow: 0 0 25px rgba(14, 165, 233, 0.4);
            font-weight: 700;
        }

        /* Animations */
        @keyframes panGrid {
            0% {
                background-position: 0 0;
            }

            100% {
                background-position: 40px 40px;
            }
        }

        @keyframes pulseGlow {
            0% {
                transform: scale(0.95);
                opacity: 0.8;
            }

            100% {
                transform: scale(1.05);
                opacity: 1;
            }
        }

        @keyframes spin {
            100% {
                transform: rotate(360deg);
            }
        }

        @keyframes spinReverse {
            100% {
                transform: rotate(-360deg);
            }
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-5px);
            }
        }

        @keyframes blink {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.3;
            }
        }

        @media (max-width: 600px) {
            h1 {
                font-size: 1.6rem;
            }

            p {
                font-size: 0.95rem;
            }

            .maintenance-card {
                padding: 40px 20px;
            }
        }
    </style>
</head>

<body>

    <div class="glow-background"></div>

    <div class="maintenance-card">

        <div class="system-icon">
            <?php if (!empty($settings['logo_image'])): ?>
                <img src="<?= base_url('uploads/logo/' . esc($settings['logo_image'], 'url')) ?>" alt="<?= esc($settings['nama_web'] ?? 'Logo') ?>">
            <?php else: ?>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 3v1.5M4.5 8.25H3m18 0h-1.5M4.5 12H3m18 0h-1.5m-15 3.75H3m18 0h-1.5M8.25 19.5V21M12 3v1.5m0 15V21m3.75-18v1.5m0 15V21m-9-1.5h10.5a2.25 2.25 0 002.25-2.25V6.75a2.25 2.25 0 00-2.25-2.25H6.75A2.25 2.25 0 004.5 6.75v10.5a2.25 2.25 0 002.25 2.25zm.75-12h9v9h-9v-9z" />
                </svg>
            <?php endif; ?>
        </div>

        <div class="brand-name">
            <?= esc($settings['nama_web'] ?? 'Sistem Internal') ?>
        </div>

        <div class="status-badge">
            <div class="status-dot"></div>
            SYSTEM_UPGRADE_IN_PROGRESS
        </div>

        <h1>Peningkatan Infrastruktur</h1>

        <p>Akses ke sistem publik saat ini sedang ditangguhkan. Kami sedang menerapkan pembaruan inti dan optimalisasi server. Silakan kembali dalam beberapa saat.</p>

        <a href="javascript:location.reload()" class="btn-check">
            > INITIALIZE_RETRY()
        </a>
    </div>

</body>

</html>