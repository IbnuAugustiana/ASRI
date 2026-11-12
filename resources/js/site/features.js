import gsap from 'gsap';

const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
const rupiah = (n) => 'Rp ' + Math.round(n).toLocaleString('id-ID');
const esc = (s) => String(s).replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
const readJson = (el) => { try { return JSON.parse(el?.textContent || 'null'); } catch (e) { return null; } };

export function initFeatures() {
    if (!document.body.classList.contains('site')) return;
    initNavMore();
    initCompare();
    initMap();
    initCalc();
    initFaq();
    initGallery();
    initFlips();

    const tour = document.querySelector('[data-tour]');
    if (tour) {
        import('./tour3d.js')
            .then(({ mountTour }) => mountTour(tour))
            .catch((err) => console.error('Gagal memuat tur 3D', err));
    }
}

/* ------------------------------------------------------------------ */
function initNavMore() {
    const more = document.querySelector('[data-more]');
    if (!more) return;
    const btn = more.querySelector('button');
    const set = (open) => { more.classList.toggle('is-open', open); btn.setAttribute('aria-expanded', String(open)); };
    btn.addEventListener('click', (e) => { e.stopPropagation(); set(!more.classList.contains('is-open')); });
    document.addEventListener('click', (e) => { if (!more.contains(e.target)) set(false); });
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') set(false); });
}

/* ------------------------------------------------------------------ */
/* Bandingkan unit: daftar disimpan di localStorage, maksimal 3 */
function initCompare() {
    const KEY = 'asri-compare';
    const read = () => { try { return JSON.parse(localStorage.getItem(KEY)) || []; } catch (e) { return []; } };
    const write = (list) => { try { localStorage.setItem(KEY, JSON.stringify(list)); } catch (e) { /* mode privat */ } };
    const tray = document.querySelector('[data-compare-tray]');
    const onComparePage = window.location.pathname.replace(/\/$/, '').endsWith('/bandingkan');

    // Di halaman bandingkan: URL adalah sumber kebenaran
    if (onComparePage) {
        const ids = new URLSearchParams(window.location.search).get('ids');
        if (ids) {
            const labels = Array.from(document.querySelectorAll('.compare__code')).map((el) => el.textContent.trim());
            write(ids.split(',').filter(Boolean).slice(0, 3).map((id, i) => ({ id, label: labels[i] || id })));
        } else if (read().length && !document.querySelector('.compare__col')) {
            window.location.replace(`${window.location.pathname}?ids=${read().map((x) => x.id).join(',')}`);
            return;
        }
    }

    const render = (pop = false) => {
        const list = read();
        document.querySelectorAll('[data-compare]').forEach((b) => {
            const on = list.some((x) => String(x.id) === b.dataset.compare);
            b.setAttribute('aria-pressed', String(on));
            b.classList.toggle('is-on', on);
            const label = b.querySelector('span');
            if (label) label.textContent = on ? 'Dibandingkan' : 'Bandingkan';
        });
        if (!tray) return;
        tray.hidden = !list.length || onComparePage;
        tray.querySelector('[data-compare-items]').innerHTML = list.map((x) =>
            `<span class="compare-tray__chip">${esc(x.label)}<button type="button" data-tray-remove="${esc(x.id)}" aria-label="Hapus ${esc(x.label)}"><i class="ri-close-line"></i></button></span>`).join('')
            + (list.length < 3 ? `<span class="compare-tray__slot">${3 - list.length} slot lagi</span>` : '');
        const go = tray.querySelector('[data-compare-go]');
        go.href = `${go.getAttribute('href').split('?')[0]}?ids=${list.map((x) => x.id).join(',')}`;
        if (pop && !reduce && !tray.hidden) gsap.fromTo(tray, { y: 30, rotateX: -40, opacity: 0 }, { y: 0, rotateX: 0, opacity: 1, duration: 0.6, ease: 'expo.out' });
    };

    document.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-compare]');
        if (btn) {
            e.preventDefault();
            e.stopPropagation();
            let list = read();
            const id = btn.dataset.compare;
            const wasEmpty = !list.length;
            if (list.some((x) => String(x.id) === id)) list = list.filter((x) => String(x.id) !== id);
            else {
                list.push({ id, label: btn.dataset.compareLabel || id });
                if (list.length > 3) list = list.slice(-3);
            }
            write(list);
            render(wasEmpty);
            if (!reduce) gsap.fromTo(btn, { scale: 0.85 }, { scale: 1, duration: 0.5, ease: 'back.out(3)' });
            return;
        }
        const rm = e.target.closest('[data-tray-remove]');
        if (rm) { write(read().filter((x) => String(x.id) !== rm.dataset.trayRemove)); render(); return; }
        if (e.target.closest('[data-compare-clear]')) { write([]); render(); return; }
        const colRm = e.target.closest('[data-compare-remove]');
        if (colRm) {
            const rest = read().filter((x) => String(x.id) !== colRm.dataset.compareRemove);
            write(rest);
            window.location.href = rest.length ? `?ids=${rest.map((x) => x.id).join(',')}` : window.location.pathname;
        }
    });
    render();
}

/* ------------------------------------------------------------------ */
/* Peta lokasi: pilih wilayah, panel diperbarui */
function initMap() {
    const map = document.querySelector('[data-map]');
    if (!map) return;
    const data = readJson(map.querySelector('script[type="application/json"]')) || [];
    const panel = document.querySelector('[data-map-panel]');
    const tiles = Array.from(map.querySelectorAll('[data-tile]'));
    const scene = map.querySelector('.map__scene');
    const p = (k) => panel.querySelector(`[data-p="${k}"]`);

    const select = (i) => {
        const w = data[i];
        if (!w) return;
        tiles.forEach((t, j) => t.classList.toggle('is-active', i === j));
        const update = () => {
            p('nama').textContent = w.nama;
            p('catatan').textContent = w.catatan;
            p('total').textContent = w.total;
            p('harga').textContent = rupiah(w.harga_min);
            p('gedung').innerHTML = w.gedung.map((g) =>
                `<li><span class="map-panel__blok">${esc(g.blok)}</span><div><b>${esc(g.nama)}</b><small>${esc(g.alamat)}</small></div><em>${g.tersedia}/${g.total}</em></li>`).join('');
            const link = p('link');
            link.href = `${link.dataset.base}?wilayah=${encodeURIComponent(w.nama)}&tersedia=1`;
            const st = { v: 0 };
            gsap.to(st, { v: w.tersedia, duration: reduce ? 0 : 0.8, ease: 'power3.out', onUpdate: () => { p('tersedia').textContent = Math.round(st.v); } });
        };
        if (reduce) { update(); return; }
        gsap.to(panel, { opacity: 0, rotateY: -12, x: 10, duration: 0.2, onComplete: () => {
            update();
            gsap.fromTo(panel, { opacity: 0, rotateY: 12, x: -10 }, { opacity: 1, rotateY: 0, x: 0, duration: 0.5, ease: 'expo.out' });
        } });
    };
    tiles.forEach((t) => t.addEventListener('click', () => select(Number(t.dataset.tile))));

    // Peta sedikit berputar mengikuti kursor
    if (!reduce && window.matchMedia('(hover: hover)').matches) {
        const rz = gsap.quickTo(scene, '--rz', { duration: 0.8, ease: 'power3.out' });
        const rx = gsap.quickTo(scene, '--rx', { duration: 0.8, ease: 'power3.out' });
        map.addEventListener('pointermove', (e) => {
            const r = map.getBoundingClientRect();
            rz(-45 + ((e.clientX - r.left) / r.width - 0.5) * 16);
            rx(56 + ((e.clientY - r.top) / r.height - 0.5) * -10);
        });
        map.addEventListener('pointerleave', () => { rz(-45); rx(56); });
    }
}

/* ------------------------------------------------------------------ */
/* Simulasi biaya */
function initCalc() {
    const root = document.querySelector('[data-calc]');
    if (!root) return;
    const harga = readJson(root.querySelector('script[type="application/json"]')) || {};
    const $ = (s) => root.querySelector(s);
    const out = (k) => root.querySelector(`[data-out="${k}"]`);
    const shown = {};
    const C = 2 * Math.PI * 48;

    const tween = (key, value) => {
        const el = out(key);
        if (!el) return;
        const from = shown[key] ?? 0;
        shown[key] = value;
        if (reduce) { el.textContent = rupiah(value); return; }
        const st = { v: from };
        gsap.to(st, { v: value, duration: 0.7, ease: 'power3.out', onUpdate: () => { el.textContent = rupiah(st.v); } });
    };

    const compute = () => {
        const w = $('[data-calc-wilayah]').value;
        const tipe = Number(root.querySelector('[data-calc-tipe]:checked').value);
        const durasi = Number($('[data-calc-durasi]').value);
        const orang = Math.min(6, Math.max(1, Number($('[data-calc-penghuni]').value) || 1));
        const va = Number($('[data-calc-listrik]').value);

        const prices = harga[w] || {};
        [24, 36].forEach((t) => { const el = root.querySelector(`[data-calc-price="${t}"]`); if (el) el.textContent = prices[t] ? `${rupiah(prices[t])}/bln` : 'Tidak tersedia'; });

        const sewa = prices[tipe] || prices[24] || prices[36] || 0;
        const listrik = Math.round(({ 900: 120000, 1300: 210000, 2200: 360000 }[va] || 210000) * (tipe === 36 ? 1.15 : 1) * (0.84 + orang * 0.08) / 1000) * 1000;
        const air = 35000 + orang * 28000;
        const ipl = tipe === 36 ? 75000 : 50000;
        const bulanan = sewa + listrik + air + ipl;

        $('[data-calc-durasi-out]').textContent = `${durasi} bulan`;
        out('durasi').textContent = `${durasi} bulan`;
        tween('sewa', sewa); tween('listrik', listrik); tween('air', air); tween('ipl', ipl); tween('bulanan', bulanan);
        tween('pertama', sewa); tween('deposit', sewa); tween('awal', sewa * 2); tween('kontrak', sewa * durasi);

        let offset = 0;
        [['sewa', sewa], ['listrik', listrik], ['air', air], ['ipl', ipl]].forEach(([k, v]) => {
            const seg = root.querySelector(`[data-seg="${k}"]`);
            const len = bulanan ? (v / bulanan) * C : 0;
            gsap.to(seg, { attr: { 'stroke-dasharray': `${Math.max(0, len - 2)} ${C}`, 'stroke-dashoffset': -offset }, duration: reduce ? 0 : 0.8, ease: 'power3.out' });
            offset += len;
        });

        const link = $('[data-calc-link]');
        link.href = `${link.dataset.base}?wilayah=${encodeURIComponent(w)}&harga_max=${sewa}&tersedia=1`;
    };

    root.addEventListener('input', compute);
    root.addEventListener('change', compute);
    root.querySelectorAll('[data-step]').forEach((b) => b.addEventListener('click', () => {
        const input = $('[data-calc-penghuni]');
        input.value = Math.min(6, Math.max(1, Number(input.value) + Number(b.dataset.step)));
        compute();
    }));
    compute();
}

/* ------------------------------------------------------------------ */
function initFaq() {
    const root = document.querySelector('[data-faq]');
    if (!root) return;
    const items = Array.from(root.querySelectorAll('[data-faq-item]'));
    items.forEach((d) => {
        d.addEventListener('toggle', () => {
            if (!d.open || reduce) return;
            gsap.fromTo(d.querySelector('.faq__answer'), { height: 0, opacity: 0 }, { height: 'auto', opacity: 1, duration: 0.45, ease: 'power3.out' });
        });
    });
    const search = document.querySelector('[data-faq-search]');
    search?.addEventListener('input', () => {
        const q = search.value.trim().toLowerCase();
        let any = false;
        root.querySelectorAll('.faq__group').forEach((g) => {
            let groupHit = false;
            g.querySelectorAll('[data-faq-item]').forEach((d) => {
                const hit = !q || d.textContent.toLowerCase().includes(q);
                d.hidden = !hit;
                if (q && hit) d.open = true;
                groupHit = groupHit || hit;
            });
            g.hidden = !groupHit;
            any = any || groupHit;
        });
        root.querySelector('[data-faq-empty]').hidden = any;
    });
}

/* ------------------------------------------------------------------ */
function initGallery() {
    const root = document.querySelector('[data-gallery]');
    if (!root) return;
    const items = Array.from(root.querySelectorAll('[data-gallery-index]'));
    const box = root.querySelector('[data-lightbox]');
    const img = box.querySelector('[data-lightbox-img]');
    const cap = box.querySelector('[data-lightbox-cap]');
    let idx = 0;
    const show = (i) => {
        idx = (i + items.length) % items.length;
        const src = items[idx].querySelector('img');
        img.src = src.src;
        img.alt = src.alt;
        cap.textContent = `${src.alt} · ${idx + 1}/${items.length}`;
        if (!reduce) gsap.fromTo(img, { opacity: 0, scale: 0.94, rotateY: 8 }, { opacity: 1, scale: 1, rotateY: 0, duration: 0.5, ease: 'expo.out' });
    };
    items.forEach((b, i) => b.addEventListener('click', () => { show(i); box.showModal?.(); }));
    box.querySelector('[data-lightbox-prev]').addEventListener('click', () => show(idx - 1));
    box.querySelector('[data-lightbox-next]').addEventListener('click', () => show(idx + 1));
    box.querySelector('[data-lightbox-close]').addEventListener('click', () => box.close());
    box.addEventListener('click', (e) => { if (e.target === box) box.close(); });
    box.addEventListener('keydown', (e) => { if (e.key === 'ArrowLeft') show(idx - 1); if (e.key === 'ArrowRight') show(idx + 1); });
}

function initFlips() {
    document.querySelectorAll('[data-flip]').forEach((f) => f.addEventListener('click', () => f.classList.toggle('is-flipped')));
}
