import gsap from 'gsap';

const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const finePointer = window.matchMedia('(hover: hover) and (pointer: fine)').matches;

export function initDash() {
    const body = document.body;
    if (!body.classList.contains('app')) return;

    initSidebar(body);
    initUserMenu();
    if (reduceMotion) return;
    introAnimation();
    initStatTilt();
    initStatCounters();
}

function initSidebar(body) {
    const toggle = document.querySelector('[data-sidebar-toggle]');
    const close = () => {
        body.classList.remove('sidebar-open');
        toggle?.setAttribute('aria-expanded', 'false');
    };
    toggle?.addEventListener('click', () => {
        const open = !body.classList.contains('sidebar-open');
        body.classList.toggle('sidebar-open', open);
        toggle.setAttribute('aria-expanded', String(open));
        if (open && !reduceMotion) {
            const items = document.querySelectorAll('.sidebar .nav-item');
            items.forEach((el) => { el.style.transition = 'none'; });
            gsap.fromTo(items, { x: -30, rotateY: -50, opacity: 0 }, {
                x: 0, rotateY: 0, opacity: 1, stagger: 0.03, duration: 0.6, ease: 'expo.out', delay: 0.1, clearProps: 'transform,opacity',
                onComplete: () => items.forEach((el) => { el.style.transition = ''; }),
            });
        }
    });
    document.querySelectorAll('[data-sidebar-close]').forEach((el) => el.addEventListener('click', close));
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') close(); });
}

function initUserMenu() {
    const menu = document.querySelector('[data-user-menu]');
    if (!menu) return;
    const btn = menu.querySelector('button');
    const set = (open) => {
        menu.classList.toggle('is-open', open);
        btn.setAttribute('aria-expanded', String(open));
    };
    btn.addEventListener('click', (e) => { e.stopPropagation(); set(!menu.classList.contains('is-open')); });
    document.addEventListener('click', (e) => { if (!menu.contains(e.target)) set(false); });
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') set(false); });
}

/* Kartu-kartu di halaman masuk satu per satu dengan efek 3D */
function introAnimation() {
    const navItems = document.querySelectorAll('.sidebar .nav-item, .sidebar .nav-section-label');
    navItems.forEach((el) => { el.style.transition = 'none'; });
    gsap.fromTo(navItems,
        { x: -24, rotateY: -40, opacity: 0 },
        { x: 0, rotateY: 0, opacity: 1, stagger: 0.025, duration: 0.7, ease: 'expo.out', delay: 0.1, clearProps: 'transform,opacity',
          onComplete: () => navItems.forEach((el) => { el.style.transition = ''; }) });
    gsap.fromTo('.topbar', { y: -30, opacity: 0 }, { y: 0, opacity: 1, duration: 0.8, ease: 'expo.out', clearProps: 'transform,opacity' });

    const body = document.querySelector('.page-body');
    if (!body) return;
    const blocks = Array.from(body.querySelectorAll('.stat-card, .card, .table-wrap'))
        .filter((el) => !el.parentElement.closest('.card, .stat-card, .table-wrap'))
        .slice(0, 40);
    if (!blocks.length) return;

    gsap.set(blocks, { transformPerspective: 1200, transformOrigin: '50% 100%' });
    const io = new IntersectionObserver((entries) => {
        const visible = entries.filter((e) => e.isIntersecting).map((e) => e.target);
        if (!visible.length) return;
        visible.forEach((el) => io.unobserve(el));
        gsap.fromTo(visible,
            { opacity: 0, y: 50, rotateX: -22, z: -60 },
            { opacity: 1, y: 0, rotateX: 0, z: 0, duration: 1.1, ease: 'expo.out', stagger: 0.07, clearProps: 'transform,opacity' });
    }, { threshold: 0.05 });
    blocks.forEach((el) => { el.style.opacity = '0'; io.observe(el); });
    // Pengaman: jangan biarkan konten tersembunyi
    setTimeout(() => blocks.forEach((el) => { if (el.style.opacity === '0') el.style.opacity = ''; }), 3500);
}

function initStatTilt() {
    if (!finePointer) return;
    document.querySelectorAll('.page-body .stat-card').forEach((el) => {
        const rx = gsap.quickTo(el, 'rotationX', { duration: 0.6, ease: 'power3.out' });
        const ry = gsap.quickTo(el, 'rotationY', { duration: 0.6, ease: 'power3.out' });
        el.addEventListener('pointermove', (e) => {
            const r = el.getBoundingClientRect();
            ry(((e.clientX - r.left) / r.width - 0.5) * 16);
            rx((0.5 - (e.clientY - r.top) / r.height) * 16);
        });
        el.addEventListener('pointerleave', () => { rx(0); ry(0); });
    });
}

/* Angka di kartu statistik menghitung naik (format Rupiah/titik ribuan dipertahankan) */
function initStatCounters() {
    document.querySelectorAll('.page-body .stat-value').forEach((el) => {
        if (el.children.length) return;
        const m = el.textContent.trim().match(/^(\D*?)(\d[\d.]*)(\D*)$/);
        // Hanya angka bulat / ribuan bertitik (1.250.000). Nilai desimal ringkas seperti "0.9jt" dibiarkan apa adanya.
        if (!m || !/^\d{1,3}(\.\d{3})*$|^\d+$/.test(m[2]) || /^[,.]\d/.test(m[3])) return;
        const end = parseInt(m[2].replace(/\./g, ''), 10);
        if (!Number.isFinite(end) || end === 0) return;
        const state = { v: 0 };
        const render = () => { el.textContent = `${m[1]}${Math.round(state.v).toLocaleString('id-ID')}${m[3]}`; };
        render();
        gsap.to(state, { v: end, duration: 1.6, ease: 'power3.out', delay: 0.3, onUpdate: render });
    });
}
