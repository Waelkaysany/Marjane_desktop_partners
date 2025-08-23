/*
  equipment-scroll.js
  Behavior: Full-screen section snap (desktop), per-page staggered reveal, optional pagination dots.

  Loading options:
  - CDN (default in equipment.php): relies on global gsap, ScrollTrigger, ScrollToPlugin
- Module bundler: comment out CDN tags in equipment.php and use imports:
    // import gsap from 'gsap';
    // import ScrollTrigger from 'gsap/ScrollTrigger';
    // import ScrollToPlugin from 'gsap/ScrollToPlugin';
    // gsap.registerPlugin(ScrollTrigger, ScrollToPlugin);

  Configurable constants are below. Adjust to taste.
*/

(function () {
  // ===== Config =====
  const REVEAL_DURATION = 0.8; // seconds
  const REVEAL_STAGGER = 0.12; // seconds between items
  const REVEAL_Y = 24; // match CSS hidden state translateY
  const SNAP_DURATION = 0.6; // seconds to snap scroll
  const SNAP_EASE = 'power2.out';
  const MOBILE_SNAP_BREAKPOINT = 900; // px, disable snap below this width by default
  const SNAP_ENABLED_ON_MOBILE = false; // set true to force-enable snap on small/touch
  const KEYBOARD_NAV_ENABLED = true;
  const PAGINATION_DOTS_ENABLED = true; // set false to hide dots

  // Accessibility: respect reduced motion
  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  // Abort if GSAP is not available (silent fail)
  if (typeof window.gsap === 'undefined') {
    console.warn('[equipment-scroll] GSAP not found. Ensure CDN or imports are loaded before this script.');
    return;
  }

  // Register required plugins
  if (gsap && gsap.registerPlugin) {
    try {
      gsap.registerPlugin(ScrollTrigger, ScrollToPlugin);
    } catch (e) {
      // In case plugins are already registered or unavailable
    }
  }

  const pages = Array.from(document.querySelectorAll('.page'));
  const pageCount = pages.length;

  // Early exit for reduced motion: reveal everything and skip triggers/snapping
  if (prefersReducedMotion) {
    document.documentElement.style.scrollBehavior = 'auto';
    const allAnim = document.querySelectorAll('.anim-item');
    allAnim.forEach((el) => {
      el.style.opacity = '1';
      el.style.transform = 'none';
    });
    console.log('[equipment-scroll] Reduced motion: animations/snapping disabled. Pages:', pageCount);
    return;
  }

  // Determine if we should enable snapping (desktop / non-touch by default)
  const isTouch = 'ontouchstart' in window || navigator.maxTouchPoints > 0;
  const isSmall = window.matchMedia(`(max-width: ${MOBILE_SNAP_BREAKPOINT}px)`).matches;
  const enableSnap = !isTouch && !isSmall ? true : (SNAP_ENABLED_ON_MOBILE && !prefersReducedMotion);

  // Reveal animations per page
  pages.forEach((page) => {
    const children = page.querySelectorAll('.anim-item');
    if (children.length === 0) return;

    // Set initial state via JS too (in case CSS not loaded yet)
    gsap.set(children, { opacity: 0, y: REVEAL_Y });

    gsap.to(children, {
      opacity: 1,
      y: 0,
      stagger: REVEAL_STAGGER,
      duration: REVEAL_DURATION,
      ease: 'power2.out',
      scrollTrigger: {
        trigger: page,
        start: 'top 65%',
        end: 'bottom 35%',
        toggleActions: 'play reverse play reverse', // play on enter, reverse on leave
      },
    });
  });

  // Snap behavior across sections (desktop by default)
  if (enableSnap && typeof ScrollTrigger !== 'undefined') {
    ScrollTrigger.create({
      trigger: document.body,
      start: 'top top',
      end: 'bottom bottom',
      snap: {
        snapTo: (value) => {
          // map scroll progress to nearest page index
          const snapIndex = Math.round(value * (pageCount - 1));
          return snapIndex / (pageCount - 1);
        },
        duration: { min: SNAP_DURATION * 0.6, max: SNAP_DURATION },
        inertia: true,
        ease: SNAP_EASE,
      },
    });
  }

  // Optional keyboard navigation
  if (KEYBOARD_NAV_ENABLED) {
    document.addEventListener('keydown', (e) => {
      if (e.defaultPrevented) return;
      if (e.key !== 'ArrowDown' && e.key !== 'ArrowUp') return;
      e.preventDefault();

      const current = ScrollTrigger.getAll()[0];
      const pos = window.scrollY;
      // Compute next/prev page by proximity
      let closestIndex = 0;
      let minDelta = Infinity;
      pages.forEach((p, i) => {
        const top = p.getBoundingClientRect().top + window.scrollY;
        const delta = Math.abs(pos - top);
        if (delta < minDelta) {
          minDelta = delta;
          closestIndex = i;
        }
      });

      let target = closestIndex + (e.key === 'ArrowDown' ? 1 : -1);
      target = Math.max(0, Math.min(pageCount - 1, target));
      const targetTop = pages[target].getBoundingClientRect().top + window.scrollY;

      gsap.to(window, {
        duration: SNAP_DURATION,
        ease: SNAP_EASE,
        scrollTo: targetTop,
      });
    });
  }

  // Optional: page pagination dots
  if (PAGINATION_DOTS_ENABLED) {
    const container = document.querySelector('.page-pagination');
    if (container) {
      container.innerHTML = '';
      pages.forEach((_, i) => {
        const dot = document.createElement('button');
        dot.className = 'pagination-dot';
        dot.type = 'button';
        dot.setAttribute('aria-label', `Go to section ${i + 1}`);
        dot.addEventListener('click', () => {
          const targetTop = pages[i].getBoundingClientRect().top + window.scrollY;
          gsap.to(window, { duration: SNAP_DURATION, ease: SNAP_EASE, scrollTo: targetTop });
        });
        container.appendChild(dot);
      });

      const dots = Array.from(container.querySelectorAll('.pagination-dot'));
      // Update active dot via ScrollTrigger
      pages.forEach((page, i) => {
        ScrollTrigger.create({
          trigger: page,
          start: 'top center',
          end: 'bottom center',
          onEnter: () => setActive(i),
          onEnterBack: () => setActive(i),
        });
      });

      function setActive(index) {
        dots.forEach((d, i) => d.classList.toggle('active', i === index));
      }
    }
  }

  // Debug / QA summary in console
  console.log('[equipment-scroll] init:', {
    pageCount,
    prefersReducedMotion,
    enableSnapDesktop: enableSnap,
    enableSnapMobile: SNAP_ENABLED_ON_MOBILE,
  });

  // Optional: Wheel hijack template (disabled). Use with caution; can be jarring.
  // let wheelLock = false;
  // window.addEventListener('wheel', (e) => {
  //   if (!enableSnap || wheelLock) return;
  //   e.preventDefault();
  //   wheelLock = true;
  //   const direction = e.deltaY > 0 ? 1 : -1;
  //   const pos = window.scrollY;
  //   let closestIndex = 0;
  //   let minDelta = Infinity;
  //   pages.forEach((p, i) => {
  //     const top = p.getBoundingClientRect().top + window.scrollY;
  //     const delta = Math.abs(pos - top);
  //     if (delta < minDelta) { minDelta = delta; closestIndex = i; }
  //   });
  //   let target = Math.max(0, Math.min(pageCount - 1, closestIndex + direction));
  //   const targetTop = pages[target].getBoundingClientRect().top + window.scrollY;
  //   gsap.to(window, { duration: SNAP_DURATION, ease: SNAP_EASE, scrollTo: targetTop, onComplete: () => { wheelLock = false; } });
  // }, { passive: false });
})();
