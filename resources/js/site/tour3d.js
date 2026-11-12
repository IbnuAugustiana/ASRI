/**
 * Tur 3D interior unit (Tipe 24 dan Tipe 36), dibangun prosedural dengan Three.js.
 * Satuan = meter. Tampilan "rumah boneka": tanpa plafon, dinding depan rendah.
 */
import * as THREE from 'three';
import { OrbitControls } from 'three/addons/controls/OrbitControls.js';
import { RoundedBoxGeometry } from 'three/addons/geometries/RoundedBoxGeometry.js';
import gsap from 'gsap';

const WALL_H = 2.3;
const WALL_T = 0.12;

const C = {
    wall: '#eef0f8', wallTop: '#2b3480', wood: '#c4925f', tile: '#e6e3dc', balcony: '#b9b2c9',
    ink: '#2b3480', leaf: '#2ed48a', lamp: '#ffb547', fabric: '#5b6bd6', fabric2: '#e07a6a',
    white: '#fafafa', dark: '#2a2d44', steel: '#c7ccd8', woodDark: '#8a5a36', glass: '#bfe6ff', plant: '#2fae6e', pot: '#d98b5f',
};

/* ---------------- Rencana ruang ---------------- */
const PLANS = {
    24: {
        w: 6, d: 4, balcony: 1.3,
        rooms: [
            { name: 'Kamar tidur', x: [-3, -0.6], z: [-2, 2], floor: 'wood' },
            { name: 'Kamar mandi', x: [-0.6, 1], z: [-2, -0.5], floor: 'tile' },
            { name: 'Dapur', x: [1, 3], z: [-2, -0.5], floor: 'tile' },
            { name: 'Ruang tamu', x: [-0.6, 3], z: [-0.5, 2], floor: 'wood' },
            { name: 'Balkon', x: [-3, 3], z: [2, 3.3], floor: 'balcony' },
        ],
        // [x1, z1, x2, z2, pintu: [mulai, akhir] relatif dari titik awal]
        walls: [
            [-3, -2, 3, -2], [-3, -2, -3, 2], [3, -2, 3, 2],
            [-0.6, -2, -0.6, 2, [2.6, 3.5]],
            [-0.6, -0.5, 1, -0.5, [0.5, 1.3]],
            [1, -2, 1, -0.5],
        ],
        front: [[-3, 2, 3, 2, [3.6, 5.4]]],
        furniture: (f) => {
            f.bed(-1.8, -0.6, 0, 1.4);
            f.nightstand(-2.75, -1.6);
            f.wardrobe(-1.6, 1.6, Math.PI);
            f.rug(-1.8, 0.7, 1.6, 1.0, C.lamp);
            f.toilet(-0.2, -1.65, 0);
            f.shower(0.55, -1.55);
            f.counterLine(2, -1.75, 2, 0);
            f.fridge(2.65, -0.95);
            f.sofa(1.2, 1.55, Math.PI, C.fabric);
            f.coffeeTable(1.2, 0.65);
            f.tv(1.2, -0.35, 0);
            f.rug(1.2, 0.8, 2.2, 1.4, C.leaf);
            f.plant(2.7, 1.7);
            f.balconyRail(-3, 3, 3.3);
            f.clothesline(-1.6, 2.75);
            f.plant(2.5, 2.9); f.plant(1.9, 3.0, 0.8);
            f.chair(0.4, 2.7, Math.PI * 0.2);
        },
        lights: [[-1.8, 0], [0.2, -1.2], [2, -1.2], [1.2, 0.8], [0, 2.6]],
        cam: [7.5, 8.5, 9],
    },
    36: {
        w: 7.5, d: 4.8, balcony: 1.3,
        rooms: [
            { name: 'Kamar utama', x: [-3.75, -1.35], z: [-2.4, 0.2], floor: 'wood' },
            { name: 'Kamar anak', x: [-3.75, -1.35], z: [0.2, 2.4], floor: 'wood' },
            { name: 'Kamar mandi', x: [-1.35, 0.25], z: [-2.4, -0.8], floor: 'tile' },
            { name: 'Dapur', x: [0.25, 3.75], z: [-2.4, -0.8], floor: 'tile' },
            { name: 'Ruang keluarga', x: [-1.35, 3.75], z: [-0.8, 2.4], floor: 'wood' },
            { name: 'Balkon', x: [-3.75, 3.75], z: [2.4, 3.7], floor: 'balcony' },
        ],
        walls: [
            [-3.75, -2.4, 3.75, -2.4], [-3.75, -2.4, -3.75, 2.4], [3.75, -2.4, 3.75, 2.4],
            [-1.35, -2.4, -1.35, 2.4, [1.2, 2.0, 3.6, 4.4]],
            [-3.75, 0.2, -1.35, 0.2],
            [-1.35, -0.8, 0.25, -0.8, [0.4, 1.2]],
            [0.25, -2.4, 0.25, -0.8],
        ],
        front: [[-3.75, 2.4, 3.75, 2.4, [4.4, 6.6]]],
        furniture: (f) => {
            f.bed(-2.75, -1.2, 0, 1.4);
            f.wardrobe(-1.7, -1.75, -Math.PI / 2, 1.1);
            f.bed(-2.7, 1.0, Math.PI / 2, 0.95, C.fabric2);
            f.desk(-2.6, 2.05, Math.PI);
            f.rug(-2.4, 1.0, 1.2, 0.9, C.leaf);
            f.toilet(-0.95, -2.05, 0);
            f.shower(-0.2, -1.95);
            f.counterL(1.6, -2.15);
            f.fridge(3.35, -1.2);
            f.dining(2.5, 0.05);
            f.sofa(0.6, 1.9, Math.PI, C.fabric);
            f.armchair(-0.75, 1.0, Math.PI / 2);
            f.coffeeTable(0.6, 1.05);
            f.tv(0.6, -0.65, 0);
            f.rug(0.4, 1.2, 2.6, 1.6, C.lamp);
            f.plant(3.4, 2.1); f.plant(-1.05, -0.5, 0.8);
            f.balconyRail(-3.75, 3.75, 3.7);
            f.clothesline(-2.4, 3.1);
            f.plant(3.2, 3.35); f.plant(2.6, 3.4, 0.8);
            f.chair(1.0, 3.1, Math.PI * 0.15); f.chair(1.7, 3.1, -Math.PI * 0.15);
        },
        lights: [[-2.5, -1.1], [-2.5, 1.3], [-0.5, -1.6], [2, -1.6], [2.4, 0.2], [0.6, 1.2], [0, 3]],
        cam: [9, 10.5, 11],
    },
};

export function mountTour(root) {
    const stage = root.querySelector('[data-tour-stage]');
    const labelsEl = root.querySelector('[data-tour-labels]');
    const roomsEl = root.querySelector('[data-tour-rooms]');
    const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const fine = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
    const counts = JSON.parse(root.querySelector('[data-tour-counts]')?.textContent || '{}');

    let renderer;
    try { renderer = new THREE.WebGLRenderer({ antialias: true }); } catch (e) { stage.classList.add('is-fallback'); return; }
    renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, fine ? 1.75 : 1.5));
    renderer.shadowMap.enabled = true;
    renderer.shadowMap.type = THREE.PCFSoftShadowMap;
    renderer.toneMapping = THREE.ACESFilmicToneMapping;
    renderer.outputColorSpace = THREE.SRGBColorSpace;
    stage.prepend(renderer.domElement);
    renderer.domElement.setAttribute('aria-label', 'Model 3D unit, seret untuk memutar');

    const scene = new THREE.Scene();
    const DAY_BG = new THREE.Color('#dfe5f6');
    const NIGHT_BG = new THREE.Color('#121640');
    scene.background = DAY_BG.clone();

    const camera = new THREE.PerspectiveCamera(36, 1, 0.1, 200);
    const controls = new OrbitControls(camera, renderer.domElement);
    controls.enableDamping = true;
    controls.dampingFactor = 0.08;
    controls.maxPolarAngle = 1.2;
    controls.minDistance = 4;
    controls.maxDistance = 22;
    controls.enablePan = false;
    controls.autoRotate = !reduce;
    controls.autoRotateSpeed = 0.7;

    /* Cahaya */
    const hemi = new THREE.HemisphereLight('#eaf0ff', '#a88f75', 1.3);
    const sun = new THREE.DirectionalLight('#fff1dd', 2.2);
    sun.position.set(-6, 10, 7);
    sun.castShadow = true;
    sun.shadow.mapSize.set(2048, 2048);
    Object.assign(sun.shadow.camera, { left: -7, right: 7, top: 7, bottom: -7, near: 1, far: 30 });
    sun.shadow.bias = -0.0004;
    sun.shadow.normalBias = 0.02;
    scene.add(hemi, sun);

    // Alas bayangan
    const ground = new THREE.Mesh(new THREE.CircleGeometry(14, 64), new THREE.ShadowMaterial({ opacity: 0.18 }));
    ground.rotation.x = -Math.PI / 2;
    ground.position.y = -0.16;
    ground.receiveShadow = true;
    scene.add(ground);

    const mats = {};
    const mat = (color, opts = {}) => {
        const key = color + JSON.stringify(opts);
        if (!mats[key]) mats[key] = new THREE.MeshStandardMaterial({ color, roughness: 0.75, ...opts });
        return mats[key];
    };

    let current = null;      // { group, items, labels, lights, plan }
    let night = false;
    const built = {};

    /* ---------------- Builder ---------------- */
    function build(type) {
        const plan = PLANS[type];
        const group = new THREE.Group();
        const items = [];   // perabot untuk animasi masuk
        const walls = [];
        const add = (mesh, parent = group, animate = true) => {
            mesh.castShadow = true; mesh.receiveShadow = true;
            parent.add(mesh);
            if (animate) items.push(mesh);
            return mesh;
        };
        const box = (w, h, d, color, x, y, z, opts) => {
            const m = new THREE.Mesh(new RoundedBoxGeometry(w, h, d, 2, Math.min(0.03, w / 4, h / 4, d / 4)), mat(color, opts));
            m.position.set(x, y, z);
            return m;
        };
        const piece = (x, z, rot = 0) => { const g = new THREE.Group(); g.position.set(x, 0, z); g.rotation.y = rot; group.add(g); items.push(g); return g; };

        // Pelat lantai & lantai tiap ruang
        const slab = box(plan.w + 0.5, 0.3, plan.d + plan.balcony + 0.5, '#4a4f7a', 0, -0.16, plan.balcony / 2);
        slab.receiveShadow = true; group.add(slab);
        plan.rooms.forEach((r) => {
            const color = { wood: C.wood, tile: C.tile, balcony: C.balcony }[r.floor];
            const fl = new THREE.Mesh(new THREE.BoxGeometry(r.x[1] - r.x[0], 0.04, r.z[1] - r.z[0]), mat(color, { roughness: r.floor === 'tile' ? 0.35 : 0.8 }));
            fl.position.set((r.x[0] + r.x[1]) / 2, 0.01, (r.z[0] + r.z[1]) / 2);
            fl.receiveShadow = true;
            group.add(fl);
        });

        // Dinding (dengan celah pintu) + list warna di atas
        const wall = (x1, z1, x2, z2, gaps = [], h = WALL_H) => {
            const len = Math.hypot(x2 - x1, z2 - z1);
            const dir = new THREE.Vector2(x2 - x1, z2 - z1).normalize();
            const cuts = [0];
            for (let i = 0; i < gaps.length; i += 2) cuts.push(gaps[i], gaps[i + 1]);
            cuts.push(len);
            for (let i = 0; i < cuts.length; i += 2) {
                const a = cuts[i], b = cuts[i + 1];
                if (b - a < 0.05) continue;
                const mid = (a + b) / 2;
                const seg = new THREE.Group();
                seg.position.set(x1 + dir.x * mid, 0, z1 + dir.y * mid);
                seg.rotation.y = -Math.atan2(dir.y, dir.x);
                const body = new THREE.Mesh(new THREE.BoxGeometry(b - a + WALL_T, h, WALL_T), mat(C.wall, { roughness: 0.9 }));
                body.position.y = h / 2;
                body.castShadow = body.receiveShadow = true;
                const cap = new THREE.Mesh(new THREE.BoxGeometry(b - a + WALL_T + 0.002, 0.04, WALL_T + 0.004), mat(C.wallTop));
                cap.position.y = h + 0.02;
                seg.add(body, cap);
                group.add(seg);
                walls.push(seg);
            }
        };
        plan.walls.forEach(([x1, z1, x2, z2, g]) => wall(x1, z1, x2, z2, g || []));
        plan.front.forEach(([x1, z1, x2, z2, g]) => wall(x1, z1, x2, z2, g || [], 0.9));

        // Jendela di dinding belakang (kaca yang menyala saat malam)
        const windows = [];
        [-plan.w / 4, plan.w / 4].forEach((x) => {
            const glass = new THREE.Mesh(new THREE.PlaneGeometry(1.1, 1.0), new THREE.MeshStandardMaterial({ color: C.glass, emissive: '#ffd27a', emissiveIntensity: 0, roughness: 0.1 }));
            glass.position.set(x, 1.45, -plan.d / 2 + WALL_T / 2 + 0.005);
            group.add(glass);
            windows.push(glass);
        });

        /* ----- Perabot ----- */
        const f = {
            bed(x, z, rot, w, cover = C.fabric) {
                const g = piece(x, z, rot);
                g.add(box(w + 0.1, 0.32, 2.05, C.woodDark, 0, 0.16, 0));
                g.add(box(w, 0.2, 1.95, C.white, 0, 0.42, 0.02));
                g.add(box(w + 0.02, 0.06, 1.25, cover, 0, 0.53, 0.35));
                g.add(box(w + 0.1, 0.7, 0.08, C.woodDark, 0, 0.5, -1.02));
                [-w / 4, w / 4].forEach((px) => g.add(box(w / 2.6, 0.12, 0.35, C.white, px, 0.57, -0.75)));
                g.children.forEach((m) => { m.castShadow = m.receiveShadow = true; });
            },
            nightstand(x, z) { const g = piece(x, z); g.add(box(0.42, 0.5, 0.38, C.woodDark, 0, 0.25, 0)); g.add(box(0.14, 0.26, 0.14, C.lamp, 0, 0.63, 0, { emissive: C.lamp, emissiveIntensity: 0.2 })); },
            wardrobe(x, z, rot, w = 1.2) { const g = piece(x, z, rot); g.add(box(w, 2.0, 0.55, C.white, 0, 1.0, 0)); g.add(box(0.03, 1.7, 0.02, '#c9cbd6', 0, 1.0, 0.28)); },
            rug(x, z, w, d, color) { const m = new THREE.Mesh(new THREE.BoxGeometry(w, 0.015, d), mat(color, { roughness: 1 })); m.position.set(x, 0.04, z); m.receiveShadow = true; group.add(m); },
            toilet(x, z, rot) { const g = piece(x, z, rot); g.add(box(0.38, 0.4, 0.5, C.white, 0, 0.2, 0.05)); g.add(box(0.4, 0.35, 0.16, C.white, 0, 0.55, -0.2)); },
            shower(x, z) {
                const g = piece(x, z);
                g.add(box(0.8, 0.06, 0.8, '#d7dbe6', 0, 0.04, 0));
                const glass = new THREE.Mesh(new THREE.BoxGeometry(0.02, 1.9, 0.8), new THREE.MeshPhysicalMaterial({ color: '#dff2ff', transparent: true, opacity: 0.35, roughness: 0.05 }));
                glass.position.set(-0.4, 0.95, 0); g.add(glass);
                g.add(box(0.06, 0.06, 0.2, C.steel, 0.25, 1.9, -0.3));
            },
            counterLine(x, z, len, rot) {
                const g = piece(x, z, rot);
                g.add(box(len, 0.85, 0.6, C.white, 0, 0.43, 0));
                g.add(box(len + 0.02, 0.05, 0.62, '#3b3f5c', 0, 0.88, 0));
                g.add(box(0.5, 0.02, 0.38, C.steel, -len / 4, 0.91, 0, { metalness: 0.6, roughness: 0.3 }));
                g.add(box(0.55, 0.02, 0.45, '#1b1d2c', len / 4, 0.91, 0));
                g.add(box(len, 0.6, 0.35, C.white, 0, 1.75, -0.12));
            },
            counterL(x, z) { this.counterLine(x, z, 2.6, 0); const g = piece(x - 0.95, z + 0.75, Math.PI / 2); g.add(box(1.2, 0.85, 0.6, C.white, 0, 0.43, 0)); g.add(box(1.22, 0.05, 0.62, '#3b3f5c', 0, 0.88, 0)); },
            fridge(x, z) { const g = piece(x, z); g.add(box(0.6, 1.8, 0.62, '#dfe2ea', 0, 0.9, 0, { metalness: 0.3, roughness: 0.35 })); g.add(box(0.03, 0.5, 0.03, C.steel, 0.22, 1.2, 0.32)); },
            sofa(x, z, rot, color) {
                const g = piece(x, z, rot);
                g.add(box(1.9, 0.42, 0.85, color, 0, 0.21, 0));
                g.add(box(1.9, 0.5, 0.2, color, 0, 0.55, -0.33));
                g.add(box(0.18, 0.55, 0.85, color, -0.86, 0.32, 0));
                g.add(box(0.18, 0.55, 0.85, color, 0.86, 0.32, 0));
                [-0.45, 0.45].forEach((px) => g.add(box(0.8, 0.12, 0.6, '#7d8ae6', px, 0.48, 0.06)));
            },
            armchair(x, z, rot) { const g = piece(x, z, rot); g.add(box(0.8, 0.4, 0.78, C.fabric2, 0, 0.2, 0)); g.add(box(0.8, 0.45, 0.16, C.fabric2, 0, 0.55, -0.31)); },
            coffeeTable(x, z) { const g = piece(x, z); g.add(box(0.95, 0.06, 0.55, C.woodDark, 0, 0.4, 0)); [-0.4, 0.4].forEach((px) => [-0.22, 0.22].forEach((pz) => g.add(box(0.05, 0.38, 0.05, C.dark, px, 0.19, pz)))); g.add(box(0.2, 0.08, 0.14, C.leaf, 0.2, 0.47, 0)); },
            tv(x, z, rot) { const g = piece(x, z, rot); g.add(box(1.6, 0.45, 0.4, C.woodDark, 0, 0.23, 0)); g.add(box(1.25, 0.72, 0.05, '#101222', 0, 0.95, -0.05, { emissive: '#3346a8', emissiveIntensity: 0.25 })); },
            desk(x, z, rot) { const g = piece(x, z, rot); g.add(box(1.1, 0.05, 0.55, C.wood, 0, 0.74, 0)); g.add(box(0.05, 0.72, 0.5, C.dark, -0.5, 0.36, 0)); g.add(box(0.05, 0.72, 0.5, C.dark, 0.5, 0.36, 0)); g.add(box(0.45, 0.3, 0.03, '#151829', 0.1, 0.92, -0.12, { emissive: '#4cc9f0', emissiveIntensity: 0.2 })); },
            dining(x, z) {
                const g = piece(x, z);
                g.add(box(1.3, 0.05, 0.8, C.wood, 0, 0.75, 0));
                g.add(box(0.1, 0.72, 0.1, C.dark, 0, 0.37, 0));
                [[-0.45, -0.55, 0], [0.45, -0.55, 0], [-0.45, 0.55, Math.PI], [0.45, 0.55, Math.PI]].forEach(([cx, cz, r]) => {
                    const c = new THREE.Group(); c.position.set(cx, 0, cz); c.rotation.y = r;
                    c.add(box(0.42, 0.05, 0.42, C.ink, 0, 0.45, 0)); c.add(box(0.42, 0.45, 0.05, C.ink, 0, 0.7, -0.19));
                    c.add(box(0.04, 0.43, 0.04, C.dark, 0, 0.22, 0));
                    g.add(c);
                });
            },
            chair(x, z, rot) { const g = piece(x, z, rot); g.add(box(0.45, 0.05, 0.45, C.lamp, 0, 0.42, 0)); g.add(box(0.45, 0.4, 0.05, C.lamp, 0, 0.65, -0.2)); g.add(box(0.04, 0.4, 0.04, C.dark, 0, 0.2, 0)); },
            plant(x, z, s = 1) {
                const g = piece(x, z); g.scale.setScalar(s);
                const pot = new THREE.Mesh(new THREE.CylinderGeometry(0.17, 0.13, 0.32, 16), mat(C.pot)); pot.position.y = 0.16; pot.castShadow = true; g.add(pot);
                const leaf = new THREE.Mesh(new THREE.IcosahedronGeometry(0.3, 1), mat(C.plant, { flatShading: true })); leaf.position.y = 0.62; leaf.castShadow = true; g.add(leaf);
                const leaf2 = leaf.clone(); leaf2.scale.setScalar(0.7); leaf2.position.set(0.12, 0.88, 0.05); g.add(leaf2);
            },
            balconyRail(x1, x2, z) {
                const g = piece(0, z);
                g.add(box(x2 - x1, 0.05, 0.06, C.dark, (x1 + x2) / 2, 1.0, 0));
                for (let x = x1; x <= x2 + 0.01; x += 0.3) g.add(box(0.025, 1.0, 0.025, C.dark, x, 0.5, 0));
            },
            clothesline(x, z) {
                const g = piece(x, z);
                g.add(box(0.04, 1.6, 0.04, C.steel, -0.9, 0.8, 0)); g.add(box(0.04, 1.6, 0.04, C.steel, 0.9, 0.8, 0));
                g.add(box(1.8, 0.02, 0.02, C.steel, 0, 1.55, 0));
                [[-0.55, C.fabric2], [0, C.lamp], [0.5, C.leaf]].forEach(([cx, col]) => g.add(box(0.36, 0.5, 0.02, col, cx, 1.28, 0)));
            },
        };
        plan.furniture(f);

        // Lampu ruangan (menyala saat malam)
        const lights = plan.lights.map(([x, z]) => {
            const l = new THREE.PointLight('#ffcf8a', 0, 5.5, 1.6);
            l.position.set(x, 2.1, z);
            group.add(l);
            const bulb = new THREE.Mesh(new THREE.SphereGeometry(0.07, 12, 8), new THREE.MeshStandardMaterial({ color: '#fff3d6', emissive: '#ffcf8a', emissiveIntensity: 0 }));
            bulb.position.copy(l.position);
            group.add(bulb);
            return { l, bulb };
        });

        // Label ruangan (DOM)
        const labels = plan.rooms.map((r) => {
            const b = document.createElement('button');
            b.type = 'button';
            b.className = 'tour__label';
            b.textContent = r.name;
            b.hidden = true;
            const pos = new THREE.Vector3((r.x[0] + r.x[1]) / 2, 1.2, (r.z[0] + r.z[1]) / 2);
            b.addEventListener('click', () => focusRoom(r));
            labelsEl.appendChild(b);
            return { el: b, pos, room: r };
        });

        scene.add(group);
        group.visible = false;
        return { group, items, walls, labels, lights, windows, plan, type };
    }

    /* ---------------- Interaksi ---------------- */
    const roomBtns = (state) => {
        roomsEl.innerHTML = '';
        state.plan.rooms.forEach((r) => {
            const b = document.createElement('button');
            b.type = 'button';
            b.className = 'tour__room';
            b.textContent = r.name;
            b.addEventListener('click', () => focusRoom(r));
            roomsEl.appendChild(b);
        });
        const all = document.createElement('button');
        all.type = 'button';
        all.className = 'tour__room tour__room--all';
        all.innerHTML = '<i class="ri-fullscreen-line" aria-hidden="true"></i> Lihat semua';
        all.addEventListener('click', () => resetView(true));
        roomsEl.appendChild(all);
    };

    function focusRoom(r) {
        const cx = (r.x[0] + r.x[1]) / 2;
        const cz = (r.z[0] + r.z[1]) / 2;
        const size = Math.max(r.x[1] - r.x[0], r.z[1] - r.z[0]);
        controls.autoRotate = false;
        spinToggle.checked = false;
        const dir = camera.position.clone().sub(controls.target).normalize();
        const dist = Math.max(4.2, size * 2.1);
        const to = new THREE.Vector3(cx, 0.6, cz).add(dir.multiplyScalar(dist));
        to.y = Math.max(to.y, 3.2);
        gsap.to(controls.target, { x: cx, y: 0.6, z: cz, duration: reduce ? 0 : 1.2, ease: 'power3.inOut' });
        gsap.to(camera.position, { x: to.x, y: to.y, z: to.z, duration: reduce ? 0 : 1.2, ease: 'power3.inOut' });
        roomsEl.querySelectorAll('.tour__room').forEach((b) => b.classList.toggle('is-active', b.textContent === r.name));
    }

    function resetView(animate) {
        const [x, y, z] = current.plan.cam;
        const narrow = stage.clientWidth < 760;
        const k = narrow ? 1.35 : 1;
        gsap.to(controls.target, { x: 0, y: 0.4, z: current.plan.balcony / 2, duration: animate && !reduce ? 1.2 : 0, ease: 'power3.inOut' });
        gsap.to(camera.position, { x: x * k, y: y * k, z: z * k, duration: animate && !reduce ? 1.2 : 0, ease: 'power3.inOut' });
        roomsEl.querySelectorAll('.tour__room').forEach((b) => b.classList.remove('is-active'));
    }

    function intro(state) {
        if (reduce) return;
        state.walls.forEach((w, i) => { w.scale.y = 0.001; gsap.to(w.scale, { y: 1, duration: 0.8, ease: 'power3.out', delay: 0.1 + i * 0.03 }); });
        state.items.forEach((m, i) => {
            const y = m.position.y;
            m.position.y = y + 3.5;
            m.scale.setScalar(m.scale.x * 0.001 || 0.001);
            const s = m.userData.s || (m.userData.s = 1);
            gsap.to(m.position, { y, duration: 0.9, ease: 'bounce.out', delay: 0.5 + i * 0.05 });
            gsap.to(m.scale, { x: s, y: s, z: s, duration: 0.5, ease: 'back.out(2)', delay: 0.5 + i * 0.05 });
        });
    }

    function show(type) {
        if (current) {
            current.group.visible = false;
            current.labels.forEach((l) => { l.el.hidden = true; });
        }
        if (!built[type]) {
            built[type] = build(type);
            built[type].items.forEach((m) => { m.userData.s = m.scale.x; });
        }
        current = built[type];
        current.group.visible = true;
        current.labels.forEach((l) => { l.el.hidden = false; });
        roomBtns(current);
        applyNight(true);
        resetView(false);
        intro(current);
        if (!reduce) gsap.fromTo(camera.position, { y: camera.position.y + 6 }, { y: camera.position.y, duration: 1.6, ease: 'expo.out' });

        root.querySelectorAll('[data-tour-type]').forEach((b) => {
            const on = b.dataset.tourType === String(type);
            b.classList.toggle('is-active', on);
            b.setAttribute('aria-checked', String(on));
        });
        const price = Number(root.dataset[`price${type}`] || 0);
        root.querySelector('[data-tour-price]').textContent = 'Rp ' + price.toLocaleString('id-ID');
        root.querySelector('[data-tour-count]').textContent = counts[type] ?? 0;
    }

    function applyNight(instant = false) {
        if (!current) return;
        const d = instant || reduce ? 0 : 1;
        gsap.to(hemi, { intensity: night ? 0.18 : 1.3, duration: d });
        gsap.to(sun, { intensity: night ? 0.05 : 2.2, duration: d });
        gsap.to(scene.background, { r: (night ? NIGHT_BG : DAY_BG).r, g: (night ? NIGHT_BG : DAY_BG).g, b: (night ? NIGHT_BG : DAY_BG).b, duration: d });
        current.lights.forEach(({ l, bulb }) => {
            gsap.to(l, { intensity: night ? 3.2 : 0, duration: d });
            gsap.to(bulb.material, { emissiveIntensity: night ? 3 : 0, duration: d });
        });
        current.windows.forEach((w) => gsap.to(w.material, { emissiveIntensity: night ? 0.9 : 0, duration: d }));
        stage.classList.toggle('is-night', night);
    }

    root.querySelectorAll('[data-tour-type]').forEach((b) => b.addEventListener('click', () => show(Number(b.dataset.tourType))));
    const nightToggle = root.querySelector('[data-tour-night]');
    nightToggle?.addEventListener('change', () => { night = nightToggle.checked; applyNight(); });
    const spinToggle = root.querySelector('[data-tour-spin]');
    if (reduce && spinToggle) spinToggle.checked = false;
    spinToggle?.addEventListener('change', () => { controls.autoRotate = spinToggle.checked; });
    controls.addEventListener('start', () => { if (controls.autoRotate) { controls.autoRotate = false; spinToggle.checked = false; } });

    /* ---------------- Ukuran & loop ---------------- */
    const resize = () => {
        const w = stage.clientWidth, h = stage.clientHeight;
        renderer.setSize(w, h, false);
        camera.aspect = w / h;
        camera.updateProjectionMatrix();
    };
    new ResizeObserver(resize).observe(stage);
    resize();

    const initial = new URLSearchParams(window.location.search).get('tipe') === '36' ? 36 : 24;
    show(initial);

    let visible = true;
    new IntersectionObserver(([e]) => { visible = e.isIntersecting; }).observe(stage);
    const v = new THREE.Vector3();
    let first = true;
    renderer.setAnimationLoop(() => {
        if (!visible || document.hidden) return;
        controls.update();
        renderer.render(scene, camera);
        const w = stage.clientWidth, h = stage.clientHeight;
        current?.labels.forEach(({ el, pos }) => {
            v.copy(pos).project(camera);
            const behind = v.z > 1;
            el.style.transform = `translate(${(v.x * 0.5 + 0.5) * w}px, ${(-v.y * 0.5 + 0.5) * h}px) translate(-50%, -50%)`;
            el.style.opacity = behind ? '0' : '1';
        });
        if (first) { first = false; stage.classList.add('is-ready'); }
    });
}
