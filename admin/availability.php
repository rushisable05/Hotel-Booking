
<?php
require_once __DIR__ . "/includes/auth.php";

$pageTitle = "Room Availability";
$activePage = "availability";

$checkIn = $_GET["check_in"] ?? date("Y-m-d");
$checkOut = $_GET["check_out"] ?? date("Y-m-d", strtotime("+1 day"));

$error = "";
$rooms = null;

$isValidDate = function ($date) {
    $d = DateTime::createFromFormat("!Y-m-d", $date);
    return $d && $d->format("Y-m-d") === $date;
};

if (
    !$isValidDate($checkIn) ||
    !$isValidDate($checkOut) ||
    $checkOut <= $checkIn
) {
    $error = "Please select valid dates. Check-out must be after check-in.";
} else {
    $stmt = $conn->prepare(
        "SELECT r.*,
            CASE
                WHEN r.status='Maintenance' THEN 'Maintenance'
                WHEN EXISTS (
                    SELECT 1
                    FROM bookings b
                    WHERE b.room_id=r.id
                      AND LOWER(b.status)<>'cancelled'
                      AND b.check_in < ?
                      AND b.check_out > ?
                ) THEN 'Booked'
                ELSE 'Available'
            END AS date_status
         FROM rooms r
         ORDER BY r.room_no"
    );

    $stmt->bind_param("ss", $checkOut, $checkIn);
    $stmt->execute();
    $rooms = $stmt->get_result();
}

include __DIR__ . "/includes/header.php";
?>

<div class="heading">
    <div>
        <h2>Room Availability</h2>
        <p class="muted">Check room availability for specific dates.</p>
    </div>
</div>

<div class="panel">
    <form method="get" class="formgrid">
        <div class="field">
            <label>Check-in Date</label>
            <input type="date" name="check_in" value="<?= e($checkIn) ?>" required>
        </div>

        <div class="field">
            <label>Check-out Date</label>
            <input type="date" name="check_out" value="<?= e($checkOut) ?>" required>
        </div>

        <div class="field full">
            <button class="btn" type="submit">Check Availability</button>
        </div>
    </form>
</div>

<?php if ($error): ?>
    <div class="alert error"><?= e($error) ?></div>
<?php elseif ($rooms): ?>

<div class="panel">
    <h3><?= e($checkIn) ?> → <?= e($checkOut) ?></h3>

    <div class="tablewrap">
    <table>
        <tr>
            <th>Room Number</th>
            <th>Room Type</th>
            <th>Capacity</th>
            <th>Price/Night</th>
            <th>Room Status</th>
            <th>Selected Dates</th>
        </tr>

        <?php while ($r = $rooms->fetch_assoc()): ?>
        <tr>
            <td><?= e($r["room_no"]) ?></td>
            <td><?= e($r["room_type"]) ?></td>
            <td><?= e($r["capacity"]) ?></td>
            <td>₹<?= number_format((float)$r["price"], 2) ?></td>
            <td>
                <span class="badge <?= e(strtolower($r["status"])) ?>">
                    <?= e($r["status"]) ?>
                </span>
            </td>
            <td>
                <span class="badge <?= e(strtolower($r["date_status"])) ?>">
                    <?= e($r["date_status"]) ?>
                </span>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
    </div>

    <p class="muted">
        Availability is calculated from booking dates. Cancelled bookings
        do not block a room. Maintenance rooms remain unavailable.
    </p>
</div>

<?php endif; ?>

<?php include __DIR__ . "/includes/footer.php"; ?>
