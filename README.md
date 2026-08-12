# Resumify

AI-powered CV builder built for the Indonesian job market — resume authoring, ATS gap analysis, and an AI mock interviewer in one flow, instead of three disconnected tools.

**Live demo:** [https://resumify-t5kx.onrender.com](https://resumify-t5kx.onrender.com/) (hosted on Render's free tier — the first request after idle may take ~a minute to cold-start)

Generic builders (Canva, Novoresume, Resume.io) don't understand local formats (BUMN/startup/corporate conventions, IPK, photo norms, Bahasa Indonesia) and don't connect CV building to ATS feedback or interview prep. Resumify's target: zero to a downloadable, tailored CV in under 10 minutes, with ATS feedback the user can actually act on. See [PRODUCT.md](PRODUCT.md) for the full product brief and [DESIGN.md](DESIGN.md) for the visual design system.

## Table of Contents

- [Features](#features)
- [Tech Stack](#tech-stack)
- [Architecture](#architecture)
- [Getting Started](#getting-started)
- [Configuration](#configuration)
- [Project Structure](#project-structure)
- [Roles & Access Control](#roles--access-control)
- [Testing](#testing)
- [Deployment](#deployment)
- [Documentation](#documentation)

## Features

### Evaluator Feature Checklist

| Feature | Route / Menu | Role Access | Evidence in Project |
| --- | --- | --- | --- |
| Resume builder and autosave editor | `/dashboard`, `/manuscripts`, `/resumes/{cv}/section/{section}` | Basic, Premium | `Cv` and `CvSection` models, `ResumeController`, `UpdateResumeSectionAction`, responsive manuscript editor views |
| CV template catalog and PDF export | `/templates`, `/resumes/{cv}/preview`, `/resumes/{cv}/pdf` | Basic, Premium; Premium unlocks premium templates | `CvTemplate`, `TemplateController`, `ResumeExportController`, `barryvdh/laravel-dompdf` |
| ATS scanner with scan history | `/ats`, `/ats/analyze`, `/ats/history/{scan}` | Basic and Premium with AI quota / feature rules | `AtsController`, `AtsScan`, `AtsScoreService`, structured Gemini response validation |
| AI resume assistance | `/resumes/{cv}/ai/refine-bullet`, `/resumes/{cv}/ai/generate-versions` | Basic and Premium with AI quota | `AiResumeController`, `RefineResumeBulletAction`, `GenerateResumeVersionsAction`, anti-fabrication validator |
| Mock interview and feedback | `/interview`, `/interview/start`, `/interview/sessions/{session}/feedback` | Basic trial, Premium full access | `InterviewController`, `InterviewSession`, `InterviewMessage`, `InterviewFeedback`, SSE streaming tests |
| Billing and subscription upgrade | `/upgrade-quota`, `/payment/create`, `/payment/callback` | Authenticated customers | `PaymentController`, `SubscriptionController`, `Transaction`, Midtrans webhook handler |
| Support tickets and chat | `/help`, `/help/tickets`, `/help/tickets/{ticket}` | Basic, Premium; Admin replies through admin support | `SupportTicket`, `TicketReply`, `TicketChat` Livewire component, mutual close flow |
| Admin operations dashboard | `/admin/dashboard`, `/admin/users`, `/admin/support`, `/admin/templates`, `/admin/logs`, `/admin/reports` | Admin only | Role middleware, admin controllers, user management actions, CSV/PDF exports, Sentry-backed monitor |
| Authentication, OAuth, localization, and security headers | `/login`, `/register`, `/auth/{provider}`, `/locale/{locale}` | Guests and authenticated users | Laravel Fortify, Socialite, `SetLocale`, `SecurityHeadersMiddleware`, `lang/en` and `lang/id` |

### Resume Builder

Section-based CV editor (`Cv` → `CvSection`) with autosave, per-section version history (`CvSectionSnapshot`) with restore, multiple templates with role-gated access (free vs. premium templates), PDF export (`barryvdh/laravel-dompdf`), and a public demo preview per template that never exposes real user data.

### ATS Scanner

Submits a CV + target job description to Gemini and returns a structured, actionable gap list (not just a score) — missing keywords, formatting issues, and concrete next steps. Every scan is persisted (`AtsScan`) with history the user can revisit or delete.

### AI Resume Assistance ("Chameleon")

- **Refine bullet**: rewrites a single bullet point into 3 alternatives.
- **Generate versions**: produces multiple tailored angles of a CV section for a target job (`ChameleonAdaptation`), which the user can selectively apply back.

All AI rewriting is constrained by explicit anti-fabrication rules (see `App\Services\AiService`) — the model may only rephrase facts already present in the source content; it is not allowed to invent employers, skills, or metrics.

### Mock Interview ("Ms Sarah")

A chat-based mock interview (`InterviewSession` / `InterviewMessage`) that references the user's own CV content, streams responses over SSE, and produces structured, STAR-based feedback (`InterviewFeedback`) at the end of a session. Basic-tier users get a limited trial via `InterviewTrialMiddleware`.

### Billing & Subscriptions

Midtrans-based payment flow (`PaymentController`, `SubscriptionController`) for upgrading from basic to premium, with webhook-driven transaction reconciliation (`Transaction`, `Subscription`).

### Admin Panel

Internal ops surface: user management (plan overrides, credit adjustments, suspension), support ticket triage with a mutual close flow, CV template CRUD, AI/finance CSV+PDF exports, and a Sentry-backed error/usage monitor (`AdminMonitorController`). Built for efficiency and legibility, not polish — see `PRODUCT.md`.

### Support

In-app help center with ticketing (`SupportTicket`, `TicketReply`), live chat via a Livewire component (`TicketChat`), and a two-sided close/confirm/reject flow so tickets don't get silently closed by either party.

### Platform features

Google OAuth via Laravel Socialite (LinkedIn sign-in is scaffolded but currently disabled — still in development), two-factor auth and email verification (Laravel Fortify), English/Indonesian localization (`lang/en`, `lang/id`), Sentry error tracking, and WCAG 2.1 AA accessibility baseline (keyboard operability, focus states, `prefers-reduced-motion`).

## Tech Stack

| Layer | Choice |
| --- | --- |
| Backend | PHP 8.2+ / Laravel 12 |
| Reactive UI | Livewire 4 (chat/ticket components), Alpine.js (lightweight interactivity) |
| Frontend build | Vite 7, Tailwind CSS 4 |
| Database | MySQL 8 |
| Auth | Laravel Fortify (2FA, email verification), Laravel Socialite (Google OAuth; LinkedIn in development) |
| AI provider | Google Gemini (`gemini-2.5-flash`) via direct HTTP calls |
| Payments | Midtrans |
| PDF export | `barryvdh/laravel-dompdf` |
| Error tracking | Sentry (`sentry-laravel`), surfaced in-app via the admin monitor |
| Testing | PHPUnit (Feature + Unit), GitHub Actions CI |
| Deployment | Docker (multi-stage: Node asset build → PHP 8.4-cli runtime), Render |

## Architecture

Resumify follows a layered Laravel structure rather than default MVC-only: fat orchestration logic that used to live in controllers has been extracted into single-purpose classes, and AI/quota boundaries are centralized rather than duplicated per endpoint. Summary:

- **Controllers** (`app/Http/Controllers`) stay thin — authorization, validation, delegating to an Action, mapping the response.
- **Actions** (`app/Actions/{Admin,Ai,Interviews,Resumes,Fortify}`) own orchestration for a single use case (e.g. generating CV versions, ending an interview session).
- **Services** (`app/Services`) hold reusable domain logic: `AiService` (Gemini calls + anti-fabrication prompting), `AiCreditService` / `AiCreditReservation` (quota accounting), `AtsScoreService`, `InterviewService`, `CvFactualityValidator`.
- **Domain DTOs** (`app/Domain/Ai/Data`) validate AI provider output before it's trusted — exact keys, required fields, enum values, numeric ranges, bounded list/string sizes. Malformed provider output is rejected (`InvalidAiProviderResponseException`), never silently normalized or persisted.
- **Support** (`app/Support`) — cross-cutting helpers, notably `ApiResponse` for a standard `{success, data}` / `{success: false, error: {code, message}}` JSON contract, with legacy string-error compatibility (`legacyError()`) kept only where existing frontend JS still depends on it.
- **Queries** (`app/Queries`) — read-side query objects kept out of controllers/services.
- **Livewire** (`app/Livewire`) — used selectively for stateful, real-time-feeling UI (admin template cards/stats, the support ticket chat), not as the default rendering layer.

**AI credit accounting** is the one piece worth understanding before touching any AI endpoint: `AiCreditService` reserves credits inside a DB transaction with `lockForUpdate()` on the user row, recording every attempt in `ai_credit_reservations` (status: `reserved` / `denied` / `bypassed` for premium+admin / refunded). This exists specifically to prevent basic-tier users from overspending quota under parallel requests, and to make refunds on provider failure idempotent. Any new AI endpoint must go through this service, validate provider output via a `Domain\Ai\Data` DTO, and respond via `ApiResponse`.

## Getting Started

### Prerequisites

- PHP 8.2+ with `pdo_mysql`, `mbstring`, `zip`, `gd`, `bcmath`, `curl` extensions
- Composer 2
- Node.js 20+ / npm
- MySQL 8

### Setup

```bash
git clone <repo-url>
cd Project
composer install
cp .env.example .env
php artisan key:generate
npm install
```

Create the database and configure `.env` (see [Configuration](#configuration)), then:

```bash
php artisan migrate
npm run build   # or: npm run dev, for hot-reloading during development
```

### Run the dev server

```bash
composer dev
```

This runs `php artisan serve`, `php artisan queue:listen`, and `npm run dev` concurrently — the app expects a queue worker for mail and any queued jobs.

Alternatively, run pieces individually:

```bash
php artisan serve
php artisan queue:listen --tries=1
npm run dev
```

## Configuration

Key `.env` variables beyond the standard Laravel/DB setup (full list in [.env.example](.env.example)):

| Variable | Purpose |
| --- | --- |
| `GEMINI_API_KEY` | Required for ATS scanning, resume AI features, and mock interviews |
| `QUOTA_BASIC` / `QUOTA_PREMIUM` | Monthly AI action quota per role (`config/quota.php`) |
| `RESUME_LIMIT_BASIC` / `RESUME_LIMIT_PREMIUM` | Max resumes per role; leave empty for unlimited (`config/plans.php`) |
| `ADMIN_EMAIL` / `ADMIN_PASSWORD` | Seeded admin account (`database/seeders/AdminSeeder.php`, gitignored — not present in this repo) |
| `GOOGLE_CLIENT_ID` / `GOOGLE_CLIENT_SECRET` | Google OAuth login |
| `LINKEDIN_CLIENT_ID` / `LINKEDIN_CLIENT_SECRET` | LinkedIn OAuth login (`linkedin-openid` driver) |
| `MIDTRANS_SERVER_KEY` / `MIDTRANS_CLIENT_KEY` / `MIDTRANS_IS_PRODUCTION` | Payment gateway for premium upgrades |
| `SENTRY_LARAVEL_DSN` | Error tracking |
| `SENTRY_AUTH_TOKEN` / `SENTRY_ORG_SLUG` / `SENTRY_PROJECT_SLUG` | Powers the in-app admin monitor page (Sentry Issues API) |

Admin bypasses AI credit debits entirely; premium is metered but generous; basic is hard-limited by `QUOTA_BASIC`.

## Project Structure

```text
app/
  Actions/          Single-use-case orchestration (Admin, Ai, Interviews, Resumes, Fortify)
  Domain/Ai/Data/    DTOs that validate untrusted Gemini output before it's trusted
  Http/Controllers/  Thin controllers, grouped by area (Admin, Auth, Billing, Public, Support, User)
  Http/Middleware/   Role/quota/suspension/locale/security-header guards
  Livewire/          Stateful components (admin template cards/stats, support ticket chat)
  Models/            Eloquent models
  Policies/          Authorization policies
  Queries/           Read-side query objects
  Services/          Reusable domain logic (AI, ATS scoring, interviews, credit reservation)
  Support/           Cross-cutting helpers (ApiResponse, etc.)
database/
  migrations/        39 migrations
  seeders/           AdminSeeder (gitignored — contains real credentials)
resources/
  css/ js/ views/    Tailwind + Alpine + Blade
routes/
  web.php            All application routes (public, authenticated customer, admin)
tests/
  Feature/ Unit/     PHPUnit — 61 test files
docker/
  entrypoint.sh      Cache config/routes/views, migrate, then serve
```

## Roles & Access Control

Three roles, enforced via `RoleMiddleware` (`role:basic,premium`, `role:admin`) and `User` model helpers (`isAdmin()`, `isCustomer()`, `isPremium()`):

- **basic** — free tier; limited resumes (`RESUME_LIMIT_BASIC`), limited AI quota (`QUOTA_BASIC`), interview access via a bounded trial (`InterviewTrialMiddleware`), no premium templates/ATS features gated by `config('plans.premium_features')`.
- **premium** — paid tier (Midtrans subscription); higher resume/AI limits, full template library, unrestricted interview and ATS access.
- **admin** — internal ops; bypasses AI credit debits (still logged for audit), full access to `/admin/*` (users, support tickets, templates, logs, monitor, reports).

Suspended users are blocked at the middleware layer (`CheckSuspended`) regardless of role.

## Testing

```bash
composer test
# or
php artisan test
```

61 test files across `tests/Feature` and `tests/Unit`. CI ([.github/workflows/laravel.yml](.github/workflows/laravel.yml)) runs the full suite against MySQL 8 on every push/PR to `develop` and `main`.

## Deployment

Live at [https://resumify-t5kx.onrender.com](https://resumify-t5kx.onrender.com/), deployed on [Render](https://render.com) via Docker.

Production database uses an external MySQL database from [Filess.io](https://filess.io/). Render only runs the Dockerized Laravel app; persistent data is stored outside the container so redeploys do not wipe application data.

Production monitoring is handled outside the application container with Prometheus and Grafana: Prometheus collects runtime/service metrics, while Grafana is used to visualize deployment health, resource usage, and uptime trends. Application-level error tracking is also supported through Sentry configuration (`SENTRY_LARAVEL_DSN` and related variables).

Docker-based, multi-stage build (see [Dockerfile](Dockerfile)):

1. **assets** stage — Node 20, builds Vite assets (`npm run build`).
2. **runtime** stage — `php:8.4-cli` with required extensions, Composer install (`--no-dev`), built assets copied in.

[docker/entrypoint.sh](docker/entrypoint.sh) caches config/routes/views, runs migrations, then serves on `$PORT` (defaults to 8080) — matches Render's free-tier hosting model.

Required Render environment variables for the production database:

| Variable | Value / Source |
| --- | --- |
| `DB_CONNECTION` | `mysql` |
| `DB_HOST` | Filess.io MySQL host |
| `DB_PORT` | Filess.io MySQL port |
| `DB_DATABASE` | Filess.io database name |
| `DB_USERNAME` | Filess.io database username |
| `DB_PASSWORD` | Filess.io database password, stored only in Render environment variables |

```bash
docker build -t resumify .
docker run -p 8080:8080 --env-file .env resumify
```

## Documentation

- [PRODUCT.md](PRODUCT.md) — product brief: users, jobs-to-be-done, brand personality, design principles, accessibility baseline
- [DESIGN.md](DESIGN.md) — visual design system: colors, typography, elevation, component vocabulary
