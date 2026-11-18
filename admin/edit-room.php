
<?php
require_once __DIR__ . "/includes/auth.php";

$pageTitle = "Edit Room";
$activePage = "rooms";
$id = (int)($_GET["id"] ?? 0);
$error = "";

$stmt = $conn->prepare("SELECT * FROM rooms WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$room = $stmt->get_result()->fetch_assoc();

if (!$room) {
    flash("Room not found.", "error");
    header("Location: rooms.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    check_csrf();

    $room_no = trim($_POST["room_no"] ?? "");
    $room_type = trim($_POST["room_type"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $capacity = (int)($_POST["capacity"] ?? 1);
    $price = (float)($_POST["price"] ?? 0);
    $status = $_POST["status"] ?? "";
    $image = trim($_POST["image"] ?? "");

    if ($room_no === "" || $room_type === "" || $capacity < 1 || $price <= 0) {
        $error = "Please enter valid room information.";
    } elseif (!in_array($status, ["Available", "Booked", "Maintenance"], true)) {
        $error = "Invalid room status.";
    } else {
        $check = $conn->prepare(
            "SELECT id FROM rooms WHERE room_no=? AND id<>?"
        );
        $check->bind_param("si", $room_no, $id);
        $check->execute();

        if ($check->get_result()->num_rows > 0) {
            $error = "Another room already uses this room number.";
        } else {
            $update = $conn->prepare(
                "UPDATE rooms
                 SET room_no=?,room_type=?,description=?,capacity=?,
                     price=?,status=?,image=?
                 WHERE id=?"
            );

            $update->bind_param(
                "sssidssi",
                $room_no,
                $room_type,
                $description,
                $capacity,
                $price,
                $status,
                $image,
                $id
            );

            if ($update->execute()) {
                flash("Room updated successfully.");
                header("Location: rooms.php");
                exit;
            }

            $error = "Unable to update room: " . $update->error;
        }
    }

    $room["room_no"] = $room_no;
    $room["room_type"] = $room_type;
    $room["description"] = $description;
    $room["capacity"] = $capacity;
    $room["price"] = $price;
    $room["status"] = $status;
    $room["image"] = $image;
}

include __DIR__ . "/includes/header.php";
?>

<div class="heading">
    <div>
        <h2>Edit Room <?= e($room["room_no"]) ?></h2>
        <p class="muted">Update room details and inventory status.</p>
    </div>
    <a class="btn alt" href="rooms.php">← Back to Rooms</a>
</div>

<div class="form">
    <?php if ($error): ?>
        <div class="alert error"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="post">
        <input type="hidden" name="csrf_token" value="<?= e(csrf()) ?>">

        <div class="formgrid">
            <div class="field">
                <label>Room Number *</label>
                <input name="room_no" value="<?= e($room["room_no"]) ?>" required>
            </div>

            <div class="field">
                <label>Room Type *</label>
                <input name="room_type" value="<?= e($room["room_type"]) ?>" required>
            </div>

            <div class="field">
                <label>Capacity *</label>
                <input type="number" min="1" name="capacity" value="<?= e($room["capacity"]) ?>" required>
            </div>

            <div class="field">
                <label>Price per Night (₹) *</label>
                <input type="number" min="1" step="0.01" name="price" value="<?= e($room["price"]) ?>" required>
            </div>

            <div class="field">
                <label>Status</label>
                <select name="status">
                    <?php foreach (["Available", "Booked", "Maintenance"] as $s): ?>
                        <option value="<?= e($s) ?>" <?= $room["status"] === $s ? "selected" : "" ?>>
                            <?= e($s) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="field">
                <label>Image Filename</label>
                <input name="image" value="<?= e($room["image"]) ?>" placeholder="deluxe-room.jpg">
            </div>

            <div class="field full">
                <label>Description</label>
                <textarea name="description"><?= e($room["description"]) ?></textarea>
            </div>

            <div class="field full">
                <button class="btn" type="submit">Save Changes</button>
            </div>
        </div>
    </form>
</div>

<?php include __DIR__ . "/includes/footer.php"; ?>
