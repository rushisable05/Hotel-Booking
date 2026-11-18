# 🏨 Hotel Booking Management System (PHP & MySQL)

A full-stack Hotel Booking & Management web application built with **PHP 8+**, **MySQL**, **HTML/CSS/JavaScript**, and **PHPMailer**.

## ✨ Features

### Guest / User Portal
- Browse available rooms with images, pricing, capacity, and destination filters
- Real-time room availability checker by check-in / check-out dates
- Online room booking with instant booking confirmation & email notification (PHPMailer)
- Built-in interactive hotel assistant chatbot
- User registration & login

### Admin Panel (`/admin/login.php`)
- Dashboard with live stats (total rooms, available rooms, bookings, pending bookings, unique customers, confirmed revenue)
- Room inventory management (add, edit, delete rooms with image upload & destination)
- Booking management & status updates (`Pending`, `Confirmed`, `Cancelled`, `Completed`)
- Customer directory & booking details view
- Date-wise room availability viewer & monthly analytics reports

### Resilience & Backup System
- **Auto-Backup:** Automatically exports the database to `database/backups/hotel_booking_latest.sql` on writes
- **Auto-Recovery:** Automatically restores the database from the latest backup or schema if `hotel_booking` is missing
- **One-Click Scripts:** `start-project.bat`, `backup-db.bat`, and `restore-db.bat`

## 🚀 Local Setup

1. **Install Prerequisites:** PHP 8+, MySQL (e.g., XAMPP / Laragon), and Composer.
2. **Install Dependencies:**
   ```bash
   composer install
   ```
3. **Configure Environment:**
   Copy `.env.example` to `.env` and update your database & SMTP email credentials:
   ```bash
   copy .env.example .env
   ```
4. **Run the Project:**
   - Double-click **`start-project.bat`** (starts MySQL, auto-initializes/restores the DB, opens website on `http://localhost:8000` and phpMyAdmin on `http://localhost:8080`), **or**
   - Import `database/hotel-booking.sql` into MySQL and run:
     ```bash
     php -S localhost:8000
     ```

## 🔐 Default Admin Login
- **URL:** `http://localhost:8000/admin/login.php`
- **Email:** `admin@example.com`
- **Password:** `Rushi123` *(change immediately in production)*
