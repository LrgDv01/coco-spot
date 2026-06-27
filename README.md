# Coco Spot

Coco Spot is a Laravel-based web application for coconut farm monitoring, disease reporting, and response coordination between farmers, technicians, and administrators.

The platform is built around three main user roles:

- Administrators manage dashboard insights, map data, reports, and notifications.
- Farmers register farms, submit coconut disease reports, view responses, and track updates.
- Technicians review reports, diagnose issues, submit findings, and monitor assigned cases.

## Table of Contents

- [Overview](#overview)
- [Key Features](#key-features)
- [Tech Stack](#tech-stack)
- [Project Structure](#project-structure)
- [Requirements](#requirements)
- [Installation](#installation)
- [Environment Setup](#environment-setup)
- [Running the Project](#running-the-project)
- [Useful Commands](#useful-commands)
- [Notes](#notes)
- [License](#license)

## Overview

Coco Spot is designed to support coconut disease monitoring workflows from first report to final diagnosis.

The application includes:

- role-based authentication and access control
- farm and location management
- disease reporting and diagnosis tracking
- map-based visualization and simulation views
- real-time notifications through broadcasting
- report export support for PDF, Excel, and CSV formats
- community forum features for authenticated users

## Key Features

### Authentication and Roles

- Login, registration, password reset, and logout flows
- Role-aware routing for admins, farmers, and technicians
- Device-aware login behavior for mobile access

### Farmer Workflow

- Add and manage farm details
- View farm locations on the map
- Submit coconut disease reports
- View submitted reports and notification updates
- Update profile information and profile image

### Technician Workflow

- Review assigned notifications and requests
- Monitor reports that require diagnosis
- Submit technician reports and recommendations
- Track report details and status changes

### Admin Workflow

- View dashboard analytics and summary counts
- Manage map location data
- Review disease reports
- Export report data to PDF, Excel, or CSV
- Mark notifications as read and manage verified reports

### Community and Communication

- Authenticated forum posts
- Comments and reactions
- Broadcast notification channels for live updates

## Tech Stack

- PHP 8.1+
- Laravel 10
- Blade templates
- Vite
- Bootstrap-based frontend
- Laravel Sanctum
- Laravel Broadcasting with Pusher
- DomPDF for PDF exports
- Maatwebsite Excel for spreadsheet exports
- Simple QR Code
- Mobile Detect

## Project Structure

The codebase follows the standard Laravel layout with a few notable areas:

- `app/Http/Controllers` - authentication, admin, farmer, technician, forum, and notification logic
- `app/Models` - Eloquent models for users, farms, reports, notifications, posts, comments, and reactions
- `app/Events` - broadcast events for real-time updates
- `resources/views` - Blade views for admin, user, technician, auth, and public pages
- `routes/web.php` - main web routes and role-based route groups
- `routes/channels.php` - broadcast channel authorization
- `database/migrations` - database schema definitions
- `public/` - public assets

## Requirements

- PHP 8.1 or newer
- Composer
- Node.js and npm
- A database server supported by Laravel

## Installation

1. Clone the repository.
2. Install PHP dependencies:

```bash
composer install
```

3. Install frontend dependencies:

```bash
npm install
```

4. Copy the environment file:

```bash
cp .env.example .env
```

If you are using Windows PowerShell, `copy .env.example .env` works as well.

5. Generate the application key:

```bash
php artisan key:generate
```

6. Run the database migrations:

```bash
php artisan migrate
```

7. If the project includes seeders for your local setup, run them as needed:

```bash
php artisan db:seed
```

## Environment Setup

Update your `.env` file with the correct values for:

- `APP_NAME`
- `APP_URL`
- database connection settings
- mail settings if you want email features to work locally
- broadcasting settings for Pusher
- any storage or queue configuration required by your environment

Example environment values may also be needed for:

- `PUSHER_APP_ID`
- `PUSHER_APP_KEY`
- `PUSHER_APP_SECRET`
- `PUSHER_APP_CLUSTER`

## Running the Project

Open two terminal windows and run:

```bash
php artisan serve
```

and:

```bash
npm run dev
```

If broadcasting or queued jobs are enabled in your environment, make sure the corresponding Laravel services are also running.

## Useful Commands

- Start the Laravel server: `php artisan serve`
- Build frontend assets: `npm run build`
- Run tests: `php artisan test`
- Clear configuration cache: `php artisan config:clear`
- Clear application cache: `php artisan cache:clear`

## Notes

- The app uses role-based middleware to separate access between admin, farmer, and technician sections.
- Several views rely on real-time notifications and location-based data, so keep the database and broadcast settings aligned with your local or production environment.
- Export features are available for report management and can generate PDF and spreadsheet outputs.

## License

This project is licensed under the MIT License.
