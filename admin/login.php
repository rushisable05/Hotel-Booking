
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../config/database.php";

if (isset($_SESSION["admin_id"])) {
    header("Location: dashboard.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    $stmt = $conn->prepare(
        "SELECT id, name, email, password
         FROM admin_users
         WHERE email = ?
         LIMIT 1"
    );

    $stmt->bind_param("s", $email);
    $stmt->execute();

    $admin = $stmt->get_result()->fetch_assoc();

    if ($admin && password_verify($password, $admin["password"])) {
        session_regenerate_id(true);

        $_SESSION["admin_id"] = (int)$admin["id"];
        $_SESSION["admin_name"] = $admin["name"];
        $_SESSION["admin_email"] = $admin["email"];

        header("Location: dashboard.php");
        exit;
    }

    $error = "Invalid email or password.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin Login</title>
<style>
*{box-sizing:border-box}
body{margin:0;min-height:100vh;display:grid;place-items:center;background:linear-gradient(135deg,#101d38,#24478d);font:15px Arial;padding:20px}
.box{background:white;padding:35px;border-radius:20px;width:100%;max-width:420px;box-shadow:0 20px 60px #0004}
h1{text-align:center;color:#172b4d}
.sub{text-align:center;color:#64748b;margin-bottom:25px}
.field{display:flex;flex-direction:column;gap:8px;margin:16px 0}
.field input{padding:12px;border:1px solid #cbd5e1;border-radius:9px}
button{width:100%;padding:13px;background:#2563eb;color:white;border:0;border-radius:9px;font-weight:bold;cursor:pointer}
.err{background:#fee2e2;color:#991b1b;padding:12px;border-radius:8px}
</style>
</head>
<body>
<div class="box">
    <div style="font-size:45px;text-align:center">🏨</div>
    <h1>Admin Login</h1>
    <p class="sub">Manage your hotel in one place</p>

    <?php if ($error): ?>
        <p class="err"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="post">
        <div class="field">
            <label>Email</label>
            <input type="email" name="email" required>
        </div>
        <div class="field">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>
        <button type="submit">Sign In →</button>
    </form>
</div>
</body>
</html>
