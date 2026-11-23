/* ============================================================
   DooVid.net — homepage interactions
   Search · metadata preview · downloads · UI animations
   ============================================================ */
(function () {
  'use strict';

  const YOUTUBE_URL_REGEX =
    /^(?:https?:\/\/)?(?:www\.|m\.)?(?:youtube\.com\/(?:watch\?(?:.*&)?v=|shorts\/|embed\/|live\/|v\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/i;

  const AUDIOMACK_URL_REGEX =
    /^(?:https?:\/\/)?(?:www\.)?audiomack\.com\/(?:song\/[^\/\s]+\/[^\/\s]+|[^\/\s]+\/song\/[^\/\s]+)/i;

  const API_KEY = 'AIzaSyCWyL20troldScmfg0Gp0bLqdaxE3WmLMQ';
  const DEFAULT_HINT = 'Tip: paste a YouTube link to auto-detect it';

  const esc = (value) =>
    String(value == null ? '' : value).replace(/[&<>"']/g, (char) => ({
      '&': '&amp;',
      '<': '&lt;',
      '>': '&gt;',
      '"': '&quot;',
      "'": '&#39;',
    }[char]));

  const $ = (selector, context) => (context || document).querySelector(selector);
  const $$ = (selector, context) => Array.from((context || document).querySelectorAll(selector));

  const prefersReducedMotion = () =>
    window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  document.addEventListener('DOMContentLoaded', init);

  function init() {
    initPreloader();
    initHeader();
    initMobileNav();
    initLanguage();
    initReveal();
    initCounters();
    initSpotlight();
    initSearch();
    initFaq();
    initFocusSearch();
  }

  /* ----------------------------------------------------------
     Preloader
     ---------------------------------------------------------- */
  function initPreloader() {
    const preloader = $('#preloader');
    if (!preloader) return;

    const hide = () => {
      if (preloader.classList.contains('is-done')) return;
      preloader.classList.add('is-done');
      window.setTimeout(() => preloader.remove(), 650);
    };

    if (prefersReducedMotion()) {
      hide();
      return;
    }

    window.addEventListener('load', () => window.setTimeout(hide, 500));
    // Safety net in case the load event never fires.
    window.setTimeout(hide, 2600);
  }

  /* ----------------------------------------------------------
     Header / scroll state / active link
     ---------------------------------------------------------- */
  function initHeader() {
    const header = $('#siteHeader');
    if (!header) return;

    const onScroll = () => {
      header.classList.toggle('scrolled', window.scrollY > 12);
    };

    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });

    const sections = ['features', 'how-it-works', 'reviews', 'faq']
      .map((id) => document.getElementById(id))
      .filter(Boolean);
    const links = $$('.nav-links a');

    if (!sections.length || !('IntersectionObserver' in window)) return;

    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (!entry.isIntersecting) return;
          const hash = `#${entry.target.id}`;
          links.forEach((link) => {
            link.classList.toggle('active', link.getAttribute('href') === hash);
          });
        });
      },
      { rootMargin: '-38% 0px -55% 0px', threshold: 0 }
    );

    sections.forEach((section) => observer.observe(section));
  }

  /* ----------------------------------------------------------
     Mobile navigation
     ---------------------------------------------------------- */
  function initMobileNav() {
    const toggle = $('#navToggle');
    const menu = $('#mobileMenu');
    if (!toggle || !menu) return;

    const setOpen = (open) => {
      toggle.setAttribute('aria-expanded', String(open));
      toggle.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
      menu.classList.toggle('open', open);
    };

    toggle.addEventListener('click', () => {
      setOpen(toggle.getAttribute('aria-expanded') !== 'true');
    });

    menu.addEventListener('click', (event) => {
      if (event.target.closest('a')) setOpen(false);
    });

    document.addEventListener('click', (event) => {
      if (!menu.classList.contains('open')) return;
      if (menu.contains(event.target) || toggle.contains(event.target)) return;
      setOpen(false);
    });

    document.addEventListener('keydown', (event) => {
      if (event.key === 'Escape') setOpen(false);
    });

    window.addEventListener('resize', () => {
      if (window.innerWidth > 1024) setOpen(false);
    });
  }

  /* ----------------------------------------------------------
     Language dropdown (visual only for now)
     ---------------------------------------------------------- */
  function initLanguage() {
    const lang = $('#lang');
    if (!lang) return;

    const button = $('.lang-btn', lang);
    const menu = $('.lang-menu', lang);
    if (!button || !menu) return;

    const close = () => {
      lang.classList.remove('open');
      button.setAttribute('aria-expanded', 'false');
    };

    button.addEventListener('click', (event) => {
      event.stopPropagation();
      const open = !lang.classList.contains('open');
      lang.classList.toggle('open', open);
      button.setAttribute('aria-expanded', String(open));
    });

    menu.addEventListener('click', (event) => {
      const option = event.target.closest('[data-lang]');
      if (!option) return;

      if (option.dataset.lang !== 'EN') {
        showToast(`${option.textContent.trim()} is coming soon — English stays active.`);
      }
      close();
    });

    document.addEventListener('click', (event) => {
      if (!lang.contains(event.target)) close();
    });
  }

  /* ----------------------------------------------------------
     Scroll reveal
     ---------------------------------------------------------- */
  function initReveal() {
    const items = $$('[data-reveal]');
    if (!items.length) return;

    if (!('IntersectionObserver' in window) || prefersReducedMotion()) {
      items.forEach((item) => item.classList.add('is-visible'));
      return;
    }

    const observer = new IntersectionObserver(
      (entries, obs) => {
        entries.forEach((entry) => {
          if (!entry.isIntersecting) return;
          entry.target.classList.add('is-visible');
          obs.unobserve(entry.target);
        });
      },
      { threshold: 0.12, rootMargin: '0px 0px -50px 0px' }
    );

    items.forEach((item) => observer.observe(item));
  }

  /* ----------------------------------------------------------
     Animated counters
     ---------------------------------------------------------- */
  function initCounters() {
    const counters = $$('[data-count]');
    if (!counters.length) return;

    const run = (el) => {
      if (el.dataset.done) return;
      el.dataset.done = '1';

      const target = parseFloat(el.dataset.count) || 0;
      const decimals = parseInt(el.dataset.decimals || '0', 10);
      const suffix = el.dataset.suffix || '';
      const duration = 1500;
      const start = performance.now();

      if (prefersReducedMotion()) {
        el.textContent = target.toFixed(decimals) + suffix;
        return;
      }

      const tick = (now) => {
        const progress = Math.min((now - start) / duration, 1);
        const eased = 1 - Math.pow(1 - progress, 3);
        el.textContent = (target * eased).toFixed(decimals) + suffix;
        if (progress < 1) requestAnimationFrame(tick);
      };

      requestAnimationFrame(tick);
    };

    if (!('IntersectionObserver' in window)) {
      counters.forEach(run);
      return;
    }

    const observer = new IntersectionObserver(
      (entries, obs) => {
        entries.forEach((entry) => {
          if (!entry.isIntersecting) return;
          run(entry.target);
          obs.unobserve(entry.target);
        });
      },
      { threshold: 0.5 }
    );

    counters.forEach((counter) => observer.observe(counter));
  }

  /* ----------------------------------------------------------
     Pointer spotlight on glass cards
     ---------------------------------------------------------- */
  function initSpotlight() {
    if (window.matchMedia && window.matchMedia('(hover: none)').matches) return;

    $$('.spotlight').forEach((card) => {
      card.addEventListener('pointermove', (event) => {
        const rect = card.getBoundingClientRect();
        card.style.setProperty('--mx', `${event.clientX - rect.left}px`);
        card.style.setProperty('--my', `${event.clientY - rect.top}px`);
      });
    });
  }

  /* ----------------------------------------------------------
     Search + video preview
     ---------------------------------------------------------- */
  function initSearch() {
    const form = $('#searchForm');
    const input = $('#videoUrl') || $('.inpu');
    const result = $('#result') || $('.result');
    if (!form || !input || !result) return;

    const pasteBtn = $('#pasteBtn');
    const hintLeft = $('#hintLeft');
    const hintText = $('#searchHintText');

    let loading = false;
    let lastPreviewUrl = '';
    let lastAutoPaste = '';
    let currentPlatform = 'youtube';

    // Platform toggle
    $$('.platform-tab').forEach((tab) => {
      tab.addEventListener('click', () => {
        currentPlatform = tab.dataset.platform;
        $$('.platform-tab').forEach((t) => {
          t.classList.toggle('active', t === tab);
          t.setAttribute('aria-selected', String(t === tab));
        });
        if (currentPlatform === 'audiomack') {
          input.placeholder = 'Paste an Audiomack link or search by title…';
          input.setAttribute('aria-label', 'Audiomack link or search query');
          setHint('Tip: paste an Audiomack link to auto-detect it', false);
        } else {
          input.placeholder = 'Paste a YouTube link or search by title…';
          input.setAttribute('aria-label', 'YouTube link or search query');
          setHint(DEFAULT_HINT, false);
        }
        result.innerHTML = '';
        lastPreviewUrl = '';
      });
    });

    const isAudiomackUrl = (text) => AUDIOMACK_URL_REGEX.test(text);

    const isYouTubeUrl = (text) => YOUTUBE_URL_REGEX.test(text);

    const setHint = (text, detected) => {
      if (hintText) hintText.textContent = text;
      if (hintLeft) hintLeft.classList.toggle('is-detected', Boolean(detected));
    };

    const resetHint = () => setHint(DEFAULT_HINT, false);

    const refreshHint = () => {
      const value = input.value.trim();

      if (!value) {
        resetHint();
      } else if (currentPlatform === 'audiomack') {
        if (isAudiomackUrl(value)) {
          setHint('Audiomack link detected — ready to download', true);
        } else if (/^https?:\/\//i.test(value)) {
          setHint('Only Audiomack links are supported here', false);
        } else {
          setHint('Press Download to search Audiomack for this title', false);
        }
      } else {
        if (isYouTubeUrl(value)) {
          setHint('YouTube link detected — ready to download', true);
        } else if (/^https?:\/\//i.test(value)) {
          setHint('Only YouTube links are supported right now', false);
        } else {
          setHint('Press Download to search YouTube for this title', false);
        }
      }
    };

    const flagError = (message) => {
      form.classList.remove('is-error');
      // Force reflow so the shake animation can replay.
      void form.offsetWidth;
      form.classList.add('is-error');
      input.focus();
      showToast(message, 'error');
    };

    const syncScrollLock = () => {
      document.body.classList.toggle('modal-open', Boolean(document.querySelector('.modal-overlay')));
    };

    const showStatus = (title, note) => {
      result.innerHTML = `
        <div class="search-status">
          <span class="ring" aria-hidden="true"></span>
          <span>${esc(title)}</span>
          ${note ? `<small>${esc(note)}</small>` : ''}
        </div>`;
    };

    const showSkeleton = (mode) => {
      if (mode === 'preview') {
        showStatus('Reading your video…', 'This usually takes a few seconds.');
        return;
      }

      result.innerHTML = `
        <div class="skeleton-grid" aria-hidden="true">
          ${Array.from({ length: 6 }).map(() => `
            <div class="skeleton-card">
              <div class="sk sk-thumb"></div>
              <div class="sk sk-line w-80"></div>
              <div class="sk sk-line w-50"></div>
              <div class="sk-actions">
                <div class="sk sk-btn"></div>
                <div class="sk sk-btn"></div>
              </div>
            </div>`).join('')}
        </div>`;
    };

    const showError = (title, message) => {
      result.innerHTML = `
        <div class="result-error" role="alert">
          <i class="fas fa-triangle-exclamation" aria-hidden="true"></i>
          <div>
            <strong>${esc(title)}</strong>
            ${esc(message)}
          </div>
        </div>`;
    };

    const renderPreview = (video) => {
      const id = esc(video.id || '');
      const title = esc(video.title || 'Unknown video');
      const channel = esc(video.channel || 'Unknown channel');
      const thumbnail = esc(video.thumbnail || `https://i.ytimg.com/vi/${video.id}/hqdefault.jpg`);
      const fallback = esc(`https://i.ytimg.com/vi/${video.id}/hqdefault.jpg`);
      const duration = esc(video.duration || '');
      const views = esc(video.views || '');
      const uploadDate = esc(video.upload_date || '');
      const description = esc(video.description || '');

      result.innerHTML = `
        <article class="video-preview-card glass">
          <div class="preview-media">
            <div class="thumbnail-container">
              <img class="preview-thumbnail" src="${thumbnail}" alt="${title}" data-fallback="${fallback}">
              ${duration ? `<span class="duration-badge">${duration}</span>` : ''}
            </div>
          </div>

          <div class="preview-body">
            <span class="preview-source"><i class="fab fa-youtube"></i> YouTube</span>
            <h3 class="video-title">${title}</h3>
            <p class="channel-name"><i class="fas fa-circle-user"></i> ${channel}</p>
            <div class="video-stats">
              ${views ? `<span class="stat-item"><i class="fas fa-eye"></i> ${views}</span>` : ''}
              ${uploadDate ? `<span class="stat-item"><i class="fas fa-calendar-days"></i> ${uploadDate}</span>` : ''}
              <span class="stat-item"><i class="fas fa-shield-halved"></i> Safe &amp; private</span>
            </div>
            ${description ? `<p class="video-description">${description}</p>` : ''}
          </div>

          <div class="download-buttons">
            <button class="download-btn mp3-btn" type="button"
              data-download="audio" data-id="${id}" data-title="${title}">
              <i class="fas fa-music"></i> MP3 · Audio
            </button>
            <button class="download-btn" type="button"
              data-download="video" data-quality="360p" data-id="${id}" data-title="${title}">
              <i class="fas fa-video"></i> MP4 · 360p
            </button>
            <button class="download-btn" type="button"
              data-download="video" data-quality="720p" data-id="${id}" data-title="${title}">
              <i class="fas fa-video"></i> MP4 · 720p
            </button>
            <button class="download-btn hd-btn" type="button"
              data-download="video" data-quality="1080p" data-id="${id}" data-title="${title}">
              <i class="fas fa-gem"></i> MP4 · 1080p
            </button>
          </div>
        </article>`;

      attachImageFallbacks(result);
      syncScrollLock();
    };

    const renderSearchResults = (videos) => {
      result.innerHTML = `
        <div class="results-grid">
          ${videos.map((video) => {
            const snippet = video.snippet || {};
            const thumbs = snippet.thumbnails || {};
            const thumb = (thumbs.high || thumbs.medium || thumbs.default || {}).url || '';
            const videoId = (video.id || {}).videoId || '';
            const title = esc(snippet.title || 'Untitled video');
            const channel = esc(snippet.channelTitle || 'Unknown channel');
            const fallback = esc(`https://i.ytimg.com/vi/${videoId}/hqdefault.jpg`);

            return `
              <article class="result-card glass">
                <div class="result-thumb">
                  <img src="${esc(thumb)}" alt="${title}" loading="lazy" data-fallback="${fallback}">
                </div>
                <h4>${title}</h4>
                <p class="result-meta"><i class="fas fa-circle-user"></i> ${channel}</p>
                <div class="result-actions">
                  <button class="mini-btn primary" type="button"
                    data-quality-options data-id="${esc(videoId)}" data-title="${title}">
                    <i class="fas fa-video"></i> MP4
                  </button>
                  <button class="mini-btn" type="button"
                    data-download="audio" data-id="${esc(videoId)}" data-title="${title}">
                    <i class="fas fa-music"></i> MP3
                  </button>
                </div>
              </article>`;
          }).join('')}
        </div>`;

      attachImageFallbacks(result);
    };

    const renderAudiomackPreview = (track) => {
      const id = esc(track.id || '');
      const title = esc(track.title || 'Unknown track');
      const artist = esc(track.channel || track.artist || 'Unknown artist');
      const thumbnail = esc(track.thumbnail || '');
      const duration = esc(track.duration || '');
      const artistSlug = esc(track.artist_slug || '');
      const songSlug = esc(track.song_slug || track.url_slug || '');

      result.innerHTML = `
        <article class="video-preview-card glass">
          <div class="preview-media">
            <div class="thumbnail-container">
              <img class="preview-thumbnail" src="${thumbnail}" alt="${title}" data-fallback="${thumbnail}">
              ${duration ? `<span class="duration-badge">${duration}</span>` : ''}
            </div>
          </div>

          <div class="preview-body">
            <span class="preview-source"><i class="fas fa-music"></i> Audiomack</span>
            <h3 class="video-title">${title}</h3>
            <p class="channel-name"><i class="fas fa-circle-user"></i> ${artist}</p>
            <div class="video-stats">
              ${duration ? `<span class="stat-item"><i class="fas fa-clock"></i> ${duration}</span>` : ''}
              <span class="stat-item"><i class="fas fa-shield-halved"></i> Safe &amp; private</span>
            </div>
          </div>

          <div class="download-buttons">
            <button class="download-btn mp3-btn" type="button"
              data-download="audio" data-platform="audiomack" data-id="${id}"
              data-artist="${artistSlug}" data-slug="${songSlug}" data-title="${title}">
              <i class="fas fa-music"></i> M4A · Audio
            </button>
          </div>
        </article>`;

      attachImageFallbacks(result);
      syncScrollLock();
    };

    const renderAudiomackResults = (tracks) => {
      result.innerHTML = `
        <div class="results-grid">
          ${tracks.map((track) => {
            const title = esc(track.title || 'Untitled track');
            const artist = esc(track.artist || 'Unknown artist');
            const thumb = esc(track.thumbnail || '');
            const id = esc(track.id || '');
            const artistSlug = esc(track.artist_slug || '');
            const songSlug = esc(track.song_slug || '');
            const duration = esc(track.duration || '');
            const fallback = esc(track.thumbnail || '');

            return `
              <article class="result-card glass">
                <div class="result-thumb">
                  <img src="${thumb}" alt="${title}" loading="lazy" data-fallback="${fallback}">
                  ${duration ? `<span class="duration-badge">${duration}</span>` : ''}
                </div>
                <h4>${title}</h4>
                <p class="result-meta"><i class="fas fa-circle-user"></i> ${artist}</p>
                <div class="result-actions">
                  <button class="mini-btn primary" type="button"
                    data-download="audio" data-platform="audiomack" data-id="${id}"
                    data-artist="${artistSlug}" data-slug="${songSlug}" data-title="${title}">
                    <i class="fas fa-music"></i> M4A
                  </button>
                </div>
              </article>`;
          }).join('')}
        </div>`;

      attachImageFallbacks(result);
    };

    const handleYouTubeUrl = async (url) => {
      if (loading || url === lastPreviewUrl) return;
      loading = true;
      lastPreviewUrl = url;
      showSkeleton('preview');

      const controller = new AbortController();
      const timeout = window.setTimeout(() => controller.abort(), 45000);

      try {
        const response = await fetch(
          `download.php?type=metadata&url=${encodeURIComponent(url)}`,
          { headers: { Accept: 'application/json' }, signal: controller.signal }
        );

        if (!response.ok) throw new Error(`Request failed (${response.status})`);

        const data = await response.json();
        if (data.error) throw new Error(data.error);

        renderPreview(data);
      } catch (error) {
        lastPreviewUrl = '';
        if (error.name === 'AbortError') {
          showError('That took too long', 'The server could not reach YouTube in time. Please try again in a moment.');
        } else {
          showError('Could not read that video', error.message || 'Please check the link and try again.');
        }
      } finally {
        window.clearTimeout(timeout);
        loading = false;
      }
    };

    const handleAudiomackUrl = async (url) => {
      if (loading || url === lastPreviewUrl) return;
      loading = true;
      lastPreviewUrl = url;
      showSkeleton('preview');

      const controller = new AbortController();
      const timeout = window.setTimeout(() => controller.abort(), 45000);

      try {
        const response = await fetch(
          `download.php?type=metadata&url=${encodeURIComponent(url)}`,
          { headers: { Accept: 'application/json' }, signal: controller.signal }
        );

        if (!response.ok) throw new Error(`Request failed (${response.status})`);

        const data = await response.json();
        if (data.error) throw new Error(data.error);

        renderAudiomackPreview(data);
      } catch (error) {
        lastPreviewUrl = '';
        if (error.name === 'AbortError') {
          showError('That took too long', 'The server could not reach Audiomack in time. Please try again in a moment.');
        } else {
          showError('Could not read that track', error.message || 'Please check the link and try again.');
        }
      } finally {
        window.clearTimeout(timeout);
        loading = false;
      }
    };

    const searchAudiomack = async (query) => {
      if (loading) return;
      loading = true;
      lastPreviewUrl = '';
      showSkeleton('search');

      try {
        const response = await fetch(
          `download.php?type=audiomack_search&q=${encodeURIComponent(query)}`
        );
        const data = await response.json();

        if (!response.ok) {
          throw new Error((data.error && data.error.message) || 'Search is unavailable right now.');
        }
        if (!data.results || !data.results.length) {
          throw new Error('No tracks matched that search. Try different keywords.');
        }

        renderAudiomackResults(data.results);
        setHint('Select a result to download it', true);
      } catch (error) {
        showError('Search did not work', error.message || 'Please try again in a moment.');
      } finally {
        loading = false;
      }
    };

    const searchYouTube = async (query) => {
      if (loading) return;
      loading = true;
      lastPreviewUrl = '';
      showSkeleton('search');

      try {
        const response = await fetch(
          `https://www.googleapis.com/youtube/v3/search?part=snippet&type=video&maxResults=9&q=${encodeURIComponent(query)}&key=${API_KEY}`
        );
        const data = await response.json();

        if (!response.ok) {
          throw new Error((data.error && data.error.message) || 'Search is unavailable right now.');
        }
        if (!data.items || !data.items.length) {
          throw new Error('No videos matched that search. Try different keywords.');
        }

        renderSearchResults(data.items);
        setHint('Select a result to download it', true);
      } catch (error) {
        showError('Search did not work', error.message || 'Please try again in a moment.');
      } finally {
        loading = false;
      }
    };

    const runSearch = () => {
      const value = input.value.trim();

      if (!value) {
        flagError('Paste a link or type something to search first.');
        return;
      }

      if (currentPlatform === 'audiomack') {
        if (isAudiomackUrl(value)) {
          handleAudiomackUrl(value);
          return;
        }

        if (/^https?:\/\//i.test(value)) {
          showError('That link is not supported yet', 'Audiomack links work here. Paste a valid Audiomack track link.');
          setHint('Only Audiomack links are supported right now', false);
          return;
        }

        if (value.length < 2) {
          flagError('Add a few more characters to search.');
          return;
        }

        searchAudiomack(value);
        return;
      }

      if (isYouTubeUrl(value)) {
        handleYouTubeUrl(value);
        return;
      }

      if (/^https?:\/\//i.test(value)) {
        showError('That link is not supported yet', 'YouTube links work today. Instagram, TikTok and Facebook are coming soon.');
        setHint('Only YouTube links are supported right now', false);
        return;
      }

      if (value.length < 2) {
        flagError('Add a few more characters to search.');
        return;
      }

      searchYouTube(value);
    };

    form.addEventListener('submit', (event) => {
      event.preventDefault();
      runSearch();
    });

    input.addEventListener('input', () => {
      refreshHint();
      if (input.value.trim() !== lastPreviewUrl) lastPreviewUrl = '';
    });
    input.addEventListener('search', refreshHint);

    input.addEventListener('paste', () => {
      window.setTimeout(() => {
        refreshHint();
        const value = input.value.trim();
        if (!value || value === lastAutoPaste || loading) return;
        if (currentPlatform === 'audiomack' && isAudiomackUrl(value)) {
          lastAutoPaste = value;
          handleAudiomackUrl(value);
        } else if (currentPlatform !== 'audiomack' && isYouTubeUrl(value)) {
          lastAutoPaste = value;
          handleYouTubeUrl(value);
        }
      }, 60);
    });

    if (pasteBtn) {
      pasteBtn.addEventListener('click', async () => {
        if (!navigator.clipboard || !navigator.clipboard.readText) {
          showToast('Clipboard access is unavailable — use Ctrl+V instead.', 'error');
          input.focus();
          return;
        }

        try {
          const text = await navigator.clipboard.readText();
          if (!text || !text.trim()) {
            showToast('Your clipboard is empty.', 'error');
            input.focus();
            return;
          }

          input.value = text.trim();
          refreshHint();
          input.focus();

          if (currentPlatform === 'audiomack' && isAudiomackUrl(input.value)) {
            showToast('Link pasted — fetching track details.', 'success');
            handleAudiomackUrl(input.value);
          } else if (currentPlatform !== 'audiomack' && isYouTubeUrl(input.value)) {
            showToast('Link pasted — fetching video details.', 'success');
            handleYouTubeUrl(input.value);
          } else {
            showToast('Pasted. Hit Download to continue.');
          }
        } catch (error) {
          showToast('Clipboard access was blocked — use Ctrl+V instead.', 'error');
          input.focus();
        }
      });
    }

    // One delegated listener handles every button inside the results area.
    result.addEventListener('click', (event) => {
      const downloadBtn = event.target.closest('[data-download]');
      if (downloadBtn) {
        startDownload(
          downloadBtn.dataset.id,
          downloadBtn.dataset.download,
          downloadBtn.dataset.quality || '',
          downloadBtn.dataset.title || '',
          {
            platform: downloadBtn.dataset.platform || '',
            artist: downloadBtn.dataset.artist || '',
            slug: downloadBtn.dataset.slug || '',
          }
        );
        return;
      }

      const optionsBtn = event.target.closest('[data-quality-options]');
      if (optionsBtn) {
        openQualityModal(optionsBtn.dataset.id, optionsBtn.dataset.title || '');
      }
    });

    // Expose the API for any inline handlers left in older templates.
    window.downloadVideo = (id, quality, title) => startDownload(id, 'video', quality, title || '');
    window.downloadAudio = (id, title) => startDownload(id, 'audio', '', title || '');
    window.showVideoOptions = (id, title) => openQualityModal(id, title || '');
    window.closeModal = closeModals;
    window.cancelDownload = closeModals;
  }

  function attachImageFallbacks(scope) {
    $$('img[data-fallback]', scope).forEach((img) => {
      img.addEventListener('error', () => {
        if (img.dataset.fallback && img.src !== img.dataset.fallback) {
          img.src = img.dataset.fallback;
        }
      }, { once: true });
    });
  }

  /* ----------------------------------------------------------
     Quality picker
     ---------------------------------------------------------- */
  function openQualityModal(videoId, title) {
    if (!videoId) return;
    closeModals();

    const overlay = document.createElement('div');
    overlay.className = 'modal-overlay';
    overlay.id = 'qualityModal';
    overlay.innerHTML = `
      <div class="modal-box" role="dialog" aria-modal="true" aria-labelledby="qualityTitle">
        <button class="modal-close" type="button" data-modal-close aria-label="Close">
          <i class="fas fa-xmark"></i>
        </button>
        <div class="modal-icon"><i class="fas fa-sliders"></i></div>
        <span class="modal-eyebrow">Choose format</span>
        <h3 id="qualityTitle">Select download quality</h3>
        <p class="modal-sub">${esc(title || 'Pick the format that works best for you.')}</p>

        <div class="quality-grid">
          <button class="quality-option" type="button" data-quality="360p" data-id="${esc(videoId)}" data-title="${esc(title)}">
            <span class="q-icon"><i class="fas fa-video"></i></span>
            <span class="q-text"><strong>MP4 · 360p</strong><small>Fastest · smallest file</small></span>
            <i class="fas fa-chevron-right"></i>
          </button>
          <button class="quality-option" type="button" data-quality="720p" data-id="${esc(videoId)}" data-title="${esc(title)}">
            <span class="q-icon"><i class="fas fa-video"></i></span>
            <span class="q-text"><strong>MP4 · 720p HD</strong><small>Recommended balance</small></span>
            <i class="fas fa-chevron-right"></i>
          </button>
          <button class="quality-option" type="button" data-quality="1080p" data-id="${esc(videoId)}" data-title="${esc(title)}">
            <span class="q-icon"><i class="fas fa-gem"></i></span>
            <span class="q-text"><strong>MP4 · 1080p Full HD</strong><small>Best quality · larger file</small></span>
            <i class="fas fa-chevron-right"></i>
          </button>
          <button class="quality-option" type="button" data-quality="audio" data-id="${esc(videoId)}" data-title="${esc(title)}">
            <span class="q-icon"><i class="fas fa-music"></i></span>
            <span class="q-text"><strong>MP3 · Audio only</strong><small>Extract sound · small file</small></span>
            <i class="fas fa-chevron-right"></i>
          </button>
        </div>

        <button class="btn btn-ghost btn-block" type="button" data-modal-close>Cancel</button>
      </div>`;

    document.body.appendChild(overlay);
    syncModalState();

    overlay.addEventListener('click', (event) => {
      if (event.target === overlay) {
        closeModals();
        return;
      }

      if (event.target.closest('[data-modal-close]')) {
        closeModals();
        return;
      }

      const option = event.target.closest('[data-quality]');
      if (!option) return;

      const id = option.dataset.id;
      const quality = option.dataset.quality;
      const selectedTitle = option.dataset.title || '';

      closeModals();

      if (quality === 'audio') {
        startDownload(id, 'audio', '', selectedTitle);
      } else {
        startDownload(id, 'video', quality, selectedTitle);
      }
    });

    const closeBtn = $('.modal-close', overlay);
    if (closeBtn) closeBtn.focus({ preventScroll: true });
  }

  /* ----------------------------------------------------------
     Download flow
     ---------------------------------------------------------- */
  function startDownload(videoId, type, quality, title, extra) {
    extra = extra || {};
    const isAudioOnly = (type === 'audio') || (extra.platform === 'audiomack');
    if (!videoId && extra.platform !== 'audiomack') {
      showToast('That video could not be identified. Try another link.', 'error');
      return;
    }

    const params = new URLSearchParams({ type: type || 'video', id: videoId || '' });
    if (quality) params.set('quality', quality);
    if (extra.platform) params.set('platform', extra.platform);
    if (extra.artist) params.set('artist', extra.artist);
    if (extra.slug) params.set('slug', extra.slug);
    const url = `download.php?${params.toString()}`;

    showProgressModal(videoId || title, type, quality, title, extra.platform || '');

    // Same-tab navigation to a Content-Disposition attachment starts the
    // download without losing the page. A hidden anchor keeps it button-free.
    const link = document.createElement('a');
    link.href = url;
    link.style.display = 'none';
    document.body.appendChild(link);
    link.click();
    link.remove();
  }

  function showProgressModal(videoId, type, quality, title, platform) {
    closeModals();

    const isAudio = type === 'audio';
    const isAudiomack = platform === 'audiomack';
    const audioLabel = isAudiomack ? 'M4A audio' : 'MP3 audio';
    const overlay = document.createElement('div');
    overlay.className = 'modal-overlay';
    overlay.id = 'downloadModal';
    overlay.innerHTML = `
      <div class="modal-box progress-modal" role="dialog" aria-modal="true" aria-labelledby="progressTitle">
        <div class="modal-icon"><i class="fas fa-${isAudio ? 'music' : 'film'}"></i></div>
        <span class="modal-eyebrow">${isAudio ? audioLabel : 'MP4 video'}${quality ? ` · ${esc(quality)}` : ''}</span>
        <h3 id="progressTitle">Preparing your download</h3>
        <p class="modal-sub">${esc(title || 'Your file is on its way.')}</p>

        <div class="progress-bar">
          <div class="progress-fill" id="downloadProgressFill" style="width:0%"></div>
        </div>
        <div class="progress-meta">
          <span id="downloadProgressText">Connecting to YouTube…</span>
          <span id="downloadProgressPercent">0%</span>
        </div>

        <p class="progress-note">
          <i class="fas fa-circle-info"></i>
          Keep this tab open. Large videos can take a little longer to prepare.
        </p>

        <button class="btn btn-ghost btn-block" type="button" data-cancel-download>Cancel</button>
      </div>`;

    document.body.appendChild(overlay);
    syncModalState();

    const fill = $('#downloadProgressFill', overlay);
    const label = $('#downloadProgressText', overlay);
    const percent = $('#downloadProgressPercent', overlay);
    const modal = $('.modal-box', overlay);
    let progress = 0;
    let finished = false;
    let timer = null;

    const stages = isAudio
      ? [
          [0, 'Connecting to YouTube…'],
          [14, 'Reading audio stream…'],
          [32, 'Downloading audio…'],
          [58, 'Converting to MP3…'],
          [80, 'Finalizing file…'],
          [94, 'Almost there…'],
        ]
      : [
          [0, 'Connecting to YouTube…'],
          [12, 'Reading video info…'],
          [28, 'Downloading video stream…'],
          [55, 'Merging audio & video…'],
          [78, 'Finalizing MP4…'],
          [94, 'Almost there…'],
        ];

    const updateStage = () => {
      let text = stages[0][1];
      stages.forEach((stage) => {
        if (progress >= stage[0]) text = stage[1];
      });
      label.textContent = text;
      percent.textContent = `${Math.min(100, Math.round(progress))}%`;
      fill.style.width = `${Math.min(100, progress)}%`;
    };

    const finish = () => {
      if (finished) return;
      finished = true;
      window.clearInterval(timer);

      progress = 100;
      updateStage();

      modal.classList.add('is-done');
      const icon = $('.modal-icon i', modal);
      if (icon) icon.className = 'fas fa-check';
      const heading = $('#progressTitle', modal);
      if (heading) heading.textContent = 'Your download has started';
      const sub = $('.modal-sub', modal);
      if (sub) sub.textContent = 'Check your browser downloads. Thanks for using DooVid.';
      label.textContent = 'Download started';
      const cancel = $('[data-cancel-download]', modal);
      if (cancel) cancel.textContent = 'Done';

      showToast('Download started — check your device.', 'success');

      window.setTimeout(() => {
        if (overlay.isConnected) {
          overlay.remove();
          syncModalState();
        }
      }, 2800);
    };

    updateStage();

    if (prefersReducedMotion()) {
      progress = 100;
      finish();
    } else {
      timer = window.setInterval(() => {
        if (finished) return;

        progress += (isAudio ? 2.1 : 1.45) + Math.random() * 1.5;
        updateStage();

        if (progress >= 97) {
          progress = 97;
          label.textContent = 'Processing on the server…';
          percent.textContent = '97%';
          fill.style.width = '97%';

          window.clearInterval(timer);
          timer = null;
          window.setTimeout(finish, 4000);
        }
      }, 120);
    }

    overlay.addEventListener('click', (event) => {
      if (event.target === overlay || event.target.closest('[data-cancel-download]')) {
        finished = true;
        window.clearInterval(timer);
        overlay.remove();
        syncModalState();
      }
    });

    const closeBtn = $('[data-cancel-download]', overlay);
    if (closeBtn) closeBtn.focus({ preventScroll: true });
  }

  /* ----------------------------------------------------------
     Modal helpers
     ---------------------------------------------------------- */
  function closeModals() {
    $$('.modal-overlay').forEach((overlay) => overlay.remove());
    syncModalState();
  }

  function syncModalState() {
    document.body.classList.toggle('modal-open', Boolean(document.querySelector('.modal-overlay')));
  }

  document.addEventListener('keydown', (event) => {
    if (event.key !== 'Escape') return;
    if (!document.querySelector('.modal-overlay')) return;
    closeModals();
  });

  /* ----------------------------------------------------------
     Toasts
     ---------------------------------------------------------- */
  function showToast(message, type) {
    let wrap = document.getElementById('toastWrap');
    if (!wrap) {
      wrap = document.createElement('div');
      wrap.className = 'toast-wrap';
      wrap.id = 'toastWrap';
      wrap.setAttribute('aria-live', 'polite');
      document.body.appendChild(wrap);
    }

    const icons = {
      success: 'fa-circle-check',
      error: 'fa-circle-exclamation',
      info: 'fa-circle-info',
    };

    const toast = document.createElement('div');
    toast.className = `toast${type ? ` ${type}` : ''}`;
    toast.innerHTML = `<i class="fas ${icons[type] || icons.info}"></i><span>${esc(message)}</span>`;
    wrap.appendChild(toast);

    window.setTimeout(() => {
      toast.classList.add('is-out');
      window.setTimeout(() => toast.remove(), 350);
    }, type === 'error' ? 4600 : 3400);
  }

  /* ----------------------------------------------------------
     FAQ accordion — only one answer open at a time
     ---------------------------------------------------------- */
  function initFaq() {
    const items = $$('.faq-item');
    items.forEach((item) => {
      item.addEventListener('toggle', () => {
        if (!item.open) return;
        items.forEach((other) => {
          if (other !== item) other.open = false;
        });
      });
    });
  }

  /* ----------------------------------------------------------
     "Start free" buttons focus the search input
     ---------------------------------------------------------- */
  function initFocusSearch() {
    const input = $('#videoUrl') || $('.inpu');
    if (!input) return;

    $$('[data-focus-search]').forEach((trigger) => {
      trigger.addEventListener('click', () => {
        window.setTimeout(() => {
          try {
            input.focus({ preventScroll: true });
          } catch (error) {
            input.focus();
          }
        }, 650);
      });
    });
  }
})();
