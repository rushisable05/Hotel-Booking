
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../../config/database.php";

// Admin login protection
if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

// Safe output
function e($value) {
    return htmlspecialchars(
        (string)($value ?? ""),
        ENT_QUOTES,
        "UTF-8"
    );
}

// Flash message
function flash($message, $type = "success") {
    $_SESSION["admin_flash"] = [
        "message" => $message,
        "type" => $type
    ];
}

// CSRF protection
function csrf() {
    if (empty($_SESSION["admin_csrf"])) {
        $_SESSION["admin_csrf"] = bin2hex(random_bytes(32));
    }

    return $_SESSION["admin_csrf"];
}

function check_csrf() {
    $token = $_POST["csrf_token"] ?? "";

    if (
        !is_string($token) ||
        !hash_equals(csrf(), $token)
    ) {
        http_response_code(403);
        exit("Invalid security token. Please go back and try again.");
    }
}
?>
