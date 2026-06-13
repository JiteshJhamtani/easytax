# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

EasyTax is a **Laravel 12** B2B tax filing platform. Agents (franchisees) submit applications for government tax services on behalf of their clients. Admins manage services, pricing, and payouts. An internal Team portal handles document processing and status updates.

---

## Commands

### Development

```bash
composer run dev        # Starts all services concurrently: PHP server, queue worker, log watcher (pail), and Vite
```

### Building

```bash
npm run build           # Compile frontend assets (Vite + Tailwind)
npm run dev             # Vite HMR dev server only
```

### Testing

```bash
php artisan test --compact                          # Run all tests
php artisan test --compact --filter=TestName        # Run a single test or filter
php artisan make:test --pest SomeFeatureTest        # Create a new Pest feature test
```

### Code Quality

```bash
vendor/bin/pint --dirty --format agent    # Auto-fix style on modified files (run before committing)
vendor/bin/phpstan analyse                # Static analysis (level 2)
```

### Common Artisan

```bash
php artisan route:list --except-vendor     # Inspect routes
php artisan config:show app.name           # Read config values
php artisan optimize:clear                 # Clear all caches
php artisan tinker --execute 'User::count();'   # Run PHP in app context (use single quotes)
```

---

## Architecture

### User Roles & Access

The system has four roles enforced via middleware and `spatie/laravel-permission`:

| Role | Middleware | Route Prefix | Access |
|------|-----------|--------------|--------|
| `ADMIN` / `SUPER_ADMIN` / `SUB-ADMIN` | `admin` | `/admin` | Full platform management |
| `AGENT` | `agent` | `/agent`, `/services` | Submit and track own applications |
| Team (operators) | `team` | `/team` | Process documents, update application status |
| Marketer | `marketer` | `/marketer` | Lead and commission management |

**Sub-admin** is a restricted admin role: sensitive fields (email, mobile, PAN, Aadhaar) are automatically masked via `App\Traits\MasksSensitiveData`. This masking intercepts `getAttributeValue()` and `toArray()` — never runs in console/queue context.

### Controller Namespacing

Controllers are grouped by portal:
- `App\Http\Controllers\Admin\` — admin-facing operations
- `App\Http\Controllers\Agent\` — agent portal
- `App\Http\Controllers\Team\` — internal operator portal
- `App\Http\Controllers\Front\` — public-facing (service catalog, application tracking)
- `App\Http\Controllers\Auth\` — authentication (Breeze-based)

### Application Lifecycle

`Application` (model) represents a service filing submitted by an agent. Key fields:
- `form_data` (JSON) — stores the entire submitted form
- `status` — `ApplicationStatus` enum (submitted → in_progress → completed/rejected/pending)
- `payment_status` — `PaymentStatus` enum
- All documents stored via `spatie/laravel-medialibrary` in the `private` disk

Documents use named media collections defined in `Application::registerMediaCollections()`: `documents`, `admin_uploads`, `itr_acknowledgement`, `computation_sheet`, `moa_document`, `aoa_document`, `final_deliverables`, `balance_sheet`.

Activity is logged automatically via `spatie/laravel-activitylog` on `status` and `payment_status` changes.

### FormEngine

The `App\FormEngine` namespace (`Form`, `Section`, `Field`) drives service application forms dynamically from configuration. Each service has a form schema in `config/service_forms.php` keyed by service `slug`. The `Form` class resolves sections, merges numbered sub-sections (e.g., `director_1_details`), generates Laravel validation rules, and renders via Blade.

To add or modify a service form, edit `config/service_forms.php` — no PHP class changes needed.

### Multi-Tenancy (Domain-Based)

`App\Http\Middleware\SetTenantContext` maps incoming hostnames to tenant names stored in `config('app.tenant')`. Current tenants: `b2b.easytax.live` → `b2b`, `upwest.easytax.live` → `upwest`, default → `default`.

The `RestrictToB2BDomains` and `VerifyB2BSecret` middleware guard B2B API/sync endpoints.

### Frontend Stack

- **Tailwind CSS v3** + **Alpine.js v3** — no React/Vue
- **AdminLTE** (`jeroennoten/laravel-adminlte`) for admin UI — sidebar menus are defined in `config/menu_admin.php` and `config/menu_agent.php`
- **Yajra DataTables** for server-side tables — `data()` methods in controllers return JSON responses
- Assets compiled via **Vite** (`vite.config.js`). The `LoadSidebarMenu` middleware injects the correct menu into views.

### Key Services

- `RazorpayService` / `PhonePeService` — payment gateway integrations
- `PayoutService` — agent commission calculations and payout batching
- `AgentCodeService` — generates unique agent codes
- `GiftEligibilityService` / `GiftPeriodResolver` — gift/incentive logic based on application counts
- `ApplicationLogger` — structured log entries in the `application_logs` table
- `FormValidator` — validates FormEngine submissions

### Global Helper

`setting($key, $default)` (defined in `app/Helpers/helpers.php`) reads from the `settings` table via the `Setting` model. Used for dynamic configuration like currency symbol.

`money($amount)` formats a number using the currency settings.

---

## Key Conventions

- **Middleware registration** is in `bootstrap/app.php` (Laravel 12 — no `Kernel.php`).
- **Pint** must run on any modified PHP file before committing: `vendor/bin/pint --dirty --format agent`.
- **GrumPHP** enforces Pint and Pest on pre-commit.
- **Soft deletes** are used on `User` and `Application`. Deleting a user cascades soft-deletes their applications.
- **Named routes** are required for all URL generation — use `route()`, not hardcoded paths.
- New models need factories; use `php artisan make:model --help` to check options.
- Service providers live in `bootstrap/providers.php`.
