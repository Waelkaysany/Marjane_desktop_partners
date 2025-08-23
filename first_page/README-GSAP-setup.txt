Add GSAP ScrollTrigger: page snap + per-page reveal; respects reduced-motion and disables snapping on small touchscreens.

Files changed/added:
- Edited: first_page/equipment/equipment.php (added .page, .anim-item, GSAP CDN + script, pagination container)
- Edited: first_page/equipment/equipment.css (added .anim-item hidden state and .page-pagination positioning)
- Added: first_page/assets/js/equipment-scroll.js (GSAP ScrollTrigger setup)

Implementation notes:
- Desktop: scroll snaps to nearest 100vh section; inside each section, elements with .anim-item reveal with staggered fade+translate when in view and reverse on exit.
- Mobile/touch (<900px): snapping disabled by default. Toggle via constants in equipment-scroll.js.
- Reduced motion: if user prefers reduced motion, all animations and snapping are skipped; elements are immediately visible.
- Keyboard navigation: ArrowUp/ArrowDown smoothly scroll to previous/next section.
- Pagination dots: optional. Clickable dots added to .page-pagination; they update with active section via ScrollTrigger.

Configurable constants (equipment-scroll.js):
- REVEAL_DURATION, REVEAL_STAGGER, REVEAL_Y
- SNAP_DURATION, SNAP_EASE
- MOBILE_SNAP_BREAKPOINT, SNAP_ENABLED_ON_MOBILE
- KEYBOARD_NAV_ENABLED, PAGINATION_DOTS_ENABLED

Styling guidance:
- .anim-item has initial hidden state (opacity 0; translateY). Do not add CSS animations that conflict with GSAP.
- Ensure each “page” section is 100vh tall. Existing #page2/#page3/#page4 already are.
- Use will-change sparingly; GSAP manages transforms efficiently. Avoid permanent will-change.

Troubleshooting:
- If animations don’t fire: ensure gsap/ScrollTrigger/ScrollToPlugin CDN scripts load before equipment-scroll.js, and that .page and .anim-item classes are present.
- If snapping feels jumpy: lower SNAP_DURATION, change SNAP_EASE, or disable inertia in the snap config.
- Performance: limit number of simultaneous .anim-item elements; consider batching if many list items in a single section.

QA checklist (run manually):
- Count of sections detected (see console): N should equal number of .page sections.
- Verify prefers-reduced-motion path by setting OS/browser to reduce; page loads with no animations/snapping.
- Desktop: scroll snaps between sections; reveals play on enter and reverse on exit.
- Mobile: no snapping by default; reveals still occur when sections enter viewport.
- Arrow keys move between sections and trigger reveals.
- Pagination dots update active state and jump on click.

End of notes.
