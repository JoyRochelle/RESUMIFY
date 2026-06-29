# Resumify — Detailed Project Context

## 1. Project Overview & Value Proposition
**Resumify** is an AI-powered CV builder web application built specifically for the Indonesian job market. 

**Core Problem Solved:** Indonesian job seekers spend hours manually editing their CV for each application, get rejected by ATS systems without feedback, and lack affordable mock interview practice. Competitors (Canva Resume, Novoresume, Resume.io) either lack deep Indonesian context, are expensive, or don't integrate CV building and interview practice.

**Key Differentiators:**
1. **Multi-version AI CV Generation**: Generates 3 parallel angles (Leadership, Technical, Ownership) from a single job description.
2. **Deep Indonesian Context**: Supports BUMN, startup, and corporate formats (IPK, photo guidelines, full Bahasa Indonesia).
3. **Mock Interview with Context Injection**: The HRD Chatbot (Bu Sari) actually reads the user's CV content and asks specific questions about their past experiences using the STAR method, not just generic job title questions.

## 2. Goals & Metrics
- **Business**: 500 users in 30 days, 10% conversion to Premium, AI API cost < 30% of premium revenue.
- **Product**: Zero to downloadable CV in < 10 mins. ATS scanner gives actionable feedback. Mock interview questions must reference CV content (≥60%).
- **Technical**: Lighthouse score ≥ 85, page load < 2s, 99.5% uptime, zero critical security vulns.

## 3. Tech Stack & Architecture
- **Framework**: Laravel 12 (PHP 8.3+)
- **Database**: MySQL 8.0
- **Frontend**: Tailwind CSS 3.x, Alpine.js, Laravel Blade
- **AI Integration**: Google Gemini API (`gemini-flash-latest`) via Laravel HTTP Client.
- **Payment Gateway**: Midtrans PHP SDK + Snap.js (GoPay, OVO, Credit Card, Bank Transfer).
- **PDF Generation**: `barryvdh/laravel-dompdf`. *(Constraint: Dompdf doesn't support flexbox/grid. Must use float/tables).*
- **Authentication**: Laravel built-in Auth + Laravel Socialite (Google, LinkedIn OpenID).
- **Real-time**: Server-Sent Events (SSE) for streaming Mock Interview chat.
- **Error Tracking**: Sentry.
- **Design System**: Primary `#4f3b2f` (Deep Brown), Accent `#0F6E56` (Emerald Green). Fonts: Newsreader (serif), Manrope (sans-serif).

## 4. Subscription Plans & Quotas
- **Free Plan**: Unlimited CV creation, PDF downloads, basic ATS score, access to all templates. Includes 5 AI credits/month and **1 Mock Interview trial**.
- **Premium PRO (Rp 49.000/month)**: Adds AI-powered ATS scanner, AI bullet optimizer, AI CV versions generation, and **Unlimited Mock Interviews**. Includes 50 AI credits/month.
- **AI Cost Breakdown**: ATS Scanner (1 cr), Optimize Bullets (1 cr), Generate 3 CVs (3 cr), Mock Interview (2 cr/session), Interview Feedback (1 cr).

## 5. Database Schema Structure
- **`users`**: Standard auth + role (`user`/`admin`), provider for OAuth.
- **`resumes`**: Main CV record (title, job_target, company_target, ats_score).
- **`resume_sections`**: Content parts of CV stored as JSON (personal_info, work_experience, education, skills). Supports auto-save.
- **`resume_versions`**: Snapshots of AI-generated CV variations.
- **`templates`**: Template catalog (category: professional, creative, tech, managerial).
- **`subscriptions`**: Tracks plan types and `ai_credits_remaining`.
- **`transactions`**: Midtrans order records and payment status.
- **`ai_usage_logs`**: Tracks tokens used and cost calculation per AI action.
- **`interview_sessions` & `interview_messages`**: Chat history, overall score, and STAR method feedback report.
- **`support_tickets` & `ticket_replies`**: Help center system.

## 6. Detailed Feature Mechanics
### A. Resume Builder Workspace
- Split-screen UI with a left panel (accordion sections) and right panel (live iframe preview).
- **Auto-save**: Debounced 1.5s AJAX requests (`PATCH /resumes/{resume}/sections/{section}`). Updates the live preview and basic ATS score dynamically.

### B. AI ATS Scanner
- Basic (Iter 2): Keyword intersection matching (No AI).
- Premium (Iter 3): Gemini API compares CV JSON against a pasted Job Description, returning match %, missing keywords, and section-by-section advice.

### C. AI Optimize & Generate Versions
- **Optimize**: Rewrites specific bullets with strong action verbs and quantified results.
- **Generate Versions**: Runs 3 concurrent API calls to Gemini to create 3 completely different CV angles:
  1. **Leadership**: Highlights team leadership, project ownership.
  2. **Technical**: Highlights specific tools, tech, methodologies.
  3. **Ownership**: Highlights startup mindset, autonomy, business impact.

### D. Mock Interview HRD (Bu Sari)
- **Context Injection**: The `InterviewService` injects the candidate's actual CV JSON into the system prompt.
- **Interaction**: AI asks 2-3 follow-up questions per topic. Evaluates answers using the STAR method.
- **Streaming**: Responses are streamed token-by-token to the frontend using SSE (`response()->stream()`).
- **Feedback Report**: Generated when the session ends, giving a 0-100 score and improvement suggestions.

### E. Admin Panel
- Dashboard with charts (Chart.js) showing Daily Active Users vs Daily AI Calls.
- Management of users, templates, AI API costs/logs, finance logs, Sentry error monitoring, and Support Tickets.

## 7. Project Methodology (LSD)
- **Framework**: Lean Software Development (LSD).
- **Timeline**: 12 Weeks, 7 Iterations, 40 User Stories.
- **Team Roles**: 
  - Lean Master (LM) - Backlog & blocking issues.
  - Lean Project Leader 1 (LP1) - UI/UX & Design System.
  - Lean Project Leader 2 (LP2) - Frontend features & Admin UI.
  - Lean Dev Team 1 (LDT1) - Backend Core API, AI Prompts, Midtrans.
  - Lean Dev Team 2 (LDT2) - Auto-save AJAX, Admin Controllers, Support Tickets, DB Migrations.
