// Counts [data-count-up] elements from 0 to their rendered value the first
// time they enter the viewport. Progressive enhancement: the final value is
// server-rendered, so without JS (or with reduced motion) it just stays put.
const DURATION_MS = 900;

// Matches the score arc's --ease-out-expo so number and arc land together.
const easeOutExpo = (t) => (t === 1 ? 1 : 1 - Math.pow(2, -10 * t));

function animateCount(el) {
    const target = parseFloat(el.dataset.countUp || el.textContent);
    if (!Number.isFinite(target)) return;

    const start = performance.now();
    const step = (now) => {
        const t = Math.min((now - start) / DURATION_MS, 1);
        el.textContent = Math.round(target * easeOutExpo(t));
        if (t < 1) requestAnimationFrame(step);
    };
    requestAnimationFrame(step);
}

function initCountUp() {
    const els = document.querySelectorAll("[data-count-up]");
    if (!els.length) return;

    if (
        window.matchMedia("(prefers-reduced-motion: reduce)").matches ||
        !("IntersectionObserver" in window)
    ) {
        return; // leave the server-rendered value untouched
    }

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;
                observer.unobserve(entry.target);
                animateCount(entry.target);
            });
        },
        { threshold: 0.4 }
    );

    els.forEach((el) => {
        if (el.dataset.countUpBound) return; // re-init after wire:navigate
        el.dataset.countUpBound = "true";
        observer.observe(el);
    });
}

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initCountUp);
} else {
    initCountUp();
}
// wire:navigate replaces the DOM without a full page load; re-scan then.
document.addEventListener("livewire:navigated", initCountUp);
