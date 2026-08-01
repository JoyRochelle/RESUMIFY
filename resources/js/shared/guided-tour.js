/**
 * Guided tour — walks a new user through the real controls on the page,
 * one step at a time, with next / back / skip.
 *
 * A page opts in by rendering <x-user.guided-tour>, which prints the overlay
 * markup and a window.guidedTourConfig payload in an inline script. Inline
 * scripts run before deferred modules, so the config is always here by the
 * time this file executes.
 *
 * Steps point at elements through a selector. A step whose target is absent
 * or invisible (responsive variants, premium-locked controls, optional
 * sections already added) is skipped instead of breaking the tour.
 */

const SPOTLIGHT_PADDING = 8;
const SPOTLIGHT_RADIUS = 16;
const EDGE_MARGIN = 12;
const TOOLTIP_GAP = 14;
const MOBILE_BREAKPOINT = 640;

const seenKey = (id) => `resumify.tour.${id}.seen`;

const prefersReducedMotion = () =>
    window.matchMedia('(prefers-reduced-motion: reduce)').matches;

/** localStorage is unavailable in private modes on some browsers. */
const storage = {
    get(key) {
        try {
            return window.localStorage.getItem(key);
        } catch {
            return null;
        }
    },
    set(key, value) {
        try {
            window.localStorage.setItem(key, value);
        } catch {
            /* ignore — the tour just replays next visit */
        }
    },
};

class GuidedTour {
    constructor(config, root) {
        this.config = config;
        this.steps = config.steps || [];
        this.i18n = config.i18n || {};
        this.root = root;

        this.backdrop = root.querySelector('[data-tour-backdrop]');
        this.spotlight = root.querySelector('[data-tour-spotlight]');
        this.tooltip = root.querySelector('[data-tour-tooltip]');
        this.titleEl = root.querySelector('[data-tour-title]');
        this.bodyEl = root.querySelector('[data-tour-body]');
        this.counterEl = root.querySelector('[data-tour-counter]');
        this.progressEl = root.querySelector('[data-tour-progress]');
        this.backBtn = root.querySelector('[data-tour-back]');
        this.nextBtn = root.querySelector('[data-tour-next]');
        this.nextLabel = root.querySelector('[data-tour-next-label]');

        this.index = -1;
        this.active = false;
        this.target = null;
        this.frame = null;
        this.lastFocused = null;

        this.onKeydown = this.onKeydown.bind(this);
        this.tick = this.tick.bind(this);

        root.querySelectorAll('[data-tour-skip]').forEach((el) =>
            el.addEventListener('click', () => this.finish(true))
        );
        this.backBtn?.addEventListener('click', () => this.go(-1));
        this.nextBtn?.addEventListener('click', () => this.go(1));
        this.backdrop?.addEventListener('click', () => this.finish(true));
    }

    /* ── lifecycle ────────────────────────────────────────────────── */

    start() {
        if (this.active || this.steps.length === 0) {
            return;
        }

        this.active = true;
        this.lastFocused = document.activeElement;
        this.root.removeAttribute('hidden');
        document.addEventListener('keydown', this.onKeydown, true);
        this.frame = requestAnimationFrame(this.tick);

        this.index = -1;
        this.go(1);
    }

    finish(markSeen) {
        if (!this.active) {
            return;
        }

        this.active = false;
        this.target = null;
        this.root.setAttribute('hidden', '');
        document.removeEventListener('keydown', this.onKeydown, true);

        if (this.frame) {
            cancelAnimationFrame(this.frame);
            this.frame = null;
        }

        if (markSeen) {
            storage.set(seenKey(this.config.id), '1');
        }

        if (this.lastFocused instanceof HTMLElement && document.contains(this.lastFocused)) {
            this.lastFocused.focus({ preventScroll: true });
        }
    }

    /** Move `direction` steps, skipping any whose target cannot be shown. */
    go(direction) {
        let next = this.index + direction;

        while (next >= 0 && next < this.steps.length) {
            const step = this.steps[next];
            const target = this.resolveTarget(step);

            if (!step.target || target) {
                this.render(next, target);
                return;
            }

            next += direction;
        }

        if (next < 0) {
            return; // already on the first showable step
        }

        this.finish(true);
    }

    /* ── step rendering ───────────────────────────────────────────── */

    resolveTarget(step) {
        if (!step.target) {
            return null;
        }

        // A selector can match more than one element when a control has
        // separate mobile and desktop variants — take the one on screen.
        const el = Array.from(document.querySelectorAll(step.target)).find((candidate) =>
            this.isVisible(candidate)
        );

        if (!el) {
            return null;
        }

        // Expand a collapsed accordion so its fields are actually on screen.
        if (step.expand) {
            el.querySelector('button[aria-expanded="false"]')?.click();
        }

        return el;
    }

    isVisible(el) {
        if (!el.isConnected || el.hidden) {
            return false;
        }

        const rect = el.getBoundingClientRect();

        if (rect.width === 0 && rect.height === 0) {
            return false;
        }

        const style = window.getComputedStyle(el);

        return style.visibility !== 'hidden' && style.display !== 'none';
    }

    render(index, target) {
        const step = this.steps[index];

        this.index = index;
        this.target = target;

        this.titleEl.textContent = step.title || '';
        this.bodyEl.textContent = step.body || '';

        const shown = index + 1;
        const total = this.steps.length;

        this.counterEl.textContent = (this.i18n.step_label || ':current / :total')
            .replace(':current', shown)
            .replace(':total', total);

        if (this.progressEl) {
            this.progressEl.style.width = `${Math.round((shown / total) * 100)}%`;
        }

        const isLast = index === total - 1;
        this.nextLabel.textContent = isLast ? this.i18n.done : this.i18n.next;
        this.backBtn.disabled = index === 0;
        this.backBtn.classList.toggle('invisible', index === 0);

        if (target) {
            target.scrollIntoView({
                block: 'center',
                inline: 'nearest',
                behavior: prefersReducedMotion() ? 'auto' : 'smooth',
            });
        }

        this.position();
        this.nextBtn.focus({ preventScroll: true });
    }

    /* ── positioning ──────────────────────────────────────────────── */

    tick() {
        if (!this.active) {
            return;
        }

        this.position();
        this.frame = requestAnimationFrame(this.tick);
    }

    position() {
        const rect = this.target
            ? this.target.getBoundingClientRect()
            : { top: -9999, left: -9999, width: 0, height: 0, bottom: -9999, right: -9999 };

        const top = rect.top - SPOTLIGHT_PADDING;
        const left = rect.left - SPOTLIGHT_PADDING;
        const width = rect.width + SPOTLIGHT_PADDING * 2;
        const height = rect.height + SPOTLIGHT_PADDING * 2;

        this.spotlight.style.top = `${top}px`;
        this.spotlight.style.left = `${left}px`;
        this.spotlight.style.width = `${Math.max(width, 0)}px`;
        this.spotlight.style.height = `${Math.max(height, 0)}px`;
        this.spotlight.style.borderRadius = this.target ? `${SPOTLIGHT_RADIUS}px` : '0';

        this.placeTooltip(rect);
    }

    placeTooltip(rect) {
        const tip = this.tooltip;
        const viewportW = window.innerWidth;
        const viewportH = window.innerHeight;

        // Narrow screens: dock the card to whichever edge leaves the
        // highlighted element visible, rather than always the bottom.
        if (viewportW < MOBILE_BREAKPOINT || !this.target) {
            const width = Math.min(viewportW - EDGE_MARGIN * 2, 380);

            tip.style.width = `${width}px`;

            const height = tip.offsetHeight;
            const dockedBottom = viewportH - height - EDGE_MARGIN;
            const dockedTop = EDGE_MARGIN;

            const covers = (top) =>
                rect.bottom > top - SPOTLIGHT_PADDING &&
                rect.top < top + height + SPOTLIGHT_PADDING;

            let top;

            if (!this.target) {
                top = Math.max((viewportH - height) / 2, EDGE_MARGIN);
            } else if (!covers(dockedBottom)) {
                top = dockedBottom;
            } else if (!covers(dockedTop)) {
                top = dockedTop;
            } else {
                // Card is taller than the free space either way — put it on
                // the opposite side of the viewport from the target.
                top = rect.top > viewportH / 2 ? dockedTop : dockedBottom;
            }

            tip.style.left = `${(viewportW - width) / 2}px`;
            tip.style.top = `${top}px`;

            return;
        }

        tip.style.width = '360px';

        const width = tip.offsetWidth;
        const height = tip.offsetHeight;
        const preferred = this.steps[this.index]?.placement || 'auto';

        const candidates = {
            bottom: {
                top: rect.bottom + SPOTLIGHT_PADDING + TOOLTIP_GAP,
                left: rect.left + rect.width / 2 - width / 2,
            },
            top: {
                top: rect.top - SPOTLIGHT_PADDING - TOOLTIP_GAP - height,
                left: rect.left + rect.width / 2 - width / 2,
            },
            right: {
                top: rect.top + rect.height / 2 - height / 2,
                left: rect.right + SPOTLIGHT_PADDING + TOOLTIP_GAP,
            },
            left: {
                top: rect.top + rect.height / 2 - height / 2,
                left: rect.left - SPOTLIGHT_PADDING - TOOLTIP_GAP - width,
            },
        };

        const order = [preferred, 'bottom', 'top', 'right', 'left'].filter(
            (name, i, all) => candidates[name] && all.indexOf(name) === i
        );

        // Only the axis a placement pushes along has to fit — the other one is
        // clamped. A target taller than the viewport still gets a side
        // placement instead of falling through to the last candidate.
        const fits = (name) => {
            const pos = candidates[name];

            return name === 'left' || name === 'right'
                ? pos.left >= EDGE_MARGIN && pos.left + width <= viewportW - EDGE_MARGIN
                : pos.top >= EDGE_MARGIN && pos.top + height <= viewportH - EDGE_MARGIN;
        };

        const chosen = candidates[order.find(fits) || 'bottom'];

        const clamp = (value, min, max) => Math.min(Math.max(value, min), max);

        tip.style.top = `${clamp(chosen.top, EDGE_MARGIN, Math.max(viewportH - height - EDGE_MARGIN, EDGE_MARGIN))}px`;
        tip.style.left = `${clamp(chosen.left, EDGE_MARGIN, Math.max(viewportW - width - EDGE_MARGIN, EDGE_MARGIN))}px`;
    }

    /* ── keyboard ─────────────────────────────────────────────────── */

    onKeydown(event) {
        if (!this.active) {
            return;
        }

        switch (event.key) {
            case 'Escape':
                event.preventDefault();
                this.finish(true);
                break;
            case 'ArrowRight':
                event.preventDefault();
                this.go(1);
                break;
            case 'ArrowLeft':
                event.preventDefault();
                this.go(-1);
                break;
            case 'Tab':
                this.trapFocus(event);
                break;
            default:
                break;
        }
    }

    trapFocus(event) {
        const focusable = this.tooltip.querySelectorAll(
            'button:not([disabled]):not(.invisible)'
        );

        if (focusable.length === 0) {
            return;
        }

        const first = focusable[0];
        const last = focusable[focusable.length - 1];

        if (event.shiftKey && document.activeElement === first) {
            event.preventDefault();
            last.focus();
        } else if (!event.shiftKey && document.activeElement === last) {
            event.preventDefault();
            first.focus();
        }
    }
}

function boot() {
    const config = window.guidedTourConfig;
    const root = document.querySelector('[data-tour-root]');

    if (!config || !root || !Array.isArray(config.steps) || config.steps.length === 0) {
        return;
    }

    const tour = new GuidedTour(config, root);

    window.startGuidedTour = () => tour.start();

    document.querySelectorAll('[data-tour-trigger]').forEach((el) =>
        el.addEventListener('click', () => tour.start())
    );

    if (config.autoStart && !storage.get(seenKey(config.id))) {
        window.setTimeout(() => tour.start(), 600);
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
} else {
    boot();
}
