const SELECTOR = "iframe[data-template-preview-src]";
const IMAGE_SELECTOR = "[data-template-preview-image]";
const MAX_CONCURRENT_LOADS = 2;

const queue = [];
let activeLoads = 0;
let observer = null;

function isVisible(frame) {
    if (frame.offsetWidth === 0 || frame.offsetHeight === 0) {
        return false;
    }

    const parent = frame.parentElement;
    if (!parent || parent.offsetWidth === 0 || parent.offsetHeight === 0) {
        return false;
    }

    const rect = parent.getBoundingClientRect();
    return rect.bottom >= 0 && rect.top <= window.innerHeight;
}

function finishLoad(frame) {
    const shell = frame.closest("[data-template-preview-shell]");
    const placeholder = shell?.querySelector("[data-template-preview-placeholder]");

    frame.dataset.templatePreviewLoaded = "true";
    frame.classList.remove("opacity-0");
    placeholder?.classList.add("opacity-0");
}

function finishImageLoad(image) {
    const shell = image.closest("[data-template-preview-shell]");
    shell
        ?.querySelector("[data-template-preview-placeholder]")
        ?.classList.add("opacity-0");
}

function fallBackToFrame(image) {
    const shell = image.closest("[data-template-preview-shell]");
    const frame = shell?.querySelector(SELECTOR);

    image.hidden = true;
    image.classList.add("hidden");
    frame?.classList.remove("hidden");

    if (shell) {
        window.queueTemplatePreviewFrames(shell);
    }
}

function observeImages(root = document) {
    root.querySelectorAll(IMAGE_SELECTOR).forEach((image) => {
        if (image.dataset.templatePreviewObserved) {
            return;
        }

        image.dataset.templatePreviewObserved = "true";
        image.addEventListener("load", () => finishImageLoad(image), {
            once: true,
        });
        image.addEventListener("error", () => fallBackToFrame(image), {
            once: true,
        });

        if (image.complete) {
            if (image.naturalWidth > 0) {
                finishImageLoad(image);
            } else {
                fallBackToFrame(image);
            }
        }
    });
}

function pumpQueue() {
    while (activeLoads < MAX_CONCURRENT_LOADS && queue.length > 0) {
        const frame = queue.shift();

        if (!frame?.isConnected || frame.dataset.templatePreviewLoaded) {
            continue;
        }

        activeLoads += 1;

        const complete = (loaded) => {
            if (loaded) {
                finishLoad(frame);
            }

            activeLoads -= 1;
            pumpQueue();
        };

        frame.addEventListener("load", () => complete(true), { once: true });
        frame.addEventListener("error", () => complete(false), { once: true });
        frame.src = frame.dataset.templatePreviewSrc;
    }
}

function enqueue(frame) {
    if (
        frame.dataset.templatePreviewLoaded ||
        frame.dataset.templatePreviewQueued ||
        !frame.dataset.templatePreviewSrc
    ) {
        return;
    }

    frame.dataset.templatePreviewQueued = "true";
    queue.push(frame);
    pumpQueue();
}

function observeFrames(root = document) {
    if (!root) {
        root = document;
    }

    root.querySelectorAll(SELECTOR).forEach((frame) => {
        if (frame.dataset.templatePreviewObserved) {
            return;
        }

        frame.dataset.templatePreviewObserved = "true";

        if (observer) {
            observer.observe(frame);
        } else if (isVisible(frame)) {
            enqueue(frame);
        }
    });
}

window.queueTemplatePreviewFrames = function queueTemplatePreviewFrames(root = document) {
    if (!root) {
        root = document;
    }

    observeImages(root);
    observeFrames(root);

    root.querySelectorAll(SELECTOR).forEach((frame) => {
        if (isVisible(frame)) {
            enqueue(frame);
        }
    });
};

function initTemplatePreviewFrames() {
    if ("IntersectionObserver" in window) {
        observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (!entry.isIntersecting) {
                        return;
                    }

                    observer.unobserve(entry.target);
                    enqueue(entry.target);
                });
            },
            { rootMargin: "240px 0px" },
        );
    }

    observeImages();
    observeFrames();
    window.queueTemplatePreviewFrames();
}

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", initTemplatePreviewFrames);
} else {
    initTemplatePreviewFrames();
}
