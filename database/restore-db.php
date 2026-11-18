<?php
if (PHP_SAPI !== "cli") {
    http_response_code(403);
    exit("Run this script from CLI or restore-db.bat.");
}

require_once __DIR__ . "/db-backup-helper.php";

$host = getenv("DB_HOST") ?: "localhost";
$username = getenv("DB_USER") ?: "root";
$password = getenv("DB_PASS") !== false ? getenv("DB_PASS") : "";
$database = getenv("DB_NAME") ?: "hotel_booking";

$onlyIfMissing = in_array("--if-missing", $argv, true);
$customFile = null;
foreach (array_slice($argv, 1) as $arg) {
    if ($arg !== "--if-missing") {
        $customFile = $arg;
        break;
    }
}

try {
    if ($onlyIfMissing) {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        $checkConn = new mysqli($host, $username, $password);
        $res = $checkConn->query("SHOW DATABASES LIKE '" . $checkConn->real_escape_string($database) . "'");
        $exists = $res && $res->num_rows > 0;
        if ($exists) {
            $checkConn->select_db($database);
            $tablesRes = $checkConn->query("SHOW TABLES");
            $hasTables = $tablesRes && $tablesRes->num_rows > 0;
            $checkConn->close();
            if ($hasTables) {
                echo "[OK] Database '{$database}' already exists and is ready.\n";
                exit(0);
            }
        } else {
            $checkConn->close();
        }
        echo "[INFO] Database '{$database}' not found or empty. Restoring from backup...\n";
    }

    $result = hotelRestoreDatabase($host, $username, $password, $database, $customFile);
    echo "[OK] Database '{$database}' restored successfully!\n";
    echo "Source: " . $result["source"] . "\n";
    echo "Tables: " . $result["tables"] . "\n";
} catch (Throwable $e) {
    fwrite(STDERR, "[ERROR] Restore failed: " . $e->getMessage() . "\n");
    exit(1);
}
