<?php

session_start();

require_once "config/database.php";

$error = "";
$success = "";


if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name =
        trim($_POST["name"] ?? "");

    $email =
        trim($_POST["email"] ?? "");

    $phone =
        trim($_POST["phone"] ?? "");

    $password =
        $_POST["password"] ?? "";

    $confirm_password =
        $_POST["confirm_password"] ?? "";


    if (
        $name === "" ||
        $email === "" ||
        $password === ""
    ) {

        $error =
            "Please fill all required fields.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error =
            "Please enter a valid email.";

    } elseif ($password !== $confirm_password) {

        $error =
            "Passwords do not match.";

    } elseif (strlen($password) < 6) {

        $error =
            "Password must contain at least 6 characters.";

    } else {

        /* Check email */

        $stmt = $conn->prepare(
            "SELECT id FROM users WHERE email = ?"
        );

        $stmt->bind_param(
            "s",
            $email
        );

        $stmt->execute();

        $exists =
            $stmt->get_result()->num_rows > 0;


        if ($exists) {

            $error =
                "This email is already registered.";

        } else {

            $hashed_password =
                password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );


            $stmt = $conn->prepare(
                "INSERT INTO users
                (name, email, password, phone)
                VALUES (?, ?, ?, ?)"
            );


            $stmt->bind_param(
                "ssss",
                $name,
                $email,
                $hashed_password,
                $phone
            );


            if ($stmt->execute()) {

                $success =
                    "Registration successful! You can now login.";

            } else {

                $error =
                    "Registration failed. Please try again.";

            }
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

    <title>Register - Hotel Booking</title>


    <style>

        /* =================================
           RESET
        ================================= */

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }


        /* =================================
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

            padding: 30px 15px;
        }


        /* =================================
           CONTAINER
        ================================= */

        .auth-container {

            width: 100%;

            max-width: 480px;
        }


        /* =================================
           REGISTER BOX
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


        /* =================================
           ANIMATION
        ================================= */

        @keyframes fadeIn {

            from {

                opacity: 0;

                transform:
                    translateY(25px);
            }

            to {

                opacity: 1;

                transform:
                    translateY(0);
            }
        }


        /* =================================
           HEADING
        ================================= */

        .auth-box h1 {

            text-align: center;

            color: #1e293b;

            font-size: 30px;

            margin-bottom: 30px;
        }


        /* =================================
           ERROR BOX
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


        /* =================================
           SUCCESS MESSAGE
        ================================= */

        .success-message {

            background: #dcfce7;

            color: #166534;

            border:
                1px solid #bbf7d0;

            padding: 15px;

            border-radius: 8px;

            margin-bottom: 20px;

            font-size: 14px;

            text-align: center;

            line-height: 1.5;
        }


        /* =================================
           FORM GROUP
        ================================= */

        .form-group {

            margin-bottom: 18px;
        }


        /* =================================
           LABEL
        ================================= */

        .form-group label {

            display: block;

            margin-bottom: 8px;

            font-weight: 600;

            color: #334155;

            font-size: 15px;
        }


        /* =================================
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

            background: #f8fafc;

            transition:
                0.3s ease;
        }


        /* =================================
           INPUT FOCUS
        ================================= */

        .form-group input:focus {

            border-color: #2563eb;

            background: #ffffff;

            box-shadow:
                0 0 0 3px
                rgba(37, 99, 235, 0.12);
        }


        /* =================================
           BUTTON
        ================================= */

        .btn {

            display: block;

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

            text-align: center;

            text-decoration: none;

            border-radius: 9px;

            cursor: pointer;

            transition:
                0.3s ease;

            margin-top: 8px;
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


        /* =================================
           LOGIN TEXT
        ================================= */

        .auth-box p {

            text-align: center;

            margin-top: 24px;

            color: #64748b;

            font-size: 14px;
        }


        /* =================================
           LOGIN LINK
        ================================= */

        .auth-box p a {

            color: #2563eb;

            text-decoration: none;

            font-weight: 600;

            margin-left: 4px;
        }


        .auth-box p a:hover {

            color: #1d4ed8;

            text-decoration: underline;
        }


        /* =================================
           MOBILE RESPONSIVE
        ================================= */

        @media (max-width: 500px) {

            body {

                padding: 20px 12px;
            }

            .auth-box {

                padding: 30px 22px;

                border-radius: 14px;
            }

            .auth-box h1 {

                font-size: 25px;

                margin-bottom: 25px;
            }

            .form-group {

                margin-bottom: 16px;
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


        <h1>🏨 Create Account</h1>


        <?php if ($error): ?>

            <div class="error-box">

                <?php
                echo htmlspecialchars($error);
                ?>

            </div>

        <?php endif; ?>


        <?php if ($success): ?>

            <div class="success-message">

                <?php
                echo htmlspecialchars($success);
                ?>

            </div>


            <a
                href="login.php"
                class="btn"
            >
                Login Now
            </a>


        <?php else: ?>


            <form
                method="POST"
                action=""
            >


                <div class="form-group">

                    <label for="name">
                        Full Name
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        placeholder="Enter your full name"
                        value="<?php
                            echo htmlspecialchars(
                                $_POST["name"] ?? ""
                            );
                        ?>"
                        required
                    >

                </div>


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

                    <label for="phone">
                        Phone
                    </label>

                    <input
                        type="text"
                        id="phone"
                        name="phone"
                        placeholder="Enter your phone number"
                        value="<?php
                            echo htmlspecialchars(
                                $_POST["phone"] ?? ""
                            );
                        ?>"
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
                        placeholder="Enter password"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="confirm_password">
                        Confirm Password
                    </label>

                    <input
                        type="password"
                        id="confirm_password"
                        name="confirm_password"
                        placeholder="Confirm your password"
                        required
                    >

                </div>


                <button
                    type="submit"
                    class="btn"
                >
                    Register
                </button>


            </form>


            <p>

                Already have an account?

                <a href="login.php">
                    Login
                </a>

            </p>


        <?php endif; ?>


    </div>

</div>


</body>

</html>