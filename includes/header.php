<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Hotel Booking System</title>


    <style>

        /* =====================================
           RESET
        ===================================== */

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }


        /* =====================================
           BODY
        ===================================== */

        body {

            font-family:
                Arial,
                Helvetica,
                sans-serif;

            background: #f1f5f9;

            color: #1e293b;

            min-height: 100vh;
        }


        /* =====================================
           CONTAINER
        ===================================== */

        .container {

            width: 90%;

            max-width: 1200px;

            margin: auto;
        }


        /* =====================================
           HEADER
        ===================================== */

        .main-header {

            background:
                linear-gradient(
                    135deg,
                    #0f172a,
                    #1e3a8a
                );

            box-shadow:
                0 4px 15px
                rgba(0, 0, 0, 0.15);

            position: sticky;

            top: 0;

            z-index: 1000;
        }


        /* =====================================
           NAVBAR
        ===================================== */

        .navbar {

            min-height: 72px;

            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 25px;
        }


        /* =====================================
           LOGO
        ===================================== */

        .logo a {

            color: #ffffff;

            text-decoration: none;

            font-size: 24px;

            font-weight: 700;

            white-space: nowrap;

            transition: 0.3s ease;
        }


        .logo a:hover {

            opacity: 0.85;
        }


        /* =====================================
           NAVIGATION
        ===================================== */

        nav {

            display: flex;

            align-items: center;

            gap: 8px;

            flex-wrap: wrap;
        }


        nav a {

            color: #ffffff;

            text-decoration: none;

            font-size: 15px;

            font-weight: 600;

            padding: 10px 14px;

            border-radius: 7px;

            transition: 0.3s ease;
        }


        nav a:hover {

            background:
                rgba(255, 255, 255, 0.15);

            color: #ffffff;

            transform:
                translateY(-1px);
        }


        /* =====================================
           WELCOME USER
        ===================================== */

        .welcome-user {

            color: #dbeafe;

            font-size: 14px;

            font-weight: 600;

            padding: 10px 8px;

            white-space: nowrap;
        }


        /* =====================================
           LOGOUT BUTTON
        ===================================== */

        nav a[href="logout.php"] {

            background: #dc2626;

            color: #ffffff;
        }


        nav a[href="logout.php"]:hover {

            background: #b91c1c;
        }


        /* =====================================
           MAIN
        ===================================== */

        main.container {

            padding-top: 30px;

            padding-bottom: 40px;

            min-height: calc(100vh - 72px);
        }


        /* =====================================
           COMMON BUTTON
        ===================================== */

        .btn {

            display: inline-block;

            padding: 11px 18px;

            border: none;

            border-radius: 7px;

            background: #2563eb;

            color: #ffffff;

            text-decoration: none;

            font-size: 15px;

            font-weight: 600;

            cursor: pointer;

            transition: 0.3s ease;
        }


        .btn:hover {

            background: #1d4ed8;

            transform:
                translateY(-2px);

            box-shadow:
                0 5px 12px
                rgba(37, 99, 235, 0.25);
        }


        /* =====================================
           COMMON CARD
        ===================================== */

        .card {

            background: #ffffff;

            border-radius: 12px;

            padding: 25px;

            box-shadow:
                0 5px 20px
                rgba(0, 0, 0, 0.08);
        }


        /* =====================================
           HEADINGS
        ===================================== */

        h1,
        h2,
        h3 {

            color: #1e293b;
        }


        /* =====================================
           FORMS
        ===================================== */

        input,
        select,
        textarea {

            font-family: inherit;
        }


        /* =====================================
           RESPONSIVE
        ===================================== */

        @media (max-width: 768px) {

            .navbar {

                flex-direction: column;

                justify-content: center;

                padding: 15px 0;

                gap: 12px;
            }


            nav {

                justify-content: center;

                gap: 4px;
            }


            nav a {

                font-size: 14px;

                padding: 8px 10px;
            }


            .welcome-user {

                font-size: 13px;

                padding: 8px 5px;
            }


            main.container {

                padding-top: 20px;
            }
        }


        @media (max-width: 500px) {

            .container {

                width: 94%;
            }


            .logo a {

                font-size: 20px;
            }


            nav {

                width: 100%;

                justify-content: center;
            }


            nav a {

                font-size: 13px;

                padding: 7px 8px;
            }


            .welcome-user {

                width: 100%;

                text-align: center;
            }
        }

    </style>

</head>


<body>


<header class="main-header">

    <div class="container navbar">


        <!-- Logo -->

        <div class="logo">

            <a href="index.php">

                🏨 Hotel Booking

            </a>

        </div>


        <!-- Navigation -->

        <nav>

            <a href="index.php">
                Home
            </a>

            <a href="rooms.php">
                Rooms
            </a>

            <a href="check-availability.php">
                Availability
            </a>


            <?php if (isset($_SESSION["user_id"])): ?>

                <span class="welcome-user">

                    Hi,

                    <?php

                    echo htmlspecialchars(
                        $_SESSION["user_name"]
                    );

                    ?>

                </span>


                <a href="logout.php">
                    Logout
                </a>


            <?php else: ?>


                <a href="login.php">
                    Login
                </a>

                <a href="register.php">
                    Register
                </a>


            <?php endif; ?>

        </nav>


    </div>

</header>


<main class="container">