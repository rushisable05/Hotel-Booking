<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["user_id"])) {
    $currentUri = $_SERVER["REQUEST_URI"] ?? "index.php";
    if ($currentUri !== "/" && $currentUri !== "/index.php") {
        $_SESSION["redirect_after_login"] = $currentUri;
    }

    header("Location: login.php");
    exit;
}
