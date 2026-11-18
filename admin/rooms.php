
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../config/database.php";

// Admin login check
if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

// CSRF token for delete action
if (empty($_SESSION["rooms_csrf_token"])) {
    $_SESSION["rooms_csrf_token"] = bin2hex(random_bytes(32));
}

$cities = [
    "Ahmedabad", "Mumbai", "Pune", "Goa", "Delhi",
    "Jaipur", "Bengaluru", "Hyderabad", "Chennai", "Kolkata"
];

$roomTypes = [
    "Standard", "Deluxe", "Super Deluxe",
    "Executive", "Suite", "Family Room"
];

$errors = [];
$success = "";

// Escape HTML output
function e($value)
{
    return htmlspecialchars(
        (string)$value,
        ENT_QUOTES,
        "UTF-8"
    );
}

// Delete room
if ($_SERVER["REQUEST_METHOD"] === "POST"
    && isset($_POST["delete_room"])) {

    $token = $_POST["csrf_token"] ?? "";
    $roomId = filter_var(
        $_POST["room_id"] ?? "",
        FILTER_VALIDATE_INT
    );

    if (
        !is_string($token) ||
        !hash_equals($_SESSION["rooms_csrf_token"], $token)
    ) {
        $errors[] = "Security token invalid. Please refresh the page.";

    } elseif ($roomId === false || $roomId < 1) {
        $errors[] = "Invalid room ID.";

    } else {
        try {
            // Keep existing bookings safe:
            // do not delete a room that has bookings.
            $check = $conn->prepare(
                "SELECT id FROM bookings WHERE room_id = ? LIMIT 1"
            );

            $check->bind_param("i", $roomId);
            $check->execute();
            $check->store_result();

            $hasBookings = $check->num_rows > 0;
            $check->close();

            if ($hasBookings) {
                $errors[] =
                    "This room has booking records. You cannot delete it.";
            } else {
                $stmt = $conn->prepare(
                    "DELETE FROM rooms WHERE id = ?"
                );

                $stmt->bind_param("i", $roomId);
                $stmt->execute();

                if ($stmt->affected_rows > 0) {
                    $stmt->close();

                    header("Location: rooms.php?deleted=1");
                    exit;
                }

                $stmt->close();
                $errors[] = "Room not found or already deleted.";
            }

        } catch (Throwable $ex) {
            error_log("Delete room error: " . $ex->getMessage());
            $errors[] =
                "Could not delete room. Check the database and booking table.";
        }
    }
}

if (isset($_GET["added"]) && $_GET["added"] === "1") {
    $success = "Room added successfully!";
}

if (isset($_GET["updated"]) && $_GET["updated"] === "1") {
    $success = "Room updated successfully!";
}

if (isset($_GET["deleted"]) && $_GET["deleted"] === "1") {
    $success = "Room deleted successfully!";
}

// Search and filters
$search = trim($_GET["search"] ?? "");
$destination = trim($_GET["destination"] ?? "");
$roomType = trim($_GET["room_type"] ?? "");
$status = trim($_GET["status"] ?? "");

if (!in_array($destination, $cities, true)) {
    $destination = "";
}

if (!in_array($roomType, $roomTypes, true)) {
    $roomType = "";
}

if (!in_array($status, ["Available", "Maintenance"], true)) {
    $status = "";
}

$where = [];
$where[] = "1=1";

if ($search !== "") {
    $safeSearch = $conn->real_escape_string($search);

    $where[] = "(
        room_no LIKE '%$safeSearch%'
        OR description LIKE '%$safeSearch%'
        OR destination LIKE '%$safeSearch%'
        OR room_type LIKE '%$safeSearch%'
    )";
}

if ($destination !== "") {
    $safeDestination = $conn->real_escape_string($destination);
    $where[] = "destination = '$safeDestination'";
}

if ($roomType !== "") {
    $safeType = $conn->real_escape_string($roomType);
    $where[] = "room_type = '$safeType'";
}

if ($status !== "") {
    $safeStatus = $conn->real_escape_string($status);
    $where[] = "status = '$safeStatus'";
}

$sql = "SELECT id, room_no, room_type, description,
               capacity, price, status, image, destination
        FROM rooms
        WHERE " . implode(" AND ", $where) . "
        ORDER BY id DESC";

$rooms = [];
$totalRooms = 0;
$availableRooms = 0;
$maintenanceRooms = 0;
$loadError = "";

try {
    $result = $conn->query($sql);

    while ($room = $result->fetch_assoc()) {
        $rooms[] = $room;
    }

    $totalRooms = count($rooms);

    foreach ($rooms as $room) {
        if ($room["status"] === "Available") {
            $availableRooms++;
        } elseif ($room["status"] === "Maintenance") {
            $maintenanceRooms++;
        }
    }

} catch (Throwable $ex) {
    error_log("Load rooms error: " . $ex->getMessage());
    $loadError =
        "Rooms load nahi ho rahe. Check that the rooms table has all required columns, including destination.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Manage Rooms | Hotel Admin</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            color: #164e63;
        }

        .navbar {
            background: linear-gradient(135deg, #075985, #2196f3);
            color: white;
            padding: 18px 5%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            flex-wrap: wrap;
        }

        .brand {
            font-size: 22px;
            font-weight: bold;
        }

        .nav-links {
            display: flex;
            gap: 18px;
            flex-wrap: wrap;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            font-size: 14px;
        }

        .nav-links a:hover {
            text-decoration: underline;
        }

        .container {
            width: 95%;
            max-width: 1450px;
            margin: 30px auto;
        }

        .heading {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
            margin-bottom: 25px;
        }

        .heading h1 {
            font-size: 28px;
            margin-bottom: 8px;
        }

        .heading p {
            color: #64748b;
            line-height: 1.5;
        }

        .btn {
            display: inline-block;
            padding: 11px 16px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
        }

        .btn-primary {
            background: #0284c7;
            color: white;
        }

        .btn-primary:hover {
            background: #0369a1;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 18px;
            margin-bottom: 25px;
        }

        .stat-card {
            background: white;
            padding: 22px;
            border-radius: 13px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 15px rgba(15, 23, 42, .04);
        }

        .stat-card p {
            color: #64748b;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .stat-card h2 {
            font-size: 29px;
        }

        .filters {
            background: white;
            padding: 22px;
            border: 1px solid #e2e8f0;
            border-radius: 13px;
            margin-bottom: 25px;
        }

        .filters h3 {
            margin-bottom: 16px;
        }

        .filter-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 13px;
        }

        input, select {
            width: 100%;
            min-width: 0;
            padding: 12px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 14px;
            background: white;
            color: #164e63;
        }

        input:focus, select:focus {
            outline: 2px solid #bae6fd;
            border-color: #0284c7;
        }

        .filter-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
            flex-wrap: wrap;
        }

        .btn-reset {
            background: #e2e8f0;
            color: #164e63;
        }

        .table-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 13px;
            overflow: hidden;
        }

        .table-heading {
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            border-bottom: 1px solid #e2e8f0;
        }

        .table-heading p {
            color: #64748b;
            font-size: 13px;
        }

        .table-wrap {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            min-width: 1100px;
            border-collapse: collapse;
        }

        th, td {
            padding: 15px 12px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
            font-size: 13px;
        }

        th {
            background: #f8fafc;
            color: #475569;
            white-space: nowrap;
        }

        tbody tr:hover {
            background: #f8fafc;
        }

        .room-image {
            width: 90px;
            height: 65px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background: #f1f5f9;
        }

        .no-image {
            width: 90px;
            height: 65px;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #e2e8f0;
            border-radius: 8px;
            color: #64748b;
            font-size: 12px;
        }

        .badge {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            white-space: nowrap;
        }

        .available {
            background: #dcfce7;
            color: #166534;
        }

        .maintenance {
            background: #fef3c7;
            color: #92400e;
        }

        .room-description {
            max-width: 230px;
            line-height: 1.5;
            color: #64748b;
            overflow-wrap: anywhere;
        }

        .room-number {
            font-weight: bold;
            color: #075985;
        }

        .price {
            font-weight: bold;
            white-space: nowrap;
        }

        .actions {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .btn-edit {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .btn-delete {
            background: #fee2e2;
            color: #b91c1c;
        }

        .delete-form {
            display: inline;
        }

        .alert {
            padding: 15px 18px;
            border-radius: 9px;
            margin-bottom: 20px;
            line-height: 1.6;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .alert-error {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .empty {
            text-align: center;
            padding: 55px 20px;
        }

        .empty h3 {
            margin-bottom: 10px;
            font-size: 21px;
        }

        .empty p {
            color: #64748b;
            margin-bottom: 18px;
        }

        @media (max-width: 900px) {
            .filter-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 600px) {
            .stats {
                grid-template-columns: 1fr;
            }

            .filter-grid {
                grid-template-columns: 1fr;
            }

            .heading h1 {
                font-size: 24px;
            }

            .navbar {
                padding: 18px 4%;
            }

            .nav-links {
                gap: 12px;
            }

            .container {
                width: 94%;
            }
        }
    </style>
</head>

<body>

<header class="navbar">
    <div class="brand">🏨 Hotel Admin</div>

    <nav class="nav-links">
        <a href="dashboard.php">Dashboard</a>
        <a href="rooms.php">Manage Rooms</a>
        <a href="add-room.php">Add Room</a>
        <a href="bookings.php">Bookings</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<main class="container">

    <div class="heading">
        <div>
            <h1>Manage Rooms</h1>
            <p>View, search, filter and manage your hotel rooms.</p>
        </div>

        <a href="add-room.php" class="btn btn-primary">
            + Add New Room
        </a>
    </div>

    <?php if ($success !== ""): ?>
        <div class="alert alert-success">
            <?= e($success) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <?php foreach ($errors as $error): ?>
                <div><?= e($error) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if ($loadError !== ""): ?>
        <div class="alert alert-error">
            <?= e($loadError) ?>
        </div>
    <?php endif; ?>

    <section class="stats">
        <div class="stat-card">
            <p>Rooms Found</p>
            <h2><?= $totalRooms ?></h2>
        </div>

        <div class="stat-card">
            <p>Available Rooms</p>
            <h2><?= $availableRooms ?></h2>
        </div>

        <div class="stat-card">
            <p>Under Maintenance</p>
            <h2><?= $maintenanceRooms ?></h2>
        </div>
    </section>

    <section class="filters">
        <h3>🔎 Search and Filter Rooms</h3>

        <form method="GET" action="rooms.php">
            <div class="filter-grid">

                <input
                    type="search"
                    name="search"
                    placeholder="Search room number, city, type..."
                    value="<?= e($search) ?>"
                >

                <select name="destination">
                    <option value="">All Destinations</option>
                    <?php foreach ($cities as $city): ?>
                        <option
                            value="<?= e($city) ?>"
                            <?= $destination === $city ? "selected" : "" ?>
                        >
                            <?= e($city) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <select name="room_type">
                    <option value="">All Room Types</option>
                    <?php foreach ($roomTypes as $type): ?>
                        <option
                            value="<?= e($type) ?>"
                            <?= $roomType === $type ? "selected" : "" ?>
                        >
                            <?= e($type) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <select name="status">
                    <option value="">All Statuses</option>
                    <option
                        value="Available"
                        <?= $status === "Available" ? "selected" : "" ?>
                    >
                        Available
                    </option>
                    <option
                        value="Maintenance"
                        <?= $status === "Maintenance" ? "selected" : "" ?>
                    >
                        Maintenance
                    </option>
                </select>

            </div>

            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">
                    Apply Filters
                </button>

                <a href="rooms.php" class="btn btn-reset">
                    Reset Filters
                </a>
            </div>
        </form>
    </section>

    <section class="table-card">

        <div class="table-heading">
            <h3>🏨 Room Inventory</h3>
            <p><?= $totalRooms ?> room(s) found</p>
        </div>

        <?php if (empty($rooms) && $loadError === ""): ?>
            <div class="empty">
                <h3>No Rooms Found</h3>
                <p>Try changing your filters or add a new room.</p>
                <a href="add-room.php" class="btn btn-primary">
                    + Add New Room
                </a>
            </div>
        <?php elseif (!empty($rooms)): ?>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Room No.</th>
                            <th>Destination</th>
                            <th>Room Type</th>
                            <th>Description</th>
                            <th>Guests</th>
                            <th>Price / Night</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php foreach ($rooms as $room): ?>
                        <tr>
                            <td>
                                <?php
                                $imageFile = basename(
                                    (string)($room["image"] ?? "")
                                );

                                $imagePath = __DIR__ . "/../images/" . $imageFile;
                                ?>

                                <?php if (
                                    $imageFile !== "" &&
                                    is_file($imagePath)
                                ): ?>
                                    <img
                                        class="room-image"
                                        src="../images/<?= rawurlencode($imageFile) ?>"
                                        alt="Room image"
                                        loading="lazy"
                                    >
                                <?php else: ?>
                                    <div class="no-image">
                                        No Image
                                    </div>
                                <?php endif; ?>
                            </td>

                            <td class="room-number">
                                <?= e($room["room_no"]) ?>
                            </td>

                            <td>
                                📍 <?= e($room["destination"] ?? "—") ?>
                            </td>

                            <td>
                                <?= e($room["room_type"]) ?>
                            </td>

                            <td>
                                <div class="room-description">
                                    <?= e($room["description"]) ?>
                                </div>
                            </td>

                            <td>
                                <?= e($room["capacity"]) ?> guests
                            </td>

                            <td class="price">
                                ₹<?= number_format((float)$room["price"], 2) ?>
                            </td>

                            <td>
                                <?php if ($room["status"] === "Available"): ?>
                                    <span class="badge available">
                                        Available
                                    </span>
                                <?php else: ?>
                                    <span class="badge maintenance">
                                        Maintenance
                                    </span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <div class="actions">
                                    <a
                                        href="edit-room.php?id=<?= (int)$room["id"] ?>"
                                        class="btn btn-edit"
                                    >
                                        Edit
                                    </a>

                                    <form
                                        method="POST"
                                        class="delete-form"
                                        onsubmit="return confirm('Are you sure you want to delete this room?');"
                                    >
                                        <input
                                            type="hidden"
                                            name="csrf_token"
                                            value="<?= e($_SESSION["rooms_csrf_token"]) ?>"
                                        >

                                        <input
                                            type="hidden"
                                            name="room_id"
                                            value="<?= (int)$room["id"] ?>"
                                        >

                                        <button
                                            type="submit"
                                            name="delete_room"
                                            value="1"
                                            class="btn btn-delete"
                                        >
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <?php endif; ?>

    </section>

</main>

</body>
</html>
