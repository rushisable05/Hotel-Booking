<?php
require_once "config/database.php";
session_start();

$result = $conn->query(
    "SELECT * FROM rooms
     WHERE status = 'Available'
     ORDER BY id ASC"
);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Our Rooms - Hotel Booking</title>

<style>
*{
    box-sizing:border-box;
    margin:0;
    padding:0;
}

body{
    font-family:Arial, Helvetica, sans-serif;
    background:#eaf6ff;
    color:#164e63;
}

a{
    text-decoration:none;
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
    color:#164e63;
    font-weight:600;
}

.nav-links a:hover{
    color:#2196f3;
}

.user{
    color:#1976d2;
    font-weight:bold;
}

.container{
    width:90%;
    max-width:1200px;
    margin:auto;
}

.page-title{
    text-align:center;
    padding:45px 10px 30px;
}

.page-title h1{
    font-size:38px;
    color:#164e63;
    margin-bottom:10px;
}

.page-title p{
    color:#64748b;
    font-size:17px;
}

.room-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(300px,1fr));
    gap:30px;
    padding-bottom:60px;
}

.room-card{
    background:white;
    border-radius:18px;
    overflow:hidden;
    box-shadow:0 8px 25px rgba(33,150,243,.12);
    transition:.3s;
}

.room-card:hover{
    transform:translateY(-8px);
    box-shadow:0 15px 35px rgba(33,150,243,.20);
}

.room-image{
    height:230px;
    background:#dceeff;
    overflow:hidden;
}

.room-image img{
    width:100%;
    height:100%;
    object-fit:cover;
}

.room-placeholder{
    height:100%;
    display:flex;
    justify-content:center;
    align-items:center;
    font-size:70px;
}

.room-info{
    padding:25px;
}

.room-info h2{
    color:#164e63;
    margin-bottom:12px;
}

.room-info p{
    color:#64748b;
    margin:10px 0;
    line-height:1.5;
}

.room-meta{
    margin:15px 0;
    color:#475569;
}

.room-price{
    font-size:24px;
    font-weight:bold;
    color:#1565c0;
    margin:18px 0;
}

.room-price span{
    font-size:14px;
    color:#64748b;
}

.btn{
    display:inline-block;
    background:#2196f3;
    color:white;
    padding:12px 20px;
    border-radius:8px;
    font-weight:bold;
    transition:.3s;
}

.btn:hover{
    background:#1565c0;
}

footer{
    background:#164e63;
    color:white;
    text-align:center;
    padding:25px;
    margin-top:30px;
}

@media(max-width:700px){
    .navbar{
        flex-direction:column;
        gap:15px;
    }

    .nav-links{
        flex-wrap:wrap;
        justify-content:center;
    }
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

            <span class="user">
                Hi, <?php echo htmlspecialchars($_SESSION["user_name"]); ?>
            </span>

            <a href="logout.php">Logout</a>

        <?php else: ?>

            <a href="login.php">Login</a>
            <a href="register.php">Register</a>

        <?php endif; ?>
    </nav>

</header>


<div class="container">

    <div class="page-title">

        <h1>Our Rooms</h1>

        <p>
            Choose a comfortable room for your perfect stay.
        </p>

    </div>


    <div class="room-grid">

        <?php if($result && $result->num_rows > 0): ?>

            <?php while($room = $result->fetch_assoc()): ?>

                <div class="room-card">

                    <div class="room-image">

                        <?php if(!empty($room["image"])): ?>

                            <img
                                src="images/<?php echo htmlspecialchars($room["image"]); ?>"
                                alt="Hotel Room"
                            >

                        <?php else: ?>

                            <div class="room-placeholder">
                                🏨
                            </div>

                        <?php endif; ?>

                    </div>


                    <div class="room-info">

                        <h2>
                            <?php echo htmlspecialchars($room["room_type"]); ?>
                        </h2>

                        <p>
                            <strong>Room No:</strong>
                            <?php echo htmlspecialchars($room["room_no"]); ?>
                        </p>

                        <p>
                            <?php echo htmlspecialchars($room["description"]); ?>
                        </p>

                        <div class="room-meta">
                            👥 Capacity:
                            <?php echo htmlspecialchars($room["capacity"]); ?>
                            Guest(s)
                        </div>

                        <div class="room-price">

                            ₹<?php echo number_format($room["price"],2); ?>

                            <span>/ Night</span>

                        </div>

                        <a
                            class="btn"
                            href="room-details.php?id=<?php echo $room["id"]; ?>"
                        >
                            View Details
                        </a>

                    </div>

                </div>

            <?php endwhile; ?>

        <?php else: ?>

            <div>
                <h2>No Rooms Available</h2>
                <p>Please check again later.</p>
            </div>

        <?php endif; ?>

    </div>

</div>


<footer>
    © <?php echo date("Y"); ?> Hotel Booking System
</footer>

</body>
</html>