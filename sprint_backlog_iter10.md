# Sprint Backlog Iteration 10: Architecture Ownership, Authorization, and View Structure Cleanup

Product: Resumify  
Source review: `technical-review-report.md`  
Sprint type: Structural architecture cleanup and security hardening  
Sprint goal: Mengurangi structural debt setelah foundation Iteration 9 stabil, terutama ownership folder, authorization, mass-assignment, query performance, security headers, dan Blade decomposition.

## Priority Scale

| Level | Meaning | Handling |
|---|---|---|
| P1 - Required | Structural/security hardening yang wajib dilakukan setelah Iteration 9 stabil | Masuk sprint utama |
| P2 - Required | Required, tetapi dapat dikerjakan setelah ownership/security foundation selesai | Masuk sprint bila P1 aman |

## Story Point Summary

Story point scale: Fibonacci (`1, 2, 3, 5, 8, 13`). Estimasi mempertimbangkan kompleksitas teknis, jumlah boundary terdampak, risiko regresi, dan kebutuhan test.

| Task | Priority | Story points |
|---|---:|---:|
| Task 1: Split controller namespaces by ownership | P1 | 5 |
| Task 2: Move user resume views into user-owned folder | P1 | 3 |
| Task 3: Promote shared Blade components | P1 | 5 |
| Task 4: Consolidate ownership authorization | P1 | 5 |
| Task 5: Harden mass-assignment boundaries | P1 | 5 |
| Task 6: Optimize query ownership | P2 | 5 |
| Task 7: Add production security header middleware | P2 | 3 |
| Task 8: Start Blade decomposition | P2 | 8 |
| **Total** |  | **39** |

## Sprint Scope

In scope:

- Controller namespace separation by ownership.
- View and component ownership cleanup.
- Policy/FormRequest authorization consolidation.
- Mass-assignment hardening.
- Query ownership/performance cleanup.
- Production security headers.
- First pass decomposition of largest Blade pages.

Out of scope:

- Reworking quota/API foundation from Iteration 9 unless bugs are discovered.
- Full UI redesign.
- Complete migration to a domain-module architecture.
- Large database schema redesign.

## Definition of Done

- [ ] Controller and view ownership is clearer than before.
- [ ] Shared components used by admin/landing are no longer under `components/user`.
- [ ] Ownership authorization is represented through policies/FormRequests where applicable.
- [ ] User-facing request payloads cannot mass-assign privilege, quota, owner, status, score, or ordering fields.
- [ ] Performance-sensitive read logic has a query/read-model owner.
- [ ] Security headers are verified by tests.
- [ ] `php artisan test` passes.
- [ ] `npm run build` passes if Blade/JS decomposition touches the asset pipeline.

## Dependency Graph

```text
Iteration 9 foundation
    -> Task 1: Controller namespace ownership
    -> Task 2: Resume view ownership
    -> Task 3: Shared component ownership
    -> Task 4: Policy ownership
    -> Task 5: Mass-assignment hardening
    -> Task 6: Query ownership cleanup
    -> Task 7: Security headers
    -> Task 8: Blade decomposition
```

## Phase 1: Folder and View Ownership

### Task 1: Split controller namespaces by ownership

Priority: P1 - Required  
Story points: 5  
Estimated scope: M  
Dependencies: Iteration 9 stable

Description:
Move controller classes out of the flat root namespace into ownership-based namespaces. Start with lower-risk controllers so route compatibility can be proven before moving large AI/resume/interview controllers.

Acceptance criteria:

- [ ] Controller namespace targets exist for `Public`, `Auth`, `Billing`, `Support`, `User`, `User/Resume`, `User/Ai`, `User/Interview`, and `Admin`.
- [ ] `LandingPageController` is moved under `Public`.
- [ ] `SocialAuthController` is moved under `Auth`.
- [ ] `PaymentController` and `SubscriptionController` are moved under `Billing`.
- [ ] Route names remain unchanged.
- [ ] `php artisan route:list` does not fail.
- [ ] Related public/auth/billing feature tests pass.

Verification:

- [ ] Run `php artisan route:list`.
- [ ] Run auth/social login related tests where available.
- [ ] Run payment gateway tests.
- [ ] Manual smoke: home, templates, pricing, social redirect route, payment create route.

Files likely touched:

- `app/Http/Controllers/*`
- `routes/web.php`
- `tests/Feature/AuthTest.php`
- `tests/Feature/PaymentGatewayTest.php`

### Task 2: Move user resume views into user-owned folder

Priority: P1 - Required  
Story points: 3  
Estimated scope: S  
Dependencies: Task 1 recommended

Description:
Move top-level resume views into the user-owned view tree because these pages extend `layouts.user.app` and belong to the user resume module.

Acceptance criteria:

- [ ] `resources/views/resumes/*` is moved to `resources/views/user/resume/*`.
- [ ] Controller view references are updated.
- [ ] All moved views still extend `layouts.user.app`.
- [ ] No broken view path remains for resume create/show/edit/index.
- [ ] Route names and URLs remain unchanged.

Verification:

- [ ] Run resume feature tests.
- [ ] Manual smoke: resume create, show, edit, index/redirect behavior.

Files likely touched:

- `resources/views/resumes/*`
- `resources/views/user/resume/*`
- `app/Http/Controllers/ResumeController.php`
- `tests/Feature/ResumeTest.php`

### Task 3: Promote shared Blade components out of `components/user`

Priority: P1 - Required  
Story points: 5  
Estimated scope: M  
Dependencies: None

Description:
Move components that are used across admin, landing, and user surfaces out of the user namespace so component ownership matches actual usage.

Acceptance criteria:

- [ ] `form-input` is available under a shared namespace such as `x-ui.form-input`.
- [ ] Admin template/settings pages no longer use `x-user.form-input`.
- [ ] `pricing-card` is available under a shared namespace such as `x-product.pricing-card` or `x-ui.pricing-card`.
- [ ] Landing pricing page no longer uses `x-user.pricing-card`.
- [ ] Existing user pages render with the same visual behavior after component alias/update.

Verification:

- [ ] Run admin template/settings tests.
- [ ] Run pricing or landing page tests.
- [ ] Run responsive user/admin page tests if available.
- [ ] Manual smoke: admin template create/edit, admin settings, landing pricing, user upgrade quota.

Files likely touched:

- `resources/views/components/user/form-input.blade.php`
- `resources/views/components/user/pricing-card.blade.php`
- `resources/views/components/ui/*`
- `resources/views/components/product/*`
- `resources/views/admin/*`
- `resources/views/landing_page/pricing.blade.php`
- `resources/views/user/upgrade-quota.blade.php`

## Checkpoint 1: Ownership Structure Gate

- [ ] Tasks 1-3 complete.
- [ ] Route names are backward compatible.
- [ ] Admin and landing no longer depend on user-only shared components.
- [ ] `php artisan route:list` succeeds.
- [ ] Relevant feature tests pass.

## Phase 2: Authorization and Model Hardening

### Task 4: Consolidate ownership authorization with policies

Priority: P1 - Required  
Story points: 5  
Estimated scope: M  
Dependencies: Task 1 recommended

Description:
Move repeated manual ownership checks into policies and FormRequest authorization so access rules are centralized and harder to forget.

Acceptance criteria:

- [ ] `InterviewSessionPolicy` exists and protects show, message, stream, feedback, and end-session flows.
- [ ] `AtsScanPolicy` exists and protects scan history show/delete flows.
- [ ] `SupportTicketPolicy` exists and protects user ticket access.
- [ ] `NotificationPolicy` exists or equivalent policy boundary protects notification read actions.
- [ ] Controllers no longer repeat `model->user_id !== auth()->id()` where a policy can represent the same rule.
- [ ] Cross-user access remains forbidden for every migrated aggregate.

Verification:

- [ ] Add or update ownership regression tests for interview sessions.
- [ ] Add or update ownership regression tests for ATS scans.
- [ ] Add or update ownership regression tests for support tickets.
- [ ] Add or update ownership regression tests for notifications.
- [ ] Run `php artisan test --filter=Ownership` if grouped tests exist, otherwise run related feature tests.

Files likely touched:

- `app/Policies/*`
- `app/Http/Controllers/InterviewController.php`
- `app/Http/Controllers/AtsController.php`
- `app/Http/Controllers/HelpController.php`
- `app/Http/Controllers/NotificationController.php`
- `tests/Feature/*`

### Task 5: Harden mass-assignment boundaries

Priority: P1 - Required  
Story points: 5  
Estimated scope: M  
Dependencies: None

Description:
Remove business-control fields from broad mass assignment and keep trusted mutations explicit through authorized actions or `forceFill()` inside audited boundaries.

Acceptance criteria:

- [ ] `User::$fillable` no longer includes `role`, `is_suspended`, `ai_quota_used`, or `ai_quota_reset_at`.
- [ ] CV user-facing mass assignment cannot write owner id, status, score, or other internal fields.
- [ ] CV section user-facing mass assignment cannot write owner link/order/internal save metadata unless through trusted actions.
- [ ] Admin plan override, admin suspension, admin credit adjustment, and payment upgrade still work.
- [ ] User-facing request payload cannot change role/quota/status.

Verification:

- [ ] Add mass-assignment regression tests for user privilege/quota fields.
- [ ] Add mass-assignment regression tests for CV/CV section internal fields where relevant.
- [ ] Run admin user management tests.
- [ ] Run payment gateway tests.
- [ ] Run resume tests.

Files likely touched:

- `app/Models/User.php`
- `app/Models/Cv.php`
- `app/Models/CvSection.php`
- `app/Actions/Admin/*`
- `app/Actions/MidtransWebhookHandler.php`
- `tests/Feature/*`
- `tests/Unit/*`

## Checkpoint 2: Security Ownership Gate

- [ ] Tasks 4-5 complete.
- [ ] Policy coverage exists for migrated ownership checks.
- [ ] Mass-assignment regression tests pass.
- [ ] Admin/payment trusted flows still pass.
- [ ] `php artisan test` passes or failures are documented with owner/action.

## Phase 3: Query Ownership, Headers, and Blade Decomposition

### Task 6: Optimize interview trend and admin reporting query ownership

Priority: P2 - Required  
Story points: 5  
Estimated scope: M  
Dependencies: Tasks 1 and 4 recommended

Description:
Move reporting/read-model logic out of controllers and prevent per-row query behavior for interview trend calculation.

Acceptance criteria:

- [ ] Interview trend calculation lives in a query object/read model.
- [ ] Trend calculation avoids N+1 queries per visible session.
- [ ] Admin report stats live in a query object/read model instead of controller private methods.
- [ ] Controller code becomes thinner without changing view output.

Verification:

- [ ] Run interview history tests.
- [ ] Run admin report tests.
- [ ] Add query-count guard if the project has query-count infrastructure.
- [ ] Manual smoke: interview history and admin reports.

Files likely touched:

- `app/Http/Controllers/InterviewController.php`
- `app/Http/Controllers/Admin/AdminReportController.php`
- `app/Queries/*` or `app/Domain/*/Queries/*`
- `tests/Feature/InterviewSessionHistoryTest.php`
- `tests/Feature/AdminReportTest.php`

### Task 7: Add production security header middleware

Priority: P2 - Required  
Story points: 3  
Estimated scope: S  
Dependencies: None

Description:
Add a centralized middleware for baseline browser security headers and document production cookie requirements.

**Deployment Note**: For production environments, `SESSION_SECURE_COOKIE=true` MUST be set in the `.env` file to ensure cookies are sent securely.

Acceptance criteria:

- [x] Middleware adds baseline headers: `X-Frame-Options`, `X-Content-Type-Options`, `Referrer-Policy`, and an initial CSP.
- [x] HSTS is enabled only in production or when the request is secure.
- [x] Local/dev behavior is not blocked by an overly strict CSP.
- [x] Deployment requirement for `SESSION_SECURE_COOKIE=true` is documented in the sprint notes or env checklist.

Verification:

- [x] Add feature test for header presence.
- [x] Run middleware/header tests.
- [x] Manual browser/devtools check in local environment.

Files likely touched:

- `app/Http/Middleware/*`
- `bootstrap/app.php`
- `config/session.php` or deployment documentation
- `tests/Feature/*`

### Task 8: Start Blade decomposition for largest pages

Priority: P2 - Required  
Story points: 8  
Estimated scope: M  
Dependencies: Tasks 2 and 3 recommended

Description:
Reduce the largest Blade files by extracting stable partials/components and moving large JavaScript into feature-specific asset modules where safe.

Acceptance criteria:

- [ ] `resources/views/user/manuscript.blade.php` becomes a page shell plus focused partials/components.
- [ ] `resources/views/user/ai-assistant.blade.php` becomes a page shell plus focused partials/components.
- [ ] Extracted partials have clear ownership, for example `user/resume/editor/*` and `user/ai/ats/*`.
- [ ] No new inline API response parsing is introduced.
- [ ] Existing UI behavior remains the same.
- [ ] If JavaScript moves to `resources/js/features/*`, Vite build succeeds.

Verification:

- [ ] Run existing UI/feature tests for resume editor and ATS analyzer.
- [ ] Run `npm run build` if asset files are touched.
- [ ] Manual smoke: resume editor autosave, preview, download, AI refine, CV versions, ATS analyzer input/results/history.

Files likely touched:

- `resources/views/user/manuscript.blade.php`
- `resources/views/user/ai-assistant.blade.php`
- `resources/views/user/resume/editor/*`
- `resources/views/user/ai/ats/*`
- `resources/js/features/*`

## Checkpoint 3: Complete Gate

- [ ] Tasks 6-8 complete or explicitly deferred with reason.
- [ ] Query ownership improvements are covered by tests.
- [ ] Security headers are visible in response tests.
- [ ] Largest Blade pages are smaller and easier to review.
- [ ] `php artisan test` passes.
- [ ] `npm run build` passes if asset files were touched.

## Parallelization Opportunities

- Task 3 can run in parallel with Task 1 if component rename compatibility is coordinated.
- Task 5 can run in parallel with folder/view moves because it touches model/action boundaries.
- Task 7 can run independently.
- Task 8 should wait until component ownership decisions in Task 3 are stable.

## Risks and Mitigations

| Risk | Impact | Mitigation |
|---|---|---|
| Namespace moves break route model binding or route names | High | Move low-risk controllers first and run `php artisan route:list` after each slice |
| Component promotion changes visual output unexpectedly | Medium | Keep markup/classes identical during the move; rename ownership only |
| Policy migration changes authorization behavior | High | Add cross-user regression tests before replacing manual checks |
| Mass-assignment hardening breaks trusted admin/payment flows | High | Update trusted actions with explicit `forceFill()` and run admin/payment tests |
| Blade decomposition causes JS regressions | Medium | Extract markup first, move JS only after smoke tests are stable |

## Sprint Review Checklist

- [ ] Struktur controller dan view lebih jelas berdasarkan ownership.
- [ ] Shared components tidak lagi berada di namespace user bila dipakai admin/landing.
- [ ] Authorization ownership pindah ke policy/FormRequest.
- [ ] Mass-assignment hardening selesai.
- [ ] `php artisan test` pass.
- [ ] `npm run build` pass bila JS/Blade decomposition menyentuh asset pipeline.
