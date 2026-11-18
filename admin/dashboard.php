
<?php
require_once __DIR__ . "/includes/auth.php";

$pageTitle = "Dashboard";
$activePage = "dashboard";

function getCount($conn, $sql) {
    $result = $conn->query($sql);
    if (!$result) return 0;
    $row = $result->fetch_row();
    return $row[0] ?? 0;
}

$totalRooms = getCount($conn, "SELECT COUNT(*) FROM rooms");
$availableRooms = getCount(
    $conn,
    "SELECT COUNT(*) FROM rooms WHERE status='Available'"
);
$totalBookings = getCount($conn, "SELECT COUNT(*) FROM bookings");
$pendingBookings = getCount(
    $conn,
    "SELECT COUNT(*) FROM bookings WHERE status='Pending'"
);
$totalCustomers = getCount(
    $conn,
    "SELECT COUNT(DISTINCT customer_email)
     FROM bookings
     WHERE customer_email IS NOT NULL AND customer_email<>''"
);
$revenue = getCount(
    $conn,
    "SELECT COALESCE(SUM(total_amount),0)
     FROM bookings
     WHERE LOWER(status) IN ('confirmed','completed')"
);

$recent = $conn->query(
    "SELECT b.id,b.customer_name,b.customer_email,
            b.check_in,b.check_out,b.total_amount,b.status,
            r.room_no,r.room_type
     FROM bookings b
     LEFT JOIN rooms r ON r.id=b.room_id
     ORDER BY b.id DESC LIMIT 8"
);

include __DIR__ . "/includes/header.php";
?>

<div class="heading">
    <div>
        <h2>Welcome back, <?= e($_SESSION["admin_name"]) ?> 👋</h2>
        <p class="muted">Here is your hotel's latest overview.</p>
    </div>
    <a class="btn" href="add-room.php">＋ Add Room</a>
</div>

<div class="grid">
    <div class="card">
        <span class="muted">Total Rooms</span>
        <div class="stat"><?= e($totalRooms) ?></div>🛏 Room inventory
    </div>
    <div class="card">
        <span class="muted">Available Rooms</span>
        <div class="stat"><?= e($availableRooms) ?></div>✅ Inventory status
    </div>
    <div class="card">
        <span class="muted">Total Bookings</span>
        <div class="stat"><?= e($totalBookings) ?></div>📅 Reservations
    </div>
    <div class="card">
        <span class="muted">Pending Bookings</span>
        <div class="stat"><?= e($pendingBookings) ?></div>⏳ Need attention
    </div>
    <div class="card">
        <span class="muted">Customers</span>
        <div class="stat"><?= e($totalCustomers) ?></div>👥 Unique guest emails
    </div>
    <div class="card">
        <span class="muted">Confirmed Revenue</span>
        <div class="stat">₹<?= number_format((float)$revenue, 2) ?></div>💰 Confirmed/completed
    </div>
</div>

<div class="panel">
    <div class="heading">
        <h2>Recent Bookings</h2>
        <a class="btn alt" href="bookings.php">View All</a>
    </div>

    <div class="tablewrap">
        <table>
            <tr>
                <th>ID</th><th>Guest</th><th>Room</th>
                <th>Stay</th><th>Amount</th><th>Status</th><th>Action</th>
            </tr>

            <?php if ($recent && $recent->num_rows > 0): ?>
                <?php while ($b = $recent->fetch_assoc()): ?>
                <tr>
                    <td>#<?= e($b["id"]) ?></td>
                    <td>
                        <?= e($b["customer_name"] ?? "Guest") ?><br>
                        <?= e($b["customer_email"] ?? "") ?>
                    </td>
                    <td><?= e($b["room_type"] ?? "Room") ?> / <?= e($b["room_no"] ?? "N/A") ?></td>
                    <td><?= e($b["check_in"]) ?> → <?= e($b["check_out"]) ?></td>
                    <td>₹<?= number_format((float)$b["total_amount"], 2) ?></td>
                    <td><span class="badge <?= e(strtolower($b["status"])) ?>"><?= e($b["status"]) ?></span></td>
                    <td><a class="btn alt" href="booking-details.php?id=<?= (int)$b["id"] ?>">Details</a></td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="7" class="empty">No bookings yet.</td></tr>
            <?php endif; ?>
        </table>
    </div>
</div>

<?php include __DIR__ . "/includes/footer.php"; ?>
