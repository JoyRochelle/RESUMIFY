/**
 * Live-updates AI quota widgets after an AI feature call succeeds, without
 * requiring a full page reload. Any page can dispatch:
 *
 *   window.dispatchEvent(new CustomEvent('ai-quota:updated', { detail: quota }));
 *
 * where `quota` is the { used, limit, remaining, percentage } shape
 * returned by App\Models\User::aiQuotaSnapshot(). Widgets opt in by adding
 * the matching data-ai-quota-* attributes to their markup; pages with no
 * widget simply ignore the event.
 */
function updateAiQuotaWidgets(quota) {
    if (!quota) return;

    document.querySelectorAll('[data-ai-quota-used]').forEach((el) => {
        el.textContent = quota.used;
    });
    document.querySelectorAll('[data-ai-quota-limit]').forEach((el) => {
        el.textContent = quota.limit;
    });
    document.querySelectorAll('[data-ai-quota-remaining]').forEach((el) => {
        el.textContent = quota.remaining;
    });
    document.querySelectorAll('[data-ai-quota-bar]').forEach((el) => {
        el.style.width = `${quota.percentage}%`;
    });
    document.querySelectorAll('[data-ai-quota-bar-remaining]').forEach((el) => {
        el.style.width = `${100 - quota.percentage}%`;
    });
}

window.addEventListener('ai-quota:updated', (event) => updateAiQuotaWidgets(event.detail));
