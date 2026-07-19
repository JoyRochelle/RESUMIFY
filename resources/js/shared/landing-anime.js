// Anime.js micro-interactions for the landing pages. Transforms plus
// short time-based fades only — every animation runs to completion, so
// nothing can park half-faded and shift the perceived palette. The
// library is imported dynamically, so pages without [data-anime-*] hooks
// never download it. Re-inits on livewire:navigated (wire:navigate swaps
// the DOM without a full page load); dataset flags keep bindings single.

let filterListenerBound = false;

async function init() {
    const steps = document.querySelector("[data-anime-steps]");
    const icons = document.querySelectorAll("[data-anime-icon]");
    const grid = document.querySelector("[data-anime-grid]");
    const bars = document.querySelectorAll("[data-anime-bar]");
    const shakes = document.querySelectorAll("[data-anime-shake]");
    if (!steps && !icons.length && !grid && !bars.length && !shakes.length) return;
    if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) return;

    const { animate, createTimeline, stagger } = await import("animejs");

    // Progress bars (e.g. the ATS match bar on the auth pages) fill from
    // empty when they enter view — transform-only, keeps its own color.
    if (bars.length && "IntersectionObserver" in window) {
        const barIo = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (!entry.isIntersecting) return;
                    barIo.unobserve(entry.target);
                    animate(entry.target, {
                        scaleX: [0, 1],
                        duration: 900,
                        delay: 350,
                        ease: "outExpo",
                    });
                });
            },
            { threshold: 0.5 }
        );
        bars.forEach((el) => {
            if (el.dataset.animeBound) return;
            el.dataset.animeBound = "true";
            el.style.transform = "scaleX(0)";
            barIo.observe(el);
        });
    }

    // Validation error boxes get one gentle horizontal nudge on arrival,
    // pulling the eye to the feedback without any color games.
    shakes.forEach((el) => {
        if (el.dataset.animeBound) return;
        el.dataset.animeBound = "true";
        const tl = createTimeline({
            defaults: { duration: 90, ease: "inOutQuad" },
        });
        tl.add(el, { translateX: -7 }, 250)
            .add(el, { translateX: 6 })
            .add(el, { translateX: -4 })
            .add(el, { translateX: 0 });
    });

    // "How it works": a 1 → 2 → 3 sequence. Each marker pops and lifts
    // its text; the connector line draws rightward between pops so the
    // next number appears just as the line reaches it.
    if (steps && !steps.dataset.animeBound && "IntersectionObserver" in window) {
        steps.dataset.animeBound = "true";

        const line = steps.querySelector("[data-anime-steps-line]");
        const stepEls = [...steps.querySelectorAll("[data-anime-step]")];

        // Pre-hide the sequence now that JS is confirmed running, so the
        // user never sees the finished state before the play-through.
        // (Reduced-motion and no-IO paths bail out above and stay static.)
        stepEls.forEach((step) => {
            const marker = step.querySelector("[data-anime-step-marker]");
            if (marker) marker.style.transform = "scale(0)";
            step.querySelectorAll("[data-anime-step-text]").forEach((el) => {
                el.style.opacity = "0";
                el.style.transform = "translateY(16px)";
            });
        });
        if (line) line.style.transform = "scaleX(0)";

        const playSequence = () => {
            const gap = 650;
            const tl = createTimeline({
                defaults: { ease: "outExpo" },
            });

            stepEls.forEach((step, i) => {
                const marker = step.querySelector("[data-anime-step-marker]");
                const texts = step.querySelectorAll("[data-anime-step-text]");
                const at = i * gap;
                if (marker) tl.add(marker, { scale: [0, 1], duration: 550 }, at);
                if (texts.length) {
                    tl.add(
                        texts,
                        {
                            translateY: [16, 0],
                            opacity: [0, 1],
                            duration: 500,
                            delay: stagger(90),
                        },
                        at + 120
                    );
                }
            });

            if (line) {
                tl.add(
                    line,
                    { scaleX: [0, 1], duration: gap * 2, ease: "inOutQuad" },
                    200
                );
            }

            // Payoff pulse on the final (emerald) marker once it has landed.
            const last = stepEls
                .at(-1)
                ?.querySelector("[data-anime-step-marker]");
            if (last) {
                tl.add(
                    last,
                    { scale: [1, 1.18, 1], duration: 520, ease: "inOutQuad" },
                    (stepEls.length - 1) * gap + 600
                );
            }
        };

        // rootMargin trims the bottom fifth of the viewport, so merely
        // peeking over the fold on load doesn't count — the section has to
        // be scrolled well into the reading zone before the sequence runs.
        const io = new IntersectionObserver(
            (entries) => {
                if (!entries.some((e) => e.isIntersecting)) return;
                io.disconnect();
                playSequence();
            },
            { threshold: 0.35, rootMargin: "0px 0px -20% 0px" }
        );
        io.observe(steps);
    }

    // Feature-card icons do a small settle pulse when the card is hovered.
    icons.forEach((icon) => {
        if (icon.dataset.animeBound) return;
        icon.dataset.animeBound = "true";
        const card = icon.closest("[data-anime-hover]") ?? icon.parentElement;
        card.addEventListener("mouseenter", () => {
            animate(icon, {
                scale: [1, 1.15, 1],
                rotate: [0, -4, 0],
                duration: 500,
                ease: "outQuad",
            });
        });
    });

    // Template grid ripples back in whenever the category filter changes
    // (event dispatched by the templateLibrary Alpine component). The grid
    // is re-queried at event time, so one window listener serves every
    // wire:navigate visit.
    if (grid && !filterListenerBound) {
        filterListenerBound = true;
        window.addEventListener("templates:filtered", () => {
            const currentGrid = document.querySelector("[data-anime-grid]");
            if (!currentGrid) return;
            const visible = [
                ...currentGrid.querySelectorAll("[data-anime-card]"),
            ].filter((el) => el.offsetParent !== null);
            if (!visible.length) return;
            animate(visible, {
                translateY: [16, 0],
                scale: [0.98, 1],
                duration: 450,
                delay: stagger(55),
                ease: "outQuad",
            });
        });
    }
}

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
} else {
    init();
}
document.addEventListener("livewire:navigated", init);
