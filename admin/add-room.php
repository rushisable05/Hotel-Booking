
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

// Available cities
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

// Available room types
$roomTypes = [
    "Standard",
    "Deluxe",
    "Super Deluxe",
    "Executive",
    "Suite",
    "Family Room"
];

$errors = [];

$room_no = "";
$room_type = "Deluxe";
$description = "";
$capacity = 2;
$price = "";
$status = "Available";
$destination = "Ahmedabad";

function e($value)
{
    return htmlspecialchars(
        (string)$value,
        ENT_QUOTES,
        "UTF-8"
    );
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Get form data
    $room_no = trim($_POST["room_no"] ?? "");
    $room_type = trim($_POST["room_type"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $destination = trim($_POST["destination"] ?? "");
    $status = trim($_POST["status"] ?? "");

    $capacity = filter_var(
        $_POST["capacity"] ?? "",
        FILTER_VALIDATE_INT
    );

    $price = trim($_POST["price"] ?? "");

    // Validate room number
    if ($room_no === "") {
        $errors[] = "Room number is required.";
    } elseif (strlen($room_no) > 30) {
        $errors[] = "Room number cannot exceed 30 characters.";
    }

    // Validate room type
    if (!in_array($room_type, $roomTypes, true)) {
        $errors[] = "Please select a valid room type.";
    }

    // Validate description
    if ($description === "") {
        $errors[] = "Room description is required.";
    } elseif (strlen($description) > 2000) {
        $errors[] = "Description cannot exceed 2000 characters.";
    }

    // Validate capacity
    if (
        $capacity === false ||
        $capacity < 1 ||
        $capacity > 20
    ) {
        $errors[] = "Capacity must be between 1 and 20.";
    }

    // Validate price
    if (
        $price === "" ||
        !is_numeric($price) ||
        !is_finite((float)$price) ||
        (float)$price <= 0 ||
        (float)$price > 99999999
    ) {
        $errors[] = "Please enter a valid room price.";
    }

    // Validate status
    if (!in_array(
        $status,
        ["Available", "Maintenance"],
        true
    )) {
        $errors[] = "Please select a valid room status.";
    }

    // Validate destination
    if (!in_array($destination, $cities, true)) {
        $errors[] = "Please select a valid destination city.";
    }

    // Check duplicate room number
    if ($room_no !== "") {
        try {
            $check = $conn->prepare(
                "SELECT room_no FROM rooms
                 WHERE room_no = ? LIMIT 1"
            );

            $check->bind_param("s", $room_no);
            $check->execute();

            $check->store_result();

            if ($check->num_rows > 0) {
                $errors[] = "This room number already exists.";
            }

            $check->close();

        } catch (Throwable $ex) {
            error_log("Room validation error: " . $ex->getMessage());
            $errors[] = "Unable to check room number. Verify your database table.";
        }
    }

    // Image upload validation
    $imageName = "";
    $uploadTmpPath = "";

    if (
        isset($_FILES["image"]) &&
        $_FILES["image"]["error"] !== UPLOAD_ERR_NO_FILE
    ) {
        $file = $_FILES["image"];

        if ($file["error"] !== UPLOAD_ERR_OK) {
            $errors[] = "Image upload failed. Please try again.";

        } elseif ($file["size"] > 5 * 1024 * 1024) {
            $errors[] = "Image size must not exceed 5 MB.";

        } else {
            $imageInfo = @getimagesize($file["tmp_name"]);

            $allowedMimeTypes = [
                "image/jpeg" => "jpg",
                "image/png"  => "png",
                "image/webp" => "webp"
            ];

            $mimeType = (
                $imageInfo !== false
            ) ? ($imageInfo["mime"] ?? "") : "";

            if (!isset($allowedMimeTypes[$mimeType])) {
                $errors[] = "Only valid JPG, PNG and WEBP images are allowed.";
            } else {
                $imageName = bin2hex(random_bytes(16))
                    . "." . $allowedMimeTypes[$mimeType];

                $uploadTmpPath = $file["tmp_name"];
            }
        }
    }

    // Insert room into database
    if (empty($errors)) {

        $imageDirectory = __DIR__ . "/../images/";

        try {
            if (
                !is_dir($imageDirectory) &&
                !mkdir($imageDirectory, 0755, true) &&
                !is_dir($imageDirectory)
            ) {
                throw new RuntimeException(
                    "Unable to create images directory."
                );
            }

            // Move image if one was selected
            if (
                $imageName !== "" &&
                !move_uploaded_file(
                    $uploadTmpPath,
                    $imageDirectory . $imageName
                )
            ) {
                throw new RuntimeException(
                    "Unable to save uploaded image."
                );
            }

            $priceValue = (float)$price;

            // Destination is included in the INSERT query
            $stmt = $conn->prepare(
                "INSERT INTO rooms
                (
                    room_no,
                    room_type,
                    description,
                    capacity,
                    price,
                    status,
                    image,
                    destination
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
            );

            $stmt->bind_param(
                "sssidsss",
                $room_no,
                $room_type,
                $description,
                $capacity,
                $priceValue,
                $status,
                $imageName,
                $destination
            );

            $stmt->execute();
            $stmt->close();

            // Room added successfully
            header("Location: rooms.php?added=1");
            exit;

        } catch (Throwable $ex) {

            error_log("Add room error: " . $ex->getMessage());

            // Remove uploaded image if saving failed
            if (
                $imageName !== "" &&
                is_file($imageDirectory . $imageName)
            ) {
                unlink($imageDirectory . $imageName);
            }

            $errors[] = "Room could not be saved. Check the rooms table columns and database configuration.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Add Room | Hotel Admin</title>

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
            padding: 18px 6%;
            display: flex;
            justify-content: space-between;
            align-items: center;
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
            width: min(1000px, 92%);
            margin: 35px auto;
        }

        .page-heading {
            margin-bottom: 22px;
        }

        .page-heading h1 {
            font-size: 28px;
            margin-bottom: 8px;
        }

        .page-heading p {
            color: #64748b;
            line-height: 1.6;
        }

        .form-card {
            background: white;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 8px 28px rgba(15, 23, 42, .07);
            border: 1px solid #e2e8f0;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 22px;
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .field.full {
            grid-column: 1 / -1;
        }

        label {
            font-weight: bold;
            font-size: 14px;
        }

        input, select, textarea {
            width: 100%;
            padding: 12px 13px;
            border: 1px solid #cbd5e1;
            border-radius: 9px;
            font-size: 15px;
            color: #164e63;
            background: white;
            outline: none;
        }

        input:focus, select:focus, textarea:focus {
            border-color: #2196f3;
            box-shadow: 0 0 0 3px rgba(33, 150, 243, .12);
        }

        textarea {
            min-height: 120px;
            resize: vertical;
        }

        .help {
            font-size: 12px;
            color: #64748b;
            line-height: 1.5;
        }

        .required {
            color: #dc2626;
        }

        .actions {
            display: flex;
            gap: 12px;
            margin-top: 28px;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-block;
            border: none;
            padding: 12px 20px;
            border-radius: 9px;
            font-size: 15px;
            font-weight: bold;
            text-decoration: none;
            cursor: pointer;
        }

        .btn-primary {
            background: #2196f3;
            color: white;
        }

        .btn-primary:hover {
            background: #0879d1;
        }

        .btn-secondary {
            background: #e2e8f0;
            color: #164e63;
        }

        .alert {
            padding: 15px 18px;
            border-radius: 10px;
            margin-bottom: 22px;
            line-height: 1.6;
        }

        .alert-error {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .alert-error ul {
            padding-left: 20px;
        }

        @media (max-width: 650px) {
            .form-grid {
                grid-template-columns: 1fr;
            }

            .field.full {
                grid-column: auto;
            }

            .form-card {
                padding: 20px;
            }

            .page-heading h1 {
                font-size: 24px;
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

    <div class="page-heading">
        <h1>➕ Add New Room</h1>
        <p>Add room details, select a destination city, set the price and upload a room photo.</p>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <strong>Please fix the following errors:</strong>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= e($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <section class="form-card">

        <form method="POST" enctype="multipart/form-data">

            <div class="form-grid">

                <div class="field">
                    <label for="room_no">
                        Room Number <span class="required">*</span>
                    </label>
                    <input
                        type="text"
                        id="room_no"
                        name="room_no"
                        value="<?= e($room_no) ?>"
                        maxlength="30"
                        placeholder="Example: 101"
                        required
                    >
                </div>

                <div class="field">
                    <label for="room_type">
                        Room Type <span class="required">*</span>
                    </label>
                    <select id="room_type" name="room_type" required>
                        <?php foreach ($roomTypes as $type): ?>
                            <option
                                value="<?= e($type) ?>"
                                <?= $room_type === $type ? "selected" : "" ?>
                            >
                                <?= e($type) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="field">
                    <label for="destination">
                        Destination City <span class="required">*</span>
                    </label>
                    <select id="destination" name="destination" required>
                        <?php foreach ($cities as $city): ?>
                            <option
                                value="<?= e($city) ?>"
                                <?= $destination === $city ? "selected" : "" ?>
                            >
                                <?= e($city) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <span class="help">
                        Select the city where this room is located.
                    </span>
                </div>

                <div class="field">
                    <label for="capacity">
                        Maximum Guests <span class="required">*</span>
                    </label>
                    <input
                        type="number"
                        id="capacity"
                        name="capacity"
                        min="1"
                        max="20"
                        value="<?= e($capacity === false ? "" : $capacity) ?>"
                        required
                    >
                </div>

                <div class="field">
                    <label for="price">
                        Price Per Night (₹) <span class="required">*</span>
                    </label>
                    <input
                        type="number"
                        id="price"
                        name="price"
                        min="0.01"
                        max="99999999"
                        step="0.01"
                        value="<?= e($price) ?>"
                        placeholder="Example: 2500"
                        required
                    >
                </div>

                <div class="field">
                    <label for="status">
                        Room Status <span class="required">*</span>
                    </label>
                    <select id="status" name="status" required>
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

                <div class="field full">
                    <label for="description">
                        Room Description <span class="required">*</span>
                    </label>
                    <textarea
                        id="description"
                        name="description"
                        maxlength="2000"
                        placeholder="Describe the room, facilities, bed type, AC, Wi-Fi, etc."
                        required
                    ><?= e($description) ?></textarea>
                </div>

                <div class="field full">
                    <label for="image">Room Image</label>
                    <input
                        type="file"
                        id="image"
                        name="image"
                        accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                    >
                    <span class="help">
                        Optional. JPG, PNG or WEBP only. Maximum size: 5 MB.
                    </span>
                </div>

            </div>

            <div class="actions">
                <button type="submit" class="btn btn-primary">
                    Save Room
                </button>

                <a href="rooms.php" class="btn btn-secondary">
                    Cancel
                </a>
            </div>

        </form>

    </section>

</main>

</body>
</html>
