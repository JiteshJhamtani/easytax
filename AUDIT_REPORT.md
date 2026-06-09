# Comprehensive Codebase Audit Report

> Project: EasyTax (workspace root)
> Date: 2026-06-01

---

## 1. Executive Summary

- Scope: Full repository audit covering architecture, security, performance, maintainability, dependencies, testing, and deployment.
- Top critical issues found (must be remediated urgently):
  - Hard-coded secrets and production-like secret defaults in source code (Critical).
  - Public utility scripts in `public/` protected only by static GET tokens (Critical).
  - `PhpSpreadsheet` dependency has a critical CVE (SSRF/RCE) in installed versions (Critical).
  - Multiple Symfony / Laravel relevant CVEs reported for installed versions (High/Critical cluster).
- Automated scan highlights:
  - `composer audit`: 19 advisories including critical and high-severity CVEs (see Dependency Analysis).
  - `npm audit`: no vulnerabilities reported.
  - `vendor/bin/pint --test`: 168 style issues across 298 PHP files (code-style/maintainability concerns).
- Route/middleware review: admin and utility endpoints are routed under web middleware, many protected by authentication and custom middlewares; nevertheless several `public/` scripts bypass app middleware and expose sensitive operations.

---

## 2. Audit Methodology

- Automated tooling used:
  - `composer audit` (dependency vulnerability scan)
  - `npm audit` (JS dependency scan)
  - `vendor/bin/pint --test` (coding standards/style detection)
  - `php artisan route:list --json` (route & middleware inventory)
- Static searches for sensitive patterns (`token`, `secret`, `password`, `shell_exec`, `base64_decode`, `unserialize`, etc.).
- Grep for `TODO`/`FIXME` to catalog technical debt.
- Manual inspection of findings and cross-referencing with code files.

Notes: Tests and deeper dynamic scans require a configured environment (database, .env values, credentials). Where execution of full tests or additional analyzers required such environment, I recorded constraints and included instructions.

---

## 3. Project Inventory

- PHP / Framework:
  - `php` requirement: `^8.2` (see [composer.json](composer.json)).
  - `laravel/framework` targeted: `^12.0` (see [composer.json](composer.json)).
- Major runtime packages (excerpt): `barryvdh/laravel-dompdf`, `jeroennoten/laravel-adminlte`, `maatwebsite/excel`, `razorpay/razorpay`, `spatie/*`, `yajra/laravel-datatables`.
- Dev tooling (excerpt): `laravel/pint`, `pestphp/pest`, `phpunit`, `phpro/grumphp`.
- Frontend tooling: `npm` project present; `npm audit` returned no vulnerabilities.
- Tests: `tests/` directory using Pest + PHPUnit adapters; `v1/tests/` also present.
- Routes: wide set of admin/agent/api routes. Route inventory extracted via `php artisan route:list --json` (full dump available in repository workspace logs).
- Vendor dependencies: `vendor/` present with pinned versions (see `composer.lock`).

---

## 4. Dependency Analysis

Summary from `composer audit` (full output recorded during scan):

- Found 19 security advisories affecting multiple packages.
- Notable and high-impact vulnerabilities detected:
  - `phpoffice/phpspreadsheet` — multiple advisories including a critical CVE (CVE-2026-34084) enabling SSRF/RCE when `IOFactory::load` is passed user-controlled filename. Severity: Critical.
    - Evidence: `composer audit` output lists CVE-2026-34084 as critical.
    - Affected package versions: various ranges up to `1.30.2`/`1.30.3` and many other branch ranges (see `composer audit` output in workspace logs).
  - `laravel/framework` — CRLF injection in default email rule (CVE-2026-48019) affecting many Laravel version ranges up to and including some 12.x releases.
  - Several Symfony component CVEs (http-foundation, http-kernel, mime, routing, yaml) that affect HTTP handling, mailer, and routing normalization (medium → high severity depending on package and version).
  - `phpoffice/phpspreadsheet` additional high/medium issues (DoS, XSS via HTML writer) — high risk where spreadsheet files are parsed.

JS/NPM: `npm audit --json` reported zero vulnerabilities for production & dev packages present in `package.json` (scan results included in workspace logs).

Dependency Risk Notes:
- Direct use of `PhpSpreadsheet` (or transitive use via `maatwebsite/excel`) for parsing untrusted XLSX/XLS files poses immediate risk of SSRF/RCE or CPU exhaustion when processing attacker-controlled files.
- Symfony/Laravel CVEs involving email header/CRLF injection and IP/network utilities may allow header injection or SSRF bypass depending on how code constructs emails and uses the No-Private-Network checks.
- Recommendation priority: treat `PhpSpreadsheet` CVEs and Symfony mail/routing vulnerabilities as urgent.

---

## 5. Security Findings

All findings follow required format: Severity / Category / Affected File(s) / Description / Risk / Evidence / Remediation Priority.

### Finding A — Hard-coded secrets and production-like secret defaults
- Severity: Critical
- Category: Secrets exposure / Configuration
- Affected File(s): [config/b2b.php](config/b2b.php#L1-L40), [public/run-migration.php](public/run-migration.php#L1-L20), [public/upgrade-team-role.php](public/upgrade-team-role.php#L1-L20), [public/upgrade-rbac.php](public/upgrade-rbac.php#L1-L20), [public/upgrade-rbac2.php](public/upgrade-rbac2.php#L1-L20), [public/vle-update.php](public/vle-update.php#L1-L20), [public/sync.php](public/sync.php#L1-L120)
- Description: Several configuration files and public scripts contain hard-coded secrets and tokens. `config/b2b.php` provides a default `sync_secret` value resembling a production secret. Multiple scripts under `public/` gate privileged operations with static GET tokens embedded in source.
- Risk / Impact: Exposure of secrets in source control permits unauthorized access to internal sync endpoints, arbitrary administrative operations, and potential data exfiltration. Static GET tokens are trivial to discover and use, enabling remote unauthorized actions (e.g., migrations, role upgrades, data sync).
- Evidence:
  - `config/b2b.php`: `'sync_secret' => env('B2B_SYNC_SECRET', 'EasyTax_Super_Secret_Key_2026!')` — default production-like secret in source.
  - `public/run-migration.php`: uses `$secretToken = 'easytax_secure_2026'` and checks `$_GET['token']` (line ~6).
  - `public/upgrade-team-role.php` and `public/upgrade-rbac2.php`: perform a token check against `'superadmin123'` (line ~4).
  - `public/sync.php`: uses `$secretKey = 'easytax_admin_2026'` and checks `$_GET['key']` before proceeding.
- Remediation Priority: Immediate — rotate secrets, remove defaults, and treat all scripts as compromised until removed or locked down.

---

### Finding B — Public utility scripts protected only by GET tokens
- Severity: Critical
- Category: Authentication / Access control
- Affected File(s): [public/run-migration.php](public/run-migration.php#L1-L30), [public/upgrade-team-role.php](public/upgrade-team-role.php#L1-L15), [public/upgrade-rbac2.php](public/upgrade-rbac2.php#L1-L15), [public/vle-update.php](public/vle-update.php#L1-L15), [public/sync.php](public/sync.php#L1-L120)
- Description: Several scripts in `public/` expose administrative operations and use static GET parameter tokens for access control rather than application authentication, CSRF protections, or network restrictions.
- Risk / Impact: Attackers can enumerate or discover these endpoints and tokens, leading to unauthorized execution of migrations, role upgrades, data sync, and potentially destructive operations. Tokens in query strings are often leaked via logs or referrers.
- Evidence:
  - `public/upgrade-team-role.php` checks `if (!isset($_GET['token']) || $_GET['token'] !== 'superadmin123')` and dies otherwise.
  - `public/run-migration.php` gate with `$secretToken = 'easytax_secure_2026'`.
  - `public/sync.php` checks for `$_GET['key']` against `'easytax_admin_2026'` and then proceeds.
- Remediation Priority: Immediate — treat these endpoints as compromised exposure points.

---

### Finding C — `PhpSpreadsheet` critical SSRF/RCE (via `IOFactory::load`)
- Severity: Critical
- Category: Dependency vulnerability / RCE / SSRF
- Affected File(s): Dependency: `phpoffice/phpspreadsheet` (used directly or via `maatwebsite/excel`); relevant code areas where spreadsheets are parsed (search for usage of `IOFactory::load` or imports) — e.g., [app/Services/ApplicationDocumentService.php](app/Services/ApplicationDocumentService.php#L1-L200) (candidate), [app/Exports/ApplicationsExport.php](app/Exports/ApplicationsExport.php#L1-L200)
- Description: `composer audit` reports CVE-2026-34084 (critical) for `phpoffice/phpspreadsheet`, allowing SSRF or RCE when `IOFactory::load` processes user-controlled filenames or remote resources.
- Risk / Impact: If user-uploaded spreadsheet files or attacker-controlled file paths are passed to spreadsheet readers, an attacker may trigger remote file reads, code execution vectors (via deserialization or remote resource inclusion), or server-side request forgery. This may lead to complete server compromise.
- Evidence:
  - `composer audit` output lists CVE-2026-34084 with critical severity (audit logs in workspace).
  - `composer.json` includes `maatwebsite/excel` and `smalot/pdfparser` — application uses file parsing features.
- Remediation Priority: Immediate — treat code paths that parse spreadsheet files as high-risk until patched versions are used or inputs are strictly validated.

---

### Finding D — Multiple Symfony/Laravel CVEs impacting mail/routing/http handling
- Severity: High
- Category: Framework / Dependency CVEs
- Affected File(s): Framework-level — impacts `laravel/framework` and various `symfony/*` components used by Laravel; application areas that construct email headers or perform URL generation.
- Description: `composer audit` lists multiple advisories including CRLF injection in Laravel email rules (CVE-2026-48019), mailer/mime header injection (CVE-2026-45067, CVE-2026-45068), and routing/IpUtils and YAML parser issues. These affect how email addresses and routes are generated and validated.
- Risk / Impact: Header injection can lead to email injection, phishing or command injection when invoking sendmail transports; routing and IP utilities CVEs can allow SSRF bypasses under specific usage patterns.
- Evidence:
  - `composer audit` output includes Laravel and Symfony advisories (see full `composer audit` log in workspace).
- Remediation Priority: High — upgrade vulnerable components and review email construction points.

---

### Finding E — Potential file-uploads and deserialization risks
- Severity: High
- Category: File upload / Deserialization / Input handling
- Affected File(s): Controllers handling document uploads: `app/Http/Controllers/Admin/ApplicationController.php` (uploadDocument), `App\Http\Controllers\Agent\ApplicationController.php`, `app/Services/ApplicationDocumentService.php`.
- Description: The application accepts uploads for application documents. File-processing flows that feed uploaded files into third-party parsers (PDF/XLSX) increase attack surface for SSRF, RCE and DoS (observed by presence of `smalot/pdfparser`, `maatwebsite/excel`).
- Risk / Impact: Malformed or crafted files may exploit parser vulnerabilities (e.g., PhpSpreadsheet CVEs) or trigger high CPU/memory usage. If any uploaded content is subsequently executed/parsed insecurely, RCE is possible.
- Evidence:
  - Presence of routes for upload endpoints (e.g., `admin.applications.uploadDocument`).
  - `composer audit` shows spreadsheet and YAML-related vulnerabilities.
- Remediation Priority: High — restrict upload types, scanning, validation, and parsing contexts.

---

### Finding F — Potential CSRF / HEAD bypass of CSRF authorization
- Severity: Medium
- Category: Framework / Request method handling
- Affected File(s): Global middleware + routes handling stateful operations; symfony/http-kernel advisory CVE-2026-45075 indicates HEAD bypass possibility
- Description: Some CVEs indicate HEAD requests can bypass filters relying solely on allowed methods. Where custom authorization attributes or CSRF checks rely on method assumptions, HEAD could be abused.
- Risk / Impact: Attackers may craft HEAD requests to bypass certain method checks leading to unintended access control bypass.
- Evidence:
  - CVE-2026-45075 in `composer audit` output.
  - Application uses method-based routes and attribute-based checks in some controllers.
- Remediation Priority: Medium — validate server-side request handling and ensure method checks are robust.

---

### Finding G — Sensitive config defaults & environment misconfigurations
- Severity: High
- Category: Configuration & Deployment
- Affected File(s): `config/b2b.php`, `config/services.php` (references to env vars), `public/*` scripts.
- Description: Several configs use `env(..., 'default')` where defaults are present in code (sensitive defaults). This increases risk when `.env` is absent or misconfigured.
- Risk / Impact: Misleading secure defaults in code may be used in production or when .env is missing, causing exposures.
- Evidence:
  - `config/b2b.php` default secret present. See earlier detection.
- Remediation Priority: High.

---

### Finding H — Use of insecure or deprecated PHP functions and dangerous patterns (search results)
- Severity: Medium
- Category: Code quality / Security patterns
- Affected File(s): Multiple — results found for patterns like `unserialize`, `eval`, `exec`, but mostly in vendor or tests.
- Description: Grep for potentially dangerous functions showed hits for `base64_decode`, `unserialize`, and shell exec patterns in the codebase (some may be in vendor/test fixtures). Any use in application code should be audited.
- Risk / Impact: Deserialization and command execution vectors can lead to RCE when coupled with unsafe inputs.
- Evidence:
  - Grep search results for patterns across the repo; many occurrences are vendor or tests but some may be in `public/sync.php` lines where password fields are copied and legacy code is present.
- Remediation Priority: Medium.

---

### Finding I — Code style, maintainability and linting issues (Pint results)
- Severity: Low (functional), Medium (maintainability)
- Category: Code quality
- Affected File(s): ~298 files; 168 style issues reported by `vendor/bin/pint --test` (summary available in workspace logs). Examples include many controllers, models, migrations, config files, and public scripts.
- Description: The codebase contains many formatting and style violations that impede readability and increase technical debt (e.g., trailing whitespace, ordered_imports, braces positions, array indentation, etc.).
- Risk / Impact: While not directly security-critical, accumulated style issues hamper maintainability and increase the chance of latent bugs. Several migration files and public scripts are flagged.
- Evidence:
  - `vendor/bin/pint --test` output: "298 files, 168 style issues" with a list of affected files (see logs).
- Remediation Priority: Medium.

---

## 6. Architecture Review

- Observations:
  - Monolithic Laravel application with admin, agent, and front sections placed in same codebase.
  - `v1/` parallel copy exists — duplicated codebase risk (increases maintenance surface). Consider clarifying purpose of `v1/`.
  - Many features rely on server-side file parsing and exports (`maatwebsite/excel`, `dompdf`, `smalot/pdfparser`) — centralized parsing increases blast radius for file-based attacks.
  - Numerous public scripts in `public/` (migration, sync, upgrades) act as maintenance endpoints outside Laravel middleware.

- Concerns:
  - The `v1/` duplicate increases technical debt; ensure only one maintained line.
  - Public scripts are anti-pattern vs. Laravel console commands or authenticated admin routes.

- Severity: Medium

---

## 7. Performance Review

- Observations & potential bottlenecks:
  - Use of heavy document parsing libraries (`PhpSpreadsheet`, `dompdf`, `maatwebsite/excel`) for on-request generation/ingestion may block PHP workers and increase latency.
  - No explicit asynchronous job queue review performed yet; check whether heavy tasks are queued (`php artisan queue:work`) or run synchronously.
  - DB structure: migrations large and many; potential N+1 risk in controllers returning related models (needs code-level query tracing).

- Evidence:
  - Migrations and models present under `database/migrations` and `app/Models`.
  - Controller methods such as `ApplicationController@index` and export endpoints exist — candidate for N+1.

- Remediation Priority: Medium (investigate slow endpoints and profile with real load).

---

## 8. Code Quality Review

- Findings:
  - Formatting/style issues (Pint findings) across many files (see Section 5 Finding I).
  - Presence of many `TODO`/`FIXME` markers (mostly vendor and public assets) — indicate outstanding work but not immediate security risk.
  - Duplicate code in `v1/` raises maintenance burden.

- Severity: Medium

---

## 9. Testing Review

- Tooling:
  - `Pest Testing Framework 4.4.1` and `PHPUnit 12.5.12` present (`vendor/bin/pest --version`, `vendor/bin/phpunit --version`).
- Test coverage and execution:
  - I did not execute the full test suite because the runtime environment for tests is not configured (no `.env` provided to the runner, DB configuration and credentials are required). Running tests without proper env would fail and possibly modify state.
  - Unit tests directory present; many Feature tests likely require DB and services.
- Observations:
  - Several security-related tests exist (e.g., `tests/Feature/SecurityAuditTest.php`). This is positive; however, coverage completeness unknown until tests are executed with working `.env`.
- Action needed to run tests in CI:
  - Provide a test `.env` using SQLite in-memory or a disposable DB and ensure `MAIL`, `QUEUE`, and external service stubs are configured.

- Remediation Priority: High to ensure CI runs tests and coverage continually.

---

## 10. Configuration & Deployment Review

- Findings:
  - `composer.json` scripts assume migrations and build steps during `setup` which can be dangerous in automated contexts (runs `php artisan migrate --force`).
  - Sensitive deployment scripts in `public/` bypass standard Laravel auth and should not be web-exposed.
  - The app uses `config()` with `env(..., default)` — defaults containing secrets are present in code.

- Severity: High

---

## 11. Technical Debt Review

- `v1/` duplication — medium technical debt.
- Numerous `TODO`/`FIXME` comments in vendor and build files — vendor TODOs lower priority, but app-level TODOs should be triaged.
- Pint style violations across many files — technical debt and maintainability concern.

---

## 12. Severity Matrix

- Critical:
  - Hard-coded secrets in source (`public/*`, `config/b2b.php`).
  - Public utility scripts protected only by GET tokens.
  - `PhpSpreadsheet` critical SSRF/RCE (CVE-2026-34084) in installed versions.
- High:
  - Symfony / Laravel advisories affecting mail, routing, and HTTP handling.
  - File upload parsing risks combined with vulnerable parsers.
  - Sensitive configuration defaults.
- Medium:
  - CSRF / HEAD method bypass concerns, deserialization patterns, style/maintainability issues.
- Low:
  - Vendor TODOs and non-functional style issues (Pint) that do not immediately break security but increase maintenance cost.

---

## 13. Prioritized Remediation Roadmap (High-level)

1. Immediate (0–7 days):
   - Remove hard-coded secrets; rotate secrets; take public scripts offline or restrict access immediately.
   - Patch or block usage of vulnerable `PhpSpreadsheet` versions and any path that parses untrusted spreadsheets; mitigate parsing by blocking untrusted uploads.
   - Block or restrict `public/*` maintenance endpoints behind firewall/ACL or remove them.
2. Short-term (1–4 weeks):
   - Upgrade Laravel/Symfony components to patched versions addressing listed CVEs.
   - Audit all file-upload flows and add validation, scanning, and sandboxed processing (e.g., worker queue and limited resources).
   - Enforce secure config patterns: no sensitive defaults in code; require `.env` values.
3. Medium-term (1–3 months):
   - Add CI jobs to run `composer audit`, `npm audit`, `vendor/bin/pint --test`, and `vendor/bin/pest` with a reproducible test env.
   - Refactor public scripts into Laravel console commands or guarded admin routes.
   - Address Pint findings and unify coding standards.
4. Long-term (3+ months):
   - Consider decomposing services that process untrusted files into isolated services (microservice or sandboxed worker).
   - Evaluate removal or de-duplication of `v1/` directory.

---

## 14. Quick Wins

- Remove or restrict `public/` scripts immediately (take offline or move to non-web-exposed maintenance area).
- Replace default secrets in `config/` that bake production secrets into source.
- Pin `phpoffice/phpspreadsheet` to a patched release or disable spreadsheet parsing until patched.
- Run `vendor/bin/pint --test` fixes incrementally or run `vendor/bin/pint --format` in a controlled branch to tidy code style.

---

## 15. Long-Term Recommendations

- Add automated vulnerability scanning to CI (`composer audit`, `npm audit`, Dependabot or similar).
- Harden file upload handling (antivirus, mime-type checks, sandboxed parsing, resource limits).
- Move maintenance scripts into internal-only execution contexts (console commands + role-based access) and remove web-exposed tokens.
- Establish a release and patching policy for dependencies; track security advisories.
- Standardize environment configuration to avoid accidental use of code defaults.

---

## Appendix A — Tool Outputs Reference
- `composer audit` full output saved to workspace logs (scan run at 2026-06-08). Key CVEs cited include CVE-2026-34084 (PhpSpreadsheet), CVE-2026-48019 (Laravel CRLF email rule), and several Symfony component CVEs. See `composer audit` output captured in session logs.
- `npm audit --json` reported zero vulnerabilities.
- `vendor/bin/pint --test` result: `298 files, 168 style issues` (compact list available in workspace logs).
- `php artisan route:list --json` output captured and used to verify route protection and middleware usage.

---

## Appendix B — Notable Evidence Snippets (selected)
- `config/b2b.php` default secret:
  - [config/b2b.php](config/b2b.php#L1-L40)
- `public/run-migration.php` token logic:
  - [public/run-migration.php](public/run-migration.php#L1-L20)
- `public/upgrade-team-role.php` token logic:
  - [public/upgrade-team-role.php](public/upgrade-team-role.php#L1-L10)
- `composer audit` highlights in logs: search for `CVE-2026-34084`, `CVE-2026-48019`, `phpoffice`.

---

