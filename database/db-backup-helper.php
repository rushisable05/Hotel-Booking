<?php
/**
 * Portable MySQL Backup & Restore Helper (Pure PHP - No XAMPP path dependency)
 */

function hotelExportDatabaseSql(mysqli $conn, string $database): string
{
    $conn->set_charset("utf8mb4");

    $lines = [];
    $lines[] = "-- ============================================";
    $lines[] = "-- Hotel Booking Database Backup";
    $lines[] = "-- Generated: " . date("Y-m-d H:i:s");
    $lines[] = "-- ============================================";
    $lines[] = "";
    $lines[] = "CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;";
    $lines[] = "USE `{$database}`;";
    $lines[] = "";
    $lines[] = "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';";
    $lines[] = "SET FOREIGN_KEY_CHECKS = 0;";
    $lines[] = "";

    $tablesResult = $conn->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'");
    $tables = [];
    while ($row = $tablesResult->fetch_row()) {
        $tables[] = $row[0];
    }

    foreach ($tables as $table) {
        $escapedTable = str_replace("`", "``", $table);
        $createRes = $conn->query("SHOW CREATE TABLE `{$escapedTable}`");
        $createRow = $createRes->fetch_row();

        $lines[] = "-- --------------------------------------------";
        $lines[] = "-- Table structure for `{$escapedTable}`";
        $lines[] = "-- --------------------------------------------";
        $lines[] = "DROP TABLE IF EXISTS `{$escapedTable}`;";
        $lines[] = $createRow[1] . ";";
        $lines[] = "";

        $dataRes = $conn->query("SELECT * FROM `{$escapedTable}`");
        if ($dataRes && $dataRes->num_rows > 0) {
            $fields = [];
            while ($fieldInfo = $dataRes->fetch_field()) {
                $fields[] = "`" . str_replace("`", "``", $fieldInfo->name) . "`";
            }
            $fieldList = implode(", ", $fields);

            while ($dataRow = $dataRes->fetch_row()) {
                $values = [];
                foreach ($dataRow as $val) {
                    if ($val === null) {
                        $values[] = "NULL";
                    } else {
                        $values[] = "'" . $conn->real_escape_string((string)$val) . "'";
                    }
                }
                $lines[] = "INSERT INTO `{$escapedTable}` ({$fieldList}) VALUES (" . implode(", ", $values) . ");";
            }
            $lines[] = "";
        }
    }

    $lines[] = "SET FOREIGN_KEY_CHECKS = 1;";
    $lines[] = "";

    return implode("\n", $lines);
}

function hotelSaveDatabaseBackup(
    string $host,
    string $username,
    string $password,
    string $database,
    bool $saveTimestamped = true
): array {
    $backupDir = __DIR__ . DIRECTORY_SEPARATOR . "backups";
    if (!is_dir($backupDir)) {
        mkdir($backupDir, 0775, true);
    }

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $conn = new mysqli($host, $username, $password, $database);
    $conn->set_charset("utf8mb4");

    $sql = hotelExportDatabaseSql($conn, $database);
    $conn->close();

    // Atomic write to latest backup file
    $latestPath = $backupDir . DIRECTORY_SEPARATOR . "hotel_booking_latest.sql";
    $tmpPath = $latestPath . ".tmp";
    file_put_contents($tmpPath, $sql, LOCK_EX);
    rename($tmpPath, $latestPath);

    $snapshotPath = null;
    if ($saveTimestamped) {
        $snapshotPath = $backupDir . DIRECTORY_SEPARATOR . "hotel_booking_" . date("Y-m-d_H-i-s") . ".sql";
        file_put_contents($snapshotPath, $sql, LOCK_EX);

        // Keep only the latest 20 timestamped snapshots
        $snapshots = glob($backupDir . DIRECTORY_SEPARATOR . "hotel_booking_2*.sql");
        if (is_array($snapshots) && count($snapshots) > 20) {
            sort($snapshots);
            $toDelete = array_slice($snapshots, 0, count($snapshots) - 20);
            foreach ($toDelete as $oldFile) {
                @unlink($oldFile);
            }
        }
    }

    return [
        "latest" => $latestPath,
        "snapshot" => $snapshotPath,
        "bytes" => strlen($sql),
    ];
}

function hotelRestoreDatabase(
    string $host,
    string $username,
    string $password,
    string $database,
    ?string $sqlFile = null
): array {
    $backupDir = __DIR__ . DIRECTORY_SEPARATOR . "backups";
    $latestBackup = $backupDir . DIRECTORY_SEPARATOR . "hotel_booking_latest.sql";
    $baseSchema = __DIR__ . DIRECTORY_SEPARATOR . "hotel-booking.sql";

    if ($sqlFile === null) {
        if (file_exists($latestBackup) && filesize($latestBackup) > 0) {
            $sqlFile = $latestBackup;
        } elseif (file_exists($baseSchema)) {
            $sqlFile = $baseSchema;
        } else {
            throw new RuntimeException("No SQL backup or schema file found.");
        }
    }

    if (!file_exists($sqlFile)) {
        throw new RuntimeException("SQL file not found: " . $sqlFile);
    }

    $sql = file_get_contents($sqlFile);
    if ($sql === false || trim($sql) === "") {
        throw new RuntimeException("SQL file is empty: " . $sqlFile);
    }

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $conn = new mysqli($host, $username, $password);
    $conn->set_charset("utf8mb4");

    $conn->query("CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $conn->select_db($database);

    if (!$conn->multi_query($sql)) {
        throw new RuntimeException("Restore failed: " . $conn->error);
    }

    do {
        if ($res = $conn->store_result()) {
            $res->free();
        }
    } while ($conn->more_results() && $conn->next_result());

    $tablesCount = (int)$conn->query("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '" . $conn->real_escape_string($database) . "'")->fetch_row()[0];
    $conn->close();

    return [
        "source" => $sqlFile,
        "tables" => $tablesCount,
    ];
}
