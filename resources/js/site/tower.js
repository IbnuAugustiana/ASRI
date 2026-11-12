/**
 * Scene 3D gedung rusun ASRI.
 * Setiap unit di database = satu modul. Warna jendela mengikuti status unit:
 * hijau = tersedia, kuning lampu = dihuni, redup = perawatan.
 *
 * mode "city"  : beranda, semua blok berdiri berjajar.
 * mode "focus" : detail unit, satu blok dengan unit terpilih disorot.
 */
import * as THREE from 'three';
import { RoundedBoxGeometry } from 'three/addons/geometries/RoundedBoxGeometry.js';
import { EffectComposer } from 'three/addons/postprocessing/EffectComposer.js';
import { RenderPass } from 'three/addons/postprocessing/RenderPass.js';
import { UnrealBloomPass } from 'three/addons/postprocessing/UnrealBloomPass.js';
import { OutputPass } from 'three/addons/postprocessing/OutputPass.js';
import gsap from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { onIntro } from './motion.js';

gsap.registerPlugin(ScrollTrigger);

const STATUS = {
    tersedia:    { color: '#2ed48a', base: 2.1, label: 'Tersedia' },
    dihuni:      { color: '#ffa63d', base: 1.25, label: 'Dihuni' },
    maintenance: { color: '#3b4180', base: 0.4, label: 'Perawatan' },
};
const FACADES = ['#c9cffb', '#bdeedb', '#ffd3bf', '#b6d9ff', '#f1c6e0', '#e8e1c7'];
const CELL = { w: 1, h: 0.86, d: 1.7 };
const PODIUM = 0.55;

const rupiah = (n) => 'Rp ' + Math.round(n).toLocaleString('id-ID');
const clamp = (v, a, b) => Math.min(b, Math.max(a, v));

function rng(seed) {
    return () => {
        seed |= 0; seed = (seed + 0x6d2b79f5) | 0;
        let t = Math.imul(seed ^ (seed >>> 15), 1 | seed);
        t = (t + Math.imul(t ^ (t >>> 7), 61 | t)) ^ t;
        return ((t ^ (t >>> 14)) >>> 0) / 4294967296;
    };
}

function dotTexture(inner = 'rgba(255,255,255,1)', outer = 'rgba(255,255,255,0)') {
    const c = document.createElement('canvas');
    c.width = c.height = 128;
    const g = c.getContext('2d');
    const grad = g.createRadialGradient(64, 64, 0, 64, 64, 64);
    grad.addColorStop(0, inner);
    grad.addColorStop(0.25, inner.replace(/[\d.]+\)$/, '0.6)'));
    grad.addColorStop(1, outer);
    g.fillStyle = grad;
    g.fillRect(0, 0, 128, 128);
    const tex = new THREE.CanvasTexture(c);
    tex.colorSpace = THREE.SRGBColorSpace;
    return tex;
}

function makeSky() {
    const mat = new THREE.ShaderMaterial({
        side: THREE.BackSide,
        depthWrite: false,
        fog: false,
        uniforms: {
            cTop: { value: new THREE.Color('#070a2a') },
            cMid: { value: new THREE.Color('#1a1e62') },
            cLow: { value: new THREE.Color('#4a338b') },
            cGlow: { value: new THREE.Color('#cf6890') },
            cHorizon: { value: new THREE.Color('#ffa27a') },
            cGround: { value: new THREE.Color('#120e34') },
        },
        vertexShader: /* glsl */`
            varying vec3 vPos;
            void main() {
                vPos = position;
                gl_Position = projectionMatrix * modelViewMatrix * vec4(position, 1.0);
            }`,
        fragmentShader: /* glsl */`
            uniform vec3 cTop, cMid, cLow, cGlow, cHorizon, cGround;
            varying vec3 vPos;
            void main() {
                float h = normalize(vPos).y;
                vec3 c;
                if (h < 0.0)       c = mix(cHorizon, cGround, smoothstep(0.0, 0.06, -h));
                else if (h < 0.05) c = mix(cHorizon, cGlow, h / 0.05);
                else if (h < 0.17) c = mix(cGlow, cLow, (h - 0.05) / 0.12);
                else if (h < 0.42) c = mix(cLow, cMid, (h - 0.17) / 0.25);
                else               c = mix(cMid, cTop, (h - 0.42) / 0.58);
                gl_FragColor = vec4(c, 1.0);
                #include <tonemapping_fragment>
                #include <colorspace_fragment>
            }`,
    });
    return new THREE.Mesh(new THREE.SphereGeometry(220, 48, 24), mat);
}

function makeStars(count, rand) {
    const pos = new Float32Array(count * 3);
    for (let i = 0; i < count; i++) {
        const th = rand() * Math.PI * 2;
        const y = 0.14 + Math.pow(rand(), 0.7) * 0.84;
        const rr = Math.sqrt(1 - y * y);
        pos[i * 3] = Math.cos(th) * rr * 200;
        pos[i * 3 + 1] = y * 200;
        pos[i * 3 + 2] = Math.sin(th) * rr * 200;
    }
    const geo = new THREE.BufferGeometry();
    geo.setAttribute('position', new THREE.BufferAttribute(pos, 3));
    return new THREE.Points(geo, new THREE.PointsMaterial({
        color: '#e8ecff', size: 1.7, sizeAttenuation: false, transparent: true, opacity: 0.85, depthWrite: false, fog: false,
    }));
}

function ellipseRing(rx, rz, width, seg = 160) {
    const pos = [];
    const idx = [];
    for (let i = 0; i <= seg; i++) {
        const a = (i / seg) * Math.PI * 2;
        const c = Math.cos(a), s = Math.sin(a);
        pos.push((rx - width / 2) * c, 0, (rz - width / 2) * s, (rx + width / 2) * c, 0, (rz + width / 2) * s);
        if (i < seg) {
            const k = i * 2;
            idx.push(k, k + 2, k + 1, k + 1, k + 2, k + 3);
        }
    }
    const geo = new THREE.BufferGeometry();
    geo.setAttribute('position', new THREE.Float32BufferAttribute(pos, 3));
    geo.setIndex(idx);
    geo.computeVertexNormals();
    return geo;
}

function makeLabel(text) {
    const c = document.createElement('canvas');
    c.width = 320; c.height = 110;
    const g = c.getContext('2d');
    g.fillStyle = 'rgba(16, 20, 60, 0.78)';
    g.strokeStyle = 'rgba(255, 255, 255, 0.28)';
    g.lineWidth = 3;
    g.beginPath();
    if (g.roundRect) g.roundRect(10, 18, 300, 74, 37); else g.rect(10, 18, 300, 74);
    g.fill(); g.stroke();
    g.fillStyle = '#eef0ff';
    g.font = '600 34px Unbounded, "Plus Jakarta Sans", sans-serif';
    g.textAlign = 'center';
    g.textBaseline = 'middle';
    g.fillText(text, 160, 57);
    const tex = new THREE.CanvasTexture(c);
    tex.colorSpace = THREE.SRGBColorSpace;
    tex.anisotropy = 4;
    const s = new THREE.Sprite(new THREE.SpriteMaterial({ map: tex, transparent: true, depthWrite: false, fog: false, toneMapped: false }));
    s.scale.set(3.2, 1.1, 1);
    return s;
}

function groupTowers(units) {
    const byBlok = new Map();
    units.forEach((u) => {
        if (!byBlok.has(u.b)) byBlok.set(u.b, []);
        byBlok.get(u.b).push(u);
    });
    const towers = [...byBlok.entries()].map(([blok, list]) => {
        const floors = [...new Set(list.map((u) => u.l))].sort((a, b) => a - b);
        const rows = floors.map((f) => list
            .filter((u) => u.l === f)
            .sort((a, b) => String(a.n).localeCompare(String(b.n), undefined, { numeric: true }) || a.id - b.id));
        return { blok, rows, cols: Math.max(...rows.map((r) => r.length)), count: list.length };
    });
    towers.sort((a, b) => b.count - a.count || a.blok.localeCompare(b.blok));
    return towers.slice(0, 6);
}

/* ===================================================================== */

export function mountTower(container) {
    const dataEl = container.querySelector('script[type="application/json"]');
    if (!dataEl) return;
    let data;
    try { data = JSON.parse(dataEl.textContent); } catch (e) { return; }
    const units = data.units || [];
    if (!units.length) return;

    const mode = data.mode === 'focus' ? 'focus' : 'city';
    const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const fine = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
    const lowPower = !fine || (navigator.hardwareConcurrency || 8) <= 4;
    const rand = rng(20240518);

    let renderer;
    try {
        renderer = new THREE.WebGLRenderer({ antialias: true, powerPreference: 'high-performance' });
    } catch (e) {
        container.classList.add('is-fallback');
        return;
    }
    renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, lowPower ? 1.5 : 1.75));
    renderer.toneMapping = THREE.ACESFilmicToneMapping;
    renderer.toneMappingExposure = 1.05;
    renderer.outputColorSpace = THREE.SRGBColorSpace;
    renderer.shadowMap.enabled = !lowPower;
    renderer.shadowMap.type = THREE.PCFSoftShadowMap;
    renderer.domElement.setAttribute('aria-hidden', 'true');
    container.prepend(renderer.domElement);

    const scene = new THREE.Scene();
    scene.fog = new THREE.Fog('#e88684', 46, 140);
    const camera = new THREE.PerspectiveCamera(30, 1, 0.1, 500);

    scene.add(makeSky());
    const stars = makeStars(lowPower ? 220 : 420, rand);
    scene.add(stars);

    /* ---- Cahaya ---- */
    scene.add(new THREE.HemisphereLight('#aab4ff', '#2a1d4f', 1.05));
    const sun = new THREE.DirectionalLight('#ffb088', 2.1);
    sun.position.set(30, 13, -12);
    const fill = new THREE.DirectionalLight('#8fa2ff', 0.85);
    fill.position.set(-22, 14, 26);
    scene.add(sun, fill);

    const sunSprite = new THREE.Sprite(new THREE.SpriteMaterial({
        map: dotTexture('rgba(255,190,140,1)'), color: '#ffc49a', blending: THREE.AdditiveBlending,
        depthWrite: false, fog: false, transparent: true,
    }));
    sunSprite.position.copy(sun.position.clone().normalize().multiplyScalar(170)).setY(9);
    sunSprite.scale.set(46, 46, 1);
    scene.add(sunSprite);

    /* ---- Dunia (diputar sebagai satu kesatuan) ---- */
    const world = new THREE.Group();
    scene.add(world);

    const M = {
        podium: new THREE.MeshStandardMaterial({ color: '#4d4a9a', roughness: 0.85 }),
        lobby: new THREE.MeshStandardMaterial({ color: '#141533', emissive: '#ffd59a', emissiveIntensity: 1.6 }),
        trim: new THREE.MeshStandardMaterial({ color: '#f3f1ff', roughness: 0.55 }),
        core: new THREE.MeshStandardMaterial({ color: '#7d84c9', roughness: 0.8 }),
        coreWin: new THREE.MeshStandardMaterial({ color: '#141533', emissive: '#ffcf8a', emissiveIntensity: 0.9 }),
        tank: new THREE.MeshStandardMaterial({ color: '#dfe3ff', roughness: 0.4, metalness: 0.3 }),
        pole: new THREE.MeshStandardMaterial({ color: '#2a2a5a', roughness: 0.6 }),
        ground: new THREE.MeshStandardMaterial({ color: '#211b55', roughness: 0.95 }),
        road: new THREE.MeshStandardMaterial({ color: '#3a3480', roughness: 0.9 }),
    };
    const G = {
        body: new RoundedBoxGeometry(CELL.w - 0.05, CELL.h - 0.05, CELL.d, 2, 0.045),
        win: new THREE.PlaneGeometry(CELL.w * 0.6, CELL.h * 0.5),
        slab: new THREE.BoxGeometry(CELL.w - 0.04, 0.05, 0.36),
        rail: new THREE.BoxGeometry(CELL.w - 0.04, 0.2, 0.03),
        coreWin: new THREE.PlaneGeometry(0.34, 0.2),
    };

    const towers = groupTowers(units);
    const n = towers.length;
    const maxW = Math.max(...towers.map((t) => t.cols)) * CELL.w;
    const spacing = maxW + 2.6;

    // Blok terbesar di tengah, yang kecil ke tepi
    const slots = [...Array(n).keys()].sort((a, b) => Math.abs(a - (n - 1) / 2) - Math.abs(b - (n - 1) / 2) || a - b);

    const built = [];
    const allModules = [];
    const hitList = [];
    const beacons = [];
    let focusModule = null;

    towers.forEach((t, k) => {
        const off = mode === 'focus' ? 0 : slots[k] - (n - 1) / 2;
        const g = new THREE.Group();
        g.position.set(off * spacing, 0, mode === 'focus' ? 0 : (Math.abs(off) % 2 === 1 ? -3 : 0) - Math.abs(off) * 0.5);
        g.rotation.y = -off * 0.06;

        const width = t.cols * CELL.w;
        const height = t.rows.length * CELL.h;
        const facade = new THREE.MeshStandardMaterial({ color: FACADES[k % FACADES.length], roughness: 0.78, metalness: 0.02 });

        const podium = new THREE.Mesh(new RoundedBoxGeometry(width + 0.9, PODIUM, CELL.d + 0.9, 2, 0.06), M.podium);
        podium.position.y = PODIUM / 2;
        podium.castShadow = podium.receiveShadow = true;
        const lobby = new THREE.Mesh(new THREE.PlaneGeometry(width * 0.5, PODIUM * 0.5), M.lobby);
        lobby.position.set(0, PODIUM * 0.45, (CELL.d + 0.9) / 2 + 0.004);
        g.add(podium, lobby);

        const modules = [];
        t.rows.forEach((row, fi) => {
            row.forEach((u, ci) => {
                const st = STATUS[u.s] || STATUS.maintenance;
                const m = new THREE.Group();
                const body = new THREE.Mesh(G.body, facade);
                body.castShadow = body.receiveShadow = true;
                const winMat = new THREE.MeshStandardMaterial({ color: '#0d0f2e', emissive: st.color, emissiveIntensity: st.base, roughness: 0.35 });
                const winF = new THREE.Mesh(G.win, winMat);
                winF.position.set(0, 0.04, CELL.d / 2 + 0.004);
                const winB = new THREE.Mesh(G.win, winMat);
                winB.position.set(0, 0.04, -CELL.d / 2 - 0.004);
                winB.rotation.y = Math.PI;
                const slab = new THREE.Mesh(G.slab, M.trim);
                slab.position.set(0, -CELL.h / 2 + 0.05, CELL.d / 2 + 0.18);
                slab.castShadow = true;
                const rail = new THREE.Mesh(G.rail, M.trim);
                rail.position.set(0, -CELL.h / 2 + 0.17, CELL.d / 2 + 0.35);
                m.add(body, winF, winB, slab, rail);

                m.position.set((ci - (t.cols - 1) / 2) * CELL.w, PODIUM + fi * CELL.h + CELL.h / 2, 0);
                m.userData = { unit: u, home: m.position.clone(), winMat, base: st.base, color: new THREE.Color(st.color), tower: k, floor: fi, col: ci };
                [body, winF, winB].forEach((o) => { o.userData.module = m; hitList.push(o); });
                g.add(m);
                modules.push(m);
                allModules.push(m);
                if (data.focus && u.id === data.focus) focusModule = m;
            });
        });

        // Tangga / inti bangunan di sisi
        const coreH = height + 1.0;
        const coreGeo = new RoundedBoxGeometry(0.8, coreH, 1.1, 2, 0.05);
        coreGeo.translate(0, coreH / 2, 0); // titik pusat di dasar agar "tumbuh" dari tanah
        const core = new THREE.Mesh(coreGeo, M.core);
        core.position.set(width / 2 + 0.42, PODIUM, -0.15);
        core.castShadow = true;
        g.add(core);
        for (let f = 0; f < t.rows.length; f++) {
            const cw = new THREE.Mesh(G.coreWin, M.coreWin);
            cw.position.set(width / 2 + 0.42, PODIUM + f * CELL.h + CELL.h * 0.6, 0.41);
            g.add(cw);
        }

        // Atap: dak, tandon air, antena + lampu suar
        const roofY = PODIUM + height;
        const roof = new THREE.Mesh(new RoundedBoxGeometry(width + 0.25, 0.16, CELL.d + 0.25, 2, 0.04), M.trim);
        roof.position.y = roofY + 0.08;
        roof.castShadow = true;
        const tank = new THREE.Mesh(new THREE.CylinderGeometry(0.34, 0.34, 0.55, 20), M.tank);
        tank.position.set(-width * 0.22, roofY + 0.44, -0.1);
        tank.castShadow = true;
        const antenna = new THREE.Mesh(new THREE.CylinderGeometry(0.02, 0.025, 1.3, 6), M.pole);
        antenna.position.set(width * 0.3, roofY + 0.8, 0.2);
        const beaconMat = new THREE.MeshStandardMaterial({ color: '#220000', emissive: '#ff3b3b', emissiveIntensity: 3 });
        const beacon = new THREE.Mesh(new THREE.SphereGeometry(0.07, 12, 8), beaconMat);
        beacon.position.set(width * 0.3, roofY + 1.48, 0.2);
        beacons.push({ mat: beaconMat, phase: k * 1.3 });
        const roofParts = [roof, tank, antenna, beacon];
        g.add(...roofParts);

        world.add(g);
        built.push({ t, g, modules, roofParts, width, height: roofY, labelY: roofY + 2.1, core });
    });

    // Label nama blok (setelah font dimuat supaya pakai Unbounded)
    const labels = [];
    let introStarted = false;
    const showLabels = (delay) => labels.forEach((l) => gsap.to(l.material, { opacity: 1, duration: 0.8, delay }));
    (document.fonts?.ready || Promise.resolve()).then(() => {
        built.forEach((b) => {
            const label = makeLabel(`Blok ${b.t.blok}`);
            label.position.set(0, b.labelY, 0);
            if (!reduce) label.material.opacity = 0;
            b.g.add(label);
            labels.push(label);
        });
        if (!reduce && introStarted) showLabels(2.2);
    });

    /* ---- Lingkungan: tanah, jalan, lampu, pohon ---- */
    const spanX = mode === 'focus' ? built[0].width + 1.6 : (n - 1) * spacing + maxW + 1.6;
    const rx = spanX / 2 + 3.4;
    const rz = mode === 'focus' ? 4.6 : 8;

    const ground = new THREE.Mesh(new THREE.CircleGeometry(150, 72), M.ground);
    ground.rotation.x = -Math.PI / 2;
    ground.receiveShadow = true;
    world.add(ground);

    const road = new THREE.Mesh(ellipseRing(rx, rz, 1.1), M.road);
    road.position.y = 0.012;
    road.receiveShadow = true;
    world.add(road);

    const lampCount = mode === 'focus' ? 18 : 40;
    const lampGeo = new THREE.SphereGeometry(0.09, 10, 8);
    const lampMat = new THREE.MeshStandardMaterial({ color: '#2a1a00', emissive: '#ffd08a', emissiveIntensity: 3.2 });
    const lamps = new THREE.InstancedMesh(lampGeo, lampMat, lampCount);
    const poles = new THREE.InstancedMesh(new THREE.CylinderGeometry(0.025, 0.03, 0.9, 6), M.pole, lampCount);
    const tmp = new THREE.Object3D();
    for (let i = 0; i < lampCount; i++) {
        const a = (i / lampCount) * Math.PI * 2;
        const x = Math.cos(a) * (rx + 0.85);
        const z = Math.sin(a) * (rz + 0.85);
        tmp.position.set(x, 0.45, z); tmp.scale.setScalar(1); tmp.updateMatrix(); poles.setMatrixAt(i, tmp.matrix);
        tmp.position.set(x, 0.94, z); tmp.updateMatrix(); lamps.setMatrixAt(i, tmp.matrix);
    }
    world.add(lamps, poles);

    // Pohon (biar "asri"), menghindari tapak gedung dan jalan
    const footprints = built.map((b) => ({ x: b.g.position.x, z: b.g.position.z, hw: b.width / 2 + 1.5, hd: CELL.d / 2 + 1.3 }));
    const treeTarget = mode === 'focus' ? 22 : 64;
    const trees = [];
    let guard = 0;
    while (trees.length < treeTarget && guard++ < 4000) {
        const x = (rand() * 2 - 1) * (rx + 5);
        const z = (rand() * 2 - 1) * (rz + 5);
        const e = (x * x) / (rx * rx) + (z * z) / (rz * rz);
        if (e > 0.82 && e < 1.25) continue;                 // di atas jalan
        if (e > 2.4) continue;                              // terlalu jauh
        if (footprints.some((f) => Math.abs(x - f.x) < f.hw && Math.abs(z - f.z) < f.hd)) continue;
        trees.push({ x, z, s: 0.65 + rand() * 0.7, delay: rand() });
    }
    const canopyGeo = new THREE.IcosahedronGeometry(0.46, 0);
    const canopy = new THREE.InstancedMesh(canopyGeo, new THREE.MeshStandardMaterial({ roughness: 0.8, flatShading: true }), trees.length);
    const trunks = new THREE.InstancedMesh(new THREE.CylinderGeometry(0.05, 0.07, 0.5, 5), M.pole, trees.length);
    const greens = ['#2ed48a', '#1fae74', '#47c98e', '#169864', '#6fdca5'].map((c) => new THREE.Color(c));
    trees.forEach((tr, i) => canopy.setColorAt(i, greens[i % greens.length]));
    canopy.castShadow = true;
    world.add(canopy, trunks);

    const setTrees = (grow) => {
        trees.forEach((tr, i) => {
            const p = reduce ? 1 : clamp((grow * 1.5 - tr.delay * 0.5) / 0.6, 0, 1);
            const s = tr.s * (p < 1 ? 1 - Math.pow(1 - p, 3) : 1);
            tmp.position.set(tr.x, 0.25 * s, tr.z); tmp.scale.setScalar(Math.max(s, 0.0001)); tmp.rotation.set(0, tr.delay * 6, 0); tmp.updateMatrix();
            trunks.setMatrixAt(i, tmp.matrix);
            tmp.position.set(tr.x, 0.72 * s, tr.z); tmp.updateMatrix();
            canopy.setMatrixAt(i, tmp.matrix);
        });
        canopy.instanceMatrix.needsUpdate = true;
        trunks.instanceMatrix.needsUpdate = true;
    };
    const treeState = { grow: reduce ? 1 : 0 };
    setTrees(treeState.grow);

    // Kunang-kunang
    const pCount = lowPower ? 110 : 240;
    const pPos = new Float32Array(pCount * 3);
    const pCol = new Float32Array(pCount * 3);
    const pSpeed = new Float32Array(pCount);
    const maxH = Math.max(...built.map((b) => b.height)) + 3;
    const cLeaf = new THREE.Color('#7dffc0');
    const cLamp = new THREE.Color('#ffcf8a');
    for (let i = 0; i < pCount; i++) {
        pPos[i * 3] = (rand() * 2 - 1) * (rx + 2);
        pPos[i * 3 + 1] = rand() * maxH;
        pPos[i * 3 + 2] = (rand() * 2 - 1) * (rz + 2);
        const c = rand() > 0.45 ? cLeaf : cLamp;
        pCol[i * 3] = c.r; pCol[i * 3 + 1] = c.g; pCol[i * 3 + 2] = c.b;
        pSpeed[i] = 0.15 + rand() * 0.45;
    }
    const pGeo = new THREE.BufferGeometry();
    pGeo.setAttribute('position', new THREE.BufferAttribute(pPos, 3));
    pGeo.setAttribute('color', new THREE.BufferAttribute(pCol, 3));
    const particles = new THREE.Points(pGeo, new THREE.PointsMaterial({
        size: 0.16, map: dotTexture(), vertexColors: true, transparent: true, opacity: 0.9,
        depthWrite: false, blending: THREE.AdditiveBlending,
    }));
    world.add(particles);

    // Bayangan
    if (renderer.shadowMap.enabled) {
        sun.castShadow = true;
        sun.shadow.mapSize.set(2048, 2048);
        const sc = sun.shadow.camera;
        sc.left = -rx - 4; sc.right = rx + 4; sc.top = rx + 4; sc.bottom = -rx - 4; sc.near = 1; sc.far = 120;
        sun.shadow.bias = -0.0004;
        sun.shadow.normalBias = 0.03;
    }

    /* ---- Post-processing (bloom pada jendela & lampu) ---- */
    let composer = null;
    let bloom = null;
    if (!lowPower) {
        composer = new EffectComposer(renderer);
        composer.addPass(new RenderPass(scene, camera));
        bloom = new UnrealBloomPass(new THREE.Vector2(256, 256), 0.62, 0.45, 0.9);
        composer.addPass(bloom);
        composer.addPass(new OutputPass());
    }

    /* ---- Framing kamera ---- */
    const view = { target: new THREE.Vector3(), dist: 30, az: 0.2, el: 0.15, w: 1, h: 1 };
    const intro = { dist: 1, lift: 0 };

    function frameCamera() {
        const w = container.clientWidth;
        const h = container.clientHeight;
        view.w = w; view.h = h;
        renderer.setSize(w, h, false);
        composer?.setSize(w, h);
        bloom?.resolution.set(w, h);

        const vHalf = THREE.MathUtils.degToRad(camera.fov / 2);
        const narrow = w < 900;
        const maxHeight = Math.max(...built.map((b) => b.height)) + 1.2;

        if (mode === 'focus') {
            camera.clearViewOffset();
            camera.aspect = w / h;
            view.az = 0.55; view.el = 0.13;
            const fy = focusModule ? focusModule.userData.home.y : maxHeight / 2;
            view.target.set(0, (fy + maxHeight * 0.55) / 2, 0);
            const projW = spanX * Math.cos(view.az) + CELL.d * Math.sin(view.az) + 3;
            const dW = (projW / 2) / (0.8 * Math.tan(vHalf) * camera.aspect);
            const dH = ((maxHeight + 2.2) * 0.62) / Math.tan(vHalf);
            view.dist = Math.max(dW, dH) + 9;
        } else if (narrow) {
            // Ponsel: gedung di bawah teks, dilihat menyerong agar muat
            const cy = Math.max(h * 0.62, h - 200);
            const fullH = cy * 2;
            camera.aspect = w / fullH;
            camera.setViewOffset(w, fullH, 0, 0, w, h);
            view.az = 0.62; view.el = 0.12;
            view.target.set(0, maxHeight * 0.4, 0);
            const projW = spanX * Math.cos(view.az) + 10 * Math.sin(view.az);
            view.dist = (projW / 2) / (1.08 * Math.tan(vHalf) * camera.aspect) + 2;
        } else {
            // Desktop: pusat gedung di ~66% lebar layar, teks di kiri
            const fullW = w * 1.32;
            camera.aspect = fullW / h;
            camera.setViewOffset(fullW, h, 0, 0, w, h);
            view.az = 0.24; view.el = 0.12;
            view.target.set(0, maxHeight * 0.42, 0);
            const projW = spanX * Math.cos(view.az) + 12 * Math.sin(view.az);
            const dW = (projW / 2) / (0.5 * Math.tan(vHalf) * camera.aspect);
            const dH = (maxHeight * 0.78) / Math.tan(vHalf);
            view.dist = Math.max(dW, dH) + 4;
        }
        camera.updateProjectionMatrix();
        scene.fog.near = view.dist + 12;
        scene.fog.far = view.dist + 120;
    }
    frameCamera();
    new ResizeObserver(() => frameCamera()).observe(container);

    /* ---- Interaksi ---- */
    const tip = container.querySelector('.tower-tip');
    const raycaster = new THREE.Raycaster();
    const ndc = new THREE.Vector2();
    const state = { px: 0, py: 0, drag: 0, scroll: 0 };
    let pointerDirty = false;
    let pointerInside = false;
    let lastX = 0;
    let lastY = 0;
    let hovered = null;
    let dragging = false;
    let dragStartX = 0;
    let dragStartVal = 0;
    let moved = false;

    function setTip(m) {
        if (!tip) return;
        if (!m) { tip.hidden = true; return; }
        const u = m.userData.unit;
        const st = STATUS[u.s] || STATUS.maintenance;
        tip.replaceChildren();
        const b = document.createElement('b');
        b.textContent = `Blok ${u.b}, lantai ${u.l}, no. ${u.n}`;
        const row = document.createElement('div');
        row.className = 'tip-row';
        const s = document.createElement('span');
        s.className = `tip-status tip-status--${u.s}`;
        s.textContent = st.label;
        const p = document.createElement('span');
        p.textContent = `${rupiah(u.h)}/bln`;
        row.append(s, p);
        const cta = document.createElement('div');
        cta.className = 'tip-cta';
        cta.textContent = m === focusModule && mode === 'focus' ? `${u.w}. Unit yang sedang kamu lihat.` : `${u.w}. Klik untuk melihat detail.`;
        tip.append(b, row, cta);
        tip.hidden = false;
    }

    function hoverOn(m) {
        gsap.to(m.position, { z: m.userData.home.z + 0.5, duration: 0.45, ease: 'power3.out', overwrite: 'auto' });
        gsap.to(m.userData.winMat, { emissiveIntensity: m.userData.base * 2 + 1.2, duration: 0.3, overwrite: 'auto' });
    }
    function hoverOff(m) {
        if (m === focusModule) return;
        gsap.to(m.position, { z: m.userData.home.z, duration: 0.55, ease: 'power3.out', overwrite: 'auto' });
        gsap.to(m.userData.winMat, { emissiveIntensity: m.userData.base, duration: 0.4, overwrite: 'auto' });
    }
    function setHover(m) {
        if (hovered === m) return;
        if (hovered) hoverOff(hovered);
        hovered = m;
        if (m) {
            hoverOn(m);
            container.classList.add('is-hover');
            setTip(m);
        } else {
            container.classList.remove('is-hover');
            setTip(focusModule);
        }
    }

    function pick(clientX, clientY) {
        const r = container.getBoundingClientRect();
        ndc.set(((clientX - r.left) / r.width) * 2 - 1, -((clientY - r.top) / r.height) * 2 + 1);
        raycaster.setFromCamera(ndc, camera);
        const hit = raycaster.intersectObjects(hitList, false)[0];
        return hit ? hit.object.userData.module : null;
    }

    container.addEventListener('pointermove', (e) => {
        const r = container.getBoundingClientRect();
        state.px = ((e.clientX - r.left) / r.width) * 2 - 1;
        state.py = ((e.clientY - r.top) / r.height) * 2 - 1;
        pointerInside = true;
        if (dragging) {
            const dx = e.clientX - dragStartX;
            if (Math.abs(dx) > 6) { moved = true; container.classList.add('is-drag'); }
            state.drag = clamp(dragStartVal + dx * 0.006, -1.3, 1.3);
        }
        if (e.pointerType === 'mouse') {
            pointerDirty = true;
            lastX = e.clientX;
            lastY = e.clientY;
        }
    });
    container.addEventListener('pointerleave', () => {
        pointerInside = false;
        state.px = 0; state.py = 0;
        if (fine) setHover(null);
    });
    container.addEventListener('pointerdown', (e) => {
        dragging = true; moved = false;
        dragStartX = e.clientX; dragStartVal = state.drag;
    });
    window.addEventListener('pointerup', (e) => {
        if (!dragging) return;
        dragging = false;
        container.classList.remove('is-drag');
        if (moved || !container.contains(e.target)) return;
        const m = pick(e.clientX, e.clientY);
        if (e.pointerType === 'mouse') {
            if (m && m.userData.unit.u && m !== focusModule) window.location.href = m.userData.unit.u;
        } else if (m) {
            // Sentuhan: ketuk pertama menampilkan info, ketuk kedua membuka unit
            if (hovered === m && m.userData.unit.u && m !== focusModule) window.location.href = m.userData.unit.u;
            else setHover(m);
        } else {
            setHover(null);
        }
    });

    /* ---- Animasi masuk: modul jatuh & tersusun, jendela menyala ---- */
    function startIntro() {
        introStarted = true;
        if (reduce) return;
        showLabels(2.2);
        const order = built.map((b, k) => ({ b, k }));
        allModules.forEach((m) => {
            const { home, winMat, base, tower, floor, col } = m.userData;
            const k = order.findIndex((o) => o.k === tower);
            const delay = k * 0.2 + floor * 0.13 + col * 0.045;
            m.position.y = home.y + 9 + floor * 0.5;
            m.scale.setScalar(0.5);
            winMat.emissiveIntensity = 0;
            gsap.to(m.position, { y: home.y, duration: 1.05, ease: 'power4.out', delay });
            gsap.to(m.scale, { x: 1, y: 1, z: 1, duration: 0.9, ease: 'back.out(2.2)', delay });
            gsap.to(winMat, { emissiveIntensity: m === focusModule ? base * 2.4 : base, duration: 0.35, ease: 'steps(4)', delay: delay + 0.8 });
        });
        built.forEach((b, k) => {
            const floorsDone = k * 0.2 + b.t.rows.length * 0.13 + 0.7;
            b.roofParts.forEach((p, j) => {
                gsap.to(p.scale, { y: 1, duration: 0.6, ease: 'back.out(2)', delay: floorsDone + j * 0.06 });
            });
            gsap.to(b.core.scale, { y: 1, duration: 1.4, ease: 'power3.out', delay: k * 0.2 });
        });
        gsap.to(treeState, { grow: 1, duration: 2.4, ease: 'power2.out', delay: 0.8, onUpdate: () => setTrees(treeState.grow) });
        lampMat.emissiveIntensity = 0;
        gsap.to(lampMat, { emissiveIntensity: 3.2, duration: 1.4, delay: 1.2 });
        intro.dist = 1.35; intro.lift = 2.5;
        gsap.to(intro, { dist: 1, lift: 0, duration: 3.4, ease: 'expo.out' });
    }

    // Sembunyikan bangunan sebelum intro supaya tidak "berkedip" muncul
    if (!reduce) {
        allModules.forEach((m) => { m.position.y += 40; });
        built.forEach((b) => {
            b.roofParts.forEach((p) => p.scale.set(1, 0.001, 1));
            b.core.scale.set(1, 0.001, 1);
        });
        const begin = () => startIntro();
        if (mode === 'focus') {
            // Halaman detail: mulai ketika gedung terlihat di layar
            const io = new IntersectionObserver(([e]) => {
                if (e.isIntersecting) { io.disconnect(); begin(); }
            }, { threshold: 0.3 });
            io.observe(container);
        } else {
            onIntro(begin);
        }
    } else {
        introStarted = true;
    }

    // Sorot unit terpilih (mode detail)
    if (focusModule) {
        focusModule.userData.winMat.emissive.set('#9dffd0');
        focusModule.position.z = focusModule.userData.home.z + 0.5;
        setTip(focusModule);
        allModules.forEach((m) => { if (m !== focusModule) m.userData.base *= 0.6; });
        allModules.forEach((m) => { if (m !== focusModule && reduce) m.userData.winMat.emissiveIntensity = m.userData.base; });
    }

    // Kamera ikut bergerak saat hero di-scroll
    const heroSec = container.closest('[data-hero]');
    if (heroSec && !reduce) {
        ScrollTrigger.create({ trigger: heroSec, start: 'top top', end: 'bottom top', scrub: true, onUpdate: (s) => { state.scroll = s.progress; } });
    }

    /* ---- Loop render ---- */
    let visible = true;
    new IntersectionObserver(([e]) => { visible = e.isIntersecting; }, { threshold: 0 }).observe(container);

    const clock = new THREE.Clock();
    const camPos = new THREE.Vector3();
    const look = new THREE.Vector3();
    const tipPos = new THREE.Vector3();
    let rotY = 0;
    let time = 0;
    let firstFrame = true;
    const smooth = { px: 0, py: 0 };

    function frame() {
        const dt = Math.min(clock.getDelta(), 0.05);
        time += dt;

        // Ayunan pelan + mengikuti kursor + seret + scroll
        const sway = reduce ? 0 : Math.sin(time * 0.16) * (mode === 'focus' ? 0.45 : 0.2);
        smooth.px += (state.px - smooth.px) * 0.05;
        smooth.py += (state.py - smooth.py) * 0.05;
        const targetRot = sway + smooth.px * (fine ? 0.22 : 0) + state.drag + state.scroll * 0.5;
        rotY += (targetRot - rotY) * 0.06;
        world.rotation.y = rotY;

        const dist = view.dist * intro.dist * (1 + state.scroll * 0.25);
        const el = view.el + (fine ? smooth.py * 0.04 : 0) + state.scroll * 0.08;
        camPos.set(
            Math.sin(view.az) * Math.cos(el) * dist,
            Math.sin(el) * dist + intro.lift,
            Math.cos(view.az) * Math.cos(el) * dist,
        ).add(view.target);
        camera.position.copy(camPos);
        look.copy(view.target).setY(view.target.y + state.scroll * 1.5);
        camera.lookAt(look);

        stars.rotation.y = time * 0.004;

        // Kunang-kunang naik pelan
        const arr = pGeo.attributes.position.array;
        for (let i = 0; i < pCount; i++) {
            arr[i * 3 + 1] += pSpeed[i] * dt * 0.6;
            arr[i * 3] += Math.sin(time * 0.8 + i) * dt * 0.08;
            if (arr[i * 3 + 1] > maxH) arr[i * 3 + 1] = 0.2;
        }
        pGeo.attributes.position.needsUpdate = true;

        // Lampu suar atap berkedip
        beacons.forEach((b) => { b.mat.emissiveIntensity = Math.sin(time * 2.4 + b.phase) > 0.7 ? 5 : 0.25; });

        // Unit terpilih berdenyut
        if (focusModule && introStarted) {
            const fm = focusModule.userData;
            if (hovered !== focusModule) fm.winMat.emissiveIntensity = fm.base * (2 + Math.sin(time * 3) * 0.9);
        }

        // Hover (dihitung sekali per frame)
        if (pointerDirty && fine && !dragging && pointerInside) {
            pointerDirty = false;
            setHover(pick(lastX, lastY));
        }

        // Tooltip mengikuti modul
        const tipTarget = hovered || focusModule;
        if (tip && tipTarget && !tip.hidden) {
            tipTarget.getWorldPosition(tipPos);
            tipPos.y += CELL.h * 0.55;
            tipPos.project(camera);
            const x = clamp((tipPos.x * 0.5 + 0.5) * view.w, 120, view.w - 120);
            const y = (-tipPos.y * 0.5 + 0.5) * view.h;
            tip.style.left = `${x}px`;
            tip.style.top = `${y}px`;
        }

        if (composer) composer.render(); else renderer.render(scene, camera);

        if (firstFrame) {
            firstFrame = false;
            container.classList.add('is-ready');
        }
    }

    renderer.setAnimationLoop(() => {
        if (!visible || document.hidden) { clock.getDelta(); return; }
        frame();
    });
}
