<?php

session_start();

require_once "config/database.php";

$error = "";


if (isset($_SESSION["user_id"])) {

    header("Location: index.php");

    exit;
}


if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email =
        trim($_POST["email"] ?? "");

    $password =
        $_POST["password"] ?? "";


    if ($email === "" || $password === "") {

        $error =
            "Please enter email and password.";

    } else {

        $stmt = $conn->prepare(
            "SELECT
                id,
                name,
                email,
                password
             FROM users
             WHERE email = ?"
        );


        $stmt->bind_param(
            "s",
            $email
        );


        $stmt->execute();

        $result =
            $stmt->get_result();

        $user =
            $result->fetch_assoc();


        if (
            $user &&
            password_verify(
                $password,
                $user["password"]
            )
        ) {

            $_SESSION["user_id"] =
                $user["id"];

            $_SESSION["user_name"] =
                $user["name"];

            $_SESSION["user_email"] =
                $user["email"];


            if (
                isset(
                    $_SESSION["redirect_after_login"]
                )
            ) {

                $redirect =
                    $_SESSION[
                        "redirect_after_login"
                    ];

                unset(
                    $_SESSION[
                        "redirect_after_login"
                    ]
                );

                header(
                    "Location: " . $redirect
                );

            } else {

                header(
                    "Location: index.php"
                );
            }

            exit;

        } else {

            $error =
                "Invalid email or password.";
        }
    }
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

    <title>Login - Hotel Booking</title>


    <style>

        /* ================================
           RESET
        ================================= */

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }


        /* ================================
           BODY
        ================================= */

        body {

            font-family:
                Arial,
                Helvetica,
                sans-serif;

            min-height: 100vh;

            background:
                linear-gradient(
                    135deg,
                    #0f172a,
                    #1e3a8a,
                    #2563eb
                );

            display: flex;

            align-items: center;

            justify-content: center;

            padding: 20px;
        }


        /* ================================
           MAIN CONTAINER
        ================================= */

        .auth-container {

            width: 100%;

            max-width: 430px;
        }


        /* ================================
           LOGIN BOX
        ================================= */

        .auth-box {

            background: #ffffff;

            width: 100%;

            padding: 40px;

            border-radius: 18px;

            box-shadow:
                0 20px 50px
                rgba(0, 0, 0, 0.25);

            animation:
                fadeIn 0.5s ease;
        }


        /* ================================
           ANIMATION
        ================================= */

        @keyframes fadeIn {

            from {

                opacity: 0;

                transform:
                    translateY(20px);
            }

            to {

                opacity: 1;

                transform:
                    translateY(0);
            }
        }


        /* ================================
           HEADING
        ================================= */

        .auth-box h1 {

            text-align: center;

            color: #1e293b;

            font-size: 30px;

            margin-bottom: 30px;
        }


        /* ================================
           ERROR MESSAGE
        ================================= */

        .error-box {

            background: #fee2e2;

            color: #b91c1c;

            border:
                1px solid #fecaca;

            padding: 12px 15px;

            border-radius: 8px;

            margin-bottom: 20px;

            font-size: 14px;

            text-align: center;
        }


        /* ================================
           FORM GROUP
        ================================= */

        .form-group {

            margin-bottom: 20px;
        }


        /* ================================
           LABEL
        ================================= */

        .form-group label {

            display: block;

            margin-bottom: 8px;

            font-weight: 600;

            color: #334155;

            font-size: 15px;
        }


        /* ================================
           INPUT
        ================================= */

        .form-group input {

            width: 100%;

            padding: 13px 15px;

            border:
                1px solid #cbd5e1;

            border-radius: 9px;

            font-size: 15px;

            outline: none;

            transition:
                0.3s ease;

            background: #f8fafc;
        }


        .form-group input:focus {

            border-color: #2563eb;

            background: #ffffff;

            box-shadow:
                0 0 0 3px
                rgba(37, 99, 235, 0.12);
        }


        /* ================================
           LOGIN BUTTON
        ================================= */

        .btn {

            width: 100%;

            border: none;

            padding: 14px;

            background:
                linear-gradient(
                    135deg,
                    #2563eb,
                    #1d4ed8
                );

            color: #ffffff;

            font-size: 16px;

            font-weight: 600;

            border-radius: 9px;

            cursor: pointer;

            transition:
                0.3s ease;

            margin-top: 5px;
        }


        .btn:hover {

            background:
                linear-gradient(
                    135deg,
                    #1d4ed8,
                    #1e40af
                );

            transform:
                translateY(-2px);

            box-shadow:
                0 8px 18px
                rgba(37, 99, 235, 0.3);
        }


        .btn:active {

            transform:
                translateY(0);
        }


        /* ================================
           REGISTER TEXT
        ================================= */

        .auth-box p {

            text-align: center;

            margin-top: 25px;

            color: #64748b;

            font-size: 14px;
        }


        /* ================================
           REGISTER LINK
        ================================= */

        .auth-box p a {

            color: #2563eb;

            text-decoration: none;

            font-weight: 600;

            margin-left: 4px;
        }


        .auth-box p a:hover {

            text-decoration: underline;

            color: #1d4ed8;
        }


        /* ================================
           MOBILE RESPONSIVE
        ================================= */

        @media (max-width: 500px) {

            body {

                padding: 15px;
            }

            .auth-box {

                padding: 30px 22px;

                border-radius: 14px;
            }

            .auth-box h1 {

                font-size: 25px;

                margin-bottom: 25px;
            }

            .form-group input {

                padding: 12px;
            }

            .btn {

                padding: 13px;
            }
        }

    </style>

</head>


<body>


<div class="auth-container">

    <div class="auth-box">


        <h1>🏨 Login</h1>


        <?php if ($error): ?>

            <div class="error-box">

                <?php
                echo htmlspecialchars($error);
                ?>

            </div>

        <?php endif; ?>


        <form method="POST" action="">


            <div class="form-group">

                <label for="email">
                    Email
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Enter your email"
                    value="<?php
                        echo htmlspecialchars(
                            $_POST["email"] ?? ""
                        );
                    ?>"
                    required
                >

            </div>


            <div class="form-group">

                <label for="password">
                    Password
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Enter your password"
                    required
                >

            </div>


            <button
                type="submit"
                class="btn"
            >
                Login
            </button>


        </form>


        <p>

            Don't have an account?

            <a href="register.php">
                Register
            </a>

        </p>


    </div>

</div>


</body>

</html>