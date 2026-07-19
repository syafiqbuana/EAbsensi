# EAbsensi 🎓

<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.3-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.3">
  <img src="https://img.shields.io/badge/Laravel-12-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 12">
  <img src="https://img.shields.io/badge/Livewire-4E56A6?style=for-the-badge&logo=livewire&logoColor=white" alt="Livewire">
  <img src="https://img.shields.io/badge/Filament-5-E5A50A?style=for-the-badge&logo=filament&logoColor=white" alt="Filament">
</p>

## 📖 Project Overview

**EAbsensi** is Student Attendance Management System designed to streamline administrative workflows, academic scheduling, and daily attendance tracking. 

Engineered with the modern PHP ecosystem, this system leverages **Laravel 12** and **PHP 8.3** for robust backend performance. The frontend and administration interfaces are powered by **Filament 5**, **Livewire**, and **Flux UI**, delivering a seamless, highly responsive, and beautiful user experience for both school administrators and parents.

## ✨ Key Features

* **🚀 Smart QR Code Attendance:** Automated generation of unique QR tokens for rapid, touchless daily check-ins. Supports digital ID card exports.
* **🛡️ Role-Based Access Control (RBAC):** Secure, isolated portals for different user types:
  * **Admin Portal:** Full control over master data, schedules, users, and comprehensive reporting.
  * **Parent Portal:** Dedicated secure access for parents to monitor their own child's real-time attendance and study progress.
* **📅 Dynamic Scheduling & Holiday Management:** Highly customizable class schedules mapped to specific days and times, integrated with a robust holiday management module to automatically adjust attendance requirements.
* **🗃️ Deep Record Memory:** Maintains comprehensive historical logs ("memories") of past student attendances, academic progress, and system activities. Includes built-in tools for database structure visibility and monitoring.
* **🔒 Advanced Security:** Hardened user authentication supporting Two-Factor Authentication (2FA) and modern Passkey integration.

## 🛠️ Tech Stack

* **Core Framework:** [Laravel 12](https://laravel.com) (PHP 8.3)
* **Admin Panel:** [Filament 5](https://filamentphp.com)
* **Frontend & UI:** [Livewire](https://livewire.laravel.com), [Flux UI](https://fluxui.dev), Tailwind CSS
* **Database:** MySQL / PostgreSQL
* **Authentication:** Laravel Fortify (with 2FA & Passkey support)

## 📋 Prerequisites

Before you begin, ensure you have met the following requirements:
* **PHP** >= 8.3
* **Composer** >= 2.0
* **Node.js** & **NPM** (for compiling frontend assets)
* **MySQL** >= 8.0 or **MariaDB** equivalent

## 🚀 Installation & Setup

1. **Clone the repository:**
   ```bash
   git clone https://github.com/syafiqbuana/eabsensi.git
   cd eabsensi
   ```

2. **Install PHP dependencies:**
   ```bash
   composer install
   ```

3. **Install NPM dependencies:**
   ```bash
   npm install && npm run build
   ```

4. **Environment Setup:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Configure your database credentials and other necessary environment variables in the `.env` file.*

5. **Database Migration & Seeding:**
   ```bash
   php artisan migrate --seed
   ```
   *This will create the necessary database structure and populate initial master data, including the superadmin account and sample schedules.*

6. **Link Storage:**
   ```bash
   php artisan storage:link
   ```

7. **Run the Application:**
   ```bash
   php artisan serve
   ```
   *Access the admin panel at `http://localhost:8000/admin`.*

## 🗄️ Database Structure Overview

The system is built on a highly relational database architecture optimized for speed and data integrity:
* `users` / `roles` / `permissions` - Handles RBAC, 2FA, and Passkeys.
* `students` / `classes` - Manages student demographics and class assignments.
* `schedules` / `schedule_class` - Maps operational hours to specific classes and days.
* `attendances` - Tracks daily check-ins, timestamps, and status (Present, Sick, Absent, Permission, Holiday).
* `holidays` / `holiday_schedule` - Overrides standard attendance expectations during configured global or specific holidays.

## 👨‍💻 Author

**Syafiq Galih Rengga Buana**
* Full-Stack Web Developer
* Passionate about building impactful applications using the TALL stack.

