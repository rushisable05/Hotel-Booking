<?php

require_once __DIR__ . "/env.php";

$host = getenv("DB_HOST") ?: "localhost";
$username = getenv("DB_USER") ?: "root";
$password = getenv("DB_PASS") !== false ? getenv("DB_PASS") : "";
$database = getenv("DB_NAME") ?: "hotel_booking";

require_once __DIR__ . "/../database/db-backup-helper.php";

// Create database connection (auto-restore from project backup if DB is missing)
$conn = null;
try {
    $conn = new mysqli($host, $username, $password, $database);
} catch (mysqli_sql_exception $e) {
    if ((int)$e->getCode() === 1049) {
        try {
            hotelRestoreDatabase($host, $username, $password, $database);
            $conn = new mysqli($host, $username, $password, $database);
        } catch (Throwable $restoreErr) {
            die("Database Auto-Restore Failed: " . $restoreErr->getMessage());
        }
    } else {
        die("Database Connection Failed: " . $e->getMessage());
    }
}

// Fallback check when mysqli exceptions are disabled
if ($conn->connect_error) {
    if ((int)$conn->connect_errno === 1049) {
        try {
            hotelRestoreDatabase($host, $username, $password, $database);
            $conn = new mysqli($host, $username, $password, $database);
        } catch (Throwable $restoreErr) {
            die("Database Auto-Restore Failed: " . $restoreErr->getMessage());
        }
    }

    if ($conn->connect_error) {
        die("Database Connection Failed: " . $conn->connect_error);
    }
}

// Set UTF-8
$conn->set_charset("utf8mb4");

// Auto-backup database silently after any POST request (bookings, rooms, users, admin changes)
if (
    PHP_SAPI !== "cli"
    && ($_SERVER["REQUEST_METHOD"] ?? "") === "POST"
    && !defined("HOTEL_AUTO_BACKUP_REGISTERED")
) {
    define("HOTEL_AUTO_BACKUP_REGISTERED", true);
    register_shutdown_function(function () use ($host, $username, $password, $database) {
        try {
            hotelSaveDatabaseBackup($host, $username, $password, $database, false);
        } catch (Throwable $e) {
            error_log("Auto-backup error: " . $e->getMessage());
        }
    });
}
