## Problem Statement

The clothing manufacturing company lacks a centralized internal system to manage its daily operations. Employee attendance, shift scheduling, stock tracking, and salary processing are handled in an ad-hoc manner, leading to inefficiencies, data gaps, and administrative overhead. Managers have no unified view of attendance, and salary disbursement is a manual process.

## Solution

A Laravel-based internal web application — accessible from both desktop and mobile browsers — that provides four integrated modules:

1. **Employee Management** — profiles, roles (employee / manager / admin), attendance records
2. **Shift Management** — scheduling, clock-in / clock-out, shift tracking per employee
3. **Stock Management** — inventory visibility for raw materials and finished goods
4. **Income Management** — salary processing and payment disbursement

Each role (employee, manager, admin) sees only the features and data appropriate to their permissions.

## User Stories

1. As an employee, I want to clock in and clock out for my shift, so that my attendance is accurately recorded.
2. As an employee, I want to view stock levels, so that I know what materials or products are available.
3. As an employee, I want to view my own profile, so that I can verify my personal details.
4. As a manager, I want to manage (create, edit, delete, assign) shifts, so that the right employees are scheduled at the right times.
5. As a manager, I want to view my own attendance records, so that I can track my presence.
6. As a manager, I want to view my own profile, so that I can verify my personal details.
7. As a visitor, I want to register an account with email verification and OTP, so that only legitimate users gain access.
8. As a user, I want to log in securely and reset my password via forgot-password or OTP, so that I can access the system.
9. As a user, I want to view and edit my own profile, so that my details stay current.
10. As a user, I want a settings page to configure my preferences (e.g. notification preferences, language).
11. As a user, I want to see a dashboard / home page with a summary of relevant information (my attendance, upcoming shifts, stock alerts).
12. As a user, I want to search and filter data across the system (employees, shifts, stock), so that I can quickly find what I need.
13. As a user, I want to receive notifications (in-app and/or email), so that I'm alerted to important events (shift changes, salary processed).
14. As a user, I want to send and receive internal messages, so that I can communicate with colleagues without leaving the system.
15. As an admin, I want an activity log of all user actions, so that I can audit who did what and when.
16. As an admin, I want a device log showing which devices users have logged in from, so that I can monitor for suspicious access.
17. As an admin, I want to manage all shifts, so that the schedule can be maintained centrally.
18. As an admin, I want to view attendance records for any employee, so that I can monitor presence across the company.
19. As an admin, I want to process and disburse salary payments through the application, so that payroll is handled efficiently.
20. As an admin, I want to manage employee profiles (create, update, deactivate), so that the employee roster stays up to date.
21. As an admin, I want to assign roles (employee / manager / admin) to users, so that permissions reflect responsibilities.

## Implementation Decisions

- **Framework**: Laravel (PHP) — as specified by the user, with authentication scaffolding (Laravel Breeze or Jetstream).
- **Database**: MySQL / MariaDB — the default Laravel companion.
- **API-first approach**: All core actions exposed via RESTful JSON API endpoints, consumed by both the web frontend and a future mobile app.
- **Roles & Permissions**: Implemented via Laravel's built-in authorization (Gates / Policies) or Spatie's `laravel-permission` package, with three roles: `employee`, `manager`, `admin`.
- **Authentication & Security**: Laravel Sanctum for API token-based auth. Email verification on registration. OTP support for login and sensitive actions. Forgot-password flow via email.
- **Modules**:
  - `Auth` — registration, login, email verification, OTP, forgot/reset password, logout
  - `Profile & Settings` — editable user profiles, configurable preferences
  - `Dashboard` — role-specific home page with widgets (upcoming shifts, recent attendance, stock alerts)
  - `Search & Filter` — global search with filtered list views across all entities
  - `Employees` — CRUD for profiles, attendance tracking (clock-in / clock-out timestamps per shift)
  - `Shifts` — CRUD for shift definitions, assignment to employees
  - `Stock` — read-only for employee/manager roles, CRUD for admin; covers raw materials and finished goods
  - `Income` — salary records, payment processing (status tracking: pending / paid / failed)
  - `Activity Log` — audit trail of all user actions (who did what, when)
  - `Device Log` — record of devices used per user (IP, user-agent, timestamp)
  - `Messaging` — internal one-to-one or group messages between users
  - `Notifications` — in-app notification center + optional email notifications
- **Schema highlights**:
  - `users` — standard Laravel users table + `role` enum field, `email_verified_at`, `otp_secret`, `settings` (JSON)
  - `password_reset_tokens` — standard Laravel password reset
  - `personal_access_tokens` — Laravel Sanctum tokens
  - `shifts` — `id`, `name`, `start_time`, `end_time`, `created_by`
  - `shift_assignments` — `id`, `user_id`, `shift_id`, `date`, `clock_in`, `clock_out`, `status`
  - `stock_items` — `id`, `name`, `type` (raw_material / finished_good), `quantity`, `unit`, `updated_by`
  - `salaries` — `id`, `user_id`, `amount`, `period_start`, `period_end`, `status`, `paid_at`, `processed_by`
  - `activity_log` — `id`, `user_id`, `action`, `entity_type`, `entity_id`, `description`, `ip_address`, `user_agent`, `created_at`
  - `device_logs` — `id`, `user_id`, `device_name`, `ip_address`, `user_agent`, `last_login_at`
  - `messages` — `id`, `sender_id`, `receiver_id`, `subject`, `body`, `read_at`, `created_at`
  - `notifications` — standard Laravel notifications table

## Testing Decisions

- **Framework**: PHPUnit with Laravel's `TestCase` — HTTP feature tests as the primary seam.
- **What to test**: External behavior only — API request/response contracts, authorization rules (e.g. employee cannot manage shifts), and business logic (e.g. cannot clock in twice for the same shift).
- **What not to test**: Internal query details, private methods, or framework mechanics.
- **Prior art**: Standard Laravel feature test patterns — `actingAs()`, `assertJson()`, and route-based assertions.

## Out of Scope

- Customer-facing storefront or e-commerce functionality.
- Mobile native app (the API will support it, but the app itself is not built here).
- Payroll tax calculations or third-party payroll integrations.
- Procurement / supplier management.

## Further Notes

- The responsive web UI should work on mobile browsers — no native app required for v1.
- The project name is `mis_manufacturing` (Management Information System for Manufacturing).
- Stock levels are view-only for employees and managers; only admin can modify.
- Salary payment status should be trackable (pending → paid) but third-party payment gateway integration is out of scope — admin marks payment as completed manually.
