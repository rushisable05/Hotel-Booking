
<?php
require_once __DIR__ . "/includes/auth.php";

$pageTitle = "Bookings";
$activePage = "bookings";
$statuses = ["Pending", "Confirmed", "Cancelled", "Completed"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    check_csrf();

    $id = (int)($_POST["id"] ?? 0);
    $status = $_POST["status"] ?? "";

    if (in_array($status, $statuses, true)) {
        $stmt = $conn->prepare("UPDATE bookings SET status=? WHERE id=?");
        $stmt->bind_param("si", $status, $id);
        $ok = $stmt->execute();

        flash(
            $ok ? "Booking status updated." : "Unable to update booking.",
            $ok ? "success" : "error"
        );
    } else {
        flash("Invalid booking status.", "error");
    }

    header("Location: bookings.php");
    exit;
}

$q = trim($_GET["q"] ?? "");
$like = "%" . $q . "%";

$stmt = $conn->prepare(
    "SELECT b.*,r.room_no,r.room_type
     FROM bookings b
     LEFT JOIN rooms r ON r.id=b.room_id
     WHERE b.customer_name LIKE ?
        OR b.customer_email LIKE ?
        OR b.customer_phone LIKE ?
        OR CAST(b.id AS CHAR) LIKE ?
     ORDER BY b.id DESC"
);
$stmt->bind_param("ssss", $like, $like, $like, $like);
$stmt->execute();
$bookings = $stmt->get_result();

include __DIR__ . "/includes/header.php";
?>

<div class="heading">
    <div>
        <h2>Booking Management</h2>
        <p class="muted">Manage reservations and guest details.</p>
    </div>
</div>

<form class="toolbar" method="get">
    <input name="q" value="<?= e($q) ?>" placeholder="Search ID, name, email or phone">
    <button class="btn">Search</button>
    <a class="btn alt" href="bookings.php">Reset</a>
</form>

<div class="panel">
<div class="tablewrap">
<table>
<tr>
    <th>ID</th><th>Guest</th><th>Contact</th><th>Room</th>
    <th>Stay</th><th>Guests</th><th>Total</th><th>Status</th><th>Details</th>
</tr>

<?php if ($bookings->num_rows > 0): ?>
<?php while ($b = $bookings->fetch_assoc()): ?>
<tr>
    <td>#<?= (int)$b["id"] ?></td>
    <td><?= e($b["customer_name"] ?? "Guest") ?></td>
    <td>
        <?= e($b["customer_email"] ?? "") ?><br>
        <?= e($b["customer_phone"] ?? "") ?>
    </td>
    <td><?= e($b["room_type"] ?? "Room") ?> / <?= e($b["room_no"] ?? "N/A") ?></td>
    <td><?= e($b["check_in"]) ?><br>to <?= e($b["check_out"]) ?></td>
    <td><?= e($b["guests"]) ?></td>
    <td>₹<?= number_format((float)$b["total_amount"], 2) ?></td>
    <td>
        <span class="badge <?= e(strtolower($b["status"])) ?>">
            <?= e($b["status"]) ?>
        </span>

        <form method="post" style="margin-top:8px;min-width:150px">
            <input type="hidden" name="csrf_token" value="<?= e(csrf()) ?>">
            <input type="hidden" name="id" value="<?= (int)$b["id"] ?>">

            <select name="status" style="width:100%">
                <?php foreach ($statuses as $s): ?>
                    <option value="<?= e($s) ?>" <?= $b["status"] === $s ? "selected" : "" ?>>
                        <?= e($s) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button class="btn" style="margin-top:5px" type="submit">Update</button>
        </form>
    </td>
    <td>
        <a class="btn alt" href="booking-details.php?id=<?= (int)$b["id"] ?>">View Details</a>
    </td>
</tr>
<?php endwhile; ?>
<?php else: ?>
<tr><td colspan="9" class="empty">No bookings found.</td></tr>
<?php endif; ?>
</table>
</div>
</div>

<?php include __DIR__ . "/includes/footer.php"; ?>
