# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a **Laravel 9** Employee Attendance System (Absensi Karyawan) with QR code-based check-in/check-out functionality. The application supports three user roles:
- **Admin** (role_id: 1) - Full access
- **Operator** (role_id: 2) - Dashboard access
- **User/Employee** (role_id: 3) - Check-in/out functionality

## Tech Stack

- **Framework**: Laravel 9 (PHP ^8.0.2)
- **Frontend**: Blade templates with Livewire components
- **Tables**: Livewire PowerGrid for datatables with Excel/CSV export
- **Authentication**: Laravel Sanctum for API, session-based for web
- **QR Codes**: simplesoftwareio/simple-qrcode
- **PDF**: barryvdh/laravel-dompdf
- **Database**: MySQL (configured for Asia/Jakarta timezone)
- **Assets**: Laravel Mix (webpack.mix.js)

## Common Commands

### Setup
```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed  # Creates admin user: admin@gmail.com / password
```

### Development
```bash
php artisan serve                    # Start dev server
npm run dev                          # Compile assets (development)
npm run watch                        # Watch for changes
```

### Testing
```bash
php artisan test                     # Run all tests
php artisan test --filter TestName   # Run single test
php artisan test --testsuite=Unit    # Run only unit tests
php artisan test --testsuite=Feature # Run only feature tests
```

### Database
```bash
php artisan migrate:fresh --seed     # Reset DB with seeders
php artisan db:seed --class=RoleSeeder
```

## Architecture

### Directory Structure
```
app/
├── Http/
│   ├── Controllers/           # MVC Controllers
│   ├── Livewire/              # Livewire components (Tables & Forms)
│   ├── Middleware/
│   │   └── EnsureUserHasRole.php   # Role-based route protection
│   ├── Requests/              # Form Request validation
│   └── Traits/                # Reusable traits
├── Models/
│   ├── User.php               # Role constants: ADMIN_ROLE_ID=1, OPERATOR_ROLE_ID=2, USER_ROLE_ID=3
│   ├── Attendance.php         # Attendance schedules with time windows
│   ├── Presence.php           # Employee check-in/out records
│   ├── Permission.php         # Leave/permission requests
│   ├── Position.php           # Job positions
│   ├── Role.php               # User roles
│   └── Holiday.php            # Company holidays
database/
├── migrations/                # Standard Laravel migrations
└── seeders/
    ├── RoleSeeder.php         # Seeds: admin, operator, user
    ├── PositionSeeder.php     # Seeds: Operator, Staff positions
    └── DatabaseSeeder.php     # Creates default admin user
```

### Key Architectural Patterns

**Role-Based Access Control**
- Routes protected via `role:admin,operator` or `role:user` middleware
- The `EnsureUserHasRole` middleware checks `role_id` against Role model
- Users are redirected to appropriate dashboards on unauthorized access

**Livewire Tables with PowerGrid**
- All CRUD index pages use Livewire PowerGrid components in `app/Http/Livewire/`
- Tables support: searching, sorting, filtering, bulk actions, Excel/CSV export
- Naming: `{Model}Table.php` for list views, `{Model}CreateForm.php`/`{Model}EditForm.php` for forms
- Example: `EmployeeTable.php` handles employee listing with join to roles and positions

**Attendance Time Windows**
- `Attendance` model has `start_time`, `batas_start_time` (deadline), `end_time`, `batas_end_time`
- The `data` accessor computes `is_start` and `is_end` to determine if check-in/out is allowed
- Attendance records linked to positions via many-to-many relationship

**QR Code Presence Flow**
1. Admin/Operator creates Attendance with optional QR code
2. QR code generated via `PresenceController::showQrcode()`
3. Employee scans QR code → hits `HomeController::sendEnterPresenceUsingQRCode()` or `sendOutPresenceUsingQRCode()`
4. Presence record created with entry/exit times

**Permission/Izin System**
- Employees can request permission via `PermissionForm` Livewire component
- Permission records have `is_accepted` field for approval workflow
- API endpoint available: `GET /api/permissions/detail`

### Route Organization (routes/web.php)

**Guest Routes**: Login only

**Authenticated Routes**:
- `admin,operator` group: Dashboard, Positions, Employees, Holidays, Attendances, Presences (with QR codes)
- `user` group: Home (check-in/out), QR code scanning, permission requests

### Testing Setup

- PHPUnit configured in `phpunit.xml`
- Test environment uses `array` cache/driver, `sync` queue
- Database connection defaults to MySQL (not SQLite in-memory)
- Test suites: `Unit`, `Feature`

## Important Notes

- **Timezone**: Set to `Asia/Jakarta` in `.env` - all time-based attendance logic depends on this
- **Default Admin**: After seeding, login with `admin@gmail.com` / `password`
- **Language**: UI uses Indonesian terms (e.g., "Hapus" = Delete, "Karyawan" = Employee)
- **QR Code PDF**: Generated via DOMPDF for download/print
