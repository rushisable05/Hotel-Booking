<?php

require_once "includes/auth.php";
require_once "config/database.php";

if(!isset($_GET["id"]) || !is_numeric($_GET["id"])){
    header("Location: rooms.php");
    exit;
}

$id = intval($_GET["id"]);

$stmt = $conn->prepare("SELECT * FROM rooms WHERE id = ?");
$stmt->bind_param("i",$id);
$stmt->execute();

$result = $stmt->get_result();

if($result->num_rows == 0){
    header("Location: rooms.php");
    exit;
}

$room = $result->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Room Details</title>

<style>

*{
    box-sizing:border-box;
    margin:0;
    padding:0;
}

body{
    font-family:Arial,Helvetica,sans-serif;
    background:#eaf6ff;
    color:#164e63;
}

.navbar{
    background:white;
    padding:18px 7%;
    display:flex;
    justify-content:space-between;
    align-items:center;
    box-shadow:0 3px 15px rgba(0,0,0,.08);
}

.logo{
    font-size:27px;
    font-weight:bold;
    color:#1976d2;
}

.nav-links{
    display:flex;
    gap:25px;
    align-items:center;
}

.nav-links a{
    text-decoration:none;
    color:#164e63;
    font-weight:bold;
}

.nav-links a:hover{
    color:#2196f3;
}

.container{
    width:90%;
    max-width:1100px;
    margin:50px auto;
}

.details-card{
    background:white;
    border-radius:20px;
    overflow:hidden;
    box-shadow:0 10px 35px rgba(33,150,243,.15);
}

.room-image{
    width:100%;
    height:420px;
    background:#dceeff;
}

.room-image img{
    width:100%;
    height:100%;
    object-fit:cover;
}

.content{
    padding:35px;
}

.content h1{
    font-size:36px;
    margin-bottom:15px;
    color:#164e63;
}

.room-number{
    color:#2196f3;
    font-size:18px;
    font-weight:bold;
}

.description{
    margin:20px 0;
    color:#64748b;
    line-height:1.7;
    font-size:17px;
}

.info{
    display:flex;
    gap:20px;
    flex-wrap:wrap;
    margin:20px 0;
}

.info-box{
    background:#eaf6ff;
    padding:18px 25px;
    border-radius:12px;
    min-width:180px;
}

.info-box strong{
    display:block;
    color:#164e63;
    margin-bottom:5px;
}

.price{
    font-size:30px;
    font-weight:bold;
    color:#1565c0;
    margin:25px 0;
}

.price span{
    font-size:15px;
    color:#64748b;
}

.btn{
    display:inline-block;
    background:#2196f3;
    color:white;
    text-decoration:none;
    padding:14px 25px;
    border-radius:9px;
    font-weight:bold;
    margin-right:10px;
}

.btn:hover{
    background:#1565c0;
}

.back{
    background:#164e63;
}

footer{
    background:#164e63;
    color:white;
    text-align:center;
    padding:25px;
}

</style>

</head>

<body>


<header class="navbar">

    <div class="logo">
        🏨 Hotel Booking
    </div>

    <nav class="nav-links">

        <a href="index.php">Home</a>
        <a href="rooms.php">Rooms</a>
        <a href="check-availability.php">Availability</a>

        <?php if(isset($_SESSION["user_id"])): ?>

            <a href="logout.php">Logout</a>

        <?php else: ?>

            <a href="login.php">Login</a>

        <?php endif; ?>

    </nav>

</header>


<div class="container">

    <div class="details-card">

        <div class="room-image">

            <?php if(!empty($room["image"])): ?>

                <img
                    src="images/<?php echo htmlspecialchars($room["image"]); ?>"
                    alt="Room"
                >

            <?php else: ?>

                <div style="
                    height:100%;
                    display:flex;
                    align-items:center;
                    justify-content:center;
                    font-size:80px;
                ">
                    🏨
                </div>

            <?php endif; ?>

        </div>


        <div class="content">

            <h1>
                <?php echo htmlspecialchars($room["room_type"]); ?>
            </h1>

            <div class="room-number">
                Room No:
                <?php echo htmlspecialchars($room["room_no"]); ?>
            </div>

            <div class="description">
                <?php echo htmlspecialchars($room["description"]); ?>
            </div>


            <div class="info">

                <div class="info-box">
                    <strong>👥 Capacity</strong>
                    <?php echo htmlspecialchars($room["capacity"]); ?>
                    Guests
                </div>

                <div class="info-box">
                    <strong>🏨 Status</strong>
                    <?php echo htmlspecialchars($room["status"]); ?>
                </div>

            </div>


            <div class="price">

                ₹<?php echo number_format($room["price"],2); ?>

                <span>/ Night</span>

            </div>


            <a
                href="booking.php?room_id=<?php echo $room["id"]; ?>"
                class="btn"
            >
                Book This Room
            </a>

            <a href="rooms.php" class="btn back">
                Back to Rooms
            </a>

        </div>

    </div>

</div>


<footer>
    © <?php echo date("Y"); ?> Hotel Booking System
</footer>

</body>
</html>