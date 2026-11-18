
<?php
require_once __DIR__ . "/config/database.php";

$name = "Hotel Administrator";
$email = "admin@example.com";
$newPassword = "Rushi123";
$hash = password_hash($newPassword, PASSWORD_DEFAULT);

$stmt = $conn->prepare(
    "UPDATE admin_users SET password = ? WHERE email = ?"
);

$stmt->bind_param("ss", $hash, $email);
$stmt->execute();

if ($stmt->affected_rows >= 0 && $stmt->errno === 0) {
    echo "Password reset complete. You can now log in.";
} else {
    echo "Password reset failed. Check the admin email and database.";
}
?>
