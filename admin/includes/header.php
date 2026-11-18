
<?php
$pageTitle = $pageTitle ?? "Admin";
$activePage = $activePage ?? "";

$flashMessage = $_SESSION["admin_flash"] ?? null;
unset($_SESSION["admin_flash"]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($pageTitle) ?> | Hotel Admin</title>

<style>
*{box-sizing:border-box}
body{margin:0;background:#f1f6fc;color:#172b4d;font:15px "Segoe UI",Arial,sans-serif}
a{text-decoration:none;color:inherit}
.shell{display:flex;min-height:100vh}
.sidebar{width:235px;padding:22px 14px;background:linear-gradient(#101d38,#24478d);color:white;flex-shrink:0}
.brand{font-size:22px;font-weight:800;padding:8px 10px 22px}
.nav a{display:block;padding:12px;margin:4px 0;border-radius:9px;color:#e4edff;font-weight:600}
.nav a:hover,.nav a.active{background:#ffffff24}
.main{flex:1;min-width:0}
.topbar{padding:22px 28px;background:white;border-bottom:1px solid #dbe5f0;display:flex;justify-content:space-between;gap:12px;align-items:center}
.topbar h1{margin:0;font-size:22px}
.content{padding:26px;max-width:1600px;margin:auto}
.heading{display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:20px}
.heading h2{margin:0}
.muted{color:#64748b}
.grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px}
.card,.panel,.form{background:white;border:1px solid #dbe5f0;border-radius:15px;padding:20px;box-shadow:0 5px 18px #15345a08;margin-bottom:18px}
.stat{font-size:30px;font-weight:800;margin:12px 0}
.btn{display:inline-block;border:0;border-radius:8px;padding:10px 14px;background:#2563eb;color:white;font-weight:700;cursor:pointer;font-size:13px}
.btn.alt{background:#eaf0f8;color:#243b5a}
.btn.red{background:#dc2626;color:white}
.toolbar{display:flex;gap:9px;flex-wrap:wrap;margin-bottom:17px}
.toolbar input,.toolbar select,.field input,.field select,.field textarea,td select{padding:10px;border:1px solid #cbd5e1;border-radius:8px;background:#fff;font:inherit;max-width:100%}
.field{display:flex;flex-direction:column;gap:7px;margin-bottom:16px}
.field label{font-weight:700;font-size:13px}
.field textarea{min-height:100px}
.form{max-width:850px}
.formgrid{display:grid;grid-template-columns:1fr 1fr;gap:0 16px}
.full{grid-column:1/-1}
.tablewrap{overflow:auto}
table{border-collapse:collapse;width:100%;min-width:700px}
th,td{text-align:left;padding:12px;border-bottom:1px solid #edf1f6;font-size:13px;vertical-align:top}
th{background:#f8fafc;color:#64748b;text-transform:uppercase;font-size:11px}
.badge{display:inline-block;padding:5px 9px;border-radius:25px;background:#e2e8f0;font-size:11px;font-weight:800}
.badge.available,.badge.confirmed,.badge.completed{background:#dcfce7;color:#166534}
.badge.pending,.badge.booked{background:#fef3c7;color:#92400e}
.badge.cancelled,.badge.maintenance{background:#fee2e2;color:#991b1b}
.actions{display:flex;gap:6px;flex-wrap:wrap}
.alert{padding:12px;border-radius:8px;margin-bottom:16px;background:#dcfce7;color:#166534}
.alert.error{background:#fee2e2;color:#991b1b}
.empty{text-align:center;padding:25px;color:#64748b}
.detailgrid{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.detail{padding:14px;background:#f8fafc;border-radius:9px;overflow-wrap:anywhere}
.detail small{display:block;color:#64748b;margin-bottom:6px}
@media(max-width:1000px){.grid{grid-template-columns:1fr 1fr}}
@media(max-width:700px){
.shell{display:block}.sidebar{width:auto;padding:10px}.brand{padding:8px}
.nav{display:flex;overflow:auto;gap:4px}.nav a{white-space:nowrap;padding:10px}
.content{padding:15px}.topbar{padding:16px}
.formgrid,.detailgrid{grid-template-columns:1fr}.full{grid-column:auto}
}
@media(max-width:430px){.grid{grid-template-columns:1fr}}
</style>
</head>
<body>
<div class="shell">

<aside class="sidebar">
    <div class="brand">🏨 Hotel Admin</div>
    <nav class="nav">
        <a class="<?= $activePage === 'dashboard' ? 'active' : '' ?>" href="dashboard.php">▦ Dashboard</a>
        <a class="<?= $activePage === 'rooms' ? 'active' : '' ?>" href="rooms.php">🛏 Rooms</a>
        <a class="<?= $activePage === 'bookings' ? 'active' : '' ?>" href="bookings.php">📅 Bookings</a>
        <a class="<?= $activePage === 'customers' ? 'active' : '' ?>" href="customers.php">👥 Customers</a>
        <a class="<?= $activePage === 'availability' ? 'active' : '' ?>" href="availability.php">🔎 Availability</a>
        <a class="<?= $activePage === 'reports' ? 'active' : '' ?>" href="reports.php">📊 Reports</a>
        <a href="../index.php" target="_blank">↗ View Website</a>
        <a href="logout.php">⇥ Logout</a>
    </nav>
</aside>

<main class="main">
<header class="topbar">
    <h1><?= e($pageTitle) ?></h1>
    <span class="muted">👤 <?= e($_SESSION["admin_name"] ?? "Admin") ?></span>
</header>

<section class="content">
<?php if ($flashMessage): ?>
    <div class="alert <?= e($flashMessage["type"]) ?>">
        <?= e($flashMessage["message"]) ?>
    </div>
<?php endif; ?>
