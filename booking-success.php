
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once "config/database.php";

$id = (int)($_GET["id"] ?? 0);

if ($id <= 0) {
    die("Invalid booking ID.");
}

$stmt = $conn->prepare("
    SELECT b.*, r.room_no, r.room_type
    FROM bookings b
    INNER JOIN rooms r ON b.room_id = r.id
    WHERE b.id = ?
");

if (!$stmt) {
    die("Database query error: " . $conn->error);
}

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();
$booking = $result->fetch_assoc();

if (!$booking) {
    die("Booking not found. Please check booking ID: " . $id);
}

include "includes/header.php";
?>

<style>
.success-page {
    max-width: 750px;
    margin: 45px auto;
    padding: 30px 20px;
}
.success-card {
    background: white;
    padding: 35px;
    border-radius: 18px;
    box-shadow: 0 8px 30px #00000012;
}
.success-card h1 {
    color: #15803d;
    text-align: center;
}
.success-card .intro {
    text-align: center;
    color: #64748b;
}
.booking-row {
    display: flex;
    justify-content: space-between;
    gap: 15px;
    padding: 13px 0;
    border-bottom: 1px solid #e2e8f0;
}
.booking-row span:first-child {
    color: #64748b;
}
.booking-row span:last-child {
    font-weight: 700;
    text-align: right;
    overflow-wrap: anywhere;
}
.booking-total {
    color: #15803d;
    font-size: 21px;
}
.success-actions {
    display: flex;
    justify-content: center;
    gap: 12px;
    flex-wrap: wrap;
    margin-top: 25px;
}
.success-actions a {
    display: inline-block;
    padding: 12px 20px;
    background: #2563eb;
    color: white;
    text-decoration: none;
    border-radius: 9px;
}
@media (max-width: 500px) {
    .success-card { padding: 22px 16px; }
    .booking-row { font-size: 14px; }
}
</style>

<div class="success-page">
    <div class="success-card">

        <h1>🎉 Booking Successful!</h1>
        <p class="intro">
            Thank you for choosing our hotel.
            Your booking details are below.
        </p>

        <h3>📋 Booking Details</h3>

        <div class="booking-row">
            <span>Booking ID</span>
            <span>#<?= htmlspecialchars((string)$booking["id"]) ?></span>
        </div>

        <div class="booking-row">
            <span>Customer Name</span>
            <span><?= htmlspecialchars($booking["customer_name"] ?? "N/A") ?></span>
        </div>

        <div class="booking-row">
            <span>Email</span>
            <span><?= htmlspecialchars($booking["customer_email"] ?? "N/A") ?></span>
        </div>

        <div class="booking-row">
            <span>Phone</span>
            <span><?= htmlspecialchars($booking["customer_phone"] ?? "N/A") ?></span>
        </div>

        <div class="booking-row">
            <span>Room Type</span>
            <span><?= htmlspecialchars($booking["room_type"]) ?></span>
        </div>

        <div class="booking-row">
            <span>Room Number</span>
            <span><?= htmlspecialchars((string)$booking["room_no"]) ?></span>
        </div>

        <div class="booking-row">
            <span>Check-in</span>
            <span><?= htmlspecialchars($booking["check_in"]) ?></span>
        </div>

        <div class="booking-row">
            <span>Check-out</span>
            <span><?= htmlspecialchars($booking["check_out"]) ?></span>
        </div>

        <div class="booking-row">
            <span>Guests</span>
            <span><?= htmlspecialchars((string)$booking["guests"]) ?></span>
        </div>

        <div class="booking-row">
            <span>Status</span>
            <span><?= htmlspecialchars($booking["status"]) ?></span>
        </div>

        <div class="booking-row">
            <span>Total Amount</span>
            <span class="booking-total">
                ₹<?= number_format((float)$booking["total_amount"], 2) ?>
            </span>
        </div>

        <div class="success-actions">
            <a href="rooms.php">🏨 Browse Rooms</a>
            <a href="index.php">🏠 Home</a>
        </div>

    </div>
</div>

<?php
include "includes/chatbot.php";
include "includes/footer.php";
?>
