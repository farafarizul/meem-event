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

---

## API Documentation

The application exposes both a versioned REST API and a set of web-accessible public and admin endpoints. All endpoints are relative to the application base URL (e.g., `http://127.0.0.1:8000`).

---

### 1. REST API — Customer Profile

#### `GET /api/v1/customer/profile`

Fetches the authenticated customer's profile from the upstream Meem service and syncs the data to the local database.

**Authentication:** Bearer token in the `Authorization` header (issued by the upstream Meem service).

**Request Headers:**

| Header          | Required | Description                              |
|-----------------|----------|------------------------------------------|
| `Authorization` | Yes      | `Bearer <token>` — upstream access token |

**Example Request:**

```http
GET /api/v1/customer/profile HTTP/1.1
Host: 127.0.0.1:8000
Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...
```

**Success Response — `200 OK`:**

```json
{
    "success": true,
    "data": {
        "id": "usr_abc123",
        "name": "Ahmad bin Ali",
        "email": "ahmad@example.com",
        "contact_no": "0123456789",
        "cs_code": "MEEM000001",
        "profile_picture": "https://cdn.example.com/profile/ahmad.jpg"
    }
}
```

**Error Responses:**

| HTTP Status | Condition                                            | Response Body                                                   |
|-------------|------------------------------------------------------|-----------------------------------------------------------------|
| `401`       | `Authorization` header is missing or not a Bearer token | `{ "success": false, "message": "Authorization token is required." }` |
| `502`       | Upstream Meem service is unreachable                 | `{ "success": false, "message": "Unable to reach upstream service." }` |

> **Note:** On a successful response, the system automatically creates or updates the local user record using `meem_id` as the unique key. A sync failure is logged silently and does not affect the response returned to the caller.

---

#### `GET /api/user`

Returns the currently authenticated user record (Sanctum session-based).

**Authentication:** Laravel Sanctum session or API token.

**Success Response — `200 OK`:**

```json
{
    "id": 1,
    "fullname": "Admin User",
    "email": "admin@meem.com.my",
    "is_admin": true,
    "created_at": "2026-03-11T00:00:00.000000Z",
    "updated_at": "2026-03-11T00:00:00.000000Z"
}
```

---

### 2. Public Check-In Endpoints

These routes are accessible without authentication and are used by event attendees to check in to an event.

#### QR Code Flow

Used when an attendee scans an event QR code. The QR code encodes a URL with an obfuscated `user_id` and the event's `unique_identifier`.

---

##### `GET /checkin?scannedfromapp={eventId}&user_id={obfuscatedId}`

Displays the check-in confirmation page for the scanned QR code.

**Query Parameters:**

| Parameter        | Type    | Required | Description                                                        |
|------------------|---------|----------|--------------------------------------------------------------------|
| `scannedfromapp` | string  | Yes      | The event's `unique_identifier` (e.g., `EVENT-AB12CD34EF`)         |
| `user_id`        | integer | Yes      | Obfuscated user ID embedded in the QR code                         |

**Responses:**

| Outcome             | View Rendered          | Description                                          |
|---------------------|------------------------|------------------------------------------------------|
| Valid, not checked in | `checkin.show`        | Displays a confirmation page to confirm check-in     |
| Already checked in  | `checkin.already`      | Informs the user they have already checked in        |
| Invalid parameters  | `checkin.invalid`      | Displayed when the event or user cannot be resolved  |

---

##### `POST /checkin`

Submits the QR code check-in.

**Request Body (form data):**

| Field            | Type    | Required | Description                                        |
|------------------|---------|----------|----------------------------------------------------|
| `scannedfromapp` | string  | Yes      | The event's `unique_identifier`                    |
| `user_id`        | integer | Yes      | Obfuscated user ID                                 |

**Responses:**

| Outcome             | View Rendered     | Description                                      |
|---------------------|-------------------|--------------------------------------------------|
| Success             | `checkin.success` | Check-in recorded; displays success confirmation |
| Already checked in  | `checkin.already` | Check-in rejected; user was already checked in   |
| Invalid parameters  | `checkin.invalid` | Event or user could not be resolved              |

---

#### Legacy List-Based Flow

Used when an attendee selects their identity from a dropdown list on the public check-in page.

---

##### `GET /checkin/{uniqueIdentifier}`

Displays the check-in page for the given event, showing a list of all registered users.

**URL Parameters:**

| Parameter          | Type   | Required | Description                                           |
|--------------------|--------|----------|-------------------------------------------------------|
| `uniqueIdentifier` | string | Yes      | The event's `unique_identifier` (e.g., `EVENT-AB12CD34EF`) |

**Responses:**

| Outcome       | Response            | Description                                            |
|---------------|---------------------|--------------------------------------------------------|
| Event found   | View `checkin.show` | Displays the check-in form with a list of active users |
| Event not found | `404 Not Found`   | Returned when no event matches the identifier          |

---

##### `POST /checkin/{uniqueIdentifier}`

Submits a check-in using the list-based form.

**URL Parameters:**

| Parameter          | Type   | Required | Description                                           |
|--------------------|--------|----------|-------------------------------------------------------|
| `uniqueIdentifier` | string | Yes      | The event's `unique_identifier`                       |

**Request Body (form data):**

| Field     | Type    | Required | Description                           |
|-----------|---------|----------|---------------------------------------|
| `user_id` | integer | Yes      | The ID of the user checking in        |

**Validation Rules:**

- `user_id` must be a valid integer that exists in the `users` table.

**Responses:**

| Outcome            | View Rendered     | Description                                        |
|--------------------|-------------------|----------------------------------------------------|
| Success            | `checkin.success` | Check-in recorded; displays success confirmation   |
| Already checked in | `checkin.already` | Check-in rejected; user was already checked in     |
| Event not found    | `404 Not Found`   | Returned when no event matches the identifier      |

---

### 3. Admin Endpoints

All admin endpoints require authentication (`auth` middleware) and the `is_admin` middleware. Unauthenticated or non-admin requests are redirected to the login page.

#### Authentication

Admin authentication uses Laravel Breeze (session-based). The login page is available at `/login`.

| Field    | Value             |
|----------|-------------------|
| Email    | admin@meem.com.my |
| Password | 12345678          |

---

#### Dashboard

| Method | URL               | Description                                   |
|--------|-------------------|-----------------------------------------------|
| `GET`  | `/admin/dashboard` | Displays statistics: total events, users, check-ins |

---

#### User Management

| Method   | URL                        | Description                                       |
|----------|----------------------------|---------------------------------------------------|
| `GET`    | `/admin/users`             | Lists all dummy users                             |
| `GET`    | `/admin/users/datatable`   | AJAX endpoint for server-side DataTable           |
| `GET`    | `/admin/users/export`      | Downloads all users as an `.xlsx` file            |
| `PUT`    | `/admin/users/{user}`      | Updates a user record by ID                       |
| `DELETE` | `/admin/users/{user}`      | Soft-deletes (marks as `deleted`) a user by ID    |

**User Update — Request Body (`PUT /admin/users/{user}`):**

| Field          | Type   | Required | Description            |
|----------------|--------|----------|------------------------|
| `fullname`     | string | Yes      | Full name of the user  |
| `phone_number` | string | Yes      | User's phone number    |

---

#### Event Management

| Method   | URL                               | Description                                             |
|----------|-----------------------------------|---------------------------------------------------------|
| `GET`    | `/admin/events`                   | Lists all events                                        |
| `GET`    | `/admin/events/create`            | Shows the event creation form                           |
| `POST`   | `/admin/events`                   | Creates a new event                                     |
| `GET`    | `/admin/events/datatable`         | AJAX endpoint for server-side DataTable                 |
| `GET`    | `/admin/events/export`            | Downloads all events as an `.xlsx` file                 |
| `GET`    | `/admin/events/{event}`           | Shows event details and QR code preview                 |
| `GET`    | `/admin/events/{event}/edit`      | Shows the event edit form                               |
| `PUT`    | `/admin/events/{event}`           | Updates an event by ID                                  |
| `DELETE` | `/admin/events/{event}`           | Deletes an event by ID                                  |
| `GET`    | `/admin/events/{event}/qr-download` | Downloads the event's QR code as a `.png` file        |

**Event Create/Update — Request Body (`POST /admin/events`, `PUT /admin/events/{event}`):**

| Field               | Type   | Required | Validation                                                        | Description                             |
|---------------------|--------|----------|-------------------------------------------------------------------|-----------------------------------------|
| `category_event`    | string | Yes      | One of: `online`, `onsite`                                        | Event type                              |
| `event_name`        | string | Yes      | Max 255 characters                                                | Name of the event                       |
| `location`          | string | Yes      | Max 255 characters                                                | Event location                          |
| `start_date`        | date   | Yes      | Valid date                                                        | Event start date                        |
| `end_date`          | date   | Yes      | Valid date, must be on or after `start_date`                      | Event end date                          |
| `unique_identifier` | string | Yes (create) | Max 16 chars, unique, format: `EVENT-[A-Z0-9]{10}`            | System-generated event identifier       |

---

#### Check-In Management

| Method   | URL                          | Description                                           |
|----------|------------------------------|-------------------------------------------------------|
| `GET`    | `/admin/checkins`            | Lists all check-ins across all events                 |
| `GET`    | `/admin/checkins/datatable`  | AJAX endpoint for server-side DataTable               |
| `GET`    | `/admin/checkins/export`     | Downloads all check-ins as an `.xlsx` file            |
| `DELETE` | `/admin/checkins/{checkin}`  | Deletes a check-in record by ID                       |

---

### API Response Structure

#### Customer Profile API

All responses from `/api/v1/customer/profile` follow this structure:

```json
{
    "success": true | false,
    "message": "Human-readable message (present on errors)",
    "data": { }
}
```

#### Admin & Public Endpoints

Admin and public check-in endpoints return **HTML views** (not JSON), as they are standard web routes rendered via Laravel Blade templates.

The AJAX DataTable endpoints (`/admin/*/datatable`) return JSON in the **Yajra DataTables** format:

```json
{
    "draw": 1,
    "recordsTotal": 100,
    "recordsFiltered": 100,
    "data": [ ]
}
```

---

### Summary of All Endpoints

| Method   | URL                                | Auth Required | Description                              |
|----------|------------------------------------|---------------|------------------------------------------|
| `GET`    | `/api/v1/customer/profile`         | Bearer token  | Get customer profile from upstream       |
| `GET`    | `/api/user`                        | Sanctum       | Get authenticated user                   |
| `GET`    | `/checkin`                         | None          | QR check-in confirmation page            |
| `POST`   | `/checkin`                         | None          | Submit QR check-in                       |
| `GET`    | `/checkin/{uniqueIdentifier}`      | None          | List-based check-in page                 |
| `POST`   | `/checkin/{uniqueIdentifier}`      | None          | Submit list-based check-in               |
| `GET`    | `/admin/dashboard`                 | Admin         | Admin dashboard with statistics          |
| `GET`    | `/admin/users`                     | Admin         | List all users                           |
| `GET`    | `/admin/users/datatable`           | Admin         | Users AJAX DataTable                     |
| `GET`    | `/admin/users/export`              | Admin         | Export users to Excel                    |
| `PUT`    | `/admin/users/{user}`              | Admin         | Update user                              |
| `DELETE` | `/admin/users/{user}`              | Admin         | Delete user                              |
| `GET`    | `/admin/events`                    | Admin         | List all events                          |
| `GET`    | `/admin/events/create`             | Admin         | Event creation form                      |
| `POST`   | `/admin/events`                    | Admin         | Create new event                         |
| `GET`    | `/admin/events/datatable`          | Admin         | Events AJAX DataTable                    |
| `GET`    | `/admin/events/export`             | Admin         | Export events to Excel                   |
| `GET`    | `/admin/events/{event}`            | Admin         | View event details & QR code             |
| `GET`    | `/admin/events/{event}/edit`       | Admin         | Event edit form                          |
| `PUT`    | `/admin/events/{event}`            | Admin         | Update event                             |
| `DELETE` | `/admin/events/{event}`            | Admin         | Delete event                             |
| `GET`    | `/admin/events/{event}/qr-download`| Admin         | Download event QR code as PNG            |
| `GET`    | `/admin/checkins`                  | Admin         | List all check-ins                       |
| `GET`    | `/admin/checkins/datatable`        | Admin         | Check-ins AJAX DataTable                 |
| `GET`    | `/admin/checkins/export`           | Admin         | Export check-ins to Excel                |
| `DELETE` | `/admin/checkins/{checkin}`        | Admin         | Delete a check-in record                 |
