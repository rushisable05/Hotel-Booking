
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/config/database.php";
require_once __DIR__ . "/config/email.php";

/* =========================
   ROOM DETAILS
========================= */

$room_id = (int)($_GET["room_id"] ?? $_POST["room_id"] ?? 0);

if ($room_id <= 0) {
    header("Location: rooms.php");
    exit;
}

$stmt = $conn->prepare("SELECT * FROM rooms WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $room_id);
$stmt->execute();
$room = $stmt->get_result()->fetch_assoc();

if (!$room || $room["status"] !== "Available") {
    http_response_code(404);
    exit("Room is unavailable. Please choose another room.");
}

/* =========================
   DEFAULT VALUES
========================= */

$error = "";
$customer_name = "";
$customer_email = "";
$customer_phone = "";
$destination = "";
$check_in = "";
$check_out = "";
$guests = 1;

$cities = [
    "Ahmedabad",
    "Mumbai",
    "Pune",
    "Goa",
    "Delhi",
    "Jaipur",
    "Bengaluru",
    "Hyderabad",
    "Chennai",
    "Kolkata"
];

/* =========================
   VALIDATE DATE FORMAT
========================= */

function validBookingDate($value) {
    $date = DateTime::createFromFormat("!Y-m-d", $value);
    return $date && $date->format("Y-m-d") === $value;
}

/* =========================
   BOOKING FORM SUBMIT
========================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $customer_name = trim($_POST["customer_name"] ?? "");
    $customer_email = trim($_POST["customer_email"] ?? "");
    $customer_phone = trim($_POST["customer_phone"] ?? "");
    $destination = trim($_POST["destination"] ?? "");
    $check_in = trim($_POST["check_in"] ?? "");
    $check_out = trim($_POST["check_out"] ?? "");
    $guests = (int)($_POST["guests"] ?? 1);

    /* Validate customer information */

    if ($customer_name === "") {
        $error = "Please enter your full name.";
    } elseif (!filter_var($customer_email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (!preg_match("/^[0-9]{10}$/", $customer_phone)) {
        $error = "Please enter a valid 10-digit phone number.";
    } elseif (!in_array($destination, $cities, true)) {
        $error = "Please select a valid destination.";
    } elseif (
        !validBookingDate($check_in) ||
        !validBookingDate($check_out)
    ) {
        $error = "Please select valid check-in and check-out dates.";
    } elseif ($check_in < date("Y-m-d")) {
        $error = "Past dates are not allowed for check-in.";
    } elseif ($check_out <= $check_in) {
        $error = "Check-out must be after check-in.";
    } elseif (
        $guests < 1 ||
        $guests > (int)$room["capacity"]
    ) {
        $error = "This room allows a maximum of "
            . (int)$room["capacity"] . " guests.";
    }

    /* Check overlapping bookings */

    if ($error === "") {
        $availability = $conn->prepare(
            "SELECT id
             FROM bookings
             WHERE room_id = ?
               AND LOWER(status) <> 'cancelled'
               AND check_in < ?
               AND check_out > ?
             LIMIT 1"
        );

        $availability->bind_param(
            "iss",
            $room_id,
            $check_out,
            $check_in
        );

        $availability->execute();

        if ($availability->get_result()->num_rows > 0) {
            $error = "Sorry! This room is already booked for these dates.";
        }
    }

    /* Calculate nights and price */

    if ($error === "") {
        $date1 = new DateTime($check_in);
        $date2 = new DateTime($check_out);
        $nights = (int)$date1->diff($date2)->days;

        if ($nights < 1) {
            $error = "Invalid booking duration.";
        } else {
            $total_amount = $nights * (float)$room["price"];
        }
    }

    /* Save booking */

    if ($error === "") {

        $bookingStmt = $conn->prepare(
            "INSERT INTO bookings
            (
                user_id,
                room_id,
                customer_name,
                customer_email,
                customer_phone,
                destination,
                check_in,
                check_out,
                guests,
                total_amount,
                status
            )
            VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Confirmed')"
        );

        if (!$bookingStmt) {
            $error = "Database error: " . $conn->error;
        } else {

            $bookingStmt->bind_param(
                "issssssid",
                $room_id,
                $customer_name,
                $customer_email,
                $customer_phone,
                $destination,
                $check_in,
                $check_out,
                $guests,
                $total_amount
            );

            if ($bookingStmt->execute()) {

                $booking_id = (int)$conn->insert_id;

                /* Send confirmation email */

                $emailSent = false;

                try {
                    $emailSent = sendBookingEmail(
                        $customer_email,
                        $customer_name,
                        $booking_id,
                        $room["room_type"],
                        $room["room_no"],
                        $check_in,
                        $check_out,
                        $guests,
                        $nights,
                        $total_amount
                    );
                } catch (Throwable $e) {
                    error_log("Booking email error: " . $e->getMessage());
                }

                /* Redirect to ticket */

                $emailStatus = $emailSent ? "sent" : "failed";

                header(
                    "Location: booking-success.php?id="
                    . $booking_id
                    . "&email="
                    . $emailStatus
                );
                exit;

            } else {
                $error = "Booking could not be saved. Please try again.";
                error_log("Booking database error: " . $bookingStmt->error);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Book Your Room | Hotel Booking</title>

<style>
* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    font-family: Arial, Helvetica, sans-serif;
    background: linear-gradient(135deg, #eaf6ff, #f8fcff);
    color: #164e63;
    min-height: 100vh;
}

.header {
    background: linear-gradient(110deg, #101d38, #24478d);
    color: white;
    padding: 18px 6%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 15px;
    box-shadow: 0 4px 18px #0002;
}

.logo {
    color: white;
    text-decoration: none;
    font-size: 25px;
    font-weight: bold;
}

.nav {
    display: flex;
    gap: 22px;
    align-items: center;
    flex-wrap: wrap;
}

.nav a {
    color: white;
    text-decoration: none;
    font-weight: 600;
}

.nav a:hover {
    color: #93c5fd;
}

.container {
    width: 92%;
    max-width: 850px;
    margin: 40px auto;
}

.booking-card {
    background: white;
    padding: 32px;
    border-radius: 22px;
    box-shadow: 0 12px 35px #2196f326;
}

.title {
    text-align: center;
    font-size: 32px;
    margin-bottom: 8px;
    color: #164e63;
}

.subtitle {
    text-align: center;
    color: #64748b;
    line-height: 1.6;
    margin-bottom: 25px;
}

.room-box {
    background: linear-gradient(135deg, #eaf6ff, white);
    border: 1px solid #cbd5e1;
    border-radius: 15px;
    padding: 22px;
    margin-bottom: 24px;
}

.room-box h2 {
    color: #1565c0;
    margin-bottom: 12px;
}

.room-box p {
    margin: 8px 0;
    color: #64748b;
    line-height: 1.5;
}

.price {
    color: #2196f3;
    font-size: 25px;
    font-weight: bold;
    margin-top: 12px;
}

.price small {
    font-size: 14px;
    color: #64748b;
    font-weight: normal;
}

.email-info {
    background: #f0f9ff;
    border: 1px solid #bae6fd;
    color: #075985;
    padding: 14px;
    border-radius: 10px;
    margin-bottom: 22px;
    line-height: 1.6;
}

.error {
    background: #fff1f2;
    border: 1px solid #fecdd3;
    color: #be123c;
    padding: 14px;
    border-radius: 10px;
    margin-bottom: 20px;
    line-height: 1.5;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 18px;
}

.form-group {
    margin-bottom: 19px;
}

.form-group label {
    display: block;
    font-weight: bold;
    margin-bottom: 8px;
}

.form-group input,
.form-group select {
    width: 100%;
    padding: 13px;
    border: 1px solid #cbd5e1;
    border-radius: 10px;
    background: white;
    color: #164e63;
    font-size: 15px;
    min-height: 47px;
}

.form-group input:focus,
.form-group select:focus {
    outline: none;
    border-color: #2196f3;
    box-shadow: 0 0 0 3px #2196f320;
}

.book-btn {
    width: 100%;
    padding: 15px;
    border: none;
    border-radius: 11px;
    background: linear-gradient(135deg, #2196f3, #1565c0);
    color: white;
    font-size: 17px;
    font-weight: bold;
    cursor: pointer;
    transition: 0.2s;
}

.book-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px #2196f34d;
}

.back-link {
    display: block;
    text-align: center;
    margin-top: 22px;
    color: #2196f3;
    text-decoration: none;
    font-weight: bold;
}

.footer {
    text-align: center;
    padding: 24px;
    background: #164e63;
    color: white;
    margin-top: 35px;
}

@media (max-width: 650px) {
    .header {
        justify-content: center;
        text-align: center;
    }

    .nav {
        justify-content: center;
    }

    .booking-card {
        padding: 22px;
    }

    .title {
        font-size: 27px;
    }

    .form-row {
        grid-template-columns: 1fr;
        gap: 0;
    }
}
</style>
</head>

<body>

<header class="header">
    <a class="logo" href="index.php">🏨 Hotel Booking</a>

    <nav class="nav">
        <a href="index.php">Home</a>
        <a href="rooms.php">Rooms</a>
        <a href="check-availability.php">Availability</a>

        <?php if (isset($_SESSION["user_id"])): ?>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
        <?php endif; ?>
    </nav>
</header>

<main class="container">
<div class="booking-card">

    <h1 class="title">🎫 Book Your Room</h1>
    <p class="subtitle">
        Choose your destination and dates, then enter your details.
    </p>

    <section class="room-box">
        <h2><?= htmlspecialchars($room["room_type"]) ?> Room</h2>
        <p>
            <strong>Room Number:</strong>
            <?= htmlspecialchars($room["room_no"]) ?>
        </p>
        <p>
            <strong>Capacity:</strong>
            <?= (int)$room["capacity"] ?> guests
        </p>
        <p>
            <?= nl2br(htmlspecialchars($room["description"] ?? "")) ?>
        </p>
        <div class="price">
            ₹<?= number_format((float)$room["price"], 2) ?>
            <small>/ night</small>
        </div>
    </section>

    <div class="email-info">
        📧 Booking confirmation and ticket will be sent to the email
        address you enter below.
    </div>

    <?php if ($error !== ""): ?>
        <div class="error">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="post" action="booking.php?room_id=<?= $room_id ?>">

        <input type="hidden" name="room_id" value="<?= $room_id ?>">

        <div class="form-group">
            <label for="destination">📍 Destination City *</label>
            <select id="destination" name="destination" required>
                <option value="">-- Select destination --</option>

                <?php foreach ($cities as $city): ?>
                    <option
                        value="<?= htmlspecialchars($city) ?>"
                        <?= $destination === $city ? "selected" : "" ?>
                    >
                        <?= htmlspecialchars($city) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="customer_name">Full Name *</label>
            <input
                type="text"
                id="customer_name"
                name="customer_name"
                value="<?= htmlspecialchars($customer_name) ?>"
                maxlength="100"
                autocomplete="name"
                required
            >
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="customer_email">Email Address *</label>
                <input
                    type="email"
                    id="customer_email"
                    name="customer_email"
                    value="<?= htmlspecialchars($customer_email) ?>"
                    maxlength="190"
                    autocomplete="email"
                    required
                >
            </div>

            <div class="form-group">
                <label for="customer_phone">Phone Number *</label>
                <input
                    type="tel"
                    id="customer_phone"
                    name="customer_phone"
                    value="<?= htmlspecialchars($customer_phone) ?>"
                    pattern="[0-9]{10}"
                    maxlength="10"
                    inputmode="numeric"
                    autocomplete="tel"
                    placeholder="10-digit mobile number"
                    required
                >
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="check_in">📅 Check-in Date *</label>
                <input
                    type="date"
                    id="check_in"
                    name="check_in"
                    value="<?= htmlspecialchars($check_in) ?>"
                    min="<?= date("Y-m-d") ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label for="check_out">📅 Check-out Date *</label>
                <input
                    type="date"
                    id="check_out"
                    name="check_out"
                    value="<?= htmlspecialchars($check_out) ?>"
                    min="<?= date("Y-m-d", strtotime("+1 day")) ?>"
                    required
                >
            </div>
        </div>

        <div class="form-group">
            <label for="guests">Number of Guests *</label>
            <select id="guests" name="guests" required>
                <?php for ($i = 1; $i <= (int)$room["capacity"]; $i++): ?>
                    <option
                        value="<?= $i ?>"
                        <?= $guests === $i ? "selected" : "" ?>
                    >
                        <?= $i ?> <?= $i === 1 ? "Guest" : "Guests" ?>
                    </option>
                <?php endfor; ?>
            </select>
        </div>

        <button class="book-btn" type="submit">
            Confirm Booking →
        </button>

    </form>

    <a class="back-link" href="rooms.php">← Back to Rooms</a>

</div>
</main>

<footer class="footer">
    <p>© <?= date("Y") ?> Hotel Booking System. All rights reserved.</p>
</footer>

<script>
const checkInInput = document.getElementById("check_in");
const checkOutInput = document.getElementById("check_out");

function updateCheckoutMin() {
    if (!checkInInput.value) {
        checkOutInput.min = "<?= date("Y-m-d", strtotime("+1 day")) ?>";
        return;
    }

    const selectedDate = new Date(checkInInput.value + "T12:00:00");
    selectedDate.setDate(selectedDate.getDate() + 1);

    const year = selectedDate.getFullYear();
    const month = String(selectedDate.getMonth() + 1).padStart(2, "0");
    const day = String(selectedDate.getDate()).padStart(2, "0");

    const minimum = `${year}-${month}-${day}`;

    checkOutInput.min = minimum;

    if (checkOutInput.value && checkOutInput.value < minimum) {
        checkOutInput.value = minimum;
    }
}

checkInInput.addEventListener("change", updateCheckoutMin);
updateCheckoutMin();
</script>

</body>
</html>
