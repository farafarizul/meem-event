# Meem Event Management System

## Project Overview

Meem Event Management System is a web-based application built with Laravel 10.x that enables administrators to manage events, generate QR codes for event check-ins, and track attendance. The system provides a dedicated admin panel for managing events and users, along with a public-facing event check-in page where attendees can scan or use their unique QR codes to register their attendance. Only administrators are permitted to log in to the management panel; normal users do not have login access.

---

## Features

- Secure admin-only authentication
- Dashboard with real-time statistics
- Dummy user management with auto-generated unique codes
- Full Event CRUD (Create, Read, Update, Delete)
- Unique event identifier generation (format: `EVENT-XXXXXXXXXX`)
- QR code generation and download per event
- Public event check-in page (no login required for attendees)
- One check-in per user per event enforcement
- Event check-in listing with AJAX server-side DataTables
- Excel export for listing modules
- Bootstrap 5 responsive UI
- jQuery-powered interactions

---

## Tech Stack

| Layer             | Technology                        |
|-------------------|-----------------------------------|
| Framework         | Laravel 10.x                      |
| Language          | PHP 8.1                           |
| Database          | MySQL                             |
| Authentication    | Laravel Breeze                    |
| UI Framework      | Bootstrap 5 (latest)              |
| JavaScript        | jQuery                            |
| Data Tables       | Yajra DataTables (AJAX server-side) |
| Excel Export      | Maatwebsite Excel                 |
| QR Code           | PHP QR Code / Laravel QR Package  |

---

## System Requirements

- PHP >= 8.1
- Composer >= 2.x
- MySQL >= 5.7 or MariaDB >= 10.3
- Node.js >= 16.x and NPM (for front-end assets, if applicable)
- A web server such as Apache, Nginx, or Laravel's built-in development server

---

## Installation

Follow the steps below to set up the project on your local machine.

### 1. Clone the Repository

```bash
git clone https://github.com/farafarizul/meem-event.git
cd meem-event
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Copy the Environment File

```bash
cp .env.example .env
```

### 4. Generate the Application Key

```bash
php artisan key:generate
```

---

## Environment Setup

Open the `.env` file and update the following values to match your local environment:

```env
APP_NAME="Meem Event"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000
```

---

## Database Configuration

In the `.env` file, configure your MySQL database connection:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=meem_event
DB_USERNAME=your_mysql_username
DB_PASSWORD=your_mysql_password
```

> Make sure the database `meem_event` (or your chosen name) is created in MySQL before running migrations.

---

## Migration and Seeder Steps

Run the following command to execute all database migrations and seed the database with initial data in a single step:

```bash
php artisan migrate --seed
```

**What this does:**

- **`migrate`** — Creates all required database tables based on the migration files located in `database/migrations/`.
- **`--seed`** — Automatically runs all database seeders located in `database/seeders/` after migrations complete. This includes seeding the default admin account and at least 30 dummy users.

> If you need to refresh and re-seed the database at any point, you can run:
> ```bash
> php artisan migrate:fresh --seed
> ```
> **Warning:** `migrate:fresh` will drop all existing tables and re-run all migrations. Use with caution in any environment that contains important data.

---

## How to Run Locally

To start the application on your local machine using Laravel's built-in development server, run:

```bash
php artisan serve
```

The application will be accessible at:

```
http://127.0.0.1:8000
```

> **Note:** `php artisan serve` is intended for **local development only**. It is not suitable for production deployments. For production, use a proper web server such as Apache or Nginx with the appropriate configuration.

---

## Default Admin Login

After running migrations and seeders, use the following credentials to log in to the admin panel:

| Field    | Value               |
|----------|---------------------|
| Email    | admin@meem.com.my   |
| Password | 12345678            |

> The admin login page is accessible at: `http://127.0.0.1:8000/login`

> ⚠️ **Security Notice:** The default password `12345678` is intended for local development only. Change it immediately after first login and **never** use default credentials in a production environment.

---

## Dummy Seeded Data

The database seeder automatically creates the following dummy data upon running `php artisan migrate --seed`:

- **At least 30 dummy users** are seeded into the system.
- Each dummy user is assigned a unique `meem_code` generated in the format:

  ```
  MEEM000001
  MEEM000002
  MEEM000003
  ...
  ```

  The numeric portion is zero-padded to 6 digits and increments sequentially.

---

## Main Modules

### 1. Admin Login
Only administrators can log in to the system. Normal (dummy) users do not have login access and cannot authenticate through the admin panel.

### 2. Dashboard
Displays an overview of key statistics including total events, total users, total check-ins, and other relevant metrics.

### 3. Dummy Users Management
Administrators can view and manage dummy users. Each user is assigned a unique `meem_code` (e.g., `MEEM000001`) used for event check-in identification.

### 4. Event Management (CRUD)
Administrators can create, view, update, and delete events. Each event is assigned a unique identifier generated in the format:

```
EVENT-XXXXXXXXXX
```

where `XXXXXXXXXX` is a randomly generated alphanumeric string.

### 5. Event QR Code Generation and Download
For each event, a QR code can be generated and downloaded. The QR code encodes the event's public check-in URL and can be printed or shared with attendees.

### 6. Public Event Check-In Page
A publicly accessible page (no login required) where attendees can check in to an event by entering their `meem_code`. The system validates the code and records the attendance.

**Rule:** Each user can only check in **once** per event. Duplicate check-in attempts for the same event will be rejected.

### 7. Event Check-In Listing (Admin)
Administrators can view a full list of check-ins per event. The listing uses **Yajra DataTables with AJAX server-side processing** for efficient handling of large datasets.

### 8. Excel Export
All major listing modules (users, check-ins, etc.) support Excel export via **Maatwebsite Excel**, allowing administrators to download data in `.xlsx` format.

---

## Notes / Important Rules

- **Admin access only:** Only the seeded admin account (or accounts created by the admin) can log in to the management panel. Regular dummy users cannot log in.
- **One check-in per event:** The system enforces a strict one check-in rule — a user cannot check in to the same event more than once.
- **meem_code format:** Every dummy user is assigned a unique `meem_code` in the format `MEEM000001`. This code is used as the check-in identifier on the public check-in page.
- **Event unique ID format:** Each event is assigned a unique identifier in the format `EVENT-XXXXXXXXXX` upon creation.
- **Local development only:** The `php artisan serve` command is for local development. Do not use it in a production environment.
- **Database must exist before migration:** Ensure the MySQL database is created before running `php artisan migrate --seed`.
- **Fresh seeding:** If you need to reset the database, use `php artisan migrate:fresh --seed`. This will drop all tables and re-seed from scratch.
