
<?php
require_once __DIR__ . "/includes/auth.php";

$pageTitle = "Booking Details";
$activePage = "bookings";
$id = (int)($_GET["id"] ?? 0);

$stmt = $conn->prepare(
    "SELECT b.*,r.room_no,r.room_type,r.price,r.capacity
     FROM bookings b
     LEFT JOIN rooms r ON r.id=b.room_id
     WHERE b.id=?"
);
$stmt->bind_param("i", $id);
$stmt->execute();
$b = $stmt->get_result()->fetch_assoc();

include __DIR__ . "/includes/header.php";
?>

<div class="heading">
    <h2>Booking #<?= $id ?></h2>
    <a class="btn alt" href="bookings.php">← All Bookings</a>
</div>

<?php if (!$b): ?>
    <div class="panel empty">Booking not found. Please check the booking ID.</div>
<?php else: ?>

<div class="panel">
    <h3>👤 Guest Information</h3>
    <div class="detailgrid">
        <?php
        $guestFields = [
            "Customer Name" => $b["customer_name"] ?? "Guest",
            "Email" => $b["customer_email"] ?? "N/A",
            "Phone" => $b["customer_phone"] ?? "N/A",
            "Linked User ID" => $b["user_id"] ?? "Guest booking"
        ];

        foreach ($guestFields as $label => $value):
        ?>
            <div class="detail">
                <small><?= e($label) ?></small>
                <strong><?= e($value) ?></strong>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="panel">
    <h3>🛏 Stay Information</h3>
    <div class="detailgrid">
        <?php
        $stayFields = [
            "Room Number" => $b["room_no"] ?? "N/A",
            "Room Type" => $b["room_type"] ?? "N/A",
            "Room Price/Night" => "₹" . number_format((float)($b["price"] ?? 0), 2),
            "Check-in" => $b["check_in"],
            "Check-out" => $b["check_out"],
            "Number of Guests" => $b["guests"],
            "Booking Status" => $b["status"],
            "Total Amount" => "₹" . number_format((float)$b["total_amount"], 2)
        ];

        foreach ($stayFields as $label => $value):
        ?>
            <div class="detail">
                <small><?= e($label) ?></small>
                <strong><?= e($value) ?></strong>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php endif; ?>

<?php include __DIR__ . "/includes/footer.php"; ?>
