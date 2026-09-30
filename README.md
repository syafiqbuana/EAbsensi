# EAbsensi 🎓 — Multi-Tenant TPQ & Attendance Management System

<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.3-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.3">
  <img src="https://img.shields.io/badge/Laravel-12-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 12">
  <img src="https://img.shields.io/badge/Livewire-4.x-4E56A6?style=for-the-badge&logo=livewire&logoColor=white" alt="Livewire">
  <img src="https://img.shields.io/badge/Filament-5.x-E5A50A?style=for-the-badge&logo=filament&logoColor=white" alt="Filament">
  <img src="https://img.shields.io/badge/Flux_UI-Enabled-06B6D4?style=for-the-badge" alt="Flux UI">
  <img src="https://img.shields.io/badge/Architecture-Multi--Tenant-green?style=for-the-badge" alt="Multi-Tenant">
</p>

---

## 📖 Executive Summary

**EAbsensi** is an multi-tenant academic management and student attendance platform tailored specifically for **Taman Pendidikan Al-Qur'an (TPQ)** educational institutions. The platform streamlines daily operational workflows for school administrators, teachers, and parents through intelligent automation, real-time analytics, dynamic scheduling, and automated QR-based attendance tracking.

Built on top of the **TALL Stack (Tailwind CSS, Alpine.js, Laravel 12, Livewire)** and powered by **Filament 3** & **Flux UI**, EAbsensi provides complete data isolation across institutions (*Multi-Tenancy via Tenant Slugs*) while offering high-performance interactive dashboards for guardians and administrative panels for operators.

---

## 🌟 Core System Modules & Features

### 🏢 1. Multi-Tenant Architecture (`TpqProfile`)
* **URL Slug-Based Tenant Routing:** Complete isolation of student data, classes, schedules, and attendance logs per TPQ instance via custom middleware (`ResolveTpqTenant`).
* **Tenant Profile Management:** Super-admins can manage global settings, institutional identity, contact info, and status (`active` / `inactive`) for each registered TPQ.
* **Super Admin Onboarding Workflow:** Interactive approval pipeline (`TpqRegistration`) for new TPQ registration requests.

### 📱 2. QR Code Attendance & Rapid Check-In System
* **Dynamic Student QR Engine:** Automatically generates encrypted/unique QR tokens for each student based on NIS (Nomor Induk Santri).
* **Scanner & Kiosk Integration:** Direct camera/scanner support for fast check-in during morning and afternoon shifts.
* **Instant ID Card Export:** Export printable digital Student Cards equipped with embedded QR codes.
* **Multi-Status Tracking:** Handles `present` (Hadir), `sick` (Sakit), `permission` (Izin), `absent` (Alpha), and `holiday` (Libur) states with automatic timestamping (`scanned_at`).

### 👨‍👩‍👧 3. Interactive Parent & Guardian Portal
* **Child Switcher (`ChildCardSelector`):** Parents with multiple children in the same TPQ can seamlessly toggle between child profiles without logging out.
* **Real-Time Attendance Widget (`AttendanceTodayWidget`):** Displays current-day check-in status, entry time, and active class schedule.
* **Interactive Calendar View (`AttendanceCalendar`):** Grid view color-coded by attendance status for tracking monthly consistency.
* **Performance & Academic Progress (`ChildPerformanceCard`):** Displays recent study records, page progression (Surah / Iqra level), and academic achievements.
* **Cached Data Performance (`StudentDataService`):** Optimized query caching mechanism minimizing database overhead for dashboard metrics.

### 📝 4. Digital Leave & Permission Requests (`LeaveRequest`)
* **Submission Pipeline:** Parents can submit leave requests directly from their portal with target date ranges (`start_date`, `end_date`), total days calculation, leave type (`sick` / `permission`), and supporting document/reason attachment.
* **Admin Approval Workflow:** Filament-integrated approval dashboard allowing administrators to approve or reject requests. Approved leaves automatically reflect as `permission` or `sick` on attendance sheets.

### 📅 5. Dynamic Schedules & Holiday Management
* **Multi-Day Class Schedules (`Schedules`):** Configurable schedules mapped to specific days (`monday` through `sunday`) using MySQL `SET` data types, featuring strict time boundary validations (`time_open` vs `time_close`).
* **Class Mapping (`Classes`):** Flexible many-to-many relationship linking multiple classes to specific schedule slots.
* **Global & Local Holidays (`Holiday`):** Configure institution-wide or schedule-specific holidays. On configured holiday dates, attendance requirements are automatically bypassed and marked appropriately.

### 📖 6. Academic Study Records (`StudyRecord`)
* **Progress Tracking:** Instructors can log individual student reading progress, including book type (e.g., Al-Qur'an, Iqra 1-6), page/ayat number, notes, and performance ratings.
* **Historical Memory:** Long-term archival of academic progression accessible by parents for home review.

### 🛡️ 7. Security & User Management
* **Role-Based Access Control (RBAC):** Powered by Spatie Permission (`super_admin`, `admin_tpq`, `teacher`, `parent`).
* **Laravel Fortify Hardening:** Built-in Two-Factor Authentication (2FA) support and Passkey / WebAuthn capability.
* **Profile Settings:** Dedicated profile updating, email verification, and password security controls.

---

## 🛠️ Technology Stack Architecture

| Layer | Technology / Package | Purpose |
| :--- | :--- | :--- |
| **Backend Framework** | **Laravel 12.x** (PHP 8.3+) | Core Application Logic & REST Services |
| **Admin Panel** | **Filament 5.x** | Enterprise Admin Backoffice & Data Tables |
| **Reactive Frontend** | **Livewire 4.x** & **Flux UI** | Dynamic Client-Side Interactivity without API overhead |
| **Styling & UI** | **Tailwind CSS 4.x** & **Vite** | Modern, responsive utility-first design system |
| **Database ORM** | **MySQL 8.0+** / **MariaDB** | Relational storage with strict foreign keys & SET fields |
| **Authentication** | **Laravel Fortify** | 2FA, Passkeys, Session Security & Password Reset |
| **Permissions** | **Spatie Laravel-Permission** | Granular Role & Permission Management |
| **QR Code Engine** | **BaconQrCode** / **SimpleSoftwareIO** | High-density vector QR code rendering |

---

## 🗄️ Database Entity-Relationship Overview

```
+----------------+       +-------------------+       +-----------------+
|  TpqProfile    | <---> |       Users       | <---> |   UserProfile   |
+----------------+       +-------------------+       +-----------------+
        |                          |
        | 1:N                      | 1:N (Parents)
        v                          v
+----------------+       +-------------------+       +-----------------+
|    Classes     | <---> |     Students      | <---> |  LeaveRequests  |
+----------------+       +-------------------+       +-----------------+
        |                          |                          |
        | N:M                      | 1:N                      | 1:N
        v                          v                          v
+----------------+       +-------------------+       +-----------------+
|   Schedules    | <---> |    Attendances    |       |  StudyRecords   |
+----------------+       +-------------------+       +-----------------+
        |
        | N:M
        v
+----------------+
|    Holidays    |
+----------------+
```

---

## 🚀 Installation & Developer Setup

### System Prerequisites
Ensure your local development environment meets the following requirements:
* **PHP** `>= 8.3` (with extensions: `pdo_mysql`, `mbstring`, `openssl`, `gd`, `xml`, `curl`)
* **Composer** `>= 2.6`
* **Node.js** `>= 18.x` & **NPM**
* **MySQL Server** `>= 8.0`

### Step-by-Step Installation

1. **Clone Repository & Navigate:**
   ```bash
   git clone https://github.com/syafiqbuana/EAbsensi.git
   cd EAbsensi
   ```

2. **Install Backend Dependencies:**
   ```bash
   composer install
   ```

3. **Install & Build Frontend Assets:**
   ```bash
   npm install
   npm run build
   ```

4. **Environment Configuration:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Edit `.env` and set your database connection details:*
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=tpq
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Execute Database Migrations & Seeders:**
   ```bash
   php artisan migrate --seed
   ```
   *This initializes the database schema, default superadmin credentials, sample TPQ profile, schedules, and test students.*

6. **Create Storage Link:**
   ```bash
   php artisan storage:link
   ```

7. **Launch Local Server:**
   ```bash
   php artisan serve
   ```
   * Access Admin tenant Panel: `http://127.0.0.1:8000/{tenant-slug}admin/login`
   * Access parent Portal: `http://127.0.0.1:8000/{tenant-slug}/login`
   * Access superadmin: `http://127.0.0.1:8000/superadmin/login`
   * Universal Login : `http://127.0.0.1:8000/universal/admin/login`

---

## 🧪 Testing & Quality Assurance

Run automated test suites to ensure code compliance and application stability:

```bash
# Run PHPUnit test suite
php artisan test

# Check code formatting (Laravel Pint)
vendor/bin/pint --test
```

<p align="center">
  Developed syafiqbuana
</p>
