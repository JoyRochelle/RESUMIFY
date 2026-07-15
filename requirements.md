# Resumify — Full Project Requirements

> **Version:** 2.0  
> **Framework:** Lean Software Development (LSD)  
> **Stack:** Laravel 12 · MySQL · Tailwind CSS · Gemini API · Midtrans  
> **Team:** 5 members — Lean Master · 2 Lean Project Leaders · 2 Lean Dev Team  
> **Timeline:** 12 Weeks · 7 Iterations · 40 User Stories · 18 Use Cases  
> **Repository:** github.com/JoyRochelle/RESUMIFY (branch: develop)

---

## Table of Contents

1. [Project Overview](#1-project-overview)
2. [Goals & Success Metrics](#2-goals--success-metrics)
3. [Target Users](#3-target-users)
4. [System Actors](#4-system-actors)
5. [Tech Stack & Architecture](#5-tech-stack--architecture)
6. [Database Schema](#6-database-schema)
7. [Use Cases](#7-use-cases)
8. [Feature Requirements](#8-feature-requirements)
   - 8.1 Authentication
   - 8.2 Manage Profile
   - 8.3 Template Library
   - 8.4 Resume Builder (Create / Edit / Download)
   - 8.5 ATS Scanner
   - 8.6 AI Features (Optimize + Generate CV Versions)
   - 8.7 Payment & Subscription
   - 8.8 Mock Interview HRD
   - 8.9 Admin Panel
   - 8.10 Support Tickets & Help Center
9. [Non-Functional Requirements](#9-non-functional-requirements)
10. [Differentiators vs Competitors](#10-differentiators-vs-competitors)
11. [Iteration Plan (LSD)](#11-iteration-plan-lsd)
12. [Team Roles & Responsibilities](#12-team-roles--responsibilities)
13. [API Integrations](#13-api-integrations)
14. [Subscription Plans & Quota](#14-subscription-plans--quota)
15. [File & Storage Structure](#15-file--storage-structure)
16. [Security Requirements](#16-security-requirements)
17. [Out of Scope (MVP)](#17-out-of-scope-mvp)

---

## 1. Project Overview

Resumify is an AI-powered CV builder web application built specifically for the Indonesian job market. It allows users to create professional resumes from curated templates, optimize them using AI against specific job descriptions, generate multiple CV versions targeting different companies simultaneously, and practice job interviews with an AI chatbot that reads their actual CV content.

**The core problem Resumify solves:** Indonesian job seekers spend hours manually editing their CV for each application, get rejected by ATS systems without knowing why, and have no affordable way to practice interviews before the real thing. Existing competitors (Canva Resume, Novoresume, Resume.io, Resmume, Intervyou.me) either lack deep Indonesian context, require expensive subscriptions, or split CV building and interview practice into separate tools.

**Resumify's position:** The only CV builder in Indonesia that combines (1) AI-powered multi-version CV generation, (2) deep Indonesian market context (BUMN, startup, IPK format, photo guidelines), and (3) mock interview practice driven by the user's own CV content — all in one platform.

---

## 2. Goals & Success Metrics

### Business Goals
- Achieve 500 registered users within 30 days of launch
- Achieve 10% free-to-premium conversion rate within 60 days
- Keep AI API cost below 30% of premium subscription revenue

### Product Goals
- Users can go from zero to a downloadable CV in under 10 minutes
- ATS scanner provides actionable keyword feedback, not just a score
- Mock interview questions feel specific to the user's actual CV (target: ≥60% of questions reference CV content)

### Technical Goals
- Lighthouse performance score ≥ 85 on all pages
- Page load time < 2 seconds on 4G mobile
- System uptime ≥ 99.5% (monitored via UptimeRobot)
- Zero critical security vulnerabilities at launch

---

## 3. Target Users

### Primary Users
**Fresh Graduates (S1/D3)**
- Applying to 5–20 companies simultaneously for the same or similar roles
- Need BUMN, startup, and corporate resume formats
- Unfamiliar with ATS systems and what HRD looks for
- Budget-sensitive — will use Free plan first

**Mid-level Professionals (2–5 years experience)**
- Actively job-hunting or passively open to opportunities
- Need to tailor their CV per company without rebuilding from scratch
- Willing to pay for Premium if it saves time

### Secondary Users
**Career Switchers**
- Pivoting industries and need help reframing their existing experience
- Benefit most from AI bullet optimizer and the Generate CV Versions feature

**Admin / Platform Operators**
- Manage users, templates, AI usage costs, and support tickets
- Need visibility into revenue, system health, and error logs

---

## 4. System Actors

| Actor | Type | Description |
|---|---|---|
| **Free User** | Primary | Registered user on the free plan. Has access to core CV building features with quota limits. |
| **Premium User** | Primary | Paid subscriber. Inherits all Free User access plus AI optimization, CV version generation, and mock interview features. |
| **User (parent)** | Generalisation | Parent actor for Free User and Premium User in UML. All shared use cases (Auth, Manage Profile, Template Library, Create Resume, Edit Resume, Download PDF, ATS Scanner basic, Payment) connect to this actor. |
| **Admin** | Primary | Platform operator. Has exclusive access to the admin panel — user management, template catalog, AI API monitoring, revenue reports, and support ticket management. |
| **Gemini API** | External System | Called by AI features: ATS Scanner (AI-powered), Optimize Resume, Generate CV Versions, and Mock Interview. Not a UML actor — represented as an external system outside the system boundary. |
| **Midtrans** | External System | Payment gateway for GoPay, OVO, Credit Card, and Bank Transfer. Called by the Payment / Upgrade use case. |
| **Email Service (SMTP/Mailgun)** | External System | Sends email verification, password reset, payment confirmation, and support ticket reply emails. |
| **Sentry** | External System | Error tracking and system health monitoring. Called by Monitor System Logs and Generate System Report. |
| **JobStreet ATS** | External System | Reference data for ATS compatibility checking. Called by the ATS Scanner use case. |

---

## 5. Tech Stack & Architecture

### Backend
| Component | Technology |
|---|---|
| Framework | Laravel 12 |
| Language | PHP 8.3+ |
| Database | MySQL 8.0 |
| Queue | Laravel Queue (database driver, upgradeable to Redis) |
| Job Scheduler | Laravel Scheduler (cron) |
| File Storage | Laravel Storage (local disk, S3-compatible for production) |
| PDF Generation | barryvdh/laravel-dompdf |
| OAuth | Laravel Socialite (Google, LinkedIn OpenID) |
| AI Integration | Google Gemini API (via Laravel HTTP Client) |
| Payment | Midtrans PHP SDK + Snap.js |
| Email | Laravel Mail + SMTP/Mailgun |
| Error Tracking | Sentry Laravel SDK |
| Authentication | Laravel built-in Auth + Sanctum (if API needed later) |
| Authorisation | Laravel Gates & Policies |

### Frontend
| Component | Technology |
|---|---|
| Templating | Laravel Blade |
| CSS Framework | Tailwind CSS 3.x |
| JavaScript | Vanilla JS + Alpine.js (lightweight reactivity) |
| Charts | Chart.js (admin dashboard) |
| Payment Modal | Midtrans Snap.js |
| Real-time Streaming | Server-Sent Events (SSE) — for mock interview chat |
| Icons | Heroicons / custom SVG |

### Design System (from Stitch)
| Token | Value |
|---|---|
| Primary colour | `#4f3b2f` (Deep Brown) |
| Accent colour | `#0F6E56` (Emerald Green) |
| Background | `#FDF8F4` (Warm Cream) |
| Heading font | Newsreader (serif) |
| Body font | Manrope (sans-serif) |
| Border radius | 8px (cards), 20px (pills) |
| AI feature style | Glassmorphism overlay |

### Infrastructure
| Component | Technology |
|---|---|
| Hosting | Railway or Laravel Forge + DigitalOcean |
| HTTPS | Let's Encrypt (auto-renewed) |
| CI/CD | GitHub Actions (develop → staging, main → production) |
| Process Manager | Supervisor (queue workers) |
| Monitoring | UptimeRobot (uptime) + Sentry (errors) |

---

## 6. Database Schema

### Table: `users`
| Column | Type | Notes |
|---|---|---|
| id | bigint PK | |
| name | varchar(255) | |
| email | varchar(255) | unique |
| email_verified_at | timestamp | nullable |
| password | varchar(255) | nullable (null for OAuth users) |
| provider | varchar(50) | nullable — 'google' or 'linkedin' |
| provider_id | varchar(255) | nullable |
| avatar | varchar(255) | nullable — URL or storage path |
| role | enum('user','admin') | default 'user' |
| created_at / updated_at | timestamp | |

### Table: `resumes`
| Column | Type | Notes |
|---|---|---|
| id | bigint PK | |
| user_id | bigint FK → users | cascadeOnDelete |
| template_id | bigint FK → templates | nullable |
| title | varchar(100) | e.g. "CV untuk Gojek" |
| job_target | varchar(100) | nullable — e.g. "UI/UX Designer" |
| company_target | varchar(100) | nullable |
| ats_score | int | default 0, range 0–100 |
| status | enum('draft','completed') | default 'draft' |
| deleted_at | timestamp | nullable (soft delete) |
| created_at / updated_at | timestamp | |

### Table: `resume_sections`
| Column | Type | Notes |
|---|---|---|
| id | bigint PK | |
| resume_id | bigint FK → resumes | cascadeOnDelete |
| type | enum | 'personal_info', 'work_experience', 'education', 'skills', 'certifications', 'projects', 'languages' |
| content | json | structured data per section type |
| order | int | default 0 — display order |
| last_saved_at | timestamp | nullable — updated by auto-save |
| created_at / updated_at | timestamp | |

### Table: `resume_versions`
| Column | Type | Notes |
|---|---|---|
| id | bigint PK | |
| resume_id | bigint FK → resumes | parent resume |
| title | varchar(100) | e.g. "CV Gojek — Leadership Angle" |
| job_target | varchar(100) | nullable |
| angle | enum('leadership','technical','ownership') | which CV version angle |
| content | json | full sections snapshot |
| created_at / updated_at | timestamp | |

### Table: `templates`
| Column | Type | Notes |
|---|---|---|
| id | bigint PK | |
| name | varchar(100) | |
| category | enum | 'professional', 'creative', 'technology', 'managerial' |
| thumbnail_path | varchar(255) | storage path |
| is_active | tinyint(1) | default 1 |
| is_bestseller | tinyint(1) | default 0 |
| is_ai_enhanced | tinyint(1) | default 0 |
| created_at / updated_at | timestamp | |

### Table: `subscriptions`
| Column | Type | Notes |
|---|---|---|
| id | bigint PK | |
| user_id | bigint FK → users | |
| plan | enum('free','premium') | |
| ai_credits_remaining | int | Free: 5, Premium: 50 |
| ai_credits_total | int | |
| starts_at | timestamp | |
| expires_at | timestamp | nullable |
| created_at / updated_at | timestamp | |

### Table: `transactions`
| Column | Type | Notes |
|---|---|---|
| id | bigint PK | |
| user_id | bigint FK → users | |
| midtrans_order_id | varchar(100) | unique |
| midtrans_transaction_id | varchar(100) | nullable |
| amount | decimal(12,2) | in IDR |
| payment_method | varchar(50) | 'gopay', 'ovo', 'credit_card', 'bank_transfer' |
| status | enum('pending','success','failed','expired') | |
| paid_at | timestamp | nullable |
| created_at / updated_at | timestamp | |

### Table: `ai_usage_logs`
| Column | Type | Notes |
|---|---|---|
| id | bigint PK | |
| user_id | bigint FK → users | |
| action_type | enum | 'ats_analyze', 'bullet_optimize', 'generate_versions', 'interview_question', 'interview_feedback' |
| tokens_used | int | |
| cost_usd | decimal(8,6) | calculated from tokens |
| resume_id | bigint | nullable |
| created_at | timestamp | |

### Table: `interview_sessions`
| Column | Type | Notes |
|---|---|---|
| id | bigint PK | |
| user_id | bigint FK → users | |
| resume_id | bigint FK → resumes | |
| job_target | varchar(100) | |
| overall_score | int | nullable, 0–100, set on endSession |
| feedback | json | nullable — AI summary generated on endSession |
| ended_at | timestamp | nullable |
| created_at / updated_at | timestamp | |

### Table: `interview_messages`
| Column | Type | Notes |
|---|---|---|
| id | bigint PK | |
| session_id | bigint FK → interview_sessions | cascadeOnDelete |
| role | enum('user','assistant') | |
| content | text | message content |
| created_at | timestamp | |

### Table: `support_tickets`
| Column | Type | Notes |
|---|---|---|
| id | bigint PK | |
| user_id | bigint FK → users | |
| subject | varchar(255) | |
| status | enum('open','pending','closed') | default 'open' |
| assigned_to | bigint FK → users | nullable — admin user |
| created_at / updated_at | timestamp | |

### Table: `ticket_replies`
| Column | Type | Notes |
|---|---|---|
| id | bigint PK | |
| ticket_id | bigint FK → support_tickets | |
| user_id | bigint FK → users | sender (user or admin) |
| body | text | |
| created_at | timestamp | |

---

## 6. Use Cases

### User-side Use Cases

| ID | Use Case | Actor | Premium Only |
|---|---|---|---|
| UC_Auth | Auth (login / register) | Free + Premium User | No |
| UC_Profile | Manage Profile | Free + Premium User | No |
| UC_Template | View Template Library | Free + Premium User | No |
| UC_Create | Create Resume | Free + Premium User | No |
| UC_Edit | Edit Resume | Free + Premium User | No |
| UC_Download | Download Resume PDF | Free + Premium User | No |
| UC_ATS | ATS Scanner | Free + Premium User | No (basic); AI-powered = Premium |
| UC_Payment | Payment / Upgrade | Free User | No |
| UC_Optimize | Optimize Resume with AI | Premium User | **Yes** |
| UC_Versions | Generate CV Versions | Premium User | **Yes** |
| UC_Interview | Mock Interview HRD | Premium User | **Yes** (1 trial for Free) |

### Admin-side Use Cases

| ID | Use Case | Actor |
|---|---|---|
| UC_ManageUser | Manage User | Admin |
| UC_ManageTemplate | Manage Template | Admin |
| UC_ManageAI | Manage AI API | Admin |
| UC_Monitor | Monitor System Logs | Admin |
| UC_Report | Generate System Report | Admin |
| UC_Support | Respond Support Ticket | Admin |
| UC_Revenue | View Revenue Report | Admin |

### Key Relations
- `UC_Edit` **include** → `UC_Optimize` and `UC_Versions` (AI features always need an existing resume)
- `UC_ATS` **extend** → `UC_Optimize` (ATS scan can trigger optimize, but not always)
- `UC_Interview` **include** → Gemini API (always calls AI)
- `UC_Payment` **include** → Midtrans (always goes through gateway)
- `UC_Auth` **include** → Email Service (verification email)

---

## 8. Feature Requirements

### 8.1 Authentication

#### Register (email + password)
- Form fields: name, email, password, password confirmation
- Validation: email must be unique, password min 8 characters, confirmation must match
- On success: create user, create free subscription record (5 AI credits), send verification email via queue
- Redirect to dashboard after verification

#### Login (email + password)
- Throttle: max 5 failed attempts per IP/email combination, lockout for 60 seconds
- Remember-me token: 30 days
- Redirect to originally intended URL if redirected by auth middleware

#### Email Verification
- Signed URL sent via queue (target delivery: < 30 seconds)
- Clicking link sets `email_verified_at`
- Resend link available on dashboard banner if not yet verified
- Unverified users can still log in but see a banner prompt

#### Forgot / Reset Password
- Email with reset link (expires in 60 minutes)
- Resetting invalidates all current sessions
- New password must pass same validation as registration

#### Google OAuth
- Uses Laravel Socialite, driver: `google`
- Redirect → Google consent → callback: `updateOrCreate` user by provider + provider_id
- Stores name, email, avatar from Google profile
- Password column set to null (nullable) for OAuth users

#### LinkedIn OAuth
- Uses Laravel Socialite, driver: `linkedin-openid` (not `linkedin` — LinkedIn uses OpenID Connect)
- Same callback flow and controller as Google OAuth (`SocialAuthController` with dynamic `$provider`)
- Requires LinkedIn Developer App with "Sign In with LinkedIn using OpenID Connect" product approved

#### Logout
- Invalidates current session
- Clears remember-me token

---

### 8.2 Manage Profile

**Fields editable:**
- Full name
- Email (must remain unique — validation against other users)
- Avatar / profile photo (upload, stored in `storage/app/public/avatars/`, max 2MB, jpg/png/webp)

**Change Password (separate form):**
- Requires current password confirmation
- New password: min 8 characters

**Danger Zone:**
- Delete Account: soft-delete user and all associated resumes
- Requires current password confirmation to verify identity

**Backend:**
- `ProfileController::update()` — validate, update user record, move uploaded file to storage
- `ProfileController::changePassword()` — verify current password with `Hash::check()`, update hash
- `ProfileController::destroy()` — soft-delete user, dispatch cleanup job

---

### 8.3 Template Library

**Display:**
- Grid layout — 3 columns desktop, 2 tablet, 1 mobile
- Each card: thumbnail image, template name, category badge, optional badges (BEST SELLER, AI ENHANCED)
- Hover: preview overlay button appears

**Filter tabs:** All · Professional · Creative · Technology · Managerial

**Preview:** Clicking preview opens a full-page overlay with a scrollable rendered preview of the template with sample data.

**Select Template:** "Use This Template" button creates a new resume record with this template's default section structure and redirects to workspace editor.

**Backend:**
- `TemplateController::index()` — returns templates filtered by `category` query param, only `is_active = 1`
- `TemplateController::show()` — returns single template for preview
- **Seeder data required:** 12 templates minimum:
  - Professional × 3 (corporate, conservative, suitable for multinational)
  - Creative × 3 (visual, project-focused, portfolio-style)
  - Technology × 3 (modern, clean, skills-forward, developer-oriented)
  - Managerial × 3 (structured, formal, leadership-focused)

---

### 8.4 Resume Builder

#### Create Resume
- Triggered from: dashboard "New Resume" button, or template library "Use This Template"
- Form: title (required), job_target (optional), company_target (optional), template_id
- On submit: creates `resumes` record + 4 default `resume_sections` (personal_info, work_experience, education, skills) with empty JSON content
- Redirects to workspace editor for the new resume

#### Workspace Editor (Edit Resume)
**Layout:** Split-screen — left panel editor, right panel live preview

**Left panel — accordion sections:**
- **Personal Info:** name, email, phone, location, LinkedIn URL, portfolio URL, professional summary, photo upload (optional, relevant for Indonesian market)
- **Work Experience:** repeatable items — company, role, start date, end date (or "Present"), bullet points (repeatable)
- **Education:** repeatable items — institution, degree, major, year, GPA/IPK (optional)
- **Skills:** tag-style input — add/remove skill chips
- **Optional sections:** Certifications, Projects, Languages (can be toggled on/off)

**Right panel — live preview:**
- Renders CV using the selected template's Blade layout
- Updates after each auto-save response (no full page reload)
- Shows ATS score progress bar in sidebar

**Auto-save:**
- Debounced: 1.5 seconds after last keystroke in any field
- AJAX `PATCH /resumes/{resume}/sections/{section}` with `content` JSON payload
- CSRF token sent in headers (`X-CSRF-TOKEN`)
- Response: `{ success: true, saved_at: "14:32:05", ats_score: 72 }`
- UI indicator: three states — "Saving..." (amber) → "✓ Saved · 14:32" (green) → "✗ Failed to save" (red)

**Duplicate Resume:**
- Eloquent `replicate()` — clones resume record and all section records
- New title: `{original title} (Copy)`
- Status reset to `draft`
- Redirect to new resume editor

**Delete Resume:**
- Soft delete (`deleted_at` timestamp)
- Requires confirmation modal
- Deleted resumes not shown on dashboard
- Can be restored within 30 days (future feature — not MVP)

**Authorization:**
- `ResumePolicy` on all methods — `user_id` must match `auth()->id()`

#### Download Resume PDF
- Endpoint: `GET /resumes/{resume}/export`
- Uses `barryvdh/laravel-dompdf`
- Blade template: `resources/views/resume/pdf/template-{template_slug}.blade.php`
- Paper size: A4 portrait
- **CSS constraint:** dompdf does not support flexbox, CSS grid, or external stylesheets. All CSS must be inline. Use float-based or table-based layout for multi-column sections.
- Filename: `{resume-title-slug}-{YYYYMMDD}.pdf`
- Preview endpoint: `GET /resumes/{resume}/preview` — streams PDF to browser tab

---

### 8.5 ATS Scanner

#### Basic (Iteration 2 — no AI, no credits)
- Reads `job_target` from the resume record
- `AtsScoreService::calculate(Resume $resume)`:
  1. Extract all text from `resume_sections.content` as a flat string
  2. Tokenise `job_target` into keywords (split by space, filter stopwords, filter < 3 chars)
  3. Count how many keywords appear (case-insensitive) in the resume text
  4. Score = (found / total) × 100, rounded to int
  5. Update `resumes.ats_score` and return the score
- Called automatically after every `updateSection` response
- Score returned in AJAX response JSON and updates the sidebar progress bar in real-time

#### AI-powered (Iteration 3 — Premium, uses AI credits)
- User pastes a job description into the ATS scanner textarea
- Sends resume text + job description to Gemini
- **Prompt output:**
  - Overall match percentage (0–100%)
  - List of missing keywords with context (why each keyword matters)
  - Section-by-section breakdown (which sections are strong, which are weak)
  - Specific suggestions for each weak section
- Uses 1 AI credit per analysis
- Results displayed in the `ai_assistant_workspace` view

---

### 8.6 AI Features

#### Prerequisites
- `AiService` class at `app/Services/AiService.php`
- Wraps Google Gemini API via Laravel HTTP Client (`Http::post()` to `generativelanguage.googleapis.com`)
- All AI calls go through this service — never direct API calls in controllers
- All calls logged to `ai_usage_logs` (action type, tokens used, cost calculated)

#### QuotaMiddleware
- Applied to all AI routes: `middleware('ai.quota')`
- Checks `subscriptions.ai_credits_remaining` for `auth()->user()`
- If credits = 0: return `response()->json(['error' => 'quota_exceeded'], 402)`
- Decrements credit after successful AI call (in the controller, after response received)

#### Optimize Resume Bullets (Premium)
- User selects a bullet point or section to optimize
- Sends bullet text + job target context to Gemini
- **Prompt instruction:** rewrite using strong action verbs, add quantified results where plausible, naturally include missing ATS keywords, maintain the user's authentic voice
- Returns 1–3 alternative rewrites
- User can accept one, reject all, or edit manually
- Uses 1 AI credit per optimization

#### Generate CV Versions (Premium)
- Input: current resume content + job description
- Sends to Gemini with instructions to generate **3 distinct versions** in **parallel** (3 concurrent API calls)
- **Version angles:**
  - **Leadership:** highlights team leadership, project ownership, cross-functional collaboration, decision-making
  - **Technical:** highlights specific tools, technologies, methodologies, technical achievements, certifications
  - **Ownership:** highlights end-to-end responsibility, initiative, startup mindset, autonomy, measurable business impact
- Each version returned as a complete resume_sections JSON snapshot
- UI: 3 cards displayed side by side — user previews each and downloads the one they prefer
- Each selected version saved as a `resume_versions` record
- Uses 3 AI credits (1 per version, billed upfront before generation)

---

### 8.7 Payment & Subscription

#### Plans

| Feature | Free | Premium PRO |
|---|---|---|
| Resume creation | Unlimited | Unlimited |
| Templates | All | All |
| PDF download | Unlimited | Unlimited |
| ATS score (basic) | ✓ | ✓ |
| ATS scanner (AI-powered) | ✗ | ✓ |
| AI bullet optimizer | ✗ | ✓ |
| Generate CV versions | ✗ | ✓ |
| Mock Interview HRD | 1 trial only | Unlimited |
| AI credits / month | 5 | 50 |
| Price | Free | Rp 49.000/month |

#### Payment Flow
1. User clicks "Activate Premium" on `/premium` or `/pricing`
2. Frontend calls `POST /payment/create` → Laravel creates Midtrans transaction, returns `snap_token`
3. Snap.js opens Midtrans payment modal with all available methods (GoPay, OVO, Credit Card, Bank Transfer)
4. User completes payment in modal
5. Midtrans sends webhook to `POST /payment/callback` (signed with server key)
6. `PaymentController::callback()`: verifies signature, updates `transactions` status, upgrades `subscriptions` plan, sets `ai_credits_remaining = 50`
7. Sends payment confirmation email via queue

#### Webhook Security
- Verify `HTTP_X_MIDTRANS_SIGNATURE` header using SHA-512 of `order_id + status_code + gross_amount + server_key`
- Reject any callback that fails signature verification

#### 30-Day Money-Back
- Manual process for MVP — user contacts support, admin manually triggers refund via Midtrans dashboard

---

### 8.8 Mock Interview HRD

This is Resumify's primary differentiator. The mock interview feature reads the user's actual resume content and generates questions specific to their experience — not generic templates.

#### User Flow
1. User navigates to `/interview`
2. Selects which resume to use (dropdown of their resumes)
3. Enters job target (optional — defaults to resume's `job_target`)
4. Clicks "Mulai Simulasi" → creates `interview_sessions` record, redirects to `/interview/{session}`
5. Chat interface loads with HRD persona "Bu Sari" opening with a personalised greeting and first question
6. User types answers, presses Send
7. AI asks 2–3 follow-up questions per topic, then moves to a new topic
8. After 5–8 exchanges, user clicks "Selesai Sesi"
9. AI generates structured feedback report, session marked as ended

#### InterviewService — Context Injection
```
system prompt = """
You are Bu Sari, a professional HR interviewer at a leading Indonesian company.
You are interviewing a candidate for the position of: {job_target}.

The candidate's CV contains the following information:
{resume_sections_formatted_text}

Your instructions:
- Ask questions that specifically reference the candidate's actual experience
  (mention company names, job titles, dates, and projects from their CV)
- Ask follow-up questions to dig deeper into each experience
- Evaluate answers using the STAR method (Situation, Task, Action, Result)
- Conduct the interview in Indonesian (Bahasa Indonesia) unless the job target is
  explicitly English-language
- After the user ends the session, provide structured feedback per question answered
"""
```

#### Response Streaming (SSE)
- Endpoint: `GET /interview/{session}/stream` (EventStream)
- Laravel streams Gemini response token-by-token using `response()->stream()`
- Frontend EventSource listens and appends tokens to the chat bubble as they arrive
- Typing indicator (3-dot animation) shown while waiting for first token

#### Feedback Report
- Triggered when user clicks "Selesai Sesi" → `POST /interview/{session}/end`
- Sends the full conversation history to Gemini with instruction to generate:
  - Per-question score (0–10) with STAR breakdown
  - Missing keywords that should have appeared in answers
  - Overall readiness score (0–100)
  - 3 specific improvement suggestions
- Saved to `interview_sessions.feedback` as JSON

#### Premium Gating
- Free users: allowed exactly 1 interview session (checked in `startSession()` — count existing sessions)
- After 1 session: return `402` with upgrade prompt
- Premium users: unlimited sessions

---

### 8.9 Admin Panel

**Access:** `/admin/*` routes protected by `middleware('admin')` — checks `users.role = 'admin'`

#### Admin Dashboard (`/admin`)
- **Stats cards:** Total Revenue (Rp, current month), AI API Cost (USD, current month), Open Support Tickets, Active Premium Users
- **Chart:** Line chart — Daily active users vs Daily AI calls (last 30 days, Chart.js)
- **System health card:** Sentry error count (last 24h), uptime status

#### Manage Users (`/admin/users`)
- Paginated table (20 per page)
- **Columns:** Name, Email, Plan (Free/Premium), AI Credits, Joined date, Status (Active/Suspended)
- **Filters:** plan, status, date range (joined)
- **Search:** by name or email
- **Actions per user:**
  - View detail
  - Override plan (manually set Free ↔ Premium without payment)
  - Adjust AI credits
  - Suspend / Activate
  - Delete (hard delete with confirmation)

#### Manage Templates (`/admin/templates`)
- Grid of all templates (including inactive)
- **Add template:** form — name, category dropdown, thumbnail upload, toggle badges
- **Edit template:** same form pre-filled
- **Toggle:** is_active, is_bestseller, is_ai_enhanced (switches reflected immediately in template library)
- Thumbnail stored in `storage/app/public/templates/`

#### AI & Finance Logs (`/admin/logs`)
- **Tab: AI Usage Log** — table: user, action type, tokens, cost (USD), resume, date — filterable by date range and action type
- **Tab: Finance Log** — table: user, order ID, amount (IDR), payment method, status, date — filterable by date range and status
- **Export CSV button** for both tabs (uses native PHP `fputcsv`, no extra package)

#### Monitor System Logs (`/admin/monitor`)
- Sentry recent errors feed (top 10 errors last 24h)
- Queue health: pending jobs count, failed jobs count, last processed at
- Cache and storage disk usage

#### Generate System Report (`/admin/reports`)
- Date range selector
- Report includes: new users, premium conversions, total AI calls, total AI cost, total revenue
- Export as PDF (dompdf) or CSV

#### Support Ticket Inbox (`/admin/support`)
- List: ticket ID, user name, subject, status badge, last reply date, assigned agent
- **Filters:** status (open/pending/closed), assigned agent, date
- **Ticket detail:** full conversation thread
- **Reply form:** textarea, submit sends email to user via SMTP queue
- **Assign:** dropdown to assign to another admin
- **Close / Reopen:** status toggle

---

### 8.10 Support Tickets & Help Center

#### Help Center (`/help`)
- FAQ accordion organised by category: Getting Started, Resume Builder, AI Features, Billing & Subscription, Technical Issues
- Contact form at bottom: name, email, subject, message → creates `support_tickets` record
- Confirmation message shown + confirmation email sent

#### User Ticket Status
- Users can see their ticket status at `/help/tickets`
- Shows: subject, status badge, last reply, created date

---

## 9. Non-Functional Requirements

### Performance
- All page loads: < 2 seconds on 4G mobile (tested via Lighthouse)
- PDF generation: < 5 seconds for standard resume
- AI responses: begin streaming within 3 seconds of request
- Auto-save response: < 500ms (database write, no AI)

### Responsiveness
- All 17 screens must be fully functional at:
  - 375px (iPhone SE — minimum)
  - 768px (iPad)
  - 1440px (desktop)
- No horizontal scroll on any breakpoint
- Touch targets minimum 44px × 44px on mobile

### Accessibility
- Colour contrast ratio ≥ 4.5:1 for all text (WCAG AA)
- All interactive elements have visible focus rings
- All icon-only buttons have `aria-label`
- Form fields have associated `<label>` elements

### Security
- CSRF protection on all state-changing routes (Laravel's built-in CSRF middleware)
- SQL injection prevention: Eloquent ORM used exclusively, no raw SQL with user input
- XSS prevention: Blade's `{{ }}` escaping used throughout; `{!! !!}` only for explicitly sanitised content
- File upload validation: mime type + extension check + max size enforcement
- Sensitive env variables never committed to repository
- Midtrans webhook signature verification on every callback
- Gemini API key stored in `.env`, never exposed to frontend
- Rate limiting on auth routes (Laravel's built-in throttle middleware)

### Availability
- Target uptime: 99.5%
- UptimeRobot monitoring every 5 minutes
- Alert sent to team email/Slack on downtime detection

---

## 10. Differentiators vs Competitors

| Feature | Canva Resume | Novoresume | Resmume (ID) | Intervyou.me (ID) | **Resumify** |
|---|---|---|---|---|---|
| CV Builder | ✓ Good | ✓ Good | △ Basic | △ Basic | ✓ Premium |
| Indonesian templates | ✗ | ✗ | △ Limited | ✗ | **✓ BUMN, Startup, IPK format** |
| AI bullet optimizer | ✗ | △ Basic | ✗ | ✗ | **✓ Context-aware** |
| Multi-version CV from 1 job desc | ✗ | ✗ | ✗ | ✗ | **✓ 3 angles, parallel generation** |
| Mock interview | ✗ | ✗ | ✗ | △ Generic templates | **✓ From user's actual CV** |
| Price (premium) | Rp 70k+/mo | USD 16+/mo | Free (very basic) | Rp 40k+/mo | **Rp 49k/mo** |
| Indonesian language | △ Partial | ✗ | ✓ | ✓ | **✓ Full** |

**Key differentiator:** No competitor reads the user's actual CV content to generate interview questions. Wawancara.ai, Terang.ai, Latihan.io, and Intervyou.me all generate questions based on the job title and industry — not the individual's specific experience. Resumify's `InterviewService` injects the user's actual resume sections into the AI system prompt, making every question feel like a real interview tailored to that person.

---

## 11. Iteration Plan (LSD)

LSD does not use sprint timeboxes. Work is pulled from the feature backlog based on value priority and dependency order. The following iteration groupings reflect logical delivery milestones.

### Iteration 0 — Initiating & Setup (Week 0)
**LSD Principle: See the Whole**

Deliverables: Laravel 12 repo, .env configured, MySQL ERD approved by all members, Tailwind design tokens, base Blade layouts (app/guest/admin), product backlog (40 stories with story points), CI/CD pipeline skeleton.

No features are built in this iteration. The goal is eliminating future waste by making the entire system visible before coding starts.

**Status:** Done — All deliverables for this iteration have been implemented.
- Laravel 12 repo initialised (`laravel/framework: ^12.0`)
- `.env` configured with MySQL, SMTP, Google OAuth, LinkedIn OAuth (placeholders), Gemini API, Midtrans (placeholder), Sentry (placeholder)
- `.env.example` updated as a comprehensive project template for new team members
- MySQL schema: 18 migrations covering users, cvs, cv_sections, cv_templates, subscriptions, oauth_providers, and supporting tables
- Tailwind CSS design tokens aligned with Stitch design system: Newsreader (serif heading), Manrope (sans body), primary `#4f3b2f`, accent `#0F6E56`, background `#FDF8F4`, border-radius 8px (cards) / 20px (pills)
- Base Blade layouts: `layouts/user/app` (authenticated), `layouts/auth/master` (guest/auth), `layouts/admin/app` (admin panel), `layouts/landing_page/app` (public)
- CI/CD pipeline skeleton: `.github/workflows/laravel.yml` (PHP 8.3, develop branch triggers)
- AI quota config: `config/quota.php` — Free: 5 credits, Premium: 50 credits

### Iteration 1 — Auth · Manage Profile · Template Library (Weeks 1–2)
**LSD Principle: Deliver Fast**

Deliverables: Register + login (email/password), email verification, forgot/reset password, Google OAuth, LinkedIn OAuth, manage profile (UI + backend), template library (UI + backend, 12 local templates seeded).

**Status:** Done — All deliverables for this iteration have been implemented.
- Auth: Register, login, email verification, forgot/reset password — Fortify-powered
- Google OAuth: fully functional via `SocialAuthController`
- LinkedIn OAuth: deferred (code scaffolding ready for future activation)
- Profile: name update, avatar upload (max 2MB), password change, account deletion (password confirmation)
- Template Library: 12 templates seeded, all 7 category filter tabs (Alpine.js), full-page preview overlay modal with iframe rendering

### Iteration 2 — Create Resume · Edit Workspace · Download PDF · ATS Score (Weeks 3–4)
**LSD Principle: Build Quality In**

Deliverables: Create resume from template (done), workspace editor with auto-save (pending), PDF export via dompdf (done), ATS score basic keyword match (pending), duplicate + soft-delete resume (done).

**Status:** In Progress — Resume CRUD, PDF export, and duplicate/soft-delete are complete. Auto-save and ATS score basic are pending.

**Lean rule:** Do not begin Iteration 3 (AI + Payment) until auto-save and PDF are both complete and tested. AI features depend on correctly stored resume data.

### Iteration 3 — ATS Scanner AI · Payment · AI Optimize · Generate CV Versions (Weeks 5–6)
**LSD Principle: Eliminate Waste**

Deliverables: AI-powered ATS scanner, Midtrans payment integration (all methods), AI bullet optimizer, generate 3 CV versions in parallel, AI quota system (Free: 5cr, Premium: 50cr), payment confirmation email.

**Lean rule:** Dedicate Day 1 of this iteration to a prompt engineering spike in Google AI Studio — do not write controller code before the prompt output quality is validated.

### Iteration 4 — Admin Panel (Weeks 7–8)
**LSD Principle: See the Whole**

Deliverables: Admin dashboard with live stats, user management CRUD, template catalog management, AI & finance logs with CSV export, Sentry integration, revenue report.

**Lean rule:** Set up Sentry on Day 1 of this iteration. The admin health card needs Sentry data to be meaningful.

### Iteration 5 — Support Tickets · Responsive Audit · UAT · Deploy (Weeks 9–10)
**LSD Principle: Learn & Adapt**

Deliverables: Admin support ticket inbox, responsive audit all 17 screens, UAT with 5 representative users, production deploy (Railway/Forge + HTTPS), GitHub Actions CI/CD live, UptimeRobot + Sentry production.

**Lean rule:** UAT must happen before production deploy, not after. Recruit 5 UAT users on Day 1 of this iteration so sessions can be scheduled for Week 1.

### Iteration 6 — Mock Interview HRD (Weeks 11–12)
**LSD Principle: Create Knowledge**

Deliverables: Interview chat UI (mobile-first, SSE streaming), HRD persona "Bu Sari" with CV context injection, STAR-based feedback report, session history, Premium gating (Free = 1 trial).

**Lean rule:** Dedicate 2 full days to prompt engineering before writing `InterviewController`. Success metric for this iteration: ≥60% of interview questions reference specific content from the user's CV (verified in usability test).

---

## 12. Team Roles & Responsibilities

All roles follow the LSD principle of **domain ownership** — each person owns an end-to-end domain, not a list of pages.

### Lean Master (LM)
- Maintains and prioritises product backlog
- Facilitates iteration planning and daily standups (15 min, async-first)
- Removes blockers — does not resolve them unilaterally
- Owns value stream map and ensures team is not building low-value features
- Plans and runs UAT sessions (Iteration 5)
- Post-launch monitoring and retrospective

### Lean Project Leader 1 — Frontend UI & Design System (LP1)
- Owns: design tokens, base layouts, all shared UI components
- Owns: landing page, workspace editor UI, AI assistant workspace UI, pricing page UI
- Ensures all UI is pixel-accurate to Stitch designs
- Responsible for responsive audit (Iteration 5) across all 17 screens

### Lean Project Leader 2 — Frontend Feature Pages (LP2)
- Owns: auth pages (login, register, forgot password), template library, manage profile page
- Owns: admin pages (user management, AI logs, support inbox, template catalog)
- Owns: help center, support ticket pages
- Wires all frontend pages to their backend endpoints

### Lean Dev Team 1 — Backend Core API & Infra (LDT1)
- Owns: AuthController, SocialAuthController, ProfileController, TemplateController
- Owns: ResumeController (CRUD), ResumePdfController
- Owns: AiService, all AI prompt engineering, QuotaMiddleware
- Owns: Midtrans integration (transaction, webhook, subscription update)
- Owns: production infrastructure (Railway/Forge, CI/CD, queue workers)
- Owns: InterviewService (context builder, prompt, SSE streaming)

### Lean Dev Team 2 — Fullstack Data & Admin (LDT2)
- Owns: auto-save AJAX (PATCH endpoint + JS debounce), AtsScoreService
- Owns: ResumePdfController (alternative: shared with LDT1)
- Owns: all Admin controllers (AdminController, AdminUserController, AdminTemplateCatalogController, AdminAiLogController)
- Owns: QuotaController, AiLogController, ResumeVersionController
- Owns: InterviewController (session management, feedback generation)
- Owns: SupportTicketController, all database migrations and seeders

---

## 13. API Integrations

### Google Gemini API
| Detail | Value |
|---|---|
| Integration | Laravel HTTP Client (`Http::post()`) — no external SDK required |
| Endpoint | `https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent` |
| Model | `gemini-flash-latest` (cost-efficient for quota-limited features) |
| Auth | `GEMINI_API_KEY` in `.env`, passed as query parameter `?key={api_key}` |
| Response format | `generationConfig.response_mime_type = 'application/json'` for structured output |
| Usage | ATS analyze, bullet optimize, generate versions (parallel), interview questions, interview feedback |
| Rate limiting | Set max spend limit on Google AI Studio during development |
| Cost estimation | Free tier available; paid tier ~$0.0001–0.001 per user action depending on token count |

### Midtrans
| Detail | Value |
|---|---|
| SDK | `midtrans/midtrans-php` |
| Integration type | Snap (redirect to hosted payment page in modal) |
| Auth | `MIDTRANS_SERVER_KEY`, `MIDTRANS_CLIENT_KEY` in `.env` |
| Sandbox mode | `MIDTRANS_IS_PRODUCTION=false` for development |
| Payment methods | GoPay, OVO, Credit Card (Visa/Mastercard), Bank Transfer (BCA, BRI, BNI, Mandiri) |
| Webhook endpoint | `POST /payment/callback` — must be publicly accessible (use ngrok for local dev) |
| Webhook verification | SHA-512 signature validation |

### Laravel Socialite — Google
| Detail | Value |
|---|---|
| Driver | `google` |
| Config | `config/services.php` → `google.client_id`, `google.client_secret`, `google.redirect` |
| Scopes | Default (email, profile) |
| Google Console | Create OAuth 2.0 Client ID, add redirect URIs for local and production |

### Laravel Socialite — LinkedIn
| Detail | Value |
|---|---|
| Driver | `linkedin-openid` (not `linkedin`) |
| Config | `config/services.php` → `linkedin-openid.client_id`, `linkedin-openid.client_secret`, `linkedin-openid.redirect` |
| LinkedIn Product | "Sign In with LinkedIn using OpenID Connect" must be approved |

### Sentry
| Detail | Value |
|---|---|
| SDK | `sentry/sentry-laravel` |
| Config | `SENTRY_LARAVEL_DSN` in `.env` |
| Usage | Captures all unhandled exceptions, performance tracing on key routes |

---

## 14. Subscription Plans & Quota

### Free Plan
- AI credits: 5 per month (reset on billing cycle start date)
- Mock Interview: 1 session (lifetime, not monthly)
- All resume creation, editing, PDF download: unlimited
- Template access: full

### Premium Plan (Rp 49.000/month)
- AI credits: 50 per month
- Mock Interview: unlimited sessions
- All Free features included
- Billing: monthly subscription via Midtrans recurring or manual renewal

### AI Credit Costs
| Action | Credits |
|---|---|
| ATS Scanner (AI-powered) | 1 |
| Optimize resume bullets | 1 |
| Generate 3 CV versions | 3 (1 per version, billed upfront) |
| Mock Interview session (per session) | 2 |
| Generate interview feedback report | 1 |

---

## 15. File & Storage Structure

```
storage/
└── app/
    └── public/
        ├── avatars/           # user profile photos
        │   └── {user_id}/
        ├── templates/         # template thumbnails
        │   └── {template_id}.jpg
        └── resumes/           # (future) cached PDF files
            └── {resume_id}/

resources/
└── views/
    ├── layouts/
    │   ├── app.blade.php      # authenticated user layout
    │   ├── guest.blade.php    # public / auth layout
    │   └── admin.blade.php    # admin panel layout
    ├── auth/                  # login, register, reset password
    ├── dashboard/             # user dashboard
    ├── resume/
    │   ├── create.blade.php
    │   ├── edit.blade.php     # workspace editor
    │   ├── show.blade.php     # preview
    │   └── pdf/
    │       └── template-default.blade.php
    ├── templates/             # template library
    ├── ai/                    # AI assistant workspace
    ├── interview/             # mock interview
    ├── profile/               # manage profile
    ├── premium/               # pricing and plans
    ├── help/                  # help center + tickets
    └── admin/
        ├── dashboard.blade.php
        ├── users/
        ├── templates/
        ├── logs/
        ├── monitor.blade.php
        ├── reports/
        └── support/
```

---

## 16. Security Requirements

| Requirement | Implementation |
|---|---|
| CSRF protection | Laravel's `VerifyCsrfToken` middleware on all web routes |
| SQL injection prevention | Eloquent ORM exclusively; no `DB::statement()` with user input |
| XSS prevention | Blade `{{ }}` escaping; `{!! !!}` only for pre-sanitised HTML |
| File upload validation | `mimes:jpg,jpeg,png,webp` + `max:2048` in form request validation |
| Password storage | bcrypt via `Hash::make()` |
| Sensitive data | `.env` in `.gitignore`; production secrets in hosting environment variables |
| Payment security | Midtrans webhook signature verified on every callback |
| Rate limiting | `throttle:5,1` on login route; `throttle:60,1` on API routes |
| Admin access control | `middleware('admin')` checks `users.role = 'admin'` via Gate |
| Object-level authorisation | `ResumePolicy`, `InterviewPolicy` — every resource operation checks ownership |
| HTTPS | Enforced in production; HTTP redirects to HTTPS |
| API keys | Never sent to frontend; Gemini/Midtrans server keys server-side only |

---

## 17. Out of Scope (MVP)

The following features have been explicitly deferred to post-launch:

- **Company Culture Intelligence** — auto-researching company culture (Glassdoor/LinkedIn scraping) to tailor CV tone per company. Requires database of Indonesian companies + external API access. Estimated +4 weeks.
- **Job Match Feed** — showing matched job listings from JobStreet/Glints based on CV content. Requires API partnerships.
- **AI-generated summaries with sentiment analysis** — advanced NLP beyond keyword matching.
- **Slack/Teams notification integration** — sending digest or alerts via external communication tools.
- **Custom enterprise branding** — white-label PDF templates for corporate clients.
- **Mobile app (iOS/Android)** — PWA is acceptable if the responsive web app is optimised.
- **Resume restore from soft-delete** — users cannot restore deleted resumes in MVP.
- **Paid team plans** — single-user accounts only in MVP.
- **LinkedIn profile import** — auto-fill resume from LinkedIn profile data (requires LinkedIn API partnership).
- **Real-time collaboration** — multiple users editing a resume simultaneously.

---

*This requirements document reflects the full scope as of the current project state. It will be updated after each iteration based on UAT feedback and team retrospectives.*

*Last updated: based on conversation context — Resumify project, LSD framework, 12-week timeline.*