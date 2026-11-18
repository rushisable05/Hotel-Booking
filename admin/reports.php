
<?php
require_once __DIR__ . "/includes/auth.php";

$pageTitle = "Reports & Analytics";
$activePage = "reports";

function reportValue($conn, $sql) {
    $result = $conn->query($sql);
    if (!$result) return 0;
    $row = $result->fetch_row();
    return $row[0] ?? 0;
}

$totalBookings = reportValue(
    $conn,
    "SELECT COUNT(*) FROM bookings"
);

$confirmedBookings = reportValue(
    $conn,
    "SELECT COUNT(*) FROM bookings WHERE LOWER(status)='confirmed'"
);

$cancelledBookings = reportValue(
    $conn,
    "SELECT COUNT(*) FROM bookings WHERE LOWER(status)='cancelled'"
);

$totalRevenue = reportValue(
    $conn,
    "SELECT COALESCE(SUM(total_amount),0)
     FROM bookings
     WHERE LOWER(status) IN ('confirmed','completed')"
);

$monthly = $conn->query(
    "SELECT DATE_FORMAT(check_in,'%Y-%m') AS month_label,
            COUNT(*) AS bookings,
            SUM(
                CASE WHEN LOWER(status) IN ('confirmed','completed')
                THEN total_amount ELSE 0 END
            ) AS revenue
     FROM bookings
     WHERE check_in >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
     GROUP BY DATE_FORMAT(check_in,'%Y-%m')
     ORDER BY month_label DESC"
);

$popular = $conn->query(
    "SELECT r.room_no,r.room_type,COUNT(b.id) AS booking_count
     FROM rooms r
     LEFT JOIN bookings b
       ON b.room_id=r.id
      AND LOWER(b.status)<>'cancelled'
     GROUP BY r.id,r.room_no,r.room_type
     ORDER BY booking_count DESC
     LIMIT 10"
);

include __DIR__ . "/includes/header.php";
?>

<div class="heading">
    <div>
        <h2>Reports & Analytics</h2>
        <p class="muted">Review your hotel's booking performance.</p>
    </div>
</div>

<div class="grid">
    <div class="card">
        <span class="muted">All Bookings</span>
        <div class="stat"><?= e($totalBookings) ?></div>
    </div>

    <div class="card">
        <span class="muted">Confirmed Bookings</span>
        <div class="stat"><?= e($confirmedBookings) ?></div>
    </div>

    <div class="card">
        <span class="muted">Cancelled Bookings</span>
        <div class="stat"><?= e($cancelledBookings) ?></div>
    </div>

    <div class="card">
        <span class="muted">Confirmed Revenue</span>
        <div class="stat">₹<?= number_format((float)$totalRevenue, 2) ?></div>
    </div>
</div>

<div class="panel">
    <h3>📅 Monthly Booking Summary</h3>

    <div class="tablewrap">
    <table>
        <tr><th>Month</th><th>Bookings</th><th>Revenue</th></tr>

        <?php if ($monthly && $monthly->num_rows > 0): ?>
            <?php while ($m = $monthly->fetch_assoc()): ?>
            <tr>
                <td><?= e($m["month_label"]) ?></td>
                <td><?= (int)$m["bookings"] ?></td>
                <td>₹<?= number_format((float)$m["revenue"], 2) ?></td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="3" class="empty">No booking data available.</td></tr>
        <?php endif; ?>
    </table>
    </div>
</div>

<div class="panel">
    <h3>🏆 Most Booked Rooms</h3>

    <div class="tablewrap">
    <table>
        <tr><th>Room Number</th><th>Room Type</th><th>Bookings</th></tr>

        <?php if ($popular && $popular->num_rows > 0): ?>
            <?php while ($p = $popular->fetch_assoc()): ?>
            <tr>
                <td><?= e($p["room_no"]) ?></td>
                <td><?= e($p["room_type"]) ?></td>
                <td><?= (int)$p["booking_count"] ?></td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="3" class="empty">No rooms available.</td></tr>
        <?php endif; ?>
    </table>
    </div>
</div>

<?php include __DIR__ . "/includes/footer.php"; ?>
