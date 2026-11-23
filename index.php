<?php
$platforms = [
    ['YouTube', 'fa-brands fa-youtube', true],
    ['YouTube Music', 'fa-solid fa-music', false],
    ['Instagram', 'fa-brands fa-instagram', false],
    ['TikTok', 'fa-brands fa-tiktok', false],
    ['Facebook', 'fa-brands fa-facebook-f', false],
    ['X', 'fa-brands fa-x-twitter', false],
    ['Vimeo', 'fa-brands fa-vimeo-v', false],
    ['SoundCloud', 'fa-brands fa-soundcloud', false],
    ['Audiomack', 'fas fa-music', true],
    ['Twitch', 'fa-brands fa-twitch', false],
    ['Dailymotion', 'fa-brands fa-dailymotion', false],
    ['Pinterest', 'fa-brands fa-pinterest', false],
];

$features = [
    ['fa-bolt', 'Lightning-fast engine', 'Powered by yt-dlp, downloads start instantly and pull the highest available quality without stalls.'],
    ['fa-window-maximize', 'Nothing to install', 'Open the site, paste a link, and go. No apps, extensions, accounts, or sketchy pop-ups — ever.'],
    ['fa-film', 'MP4 up to 1080p', 'Pick 360p for a quick save, 720p for everyday viewing, or Full HD 1080p when quality matters.'],
    ['fa-shield-halved', 'Private by design', 'No sign-up, no download history, no trackers. Files are wiped from our server within an hour.'],
    ['fa-infinity', 'Free & unlimited', 'No daily caps, no premium wall, no watermark. Download as many files as you need, whenever you need.'],
    ['fa-mobile-screen-button', 'Works everywhere', 'Built for every screen — phone, tablet, laptop, or desktop. Same speed, same simple flow.'],
];

$steps = [
    ['01', 'fa-link', 'Paste your link', 'Copy a YouTube URL and drop it into the box above. We auto-detect the link and pull the video details.', ['YouTube links', 'Auto-detect']],
    ['02', 'fa-sliders', 'Pick your format', 'Choose MP4 in 360p, 720p or 1080p, or extract the audio as a clean MP3 file.', ['MP4', 'MP3', '1080p']],
    ['03', 'fa-circle-down', 'Download instantly', 'Hit download and your file is prepared in seconds. It saves straight to your device.', ['No signup', 'No watermark']],
];

$reviews = [
    ['Sarah Johnson', 'Content Creator', 'SJ', 'a1', 5, 'I save reference videos for editing every day. DooVid is the first downloader that actually feels premium — fast, clean, and zero annoying pop-ups.'],
    ['Mike Chen', 'Podcast Producer', 'MC', 'a2', 5, 'The MP3 extraction is genuinely excellent. I pull full episodes and the audio quality is clean every single time. It has replaced three other tools for me.'],
    ['Emily Rodriguez', 'Teacher', 'ER', 'a3', 5, 'I use it on my phone between classes. Paste, tap, done — the whole thing takes seconds and my students can follow along without any setup.'],
];

$faqs = [
    ['Is DooVid really free?', 'Yes. Every download is free, with no daily limits, no account, and no premium tier. There are no watermarks or hidden fees either.'],
    ['Do I need to install anything?', 'No. DooVid runs entirely in your browser on phones, tablets, and computers. Just paste a link and download — nothing to install or update.'],
    ['Which platforms are supported right now?', 'YouTube and Audiomack are fully supported today. YouTube videos and Shorts, plus Audiomack tracks and search, are ready to download. Instagram, TikTok, Facebook and more are actively in development.'],
    ['What quality can I download?', 'You can choose MP4 at 360p, 720p or 1080p, or extract audio as MP3. Available resolutions depend on what the original upload provides.'],
    ['Are my downloads private?', 'Yes. We do not require an account and do not keep a history of what you download. Files are stored on the server only long enough to send them to you, then removed automatically.'],
    ['Does it work on mobile?', 'Absolutely. The interface is designed mobile-first and works in any modern browser. Downloads save to your device the same way as on desktop.'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DooVid.net — Fast, Free YouTube Video Downloader</title>
    <meta name="description" content="Download YouTube videos in MP4 up to 1080p or extract MP3 audio in seconds. Free, private, no signup, no limits.">
    <meta name="theme-color" content="#06060b">
    <meta property="og:title" content="DooVid.net — Fast, Free YouTube Video Downloader">
    <meta property="og:description" content="Paste a YouTube link and get MP4 or MP3 in seconds. Free, private, no signup.">
    <meta property="og:type" content="website">
    <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'%3E%3Crect width='64' height='64' rx='16' fill='%23ff5a1f'/%3E%3Cpath d='M32 14v24m0 0 9-9m-9 9-9-9M16 45v3a5 5 0 0 0 5 5h22a5 5 0 0 0 5-5v-3' stroke='white' stroke-width='4' fill='none' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@600;700;800&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="index.css">
</head>
<body>

    <!-- Ambient background -->
    <div class="bg-fx" aria-hidden="true">
        <span class="orb orb-1"></span>
        <span class="orb orb-2"></span>
        <span class="orb orb-3"></span>
    </div>

    <!-- Preloader -->
    <div class="preloader" id="preloader" aria-hidden="true">
        <div class="preloader-inner">
            <span class="logo-mark">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M12 3v11m0 0 4.4-4.4M12 14 7.6 9.6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M4 16.5V18a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-1.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </span>
            <span class="preloader-word">DooVid<span>.net</span></span>
            <div class="preloader-bar"><span></span></div>
        </div>
    </div>

    <!-- Header -->
    <header class="site-header" id="siteHeader">
        <div class="container header-inner">
            <a class="logo" href="#home" aria-label="DooVid.net home">
                <span class="logo-mark">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M12 3v11m0 0 4.4-4.4M12 14 7.6 9.6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M4 16.5V18a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-1.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </span>
                <span class="logo-text">DooVid<span>.net</span></span>
            </a>

            <nav class="nav-links" aria-label="Main navigation">
                <a href="#features">Features</a>
                <a href="#how-it-works">How it works</a>
                <a href="#reviews">Reviews</a>
                <a href="#faq">FAQ</a>
            </nav>

            <div class="header-actions">
                <div class="lang" id="lang">
                    <button class="lang-btn" type="button" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-globe"></i>
                        <span class="lang-label">EN</span>
                        <i class="fas fa-chevron-down chev"></i>
                    </button>
                    <div class="lang-menu" role="menu">
                        <button type="button" class="selected" data-lang="EN" role="menuitem">English</button>
                        <button type="button" data-lang="RW" role="menuitem">Kinyarwanda</button>
                        <button type="button" data-lang="FR" role="menuitem">Français</button>
                    </div>
                </div>
                <a href="#downloader" class="btn btn-primary btn-sm" data-focus-search>
                    Start free <i class="fas fa-arrow-right"></i>
                </a>
                <button class="nav-toggle" id="navToggle" type="button" aria-label="Open menu" aria-expanded="false" aria-controls="mobileMenu">
                    <span></span><span></span><span></span>
                </button>
            </div>
        </div>

        <div class="mobile-menu" id="mobileMenu">
            <a href="#features"><i class="fas fa-layer-group"></i> Features</a>
            <a href="#how-it-works"><i class="fas fa-wand-magic-sparkles"></i> How it works</a>
            <a href="#reviews"><i class="fas fa-star"></i> Reviews</a>
            <a href="#faq"><i class="fas fa-circle-question"></i> FAQ</a>
            <a href="#downloader" class="btn btn-primary" data-focus-search>
                Start free <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </header>

    <main id="home">
        <!-- Hero -->
        <section class="hero">
            <div class="float-card fc-1" aria-hidden="true">
                <span class="fc-icon youtube"><i class="fab fa-youtube"></i></span>
                <div>
                    <strong>1080p selected</strong>
                    <span>MP4 · 84.2 MB</span>
                </div>
            </div>
            <div class="float-card fc-2" aria-hidden="true">
                <span class="fc-icon audio"><i class="fas fa-music"></i></span>
                <div>
                    <strong>MP3 extracted</strong>
                    <span>320 kbps · 4.1 MB</span>
                </div>
            </div>
            <div class="float-card fc-3" aria-hidden="true">
                <span class="bars"><i></i><i></i><i></i><i></i></span>
                <div>
                    <strong>18.4 MB/s</strong>
                    <span>Peak download speed</span>
                </div>
            </div>

            <div class="container hero-inner">
                <span class="pill" data-reveal>
                    <span class="live-dot"></span>
                    Free forever · No signup required
                </span>

                <h1 data-reveal style="--d:.06s">
                    Download YouTube videos at the
                    <span class="gradient-text">speed of light</span>
                </h1>

                <p class="hero-sub" data-reveal style="--d:.12s">
                    Paste any YouTube link and get crisp <strong>MP4</strong> or <strong>MP3</strong> in seconds —
                    up to <strong>1080p</strong>, with no ads, no watermarks, and no limits.
                </p>

                <div class="search-panel" id="downloader" data-reveal style="--d:.18s">
                    <div class="platform-toggle" role="tablist" aria-label="Choose platform">
                        <button type="button" class="platform-tab active" data-platform="youtube" role="tab" aria-selected="true">
                            <i class="fab fa-youtube"></i> YouTube
                        </button>
                        <button type="button" class="platform-tab" data-platform="audiomack" role="tab" aria-selected="false">
                            <i class="fas fa-music"></i> Audiomack
                        </button>
                    </div>
                    <form class="search-shell" id="searchForm" novalidate>
                        <span class="search-icon" aria-hidden="true"><i class="fas fa-link"></i></span>
                        <input
                            type="text"
                            class="inpu"
                            id="videoUrl"
                            placeholder="Paste a YouTube link or search by title…"
                            autocomplete="off"
                            autocapitalize="off"
                            spellcheck="false"
                            aria-label="YouTube link or search query"
                        >
                        <button class="paste-btn" id="pasteBtn" type="button" title="Paste from clipboard">
                            <i class="fas fa-paste"></i> Paste
                        </button>
                        <button class="searc" type="submit">
                            <span>Download</span>
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    </form>

                    <div class="search-hint">
                        <span class="hint-left" id="hintLeft">
                            <i class="fas fa-circle-info"></i>
                            <span id="searchHintText">Tip: paste a YouTube link to auto-detect it</span>
                        </span>
                        <span class="hint-formats">
                            <span>MP4</span>
                            <span>MP3</span>
                            <span>1080p</span>
                        </span>
                    </div>
                </div>

                <div class="result" id="result" aria-live="polite"></div>

                <div class="hero-trust" data-reveal style="--d:.24s">
                    <div class="avatars" aria-hidden="true">
                        <span class="avatar a1">SJ</span>
                        <span class="avatar a2">MC</span>
                        <span class="avatar a3">ER</span>
                        <span class="avatar a4">AK</span>
                    </div>
                    <span class="trust-stars" aria-label="Rated 4.9 out of 5">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-stroke"></i>
                        <strong>4.9/5</strong>
                    </span>
                    <span>Loved by 500,000+ creators worldwide</span>
                </div>
            </div>
        </section>

        <!-- Stats -->
        <section class="stats-section" aria-label="DooVid in numbers">
            <div class="container">
                <div class="stats-band glass" data-reveal>
                    <div class="stat">
                        <span class="stat-value" data-count="10" data-suffix="M+">0</span>
                        <span class="stat-label">Files delivered</span>
                    </div>
                    <div class="stat">
                        <span class="stat-value" data-count="500" data-suffix="K+">0</span>
                        <span class="stat-label">Happy creators</span>
                    </div>
                    <div class="stat">
                        <span class="stat-value" data-count="100" data-suffix="%">0</span>
                        <span class="stat-label">Free, forever</span>
                    </div>
                    <div class="stat">
                        <span class="stat-value" data-count="4.9" data-decimals="1" data-suffix="/5">0</span>
                        <span class="stat-label">Average rating</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Platforms -->
        <section class="platforms" aria-label="Supported platforms">
            <div class="container strip-head">
                <span class="live-now"><span class="live-dot"></span> YouTube is live now</span>
                <span>More platforms rolling out soon</span>
            </div>
            <div class="marquee">
                <div class="marquee-track">
                    <?php for ($copy = 0; $copy < 2; $copy++): ?>
                        <div class="marquee-group" <?php echo $copy === 1 ? 'aria-hidden="true"' : ''; ?>>
                            <?php foreach ($platforms as $platform): ?>
                                <span class="platform-chip<?php echo $platform[2] ? ' available' : ''; ?>">
                                    <i class="<?php echo $platform[1]; ?>"></i>
                                    <?php echo $platform[0]; ?>
                                    <?php if (!$platform[2]): ?><span class="soon">Soon</span><?php endif; ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>
        </section>

        <!-- Features -->
        <section class="section" id="features">
            <div class="container">
                <div class="section-head" data-reveal>
                    <span class="eyebrow">Why DooVid</span>
                    <h2>A downloader that respects your time</h2>
                    <p>Every detail is built around one goal: getting the file you want onto your device with as little friction as possible.</p>
                </div>

                <div class="features-grid">
                    <?php foreach ($features as $index => $feature): ?>
                        <article class="feature-card glass spotlight" data-reveal style="--d:<?php echo ($index % 3) * 0.06; ?>s">
                            <span class="feature-icon"><i class="fas <?php echo $feature[0]; ?>"></i></span>
                            <h3><?php echo $feature[1]; ?></h3>
                            <p><?php echo $feature[2]; ?></p>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- How it works -->
        <section class="section steps-section" id="how-it-works">
            <div class="container">
                <div class="section-head" data-reveal>
                    <span class="eyebrow">How it works</span>
                    <h2>Three steps. Under ten seconds.</h2>
                    <p>No tutorial needed. If you can copy and paste, you can download.</p>
                </div>

                <div class="steps">
                    <?php foreach ($steps as $index => $step): ?>
                        <article class="step-card glass spotlight" data-reveal style="--d:<?php echo $index * 0.08; ?>s">
                            <div class="step-top">
                                <span class="step-num"><?php echo $step[0]; ?></span>
                                <i class="fas <?php echo $step[1]; ?>"></i>
                            </div>
                            <h3><?php echo $step[2]; ?></h3>
                            <p><?php echo $step[3]; ?></p>
                            <div class="step-tags">
                                <?php foreach ($step[4] as $tag): ?>
                                    <span><?php echo $tag; ?></span>
                                <?php endforeach; ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- Showcase -->
        <section class="section" id="experience">
            <div class="container showcase">
                <div class="showcase-copy" data-reveal>
                    <span class="eyebrow" style="justify-content:flex-start">The experience</span>
                    <h2>Built to feel effortless, from link to download</h2>
                    <p>
                        No redirects, no countdown timers, no fake download buttons. Just a clean preview of
                        your video, the formats available, and one tap to save it.
                    </p>
                    <ul class="check-list">
                        <li><span class="check"><i class="fas fa-check"></i></span> Instant link detection with a rich video preview</li>
                        <li><span class="check"><i class="fas fa-check"></i></span> Clear format and quality choices — no guessing</li>
                        <li><span class="check"><i class="fas fa-check"></i></span> Live progress feedback while your file is prepared</li>
                        <li><span class="check"><i class="fas fa-check"></i></span> Files auto-deleted from our servers within an hour</li>
                    </ul>
                    <a href="#downloader" class="btn btn-primary" data-focus-search>
                        Try it now <i class="fas fa-arrow-right"></i>
                    </a>
                </div>

                <div class="showcase-visual" data-reveal style="--d:.1s">
                    <div class="showcase-card glass">
                        <div class="mock-window">
                            <div class="mock-topbar" aria-hidden="true">
                                <i></i><i></i><i></i>
                                <span>DOOVID.NET</span>
                            </div>
                            <div class="mock-body">
                                <div class="mock-thumb">
                                    <span class="mock-play"><i class="fas fa-play"></i></span>
                                    <span class="mock-time">12:48</span>
                                </div>
                                <div class="mock-title"></div>
                                <div class="mock-sub"></div>
                                <div class="mock-options" aria-hidden="true">
                                    <span class="mock-option">MP3</span>
                                    <span class="mock-option active">720p</span>
                                    <span class="mock-option">1080p</span>
                                </div>
                                <div class="mock-download"><i class="fas fa-download"></i> Download · 24.6 MB</div>
                                <div class="mock-progress" aria-hidden="true">
                                    <span class="ring"></span>
                                    <span class="lines"><span></span><span></span></span>
                                    <small>READY</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Reviews -->
        <section class="section" id="reviews">
            <div class="container">
                <div class="section-head" data-reveal>
                    <span class="eyebrow">Reviews</span>
                    <h2>Creators keep coming back</h2>
                    <p>Join half a million people who save videos, audio and inspiration with DooVid every month.</p>
                </div>

                <div class="reviews-grid">
                    <?php foreach ($reviews as $index => $review): ?>
                        <article class="review-card glass" data-reveal style="--d:<?php echo $index * 0.07; ?>s">
                            <span class="quote-mark" aria-hidden="true">&ldquo;</span>
                            <div class="review-stars" aria-label="<?php echo $review[4]; ?> out of 5 stars">
                                <?php for ($star = 0; $star < $review[4]; $star++): ?>
                                    <i class="fas fa-star"></i>
                                <?php endfor; ?>
                            </div>
                            <blockquote>&ldquo;<?php echo $review[5]; ?>&rdquo;</blockquote>
                            <div class="review-person">
                                <span class="avatar <?php echo $review[3]; ?>"><?php echo $review[2]; ?></span>
                                <div>
                                    <strong><?php echo $review[0]; ?></strong>
                                    <span><?php echo $review[1]; ?></span>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- FAQ -->
        <section class="section" id="faq">
            <div class="container">
                <div class="section-head" data-reveal>
                    <span class="eyebrow">FAQ</span>
                    <h2>Questions, answered</h2>
                    <p>Everything you need to know before your first download.</p>
                </div>

                <div class="faq-wrap">
                    <?php foreach ($faqs as $index => $faq): ?>
                        <details class="faq-item" data-reveal style="--d:<?php echo $index * 0.04; ?>s" <?php echo $index === 0 ? 'open' : ''; ?>>
                            <summary>
                                <?php echo $faq[0]; ?>
                                <span class="faq-icon"><i class="fas fa-plus"></i></span>
                            </summary>
                            <div class="faq-answer"><?php echo $faq[1]; ?></div>
                        </details>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="cta-section">
            <div class="container">
                <div class="cta-panel" data-reveal>
                    <span class="eyebrow">Ready when you are</span>
                    <h2>Your next download is one paste away</h2>
                    <p>No signup. No credit card. Just paste a YouTube link and get your file in seconds.</p>
                    <div class="cta-actions">
                        <a href="#downloader" class="btn btn-primary" data-focus-search>
                            <i class="fas fa-download"></i> Start downloading
                        </a>
                        <a href="#features" class="btn btn-ghost">Explore features</a>
                    </div>
                    <div class="cta-note">
                        <span><i class="fas fa-circle-check"></i> 100% free</span>
                        <span><i class="fas fa-circle-check"></i> No account needed</span>
                        <span><i class="fas fa-circle-check"></i> Files auto-deleted</span>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-brand">
                    <a class="logo" href="#home" aria-label="DooVid.net home">
                        <span class="logo-mark">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M12 3v11m0 0 4.4-4.4M12 14 7.6 9.6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M4 16.5V18a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-1.5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </span>
                        <span class="logo-text">DooVid<span>.net</span></span>
                    </a>
                    <p>The fastest, cleanest way to save YouTube videos and audio. Built for creators who value their time.</p>
                    <div class="footer-socials">
                        <a href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                        <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="#" aria-label="TikTok"><i class="fab fa-tiktok"></i></a>
                        <a href="#" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>

                <div class="footer-col">
                    <h4>Product</h4>
                    <ul>
                        <li><a href="#downloader">Video downloader</a></li>
                        <li><a href="#downloader">YouTube to MP3</a></li>
                        <li><a href="#downloader">YouTube Shorts</a></li>
                        <li><a href="#features">All features</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h4>Resources</h4>
                    <ul>
                        <li><a href="#how-it-works">How it works</a></li>
                        <li><a href="#reviews">Reviews</a></li>
                        <li><a href="#faq">Help &amp; FAQ</a></li>
                        <li><a href="#experience">Why DooVid</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h4>Company</h4>
                    <ul>
                        <li><a href="#">About us</a></li>
                        <li><a href="#">Contact</a></li>
                        <li><a href="#">Privacy policy</a></li>
                        <li><a href="#">Terms of service</a></li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <span>&copy; <?php echo date('Y'); ?> DooVid.net. All rights reserved.</span>
                <span class="status-pill"><span class="live-dot"></span> All systems operational</span>
                <span class="made">Made with <i class="fas fa-heart"></i> for creators</span>
            </div>
        </div>
    </footer>

    <div class="toast-wrap" id="toastWrap" aria-live="polite" aria-atomic="true"></div>

    <script src="index.js" defer></script>
</body>
</html>
