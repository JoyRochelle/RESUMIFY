---
name: Resumify
description: An AI-powered CV builder for the Indonesian job market, styled as a warm, handcrafted atelier rather than a cold SaaS tool.
colors:
  primary: "#4f3b2f"
  primary-fixed: "#fcdccb"
  secondary: "#0F6E56"
  tertiary: "#ffffff"
  neutral: "#fdf8f4"
  surface: "#fdf8f4"
  surface-container-low: "#f7f2ee"
  surface-container-lowest: "#ffffff"
  on-surface: "#1d1b19"
  on-surface-variant: "#4f453f"
  outline-variant: "#d2c4bc"
  nav-footer: "#f0e8dc"
  premium-gold: "#A16207"
typography:
  headline:
    fontFamily: "Newsreader, serif"
    fontWeight: 700
    lineHeight: 1.2
  body:
    fontFamily: "Manrope, sans-serif"
    fontWeight: 400
    lineHeight: 1.5
  label:
    fontFamily: "Manrope, sans-serif"
    fontWeight: 700
    letterSpacing: "0.05em"
rounded:
  lg: "8px"
  xl: "12px"
  2xl: "16px"
  pill: "20px"
  full: "9999px"
components:
  button-primary:
    backgroundColor: "{colors.primary}"
    textColor: "{colors.tertiary}"
    rounded: "{rounded.lg}"
    padding: "8px 20px"
  button-primary-hover:
    backgroundColor: "{colors.primary}"
  button-secondary-accent:
    backgroundColor: "{colors.secondary}"
    textColor: "{colors.tertiary}"
    rounded: "{rounded.full}"
    padding: "6px 12px"
  button-outline:
    backgroundColor: "{colors.tertiary}"
    textColor: "{colors.primary}"
    rounded: "{rounded.lg}"
    padding: "8px 20px"
  input-underline:
    backgroundColor: "transparent"
    textColor: "{colors.primary}"
    padding: "8px 0"
  card:
    backgroundColor: "{colors.tertiary}"
    rounded: "{rounded.lg}"
    padding: "24px"
  card-premium:
    backgroundColor: "{colors.tertiary}"
    rounded: "{rounded.2xl}"
    padding: "40px"
---

# Design System: Resumify

## 1. Overview

**Creative North Star: "The Career Atelier"**

Resumify is a craftsperson's workshop for one document that matters: someone's career, told well. The system reads as inked and considered — deep brown surfaces and serif headlines borrowed from the language of a well-typeset page — rather than the glassy, gradient-lit dashboard of a generic AI-SaaS wrapper. A single emerald accent stands in for the atelier's one tool that matters: it marks progress, success, and the moments where the product actively helps (scores, checkmarks, the premium "unlimited" tier). Everything else stays quiet — warm cream surfaces, hairline borders, soft infrequent shadows — so the accent and the user's own words keep the room's attention.

This is a candidate-facing tool, not recruiter-facing admin software, and it should never feel like either. It explicitly rejects the interchangeable template-picker aesthetic of Canva-style builders (no wall of identical thumbnails competing for attention), the sterile chrome of corporate HR/ATS systems (no dense gray forms, no institutional coldness), and the cream-background/gradient-text/glass-card default that most AI tools reach for in 2025–26. Warmth here comes from typography, color restraint, and calm confident copy — not from decoration.

**Key Characteristics:**
- Warm-neutral surfaces (`#fdf8f4` family) with deep-brown ink (`#4f3b2f`), never pure black-on-white.
- One accent color (`#0F6E56` emerald) used sparingly and consistently for progress, success, and links — never decoratively.
- Newsreader serif for headlines paired with Manrope sans for body and interface labels — an editorial, not corporate, pairing.
- Flat-by-default surfaces with hairline borders; shadow is a response to hover, elevation (modals), or the premium moment, not a resting decoration.
- A distinct premium-gold (`#A16207`) role, kept separate from the emerald accent, reserved only for Premium-tier signals (badges, avatar rings, upsell glow).

## 2. Colors

The palette is warm-ink-on-cream with a single emerald signal color; nothing else competes for attention.

### Primary
- **Deep Brown Ink** (`#4f3b2f`): The system's ink. Used for all body/heading text, primary buttons, active nav states, and borders (always at reduced opacity, e.g. `primary/10`, `primary/15`, `primary/60`). This is the "confident and professional" voice made visible — grounded, not flashy.

### Secondary
- **Atelier Emerald** (`#0F6E56`): The one deliberate accent. Reserved for progress rings, success states, active links/tabs, primary CTAs on marketing surfaces, and premium-tier action buttons. Its rarity is the point — if a screen has more than one emerald element competing for attention, something has gone wrong.

### Tertiary
- **Paper White** (`#ffffff`): Card and panel surfaces sitting on top of the cream background — the "raised page" in the atelier metaphor.

### Neutral
- **Warm Cream** (`#fdf8f4`): The base page/body background (`--color-surface`, `--color-neutral`).
- **Low Cream** (`#f7f2ee` / `#f8f3ef`): Alternating rows, subtle sunken containers (`surface-container-low`).
- **Nav Tan** (`#f0e8dc`): Sidebar, navbar, and footer background — one shade warmer/deeper than the page body so navigation reads as its own layer.
- **Ink Variant** (`#1d1b19` / `#4f453f`): `on-surface` / `on-surface-variant` — reserved for template-rendering contexts needing Material-style role names.
- **Peach Fixed** (`#fcdccb`): Extended palette entry for template/CV rendering support; not used in core app chrome.
- **Outline Variant** (`#d2c4bc`): Soft dividing lines in template contexts.

### Named Rules
**The One Accent Rule.** Emerald (`#0F6E56`) is the only saturated accent in the interface. If a new state or component needs color, reach for brown-at-opacity or cream first; only use emerald if the moment is genuinely about progress, success, or the primary action.

**The Premium-Gold Exception.** Premium-tier signals (badges, avatar ring, upsell glow) use a dedicated gold (`#A16207`), never emerald and never a gradient. Gold means "you're on Premium"; emerald means "this worked." Keeping them distinct keeps both signals legible.

## 3. Typography

**Display/Headline Font:** Newsreader (serif), with system serif fallback
**Body Font:** Manrope (sans-serif), with system sans fallback
**Label Font:** Manrope, same family as body, differentiated by weight/case/tracking rather than a third typeface

**Character:** A magazine-editorial pairing — Newsreader's serif gives headlines warmth and credibility (a well-typeset document, not a software dashboard), while Manrope keeps body copy and UI chrome brisk and legible.

### Hierarchy
- **Display / Headline** (bold 700, `text-2xl`–`text-4xl`, tight line-height): Page titles, card titles (`h1`–`h4` all inherit `font-headline`), pricing numbers.
- **Title** (bold, `text-xl`, `font-headline`): Modal titles, section headers, resume card titles.
- **Body** (regular 400, `text-sm`–`text-base`, `font-body`): Paragraph copy, descriptions. Keep prose blocks to 65–75ch.
- **Label** (bold 700, `text-[10px]`–`text-xs`, uppercase, `tracking-widest`, `font-label`): Nav items, admin table headers, badges, section eyebrows on admin surfaces — the one place uppercase-tracked text is idiomatic here, since it's functional (table/nav structure), not decorative.

### Named Rules
**The Serif-Headline Rule.** Any `h1`–`h4` is `font-headline` (Newsreader) by default; never set a sans headline. This is what keeps the atelier feeling editorial instead of corporate.

## 4. Elevation

Resumify is flat by default. Cards, panels, and nav rest on a hairline border (`border-primary/10`) with at most `shadow-sm`; there is no ambient drop-shadow on resting elements. Shadow appears only as a response to state: hover on interactive cards, the elevated plane of a modal, or the deliberate "this is special" glow on the Premium pricing card. Depth in ordinary chrome comes from surface-color steps (`surface` → `surface-container-low` → `tertiary`), not shadow.

### Shadow Vocabulary
- **Resting card** (`shadow-sm`): Default state for cards, admin panels, buttons — barely-there definition, not depth.
- **Hover lift** (`hover:shadow-lg`, or the bespoke `0 16px 32px rgba(79,59,47,0.08)` on resume cards): Signals interactivity on hover only; uses the brand ink color in the shadow, not neutral black.
- **Modal** (`shadow-2xl`): The one interface plane that's meant to read as physically above the page.
- **Premium glow** (`.premium-glow` utility — `0 18px 42px rgba(161,98,7,0.14), 0 0 0 1px rgba(161,98,7,0.08)`): Gold-tinted, reserved exclusively for the Premium pricing card and premium upsell moments.

### Named Rules
**The Flat-at-Rest Rule.** No element carries a shadow in its default state except modals. If a shadow is visible before the user interacts with or hovers something, it's being used decoratively — remove it.

## 5. Components

### Buttons
- **Shape:** `rounded-lg` (8px) for standard buttons; `rounded-full` for the `pill` variant (secondary CTA badges) and icon buttons.
- **Primary:** Deep brown fill (`bg-primary`), white text, bold, `hover:opacity-90`. Used for the single main action per view.
- **Secondary/Pill:** Emerald fill (`bg-secondary`), white text, `rounded-full` — used for compact accent CTAs (e.g. "Edit" overlay on resume cards).
- **Outline:** Transparent background, `border-primary/20`, brown text, `hover:bg-primary/5` — the default secondary action.
- **Ghost / Text:** No border or fill; brown text at reduced opacity, `hover:bg-primary/5` — tertiary/dismissive actions.
- **Danger:** Red border/text at rest, inverts to solid red on hover — reserved for destructive actions only.
- **Dashed:** `border-2 border-dashed border-primary/20` — used specifically as an "add new" affordance (e.g. empty resume slot), never for a regular action.
- **Loading state:** Buttons swap their label/icon for a spinning `progress_activity` icon and set `aria-busy`; never disable without a visible reason.
- **Hit target:** All buttons and icon buttons hold `min-h-11 min-w-11` (44px) regardless of visual size, for touch accessibility.

### Chips / Badges
- **Plan badge:** `rounded-full`, uppercase, bold, tracked label. Premium uses the gold role (`border-[#A16207]/30 bg-[#A16207]/10 text-[#7C4A03]`); Basic uses brown-at-low-opacity. Never emerald — badges are identity, not progress.
- **Keyword/skill tags:** Small `rounded-full` chips at low-opacity brown fill.

### Cards / Containers
- **Corner style:** `rounded-lg` (8px) for functional cards (admin, resume cards, alerts), stepping up to `rounded-2xl` (16px) only for the pricing cards, where a softer, more inviting shape suits a sales moment.
- **Background:** `bg-tertiary` (white) on top of the cream page background — the "raised page" relationship is the core depth cue.
- **Border:** `border-primary/10` hairline by default; the Premium pricing card upgrades to `border-2 border-secondary` plus the premium glow.
- **Shadow strategy:** see Elevation — flat at rest, lift on hover.
- **Internal padding:** `p-6` for standard cards, `p-10` for pricing cards.

### Inputs / Fields
- **Style:** Underline-only (`border-b-2 border-primary/15`), transparent background, no box — the form feels like writing on a page, not filling out an enterprise form.
- **Focus:** Border shifts to emerald (`focus:border-secondary`); the floating label above turns emerald too.
- **Validation:** Inline, real-time on blur — a green checkmark or red error icon appears at the input's trailing edge, with the error message replacing the hint text below. Never a red box-shadow or shake; the color and icon carry the signal.
- **Disabled/current-plan state:** Reduced opacity (`opacity-60`) plus `cursor-not-allowed`, no separate visual language.

### Navigation
- **Sidebar (user & admin):** Fixed 256px column on `bg-nav-footer` (warm tan), one shade deeper than the page body. Active item gets a white/tertiary pill background with bold ink text; hover is a soft tint, never an underline.
- **Top navbar (landing):** Same tan background, sticky, active link marked with a 2px emerald underline rather than a filled state — landing nav is link-like, app nav is button-like.
- **Mobile:** Sidebar collapses to a bottom tab bar (user) or a slide-down drawer (landing); the drawer keeps the same active-state language as desktop (brown fill / tint) rather than inventing a new one.
- **Logout:** Always the last item, always red-on-hover (`hover:bg-red-50 hover:text-red-600`) — the only place red appears in navigation chrome, by design, so it reads as a boundary.

### Score Circle (signature component)
A circular SVG progress ring (ATS score, interview score) — brown track at 10% opacity, emerald progress arc, bold serif percentage centered inside. This is the product's core "actionable feedback" moment made visual: it appears anywhere a number needs to feel earned rather than just stated.

## 6. Do's and Don'ts

### Do:
- **Do** keep emerald (`#0F6E56`) to a single accent role per screen — progress, success, or the one primary action. If you're reaching for a second saturated color, use brown-at-opacity or the dedicated premium gold instead.
- **Do** default every card and panel to flat with a hairline border (`border-primary/10`) and add shadow only on hover, in modals, or for the Premium moment.
- **Do** set headlines in Newsreader and body/UI text in Manrope — never swap a headline to a sans font for "cleanliness."
- **Do** use the underline input style for text fields; keep validation inline (icon + message swap), not a red glow or shake.
- **Do** maintain `min-h-11 min-w-11` (44px) tap targets on every button and icon button.
- **Do** use the premium-gold role exclusively for Premium-tier signals, kept visually distinct from emerald's success/progress meaning.

### Don't:
- **Don't** build a wall of interchangeable template-picker cards with no guidance or point of view — that's the generic Canva-style pattern Resumify exists to avoid.
- **Don't** make any surface feel like sterile corporate HR/ATS software: no dense gray admin-only forms, no institutional coldness aimed at recruiters rather than the candidate.
- **Don't** default to the cream-background + gradient-text + glass-card AI-SaaS look. No `background-clip: text` gradients, no decorative glassmorphism outside the two purpose-built `glass-panel`/`glass-effect` utilities.
- **Don't** add a resting shadow to any element that isn't a modal — if it's visible before hover, remove it (see The Flat-at-Rest Rule).
- **Don't** use emerald and gold interchangeably; emerald means "this worked," gold means "you're on Premium." Mixing them muddies both signals.
- **Don't** use `border-left`/`border-right` colored stripes as a callout or card accent; alerts and callouts use full borders and background tints instead (see `x-ui.alert`).
- **Don't** round cards or sections past `rounded-2xl` (16px); full-pill rounding is reserved for buttons, badges, and avatars only.
