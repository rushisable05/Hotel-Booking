
<?php
require_once __DIR__ . "/includes/auth.php";

$pageTitle = "Customers";
$activePage = "customers";

$q = trim($_GET["q"] ?? "");
$like = "%" . $q . "%";

$stmt = $conn->prepare(
    "SELECT customer_email,
            MAX(customer_name) AS customer_name,
            MAX(customer_phone) AS customer_phone,
            COUNT(*) AS total_bookings,
            SUM(
                CASE WHEN LOWER(status)<>'cancelled'
                THEN total_amount ELSE 0 END
            ) AS booking_value,
            MAX(id) AS latest_booking
     FROM bookings
     WHERE customer_email IS NOT NULL
       AND customer_email<>''
       AND (
            customer_name LIKE ?
            OR customer_email LIKE ?
            OR customer_phone LIKE ?
       )
     GROUP BY customer_email
     ORDER BY latest_booking DESC"
);
$stmt->bind_param("sss", $like, $like, $like);
$stmt->execute();
$customers = $stmt->get_result();

include __DIR__ . "/includes/header.php";
?>

<div class="heading">
    <div>
        <h2>Customer Directory</h2>
        <p class="muted">Guest contact details and booking overview.</p>
    </div>
</div>

<form class="toolbar" method="get">
    <input name="q" value="<?= e($q) ?>" placeholder="Search name, email or phone">
    <button class="btn">Search</button>
    <a class="btn alt" href="customers.php">Reset</a>
</form>

<div class="panel">
<div class="tablewrap">
<table>
<tr>
    <th>Customer Name</th><th>Email</th><th>Phone</th>
    <th>Bookings</th><th>Booking Value</th><th>Latest Booking</th><th>Action</th>
</tr>

<?php if ($customers->num_rows > 0): ?>
<?php while ($c = $customers->fetch_assoc()): ?>
<tr>
    <td><?= e($c["customer_name"] ?? "Guest") ?></td>
    <td><?= e($c["customer_email"]) ?></td>
    <td><?= e($c["customer_phone"] ?? "N/A") ?></td>
    <td><?= (int)$c["total_bookings"] ?></td>
    <td>₹<?= number_format((float)$c["booking_value"], 2) ?></td>
    <td>#<?= (int)$c["latest_booking"] ?></td>
    <td>
        <a class="btn alt" href="booking-details.php?id=<?= (int)$c["latest_booking"] ?>">
            View Booking
        </a>
    </td>
</tr>
<?php endwhile; ?>
<?php else: ?>
<tr><td colspan="7" class="empty">No customers found.</td></tr>
<?php endif; ?>
</table>
</div>
</div>

<?php include __DIR__ . "/includes/footer.php"; ?>
