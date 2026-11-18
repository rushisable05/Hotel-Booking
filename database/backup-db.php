<?php
if (PHP_SAPI !== "cli") {
    http_response_code(403);
    exit("Run this script from CLI or backup-db.bat.");
}

require_once __DIR__ . "/db-backup-helper.php";

$host = getenv("DB_HOST") ?: "localhost";
$username = getenv("DB_USER") ?: "root";
$password = getenv("DB_PASS") !== false ? getenv("DB_PASS") : "";
$database = getenv("DB_NAME") ?: "hotel_booking";

try {
    $result = hotelSaveDatabaseBackup($host, $username, $password, $database, true);
    echo "[OK] Database backup saved!\n";
    echo "Latest:   " . $result["latest"] . "\n";
    echo "Snapshot: " . $result["snapshot"] . "\n";
    echo "Size:     " . number_format($result["bytes"]) . " bytes\n";
} catch (Throwable $e) {
    fwrite(STDERR, "[ERROR] Backup failed: " . $e->getMessage() . "\n");
    exit(1);
}
