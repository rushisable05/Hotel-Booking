<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


/*
====================================================
PHPMailer AUTOLOAD
====================================================
*/

require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/env.php";


/*
====================================================
SEND BOOKING EMAIL FUNCTION
====================================================
*/

function sendBookingEmail(
    $customerEmail,
    $customerName,
    $bookingId,
    $roomType,
    $roomNo,
    $checkIn,
    $checkOut,
    $guests,
    $nights,
    $totalAmount
) {

    $mail = new PHPMailer(true);


    try {

        /*
        ==============================================
        SMTP SETTINGS
        ==============================================
        */

        $smtpHost = getenv("SMTP_HOST") ?: "smtp.gmail.com";
        $smtpPort = (int)(getenv("SMTP_PORT") ?: 587);
        $smtpUser = getenv("SMTP_USER") ?: "";
        $smtpPass = getenv("SMTP_PASS") ?: "";
        $smtpFromName = getenv("SMTP_FROM_NAME") ?: "Hotel Booking System";

        if ($smtpUser === "" || $smtpPass === "") {
            error_log("SMTP credentials not configured in .env");
            return false;
        }

        $mail->isSMTP();

        $mail->Host = $smtpHost;

        $mail->SMTPAuth = true;

        $mail->Username = $smtpUser;

        $mail->Password = $smtpPass;

        $mail->SMTPSecure =
            PHPMailer::ENCRYPTION_STARTTLS;

        $mail->Port = $smtpPort;

        $mail->setFrom(
            $smtpUser,
            $smtpFromName
        );


        /*
        ==============================================
        CUSTOMER EMAIL
        ==============================================
        */

        $mail->addAddress(
            $customerEmail,
            $customerName
        );


        /*
        ==============================================
        EMAIL FORMAT
        ==============================================
        */

        $mail->isHTML(true);


        /*
        ==============================================
        SUBJECT
        ==============================================
        */

        $mail->Subject =
            "Booking Confirmed - Ticket #HB"
            . $bookingId;


        /*
        ==============================================
        EMAIL BODY
        ==============================================
        */

        $mail->Body = '

<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<title>
    Booking Confirmation
</title>

</head>


<body style="
    margin:0;
    padding:30px 10px;
    background:#eaf6ff;
    font-family:Arial,Helvetica,sans-serif;
">


<div style="
    max-width:650px;
    margin:auto;
    background:#ffffff;
    border-radius:18px;
    overflow:hidden;
    box-shadow:0 8px 30px rgba(0,0,0,0.12);
">


<!-- =========================================
     HEADER
========================================= -->

<div style="
    background:linear-gradient(
        135deg,
        #2196f3,
        #1565c0
    );
    color:white;
    padding:30px 20px;
    text-align:center;
">

    <div style="
        font-size:45px;
        margin-bottom:8px;
    ">
        🏨
    </div>

    <h1 style="
        margin:0;
        font-size:30px;
    ">
        Hotel Booking
    </h1>

    <p style="
        margin:8px 0 0;
        font-size:15px;
    ">
        Booking Confirmation
    </p>

</div>


<!-- =========================================
     SUCCESS MESSAGE
========================================= -->

<div style="
    padding:30px 25px 15px;
    text-align:center;
">

    <div style="
        font-size:48px;
    ">
        🎉
    </div>


    <h2 style="
        color:#164e63;
        margin:10px 0;
        font-size:26px;
    ">
        Booking Successful!
    </h2>


    <p style="
        color:#64748b;
        margin:0;
        line-height:1.6;
    ">
        Hello <strong>'
        . htmlspecialchars($customerName)
        . '</strong>,
        your room has been successfully booked.
    </p>

</div>


<!-- =========================================
     TICKET
========================================= -->

<div style="
    margin:20px 25px;
    border:2px dashed #2196f3;
    border-radius:15px;
    padding:22px;
    background:#f8fcff;
">


    <h3 style="
        margin:0 0 20px;
        color:#164e63;
        font-size:21px;
    ">
        🎫 Booking Ticket
    </h3>


    <table style="
        width:100%;
        border-collapse:collapse;
        font-size:15px;
    ">


        <!-- TICKET -->

        <tr>

            <td style="
                padding:10px 0;
                color:#64748b;
            ">
                Ticket No.
            </td>

            <td style="
                padding:10px 0;
                text-align:right;
                color:#164e63;
                font-weight:bold;
            ">
                HB'
                . htmlspecialchars($bookingId)
                . '
            </td>

        </tr>


        <!-- NAME -->

        <tr>

            <td style="
                padding:10px 0;
                color:#64748b;
            ">
                Customer Name
            </td>

            <td style="
                padding:10px 0;
                text-align:right;
                color:#164e63;
                font-weight:bold;
            ">
                '
                . htmlspecialchars($customerName)
                . '
            </td>

        </tr>


        <!-- EMAIL -->

        <tr>

            <td style="
                padding:10px 0;
                color:#64748b;
            ">
                Email
            </td>

            <td style="
                padding:10px 0;
                text-align:right;
                color:#164e63;
                word-break:break-word;
            ">
                '
                . htmlspecialchars($customerEmail)
                . '
            </td>

        </tr>


        <!-- ROOM -->

        <tr>

            <td style="
                padding:10px 0;
                color:#64748b;
            ">
                Room Type
            </td>

            <td style="
                padding:10px 0;
                text-align:right;
                color:#164e63;
                font-weight:bold;
            ">
                '
                . htmlspecialchars($roomType)
                . '
            </td>

        </tr>


        <!-- ROOM NUMBER -->

        <tr>

            <td style="
                padding:10px 0;
                color:#64748b;
            ">
                Room No.
            </td>

            <td style="
                padding:10px 0;
                text-align:right;
                color:#164e63;
                font-weight:bold;
            ">
                '
                . htmlspecialchars($roomNo)
                . '
            </td>

        </tr>


        <!-- CHECK IN -->

        <tr>

            <td style="
                padding:10px 0;
                color:#64748b;
            ">
                Check-in
            </td>

            <td style="
                padding:10px 0;
                text-align:right;
                color:#164e63;
            ">
                '
                . htmlspecialchars($checkIn)
                . '
            </td>

        </tr>


        <!-- CHECK OUT -->

        <tr>

            <td style="
                padding:10px 0;
                color:#64748b;
            ">
                Check-out
            </td>

            <td style="
                padding:10px 0;
                text-align:right;
                color:#164e63;
            ">
                '
                . htmlspecialchars($checkOut)
                . '
            </td>

        </tr>


        <!-- GUESTS -->

        <tr>

            <td style="
                padding:10px 0;
                color:#64748b;
            ">
                Guests
            </td>

            <td style="
                padding:10px 0;
                text-align:right;
                color:#164e63;
            ">
                '
                . htmlspecialchars($guests)
                . '
            </td>

        </tr>


        <!-- NIGHTS -->

        <tr>

            <td style="
                padding:10px 0;
                color:#64748b;
            ">
                Total Nights
            </td>

            <td style="
                padding:10px 0;
                text-align:right;
                color:#164e63;
            ">
                '
                . htmlspecialchars($nights)
                . '
            </td>

        </tr>


        <!-- TOTAL -->

        <tr>

            <td style="
                padding:18px 0 10px;
                color:#164e63;
                font-size:18px;
                font-weight:bold;
                border-top:1px solid #cbd5e1;
            ">
                Total Amount
            </td>

            <td style="
                padding:18px 0 10px;
                text-align:right;
                color:#1565c0;
                font-size:21px;
                font-weight:bold;
                border-top:1px solid #cbd5e1;
            ">
                ₹'
                . number_format($totalAmount, 2)
                . '
            </td>

        </tr>


        <!-- STATUS -->

        <tr>

            <td style="
                padding:10px 0;
                color:#64748b;
            ">
                Booking Status
            </td>

            <td style="
                padding:10px 0;
                text-align:right;
                color:#15803d;
                font-weight:bold;
            ">
                ✓ Confirmed
            </td>

        </tr>


    </table>

</div>


<!-- =========================================
     CHECK-IN MESSAGE
========================================= -->

<div style="
    margin:20px 25px;
    padding:16px;
    border-radius:10px;
    background:#eaf6ff;
    color:#164e63;
    font-size:14px;
    line-height:1.6;
">

    <strong>
        ℹ️ Important:
    </strong>

    Please keep this booking ticket with you
    and show it at the hotel reception during
    check-in.

</div>


<!-- =========================================
     FOOTER
========================================= -->

<div style="
    background:#164e63;
    color:white;
    text-align:center;
    padding:22px 15px;
">

    <p style="
        margin:0 0 8px;
        font-size:15px;
    ">
        Thank you for choosing
        <strong>Hotel Booking System</strong>.
    </p>


    <p style="
        margin:0;
        font-size:12px;
        opacity:0.8;
    ">
        This is an automated booking confirmation email.
    </p>

</div>


</div>

</body>

</html>
';


        /*
        ==============================================
        PLAIN TEXT VERSION
        ==============================================
        */

        $mail->AltBody =

            "HOTEL BOOKING - BOOKING CONFIRMATION\n\n"

            . "Booking Successful!\n\n"

            . "Ticket No: HB"
            . $bookingId
            . "\n"

            . "Customer Name: "
            . $customerName
            . "\n"

            . "Email: "
            . $customerEmail
            . "\n"

            . "Room Type: "
            . $roomType
            . "\n"

            . "Room No: "
            . $roomNo
            . "\n"

            . "Check-in: "
            . $checkIn
            . "\n"

            . "Check-out: "
            . $checkOut
            . "\n"

            . "Guests: "
            . $guests
            . "\n"

            . "Total Nights: "
            . $nights
            . "\n"

            . "Total Amount: ₹"
            . number_format(
                $totalAmount,
                2
            )
            . "\n"

            . "Status: Confirmed\n\n"

            . "Please show this ticket during check-in.";


        /*
        ==============================================
        SEND
        ==============================================
        */

        $mail->send();


        return true;


    } catch (Exception $e) {


        /*
        ==============================================
        EMAIL FAILED
        ==============================================
        */

        return false;

    }

}