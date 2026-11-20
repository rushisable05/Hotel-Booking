<?php
require_once "includes/auth.php";
require_once "config/database.php";

$rooms = [];
$message = "";

if(isset($_POST["check"])){

    $check_in = $_POST["check_in"];
    $check_out = $_POST["check_out"];
    $guests = intval($_POST["guests"]);

    if($check_in >= $check_out){

        $message = "Check-out date must be after check-in date.";

    }else{

        $sql = "
        SELECT * FROM rooms
        WHERE capacity >= ?
        AND status = 'Available'
        AND id NOT IN (
            SELECT room_id
            FROM bookings
            WHERE check_in < ?
            AND check_out > ?
        )
        ORDER BY price ASC
        ";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            "iss",
            $guests,
            $check_out,
            $check_in
        );

        $stmt->execute();

        $result = $stmt->get_result();

        while($row = $result->fetch_assoc()){
            $rooms[] = $row;
        }

    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">

<title>Check Availability</title>

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
    max-width:1000px;
    margin:50px auto;
}

.card{
    background:white;
    padding:35px;
    border-radius:20px;
    box-shadow:0 10px 35px rgba(33,150,243,.15);
}

h1{
    text-align:center;
    margin-bottom:10px;
    color:#164e63;
}

.subtitle{
    text-align:center;
    color:#64748b;
    margin-bottom:30px;
}

.form-grid{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:20px;
}

label{
    font-weight:bold;
    display:block;
    margin-bottom:8px;
}

input,
select{
    width:100%;
    padding:13px;
    border:1px solid #cbd5e1;
    border-radius:8px;
    font-size:15px;
}

input:focus,
select:focus{
    outline:none;
    border-color:#2196f3;
    box-shadow:0 0 0 3px rgba(33,150,243,.12);
}

button{
    margin-top:25px;
    width:100%;
    padding:14px;
    background:#2196f3;
    color:white;
    border:0;
    border-radius:9px;
    font-size:17px;
    font-weight:bold;
    cursor:pointer;
}

button:hover{
    background:#1565c0;
}

.error{
    background:#fee2e2;
    color:#991b1b;
    padding:15px;
    border-radius:8px;
    margin-top:20px;
}

.results{
    margin-top:40px;
}

.room{
    background:#f8fbff;
    border:1px solid #dbeafe;
    border-radius:15px;
    padding:20px;
    margin-top:15px;
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.room h3{
    color:#164e63;
}

.room p{
    color:#64748b;
    margin-top:7px;
}

.price{
    color:#1565c0;
    font-size:21px;
    font-weight:bold;
}

.book{
    display:inline-block;
    margin-top:10px;
    background:#2196f3;
    color:white;
    padding:10px 18px;
    border-radius:7px;
    text-decoration:none;
}

footer{
    margin-top:60px;
    background:#164e63;
    color:white;
    text-align:center;
    padding:25px;
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

    .form-grid{
        grid-template-columns:1fr;
    }

    .room{
        flex-direction:column;
        align-items:flex-start;
        gap:15px;
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

            <a href="logout.php">Logout</a>

        <?php else: ?>

            <a href="login.php">Login</a>

        <?php endif; ?>

    </nav>

</header>


<div class="container">

    <div class="card">

        <h1>🔎 Check Room Availability</h1>

        <p class="subtitle">
            Select your dates and number of guests.
        </p>


        <form method="POST">

            <div class="form-grid">

                <div>

                    <label>Check-in</label>

                    <input
                        type="date"
                        name="check_in"
                        required
                    >

                </div>


                <div>

                    <label>Check-out</label>

                    <input
                        type="date"
                        name="check_out"
                        required
                    >

                </div>


                <div>

                    <label>Guests</label>

                    <select name="guests">

                        <option value="1">1 Guest</option>
                        <option value="2">2 Guests</option>
                        <option value="3">3 Guests</option>
                        <option value="4">4 Guests</option>
                        <option value="5">5 Guests</option>

                    </select>

                </div>

            </div>


            <button type="submit" name="check">
                Check Availability
            </button>

        </form>


        <?php if($message): ?>

            <div class="error">
                <?php echo htmlspecialchars($message); ?>
            </div>

        <?php endif; ?>


        <?php if(isset($_POST["check"]) && !$message): ?>

            <div class="results">

                <h2>Available Rooms</h2>

                <?php if(count($rooms) > 0): ?>

                    <?php foreach($rooms as $room): ?>

                        <div class="room">

                            <div>

                                <h3>
                                    <?php echo htmlspecialchars($room["room_type"]); ?>
                                </h3>

                                <p>
                                    Room No:
                                    <?php echo htmlspecialchars($room["room_no"]); ?>
                                </p>

                                <p>
                                    Capacity:
                                    <?php echo htmlspecialchars($room["capacity"]); ?>
                                    Guests
                                </p>

                            </div>


                            <div>

                                <div class="price">
                                    ₹<?php echo number_format($room["price"],2); ?>
                                    / Night
                                </div>

                                <a
                                    class="book"
                                    href="booking.php?room_id=<?php echo $room["id"]; ?>"
                                >
                                    Book Now
                                </a>

                            </div>

                        </div>

                    <?php endforeach; ?>

                <?php else: ?>

                    <p style="margin-top:20px;color:#64748b;">
                        Sorry, no rooms are available for these dates.
                    </p>

                <?php endif; ?>

            </div>

        <?php endif; ?>

    </div>

</div>


<footer>
    © <?php echo date("Y"); ?> Hotel Booking System
</footer>

</body>
</html>