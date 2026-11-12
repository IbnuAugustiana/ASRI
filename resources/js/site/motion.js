import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import Lenis from 'lenis';

gsap.registerPlugin(ScrollTrigger);

const root = document.documentElement;
const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const finePointer = window.matchMedia('(hover: hover) and (pointer: fine)').matches;

let lenis = null;

/** Jalankan cb saat elemen masuk layar; langsung jalan jika sudah terlewat (mis. reload di tengah halaman). */
function onView(el, cb, ratio = 0.88) {
    if (el.getBoundingClientRect().top < window.innerHeight * ratio) {
        requestAnimationFrame(() => cb());
        return;
    }
    ScrollTrigger.create({ trigger: el, start: `top ${ratio * 100}%`, once: true, onEnter: () => cb() });
}

/** Jalankan callback setelah loader pembuka selesai (atau langsung jika tidak ada loader). */
export function onIntro(cb) {
    const loader = document.getElementById('loader');
    if (loader && root.classList.contains('has-loader')) {
        document.addEventListener('asri:intro-done', () => cb(), { once: true });
    } else {
        requestAnimationFrame(() => cb());
    }
}

export function initSite() {
    if (!document.body.classList.contains('site')) return;
    root.classList.add('motion-ready');

    initLenis();
    initLoader();
    initNav();
    initSplit();
    initHeroIn();
    initReveal();
    initCounters();
    initTilt();
    initMagnetic();
    initRibbonSkew();
    initStairs();
    initPrism();
    initProgress();
    initToasts();
    initForms();

    window.addEventListener('load', () => ScrollTrigger.refresh());
}

/* ------------------------------------------------------------------ */
/* Formulir: tampilkan password & isi otomatis akun demo */
function initForms() {
    document.querySelectorAll('[data-pass-toggle]').forEach((btn) => {
        btn.addEventListener('click', () => {
            const input = btn.parentElement.querySelector('input');
            const show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            btn.setAttribute('aria-label', show ? 'Sembunyikan password' : 'Tampilkan password');
            btn.querySelector('i').className = show ? 'ri-eye-off-line' : 'ri-eye-line';
        });
    });

    const demos = document.querySelectorAll('[data-demo-email]');
    demos.forEach((btn) => {
        btn.addEventListener('click', () => {
            const email = document.getElementById('email');
            const pass = document.getElementById('password');
            if (!email || !pass) return;
            demos.forEach((b) => b.classList.toggle('is-picked', b === btn));
            email.value = btn.dataset.demoEmail;
            pass.value = btn.dataset.demoPassword;
            if (!reduceMotion) gsap.fromTo([email, pass], { scale: 0.97 }, { scale: 1, duration: 0.5, ease: 'back.out(3)', stagger: 0.06 });
            email.form.querySelector('[type="submit"]')?.focus();
        });
    });
}

/* ------------------------------------------------------------------ */
function initLenis() {
    if (reduceMotion) return;
    lenis = new Lenis({ duration: 1.15, smoothWheel: true });
    window.__lenis = lenis; // dipakai skrip perekam video demo
    lenis.on('scroll', ScrollTrigger.update);
    gsap.ticker.add((time) => lenis.raf(time * 1000));
    gsap.ticker.lagSmoothing(0);

    document.querySelectorAll('a[href^="#"]').forEach((a) => {
        a.addEventListener('click', (e) => {
            const id = a.getAttribute('href');
            if (id.length < 2) return;
            const target = document.querySelector(id);
            if (!target) return;
            e.preventDefault();
            lenis.scrollTo(target, { offset: -20 });
        });
    });
}

/* ------------------------------------------------------------------ */
function initLoader() {
    const loader = document.getElementById('loader');
    if (!loader) return;
    if (!root.classList.contains('has-loader')) {
        loader.remove();
        return;
    }
    try { sessionStorage.setItem('asri-intro', '1'); } catch (e) { /* mode privat */ }

    let finished = false;
    const done = () => {
        if (finished) return;
        finished = true;
        loader.classList.add('is-done');
        document.dispatchEvent(new CustomEvent('asri:intro-done'));
        setTimeout(() => {
            loader.remove();
            root.classList.remove('has-loader');
        }, 1100);
    };

    const minTime = new Promise((r) => setTimeout(r, reduceMotion ? 0 : 1500));
    const loaded = document.readyState === 'complete'
        ? Promise.resolve()
        : new Promise((r) => window.addEventListener('load', r, { once: true }));
    Promise.all([minTime, loaded]).then(done);
    setTimeout(done, 4000);
}

/* ------------------------------------------------------------------ */
function initNav() {
    const nav = document.querySelector('[data-nav]');
    if (!nav) return;
    let last = 0;

    const onScroll = (y) => {
        nav.classList.toggle('is-scrolled', y > 30);
        if (!nav.classList.contains('is-open')) {
            nav.classList.toggle('is-hidden', y > last && y > 320);
        }
        last = y;
    };
    if (lenis) lenis.on('scroll', ({ scroll }) => onScroll(scroll));
    else window.addEventListener('scroll', () => onScroll(window.scrollY), { passive: true });

    const toggle = nav.querySelector('.nav__toggle');
    const menu = document.getElementById('mobile-menu');
    toggle?.addEventListener('click', () => {
        const open = toggle.getAttribute('aria-expanded') === 'true';
        toggle.setAttribute('aria-expanded', String(!open));
        toggle.setAttribute('aria-label', open ? 'Buka menu' : 'Tutup menu');
        menu.hidden = open;
        nav.classList.toggle('is-open', !open);
        if (!open) {
            nav.classList.remove('is-hidden');
            gsap.from(menu.children, { opacity: 0, y: -12, rotateX: -40, stagger: 0.05, duration: 0.5, ease: 'expo.out' });
        }
    });
}

/* ------------------------------------------------------------------ */
function splitText(el) {
    const text = el.textContent.trim().replace(/\s+/g, ' ');
    el.setAttribute('aria-label', text);
    el.textContent = '';
    const words = text.split(' ');
    words.forEach((word, i) => {
        const w = document.createElement('span');
        w.className = 'split-word';
        w.setAttribute('aria-hidden', 'true');
        for (const ch of word) {
            const c = document.createElement('span');
            c.className = 'split-char';
            c.textContent = ch;
            w.appendChild(c);
        }
        el.appendChild(w);
        if (i < words.length - 1) el.appendChild(document.createTextNode(' '));
    });
    return el.querySelectorAll('.split-char');
}

function initSplit() {
    document.querySelectorAll('[data-split]').forEach((el) => {
        const chars = splitText(el);
        el.style.visibility = 'visible';
        if (reduceMotion) return;

        const anim = gsap.from(chars, {
            opacity: 0,
            yPercent: 70,
            rotateX: -100,
            transformOrigin: '50% 100% -0.35em',
            duration: 1.1,
            ease: 'expo.out',
            stagger: 0.024,
            paused: true,
        });

        const inHero = el.closest('[data-hero], .page-hero, .detail-hero, .article-hero, .auth');
        if (inHero) onIntro(() => anim.play());
        else onView(el, () => anim.play(), 0.86);
    });
}

function initHeroIn() {
    const items = gsap.utils.toArray('[data-hero-in]');
    if (!items.length) return;
    if (reduceMotion) { gsap.set(items, { opacity: 1 }); return; }
    gsap.set(items, { opacity: 0, y: 30, rotateX: -25, transformPerspective: 800, transformOrigin: '50% 100%' });
    onIntro(() => {
        gsap.to(items, { opacity: 1, y: 0, rotateX: 0, duration: 1.1, ease: 'expo.out', stagger: 0.09, delay: 0.35, clearProps: 'transform' });
    });
}

/* ------------------------------------------------------------------ */
function initReveal() {
    const singles = gsap.utils.toArray('[data-reveal]');
    const groups = gsap.utils.toArray('[data-reveal-group]');

    if (reduceMotion) {
        gsap.set(singles, { opacity: 1 });
        groups.forEach((g) => gsap.set(g.children, { opacity: 1 }));
        return;
    }

    singles.forEach((el) => {
        gsap.set(el, { opacity: 0, y: 60, rotateX: -18, transformPerspective: 1000, transformOrigin: '50% 100%' });
        onView(el, () => gsap.to(el, { opacity: 1, y: 0, rotateX: 0, duration: 1.2, ease: 'expo.out', clearProps: 'transform' }));
    });

    const show = (batch) => gsap.to(batch, {
        opacity: 1, y: 0, rotateX: 0, rotateY: 0, z: 0,
        duration: 1.3, ease: 'expo.out', stagger: 0.12, clearProps: 'transform',
    });
    groups.forEach((group) => {
        const kids = Array.from(group.children);
        gsap.set(kids, { opacity: 0, y: 90, rotateX: -35, rotateY: 8, z: -120, transformPerspective: 1200, transformOrigin: '50% 100%' });
        const seen = kids.filter((k) => k.getBoundingClientRect().top < window.innerHeight * 0.9);
        const later = kids.filter((k) => !seen.includes(k));
        if (seen.length) requestAnimationFrame(() => show(seen));
        if (later.length) ScrollTrigger.batch(later, { start: 'top 90%', once: true, onEnter: show });
    });
}

/* ------------------------------------------------------------------ */
function initCounters() {
    document.querySelectorAll('[data-count]').forEach((el) => {
        const end = parseFloat(el.dataset.count) || 0;
        const fmt = (n) => Math.round(n).toLocaleString('id-ID');
        if (reduceMotion || end === 0) { el.textContent = fmt(end); return; }

        const state = { v: 0 };
        el.textContent = '0';
        const run = () => gsap.to(state, { v: end, duration: 2, ease: 'power3.out', onUpdate: () => { el.textContent = fmt(state.v); } });

        if (el.closest('[data-hero]')) onIntro(() => gsap.delayedCall(0.7, run));
        else onView(el, run, 0.9);
    });
}

/* ------------------------------------------------------------------ */
function initTilt() {
    if (reduceMotion || !finePointer) return;
    document.querySelectorAll('[data-tilt]').forEach((el) => {
        const max = parseFloat(el.dataset.tilt) || 8;
        gsap.set(el, { transformPerspective: 1100 });
        const rx = gsap.quickTo(el, 'rotationX', { duration: 0.7, ease: 'power3.out' });
        const ry = gsap.quickTo(el, 'rotationY', { duration: 0.7, ease: 'power3.out' });
        const lift = gsap.quickTo(el, 'z', { duration: 0.7, ease: 'power3.out' });

        el.addEventListener('pointermove', (e) => {
            const r = el.getBoundingClientRect();
            const px = (e.clientX - r.left) / r.width;
            const py = (e.clientY - r.top) / r.height;
            ry((px - 0.5) * max * 2);
            rx((0.5 - py) * max * 2);
            lift(24);
            el.style.setProperty('--gx', `${px * 100}%`);
            el.style.setProperty('--gy', `${py * 100}%`);
        });
        el.addEventListener('pointerleave', () => { rx(0); ry(0); lift(0); });
    });
}

function initMagnetic() {
    if (reduceMotion || !finePointer) return;
    document.querySelectorAll('[data-magnetic]').forEach((el) => {
        const xTo = gsap.quickTo(el, 'x', { duration: 0.5, ease: 'power3.out' });
        const yTo = gsap.quickTo(el, 'y', { duration: 0.5, ease: 'power3.out' });
        el.addEventListener('pointermove', (e) => {
            const r = el.getBoundingClientRect();
            xTo((e.clientX - (r.left + r.width / 2)) * 0.3);
            yTo((e.clientY - (r.top + r.height / 2)) * 0.4);
        });
        el.addEventListener('pointerleave', () => { xTo(0); yTo(0); });
    });
}

/* Pita berjalan sedikit miring mengikuti kecepatan scroll */
function initRibbonSkew() {
    const els = document.querySelectorAll('[data-skew]');
    if (!els.length || !lenis) return;
    const skew = gsap.quickTo(els, 'skewX', { duration: 0.6, ease: 'power3.out' });
    lenis.on('scroll', ({ velocity }) => skew(gsap.utils.clamp(-14, 14, velocity * -0.5)));
}

/* ------------------------------------------------------------------ */
/* Tangga 3D: tiap langkah naik saat di-scroll, kubus memanjat */
function initStairs() {
    const sec = document.querySelector('[data-steps]');
    if (!sec) return;
    const stairsEl = sec.querySelector('.stairs');
    const stairs = Array.from(sec.querySelectorAll('.stair'));
    const items = Array.from(sec.querySelectorAll('.steps__item'));
    const key = sec.querySelector('.stairs__key');

    const dims = () => {
        const cs = getComputedStyle(stairsEl);
        return { w: parseFloat(cs.getPropertyValue('--sw')), d: parseFloat(cs.getPropertyValue('--sd')), h: parseFloat(cs.getPropertyValue('--sh')) };
    };
    const setActive = (i) => items.forEach((it, j) => it.classList.toggle('is-active', j === i));
    const keyPos = (i, { w, d, h }) => ({ x: i * w + (w - 40) / 2, y: (d - 40) / 2, z: (i + 1) * h });

    const finalState = () => {
        const dm = dims();
        stairs.forEach((s, i) => s.style.setProperty('--h', `${(i + 1) * dm.h}px`));
        gsap.set(key, keyPos(3, dm));
        setActive(3);
    };
    if (reduceMotion) { finalState(); return; }

    const mm = gsap.matchMedia();
    mm.add('(min-width: 900px)', () => {
        const dm = dims();
        stairs.forEach((s) => s.style.setProperty('--h', '0px'));
        gsap.set(key, { ...keyPos(0, dm), z: 0 });
        setActive(-1);

        const tl = gsap.timeline({
            scrollTrigger: {
                trigger: sec,
                start: 'top top',
                end: '+=1800',
                scrub: 0.8,
                pin: true,
                anticipatePin: 1,
                onUpdate: (self) => setActive(self.progress < 0.04 ? -1 : Math.min(3, Math.floor(self.progress * 4))),
            },
        });
        stairs.forEach((s, i) => {
            tl.to(s, { '--h': `${(i + 1) * dm.h}px`, duration: 1, ease: 'power2.out' }, i);
            tl.to(key, { ...keyPos(i, dm), duration: 0.8, ease: 'power2.inOut' }, i + 0.25);
        });
        tl.to({}, { duration: 0.4 });
    });
    mm.add('(max-width: 899px)', () => {
        const dm = dims();
        stairs.forEach((s) => s.style.setProperty('--h', '0px'));
        gsap.set(key, { ...keyPos(0, dm), z: 0 });
        const tl = gsap.timeline({ paused: true });
        stairs.forEach((s, i) => {
            tl.to(s, { '--h': `${(i + 1) * dm.h}px`, duration: 0.7, ease: 'back.out(1.6)' }, i * 0.3);
            tl.to(key, { ...keyPos(i, dm), duration: 0.6, ease: 'power2.inOut' }, i * 0.3 + 0.15);
            tl.call(() => setActive(i), null, i * 0.3);
        });
        ScrollTrigger.create({ trigger: stairsEl, start: 'top 70%', once: true, onEnter: () => tl.play() });
    });
}

/* ------------------------------------------------------------------ */
/* Prisma 3D tiga peran: tab, keyboard, geser, dan putar otomatis */
function initPrism() {
    const prism = document.querySelector('[data-prism]');
    if (!prism) return;
    const tabs = Array.from(document.querySelectorAll('[data-role]'));
    const faces = Array.from(prism.querySelectorAll('.prism__face'));
    let idx = 0;
    let turn = 0;
    let timer = null;
    let userTouched = false;

    const go = (i, fromUser = false) => {
        let diff = i - idx;
        if (diff > 1) diff -= 3;
        if (diff < -1) diff += 3;
        turn += diff;
        idx = ((i % 3) + 3) % 3;
        prism.style.setProperty('--r', String(turn));
        tabs.forEach((t, j) => {
            t.setAttribute('aria-selected', String(j === idx));
            t.tabIndex = j === idx ? 0 : -1;
        });
        faces.forEach((f, j) => f.setAttribute('aria-hidden', String(j !== idx)));
        if (fromUser) { userTouched = true; stop(); }
    };
    const start = () => { if (!userTouched && !reduceMotion && !timer) timer = setInterval(() => go(idx + 1), 4200); };
    const stop = () => { clearInterval(timer); timer = null; };

    tabs.forEach((t, j) => {
        t.addEventListener('click', () => go(j, true));
        t.addEventListener('keydown', (e) => {
            if (e.key !== 'ArrowRight' && e.key !== 'ArrowLeft') return;
            e.preventDefault();
            const next = (idx + (e.key === 'ArrowRight' ? 1 : -1) + 3) % 3;
            go(next, true);
            tabs[next].focus();
        });
    });

    let startX = null;
    const wrap = prism.parentElement;
    wrap.addEventListener('pointerdown', (e) => { startX = e.clientX; });
    wrap.addEventListener('pointerup', (e) => {
        if (startX === null) return;
        const dx = e.clientX - startX;
        if (Math.abs(dx) > 40) go(idx + (dx < 0 ? 1 : -1), true);
        startX = null;
    });

    new IntersectionObserver(([entry]) => (entry.isIntersecting ? start() : stop()), { threshold: 0.4 }).observe(prism);
}

/* ------------------------------------------------------------------ */
function initProgress() {
    const bar = document.querySelector('[data-progress]');
    if (!bar) return;
    gsap.to(bar, { scaleX: 1, ease: 'none', scrollTrigger: { trigger: document.body, start: 'top top', end: 'bottom bottom', scrub: 0.3 } });
}

function initToasts() {
    document.querySelectorAll('[data-toast]').forEach((t) => {
        const close = () => { t.classList.add('is-leaving'); setTimeout(() => t.remove(), 400); };
        t.querySelector('[data-toast-close]')?.addEventListener('click', close);
        setTimeout(close, 6000);
    });
}
