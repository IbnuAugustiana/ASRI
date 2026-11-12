import { initSite } from './site/motion.js';
import { initDash } from './site/dash.js';
import { initFeatures } from './site/features.js';

initSite();
initDash();
initFeatures();

// Three.js hanya dimuat di halaman yang punya gedung 3D (dipisah jadi chunk sendiri)
document.querySelectorAll('[data-tower]').forEach((el) => {
    import('./site/tower.js')
        .then(({ mountTower }) => mountTower(el))
        .catch((err) => {
            console.error('Gagal memuat scene 3D', err);
            el.classList.add('is-fallback');
        });
});
