# Product

## Register

product

## Users

Indonesian job seekers — students, first-jobbers, and mid-career professionals applying to BUMN, startup, and corporate roles. They arrive stressed and time-pressed: tailoring a CV per application, guessing what an ATS filter wants, and unable to afford real interview coaching. Sessions are goal-directed (finish a CV, check a score, rehearse before an interview), often on a deadline, sometimes on mobile between other tasks.

Primary jobs to be done, by surface:

- **Resume Builder**: go from blank to a downloadable, tailored CV fast, without losing work (auto-save is load-bearing trust).
- **ATS Scanner**: get a concrete, actionable gap list against a specific job description, not a vague score.
- **Mock Interview (Bu Sari)**: rehearse real answers under light pressure and get specific, structured (STAR-based) feedback tied to their own CV content.
- **Admin Panel**: internal ops (support, AI cost/usage, users, Sentry) — efficiency and legibility over polish.

## Product Purpose

Resumify is an AI-powered CV builder built specifically for the Indonesian job market. It exists because generic tools (Canva, Novoresume, Resume.io) don't understand local formats (BUMN/startup/corporate conventions, IPK, photo norms, Bahasa Indonesia) and don't connect CV building to ATS feedback or interview prep in one flow. Success looks like: zero to a downloadable, tailored CV in under 10 minutes; ATS feedback the user can actually act on; a mock interview that visibly references the user's own experience rather than asking generic questions.

## Brand Personality

Confident and professional, warm and encouraging, direct and efficient. The product should read like a competent, calm coach who respects the user's time — not a cold enterprise tool, and not a try-hard "delightful" consumer app. Copy should be encouraging without being saccharine, and efficient without being curt: job hunting is already stressful, so the interface should lower pressure, not add to it. Voice can lean locally grounded (Indonesian workplace context) rather than a generic translated-Western-SaaS tone, but this wasn't confirmed as a hard requirement — treat as a light default, not a strict rule.

## Anti-references

- **Generic Canva-style template pickers** — a wall of interchangeable templates with no point of view or guidance is exactly the gap Resumify is built to close.
- **Corporate HR/ATS admin software** — sterile, recruiter-facing enterprise tooling. Resumify is candidate-facing; it should feel like it's on the user's side.
- **Generic AI-SaaS look** — the 2025-26 cream-background, gradient-text, glass-card wrapper aesthetic. Resumify already carries a distinct identity (deep brown `#4f3b2f` + emerald `#0F6E56`, Newsreader serif headlines, Manrope sans body) — lean into that instead of drifting toward category-default AI-tool visuals.

## Design Principles

1. **Trust through visible progress** — auto-save, ATS scores, and interview feedback must always make it obvious that the system is tracking and protecting the user's work; silent failure or ambiguous state is the worst failure mode here.
2. **Actionable over impressive** — ATS and interview feedback should read as a specific next step, not a decorative score. If a piece of feedback doesn't tell the user what to change, cut it.
3. **Respect the deadline** — most users are mid-application. Every flow should default to the fastest correct path (sensible defaults, minimal required fields) while keeping an escape hatch for users who want to go deeper (3 CV angles, section-by-section detail).
4. **Local first, not localized-after** — Indonesian formats, context, and language are the default assumption baked into content and flow, not a translation layer bolted onto a Western template.
5. **Calm confidence, not hype** — visual and copy choices should reduce anxiety (clear states, encouraging but honest feedback) rather than perform excitement; this is a tool people lean on during a stressful process.

## Accessibility & Inclusion

WCAG 2.1 AA baseline: body text ≥4.5:1 contrast, large text ≥3:1, full keyboard operability, visible focus states, and a working `prefers-reduced-motion` path (already present in `resources/css/app.css`). No stricter or user-specific requirement has been identified beyond AA; revisit if a specific user need surfaces.
