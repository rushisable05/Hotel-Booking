<?php

require_once "config/database.php";

include "includes/header.php";

?>

<style>

/* =====================================================
   HOTEL BOOKING - INDEX PAGE FINAL THEME
   ===================================================== */

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: "Segoe UI", Arial, sans-serif;
    background: #eef7ff;
    color: #164e63;
}


/* =====================================================
   HERO SECTION
   ===================================================== */

.hero {
    min-height: 590px;

    display: flex;
    align-items: center;
    justify-content: center;

    text-align: center;

    padding: 80px 20px;

    position: relative;

    background:
        linear-gradient(
            135deg,
            rgba(33, 150, 243, 0.92),
            rgba(79, 70, 229, 0.90)
        ),
        url("images/hotel-bg.jpg");

    background-size: cover;
    background-position: center;

    overflow: hidden;
}


/* Decorative circles */

.hero::before {
    content: "";

    position: absolute;

    width: 350px;
    height: 350px;

    border-radius: 50%;

    background: rgba(255,255,255,0.08);

    top: -150px;
    left: -100px;
}

.hero::after {
    content: "";

    position: absolute;

    width: 450px;
    height: 450px;

    border-radius: 50%;

    background: rgba(255,255,255,0.07);

    bottom: -220px;
    right: -120px;
}


/* Hero Content */

.hero-content {
    position: relative;

    z-index: 2;

    max-width: 850px;

    color: white;
}


.hero-icon {
    font-size: 70px;

    margin-bottom: 15px;

    animation: hotelFloat 3s ease-in-out infinite;
}


@keyframes hotelFloat {

    0% {
        transform: translateY(0);
    }

    50% {
        transform: translateY(-10px);
    }

    100% {
        transform: translateY(0);
    }

}


.hero h1 {
    font-size: 58px;

    margin: 0 0 20px;

    font-weight: 800;

    letter-spacing: -1px;
}


.hero h1 span {
    color: #ffd166;
}


.hero p {
    font-size: 20px;

    line-height: 1.7;

    margin: 0 auto 35px;

    color: #f8fbff;

    max-width: 700px;
}


/* =====================================================
   HERO BUTTONS
   ===================================================== */

.hero-buttons {

    display: flex;

    justify-content: center;

    gap: 15px;

    flex-wrap: wrap;

}


.primary-btn,
.secondary-btn {

    display: inline-block;

    padding: 15px 30px;

    border-radius: 12px;

    text-decoration: none;

    font-size: 16px;

    font-weight: 700;

    transition: all 0.3s ease;

}


.primary-btn {

    background: #ffffff;

    color: #1769aa;

    box-shadow:
        0 8px 25px rgba(0,0,0,0.15);

}


.primary-btn:hover {

    transform: translateY(-4px);

    background: #ffd166;

    color: #164e63;

}


.secondary-btn {

    background: rgba(255,255,255,0.12);

    color: white;

    border: 2px solid rgba(255,255,255,0.7);

}


.secondary-btn:hover {

    background: white;

    color: #1769aa;

    transform: translateY(-4px);

}


/* =====================================================
   AVAILABILITY SECTION
   ===================================================== */

.availability-section {

    max-width: 1150px;

    margin: -65px auto 0;

    position: relative;

    z-index: 5;

    padding: 0 20px;
}


.availability-card {

    background: white;

    border-radius: 22px;

    padding: 35px;

    box-shadow:
        0 20px 50px rgba(15, 23, 42, 0.15);

    border: 1px solid #dbeafe;
}


.availability-title {

    text-align: center;

    margin-bottom: 25px;
}


.availability-title h2 {

    margin: 0 0 8px;

    font-size: 28px;

    color: #164e63;
}


.availability-title p {

    margin: 0;

    color: #64748b;

}


/* Form */

.availability-form {

    display: grid;

    grid-template-columns:
        repeat(3, 1fr) auto;

    gap: 15px;

    align-items: end;
}


.form-box label {

    display: block;

    margin-bottom: 8px;

    font-size: 14px;

    font-weight: 700;

    color: #164e63;
}


.form-box input,
.form-box select {

    width: 100%;

    padding: 13px 14px;

    border: 1px solid #cbd5e1;

    border-radius: 10px;

    background: #f8fbff;

    color: #334155;

    font-size: 15px;

    outline: none;

    transition: 0.3s;
}


.form-box input:focus,
.form-box select:focus {

    border-color: #2196f3;

    box-shadow:
        0 0 0 3px rgba(33,150,243,0.12);

    background: white;
}


.check-btn {

    padding: 13px 22px;

    border: none;

    border-radius: 10px;

    background:
        linear-gradient(
            135deg,
            #2196f3,
            #4f46e5
        );

    color: white;

    font-size: 15px;

    font-weight: 700;

    cursor: pointer;

    transition: 0.3s;

    white-space: nowrap;
}


.check-btn:hover {

    transform: translateY(-3px);

    box-shadow:
        0 8px 20px rgba(33,150,243,0.25);
}


/* =====================================================
   FEATURES
   ===================================================== */

.features-section {

    padding: 80px 20px 70px;

    max-width: 1200px;

    margin: auto;
}


.section-heading {

    text-align: center;

    margin-bottom: 45px;
}


.section-heading h2 {

    font-size: 36px;

    margin: 0 0 10px;

    color: #164e63;
}


.section-heading p {

    margin: 0;

    color: #64748b;

    font-size: 16px;
}


.features-grid {

    display: grid;

    grid-template-columns:
        repeat(3, 1fr);

    gap: 25px;
}


.feature-card {

    background: white;

    padding: 32px 25px;

    text-align: center;

    border-radius: 18px;

    border: 1px solid #dbeafe;

    box-shadow:
        0 8px 25px rgba(15,23,42,0.07);

    transition: all 0.3s ease;
}


.feature-card:hover {

    transform: translateY(-8px);

    box-shadow:
        0 18px 35px rgba(15,23,42,0.12);

}


.feature-icon {

    width: 70px;

    height: 70px;

    margin: 0 auto 18px;

    display: flex;

    align-items: center;

    justify-content: center;

    border-radius: 18px;

    font-size: 32px;

    background:
        linear-gradient(
            135deg,
            #dbeafe,
            #ede9fe
        );
}


.feature-card:nth-child(2)
.feature-icon {

    background:
        linear-gradient(
            135deg,
            #dcfce7,
            #dbeafe
        );
}


.feature-card:nth-child(3)
.feature-icon {

    background:
        linear-gradient(
            135deg,
            #fef3c7,
            #fed7aa
        );
}


.feature-card h3 {

    margin: 0 0 10px;

    color: #164e63;

    font-size: 20px;
}


.feature-card p {

    margin: 0;

    color: #64748b;

    line-height: 1.7;

    font-size: 15px;
}


/* =====================================================
   ROOM PREVIEW
   ===================================================== */

.rooms-preview {

    padding: 70px 20px 80px;

    background:
        linear-gradient(
            180deg,
            #eaf6ff,
            #f8fbff
        );
}


.rooms-container {

    max-width: 1200px;

    margin: auto;
}


.room-preview-grid {

    display: grid;

    grid-template-columns:
        repeat(3, 1fr);

    gap: 25px;
}


.preview-card {

    background: white;

    border-radius: 18px;

    overflow: hidden;

    border: 1px solid #dbeafe;

    box-shadow:
        0 8px 25px rgba(15,23,42,0.08);

    transition: 0.3s;
}


.preview-card:hover {

    transform: translateY(-7px);

    box-shadow:
        0 18px 35px rgba(15,23,42,0.13);
}


.preview-image {

    height: 210px;

    overflow: hidden;

    background:
        linear-gradient(
            135deg,
            #bfdbfe,
            #ddd6fe
        );
}


.preview-image img {

    width: 100%;

    height: 100%;

    object-fit: cover;

    display: block;

    transition: 0.5s;
}


.preview-card:hover
.preview-image img {

    transform: scale(1.06);
}


.preview-content {

    padding: 22px;
}


.preview-content h3 {

    margin: 0 0 8px;

    color: #164e63;

    font-size: 21px;
}


.preview-content p {

    margin: 0 0 15px;

    color: #64748b;

}


.preview-price {

    color: #1769aa;

    font-weight: 800;

    font-size: 18px;
}


/* =====================================================
   WHY CHOOSE US
   ===================================================== */

.why-section {

    max-width: 1100px;

    margin: auto;

    padding: 80px 20px;
}


.why-box {

    display: grid;

    grid-template-columns: 1fr 1fr;

    gap: 50px;

    align-items: center;

    background: white;

    border-radius: 25px;

    padding: 45px;

    box-shadow:
        0 15px 40px rgba(15,23,42,0.08);

    border: 1px solid #dbeafe;
}


.why-content h2 {

    margin: 0 0 15px;

    font-size: 34px;

    color: #164e63;
}


.why-content > p {

    color: #64748b;

    line-height: 1.8;

    margin-bottom: 25px;
}


.why-list {

    list-style: none;

    padding: 0;

    margin: 0;
}


.why-list li {

    padding: 10px 0;

    color: #334155;

    font-weight: 600;
}


.why-list li span {

    color: #16a34a;

    margin-right: 8px;
}


.why-visual {

    min-height: 300px;

    border-radius: 20px;

    display: flex;

    justify-content: center;

    align-items: center;

    text-align: center;

    font-size: 100px;

    background:
        linear-gradient(
            135deg,
            #dbeafe,
            #ede9fe,
            #fce7f3
        );
}


/* =====================================================
   CTA
   ===================================================== */

.cta-section {

    padding: 70px 20px;

    background:
        linear-gradient(
            135deg,
            #164e63,
            #2563eb,
            #4f46e5
        );

    text-align: center;

    color: white;
}


.cta-section h2 {

    margin: 0 0 12px;

    font-size: 36px;
}


.cta-section p {

    margin: 0 auto 25px;

    max-width: 650px;

    color: #e0f2fe;

    line-height: 1.7;
}


.cta-btn {

    display: inline-block;

    padding: 14px 30px;

    background: #ffd166;

    color: #164e63;

    border-radius: 11px;

    text-decoration: none;

    font-weight: 800;

    transition: 0.3s;
}


.cta-btn:hover {

    background: white;

    transform: translateY(-3px);
}


/* =====================================================
   RESPONSIVE
   ===================================================== */

@media (max-width: 900px) {

    .hero h1 {
        font-size: 45px;
    }

    .availability-form {
        grid-template-columns: 1fr 1fr;
    }

    .features-grid {
        grid-template-columns: 1fr 1fr;
    }

    .room-preview-grid {
        grid-template-columns: 1fr 1fr;
    }

    .why-box {
        grid-template-columns: 1fr;
    }

}


@media (max-width: 600px) {

    .hero {
        min-height: 520px;
    }

    .hero h1 {
        font-size: 35px;
    }

    .hero p {
        font-size: 17px;
    }

    .availability-card {
        padding: 22px;
    }

    .availability-form {
        grid-template-columns: 1fr;
    }

    .features-grid {
        grid-template-columns: 1fr;
    }

    .room-preview-grid {
        grid-template-columns: 1fr;
    }

    .why-box {
        padding: 25px;
    }

    .why-visual {
        min-height: 220px;
        font-size: 75px;
    }

    .section-heading h2 {
        font-size: 29px;
    }

    .cta-section h2 {
        font-size: 29px;
    }

}

</style>


<!-- =====================================================
     HERO
     ===================================================== -->

<section class="hero">

    <div class="hero-content">

        <div class="hero-icon">
            🏨
        </div>

        <h1>
            Welcome to
            <span>Our Hotel</span>
        </h1>

        <p>
            Experience comfortable rooms, modern facilities,
            affordable prices and easy online booking.
        </p>


        <div class="hero-buttons">

            <a
                href="rooms.php"
                class="primary-btn"
            >
                🛏️ Explore Rooms
            </a>

            <a
                href="check-availability.php"
                class="secondary-btn"
            >
                📅 Check Availability
            </a>

        </div>

    </div>

</section>


<!-- =====================================================
     AVAILABILITY
     ===================================================== -->


<form action="rooms.php" method="get"
      style="max-width:1000px;margin:25px auto;padding:22px;background:#fff;border-radius:16px;box-shadow:0 8px 25px #2196f31a">

    <h2 style="color:#164e63;margin-bottom:18px">
        📍 Find Rooms by Destination
    </h2>

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:14px">

        <div>
            <label for="home_destination">Destination</label>
            <select id="home_destination" name="destination"
                    style="width:100%;padding:12px;margin-top:7px;border:1px solid #cbd5e1;border-radius:9px">
                <option value="">All destinations</option>
                <option value="Ahmedabad">Ahmedabad</option>
                <option value="Mumbai">Mumbai</option>
                <option value="Pune">Pune</option>
                <option value="Goa">Goa</option>
                <option value="Delhi">Delhi</option>
                <option value="Jaipur">Jaipur</option>
                <option value="Bengaluru">Bengaluru</option>
                <option value="Hyderabad">Hyderabad</option>
                <option value="Chennai">Chennai</option>
                <option value="Kolkata">Kolkata</option>
            </select>
        </div>

        <div>
            <label for="home_check_in">Check-in</label>
            <input type="date" id="home_check_in" name="check_in"
                   min="<?= date('Y-m-d') ?>"
                   style="width:100%;padding:12px;margin-top:7px;border:1px solid #cbd5e1;border-radius:9px">
        </div>

        <div>
            <label for="home_check_out">Check-out</label>
            <input type="date" id="home_check_out" name="check_out"
                   min="<?= date('Y-m-d', strtotime('+1 day')) ?>"
                   style="width:100%;padding:12px;margin-top:7px;border:1px solid #cbd5e1;border-radius:9px">
        </div>
    </div>

    <button type="submit"
            style="width:100%;margin-top:18px;padding:13px;border:0;border-radius:9px;background:#2196f3;color:#fff;font-weight:bold;cursor:pointer">
        Search Destination
    </button>
</form>

<script>
const homeIn = document.getElementById("home_check_in");
const homeOut = document.getElementById("home_check_out");

homeIn.addEventListener("change", function () {
    if (!homeIn.value) return;

    const d = new Date(homeIn.value + "T12:00:00");
    d.setDate(d.getDate() + 1);

    const min = d.getFullYear() + "-" +
        String(d.getMonth() + 1).padStart(2, "0") + "-" +
        String(d.getDate()).padStart(2, "0");

    homeOut.min = min;

    if (homeOut.value && homeOut.value < min) {
        homeOut.value = min;
    }
});
</script>



<!-- =====================================================
     FEATURES
     ===================================================== -->

<section class="features-section">

    <div class="section-heading">

        <h2>
            Why Stay With Us?
        </h2>

        <p>
            Everything you need for a comfortable stay.
        </p>

    </div>


    <div class="features-grid">


        <div class="feature-card">

            <div class="feature-icon">
                🛏️
            </div>

            <h3>
                Comfortable Rooms
            </h3>

            <p>
                Clean, spacious and comfortable rooms
                designed for a relaxing stay.
            </p>

        </div>


        <div class="feature-card">

            <div class="feature-icon">
                💰
            </div>

            <h3>
                Affordable Prices
            </h3>

            <p>
                Enjoy quality accommodation with
                reasonable and transparent pricing.
            </p>

        </div>


        <div class="feature-card">

            <div class="feature-icon">
                🔒
            </div>

            <h3>
                Easy & Secure Booking
            </h3>

            <p>
                Check availability and reserve your
                preferred room with ease.
            </p>

        </div>


    </div>

</section>


<!-- =====================================================
     ROOM PREVIEW
     ===================================================== -->

<section class="rooms-preview">

    <div class="rooms-container">


        <div class="section-heading">

            <h2>
                🏨 Our Popular Rooms
            </h2>

            <p>
                Choose the room that fits your needs.
            </p>

        </div>


        <div class="room-preview-grid">


            <!-- SINGLE -->

            <div class="preview-card">

                <div class="preview-image">

                    <img
                        src="images/single-room.jpg"
                        alt="Single Room"
                        onerror="this.style.display='none';"
                    >

                </div>

                <div class="preview-content">

                    <h3>
                        Single Room
                    </h3>

                    <p>
                        Perfect for one guest.
                    </p>

                    <div class="preview-price">
                        ₹1,500 / Night
                    </div>

                </div>

            </div>


            <!-- DOUBLE -->

            <div class="preview-card">

                <div class="preview-image">

                    <img
                        src="images/double-room.jpg"
                        alt="Double Room"
                        onerror="this.style.display='none';"
                    >

                </div>

                <div class="preview-content">

                    <h3>
                        Double Room
                    </h3>

                    <p>
                        Spacious room for two guests.
                    </p>

                    <div class="preview-price">
                        ₹2,500 / Night
                    </div>

                </div>

            </div>


            <!-- DELUXE -->

            <div class="preview-card">

                <div class="preview-image">

                    <img
                        src="images/deluxe-room.jpg"
                        alt="Deluxe Room"
                        onerror="this.style.display='none';"
                    >

                </div>

                <div class="preview-content">

                    <h3>
                        Deluxe Room
                    </h3>

                    <p>
                        Premium room with modern facilities.
                    </p>

                    <div class="preview-price">
                        ₹3,500 / Night
                    </div>

                </div>

            </div>


        </div>


        <div style="
            text-align:center;
            margin-top:35px;
        ">

            <a
                href="rooms.php"
                class="primary-btn"
                style="
                    background:#2196f3;
                    color:white;
                "
            >
                View All Rooms →
            </a>

        </div>

    </div>

</section>


<!-- =====================================================
     WHY CHOOSE US
     ===================================================== -->

<section class="why-section">

    <div class="why-box">


        <div class="why-content">

            <h2>
                Your Comfort Is Our Priority
            </h2>

            <p>
                We provide a simple hotel booking experience
                where you can explore rooms, check availability
                and make your booking easily.
            </p>


            <ul class="why-list">

                <li>
                    <span>✓</span>
                    Clean and comfortable rooms
                </li>

                <li>
                    <span>✓</span>
                    Easy online room availability
                </li>

                <li>
                    <span>✓</span>
                    Simple booking process
                </li>

                <li>
                    <span>✓</span>
                    Multiple room options
                </li>

                <li>
                    <span>✓</span>
                    Helpful customer support
                </li>

            </ul>

        </div>


        <div class="why-visual">

            🏨

        </div>


    </div>

</section>


<!-- =====================================================
     CALL TO ACTION
     ===================================================== -->

<section class="cta-section">

    <h2>
        Ready for a Comfortable Stay?
    </h2>

    <p>
        Explore our rooms and find the perfect
        accommodation for your next stay.
    </p>

    <a
        href="rooms.php"
        class="cta-btn"
    >
        🛎️ Book Your Room
    </a>

</section>


<!-- =====================================================
     CHATBOT
     ===================================================== -->

<?php include "includes/chatbot.php"; ?>


<!-- =====================================================
     FOOTER
     ===================================================== -->

<?php include "includes/footer.php"; ?>