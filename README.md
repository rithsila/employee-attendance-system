# Employee Attendance System

A web-based employee attendance management system built with **Laravel 9**, featuring QR code check-in/out, role-based access control, Livewire-powered tables, and Excel/CSV reporting.

---

## Table of Contents

- [Features](#features)
- [Tech Stack](#tech-stack)
- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [User Roles](#user-roles)
- [Default Credentials](#default-credentials)
- [Project Structure](#project-structure)
- [Available Commands](#available-commands)
- [Contributing](#contributing)
- [License](#license)

---

## Features

- **Role-Based Access Control** — Three distinct roles: Admin, Operator, and Employee
- **QR Code Attendance** — Generate printable QR codes for touchless check-in/out
- **Attendance Schedules** — Define time windows for check-in and check-out
- **CRUD Positions** — Manage job positions within the organization
- **CRUD Employees** — Full employee management with role and position assignment
- **CRUD Holidays** — Holiday management that auto-blocks attendance on those dates
- **Presence Tracking** — Real-time check-in/check-out records per attendance schedule
- **Permission/Leave Requests** — Employees submit leave requests; Admins approve them
- **Bulk Operations** — Select and edit/delete multiple records at once
- **Export to Excel & CSV** — All data tables support one-click exports
- **PDF QR Code Download** — Print-ready QR code PDFs via DOMPDF
- **Livewire PowerGrid Tables** — Interactive, searchable, sortable, filterable datatables
- **API Endpoints** — REST API for permission details (Sanctum-authenticated)

---

## Tech Stack

| Layer        | Technology                                    |
|--------------|-----------------------------------------------|
| Framework    | Laravel 9 (PHP ^8.0.2)                       |
| Frontend     | Blade Templates + Livewire 2                  |
| Datatables   | Livewire PowerGrid 3                          |
| Auth         | Session-based (web) + Laravel Sanctum (API)   |
| QR Codes     | `simplesoftwareio/simple-qrcode` ~4           |
| PDF          | `barryvdh/laravel-dompdf` ^1.0               |
| Database     | MySQL                                         |
| Asset Build  | Laravel Mix (Webpack)                         |
| Testing      | PHPUnit 9                                     |

---

## Prerequisites

- **PHP** >= 8.0.2 with extensions: `mbstring`, `openssl`, `pdo`, `tokenizer`, `xml`  
- **Composer** >= 2.x  
- **MySQL** >= 5.7 / MariaDB >= 10.3  
- **Node.js** >= 14.x & **npm**  
- **Git**  

---

## Installation

```bash
# 1. Clone the repository
git clone https://github.com/sgnd/employee-attendance-system.git
cd employee-attendance-system

# 2. Install PHP dependencies
composer install

# 3. Install Node.js dependencies
npm install

# 4. Copy environment file and configure
cp .env.example .env

# 5. Generate application key
php artisan key:generate

# 6. Run database migrations
php artisan migrate

# 7. Seed the database (creates default admin account and roles)
php artisan db:seed

# 8. Compile frontend assets
npm run dev

# 9. Start the development server
php artisan serve
```

Open your browser at **http://localhost:8000**

---

## Configuration

Edit `.env` with your local settings:

```env
APP_NAME="Employee Attendance System"
APP_URL=http://localhost

TIMEZONE="Asia/Jakarta"         # Required — all time-window logic depends on this

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=absensi_app
DB_USERNAME=root
DB_PASSWORD=
```

> **Important:** The `TIMEZONE` value must be set correctly. All attendance time-window validation (check-in/out windows) relies on this setting.

---

## Usage

### Admin / Operator

1. Log in and access the **Dashboard** for a system overview.
2. Manage **Positions**, **Employees**, and **Holidays** via the sidebar.
3. Create **Attendance Schedules** with check-in/out time windows and assign them to positions.
4. Generate and print **QR Codes** for touchless employee check-in.
5. Monitor **Presence Records** — view who checked in, who is absent, and approve leave requests.
6. **Export** any table to Excel or CSV with one click.

### Employee

1. Log in and view available attendance schedules on the **Home** page.
2. **Check In** by clicking "Absen Masuk" or scanning the QR code during the allowed time window.
3. **Check Out** by clicking "Absen Pulang" or scanning the QR code.
4. Submit **Permission/Leave Requests** via "Ajukan Izin" if unable to attend.
5. View your **attendance history** (last 30 days) on any attendance detail page.

---

## User Roles

| Role         | `role_id` | Access                                                              |
|--------------|-----------|---------------------------------------------------------------------|
| Admin        | 1         | Full access — manage all entities, view all reports                |
| Operator     | 2         | Same as Admin (administrative functions)                            |
| Employee     | 3         | Self-service — check-in/out, view history, submit leave requests   |

Role-based route protection is enforced via the `EnsureUserHasRole` middleware.

---

## Default Credentials

After seeding the database (`php artisan db:seed`):

| Field    | Value             |
|----------|-------------------|
| Email    | `admin@gmail.com` |
| Password | `password`        |
| Role     | Admin             |

> **Security:** Change the default password immediately in any non-local environment.

---

## Project Structure

```
employee-attendance-system/
├── app/
│   ├── Http/
│   │   ├── Controllers/          # Request handling & business logic
│   │   ├── Livewire/             # Livewire PowerGrid tables & forms
│   │   ├── Middleware/
│   │   │   └── EnsureUserHasRole.php
│   │   └── Requests/             # Form Request validation classes
│   └── Models/
│       ├── User.php              # Role constants (ADMIN=1, OPERATOR=2, USER=3)
│       ├── Attendance.php        # Schedules with check-in/out time windows
│       ├── Presence.php          # Employee check-in/out records
│       ├── Permission.php        # Leave/permission requests
│       ├── Position.php          # Job positions
│       ├── Role.php              # User roles
│       └── Holiday.php           # Company holidays
├── database/
│   ├── migrations/               # Database schema migrations
│   └── seeders/
│       ├── RoleSeeder.php        # Seeds: admin, operator, user roles
│       ├── PositionSeeder.php    # Seeds: default positions
│       └── DatabaseSeeder.php   # Creates default admin user
├── resources/views/              # Blade templates
├── routes/
│   ├── web.php                   # Web routes (guest + role-guarded groups)
│   └── api.php                   # API routes (Sanctum)
├── .env.example                  # Environment template
├── composer.json
├── package.json
└── webpack.mix.js
```

---

## Available Commands

```bash
# Development
php artisan serve                     # Start development server
npm run dev                           # Compile assets (development)
npm run watch                         # Watch and recompile on changes
npm run prod                          # Compile assets (production)

# Database
php artisan migrate                   # Run pending migrations
php artisan db:seed                   # Seed the database
php artisan migrate:fresh --seed      # Full reset with seeding

# Testing
php artisan test                      # Run all tests
php artisan test --filter TestName    # Run a specific test
php artisan test --testsuite=Feature  # Run only feature tests
php artisan test --testsuite=Unit     # Run only unit tests
```

---

## Contributing

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/your-feature-name`
3. Commit your changes: `git commit -m "feat: add your feature"`
4. Push to the branch: `git push origin feature/your-feature-name`
5. Open a Pull Request

Please ensure all tests pass before submitting a PR.

---

## License

This project is open-sourced software licensed under the [MIT License](https://opensource.org/licenses/MIT).
