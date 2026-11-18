document.addEventListener("DOMContentLoaded", function () {

    /* =====================================
       AUTO HIDE MESSAGES
    ===================================== */

    const messages = document.querySelectorAll(
        ".success-message, .error-box"
    );

    messages.forEach(function (message) {

        setTimeout(function () {

            message.style.transition = "opacity 0.5s";
            message.style.opacity = "0";

            setTimeout(function () {
                message.remove();
            }, 500);

        }, 5000);

    });


    /* =====================================
       CHECK-IN / CHECK-OUT DATE
    ===================================== */

    const checkIn = document.getElementById("check_in");
    const checkOut = document.getElementById("check_out");

    if (checkIn && checkOut) {

        // Today's date
        const today = new Date();

        const year = today.getFullYear();
        const month = String(
            today.getMonth() + 1
        ).padStart(2, "0");

        const day = String(
            today.getDate()
        ).padStart(2, "0");

        const todayString =
            year + "-" + month + "-" + day;


        // Minimum check-in date
        checkIn.min = todayString;


        // When check-in changes
        checkIn.addEventListener(
            "change",
            function () {

                if (checkIn.value) {

                    checkOut.min = checkIn.value;

                    // If checkout is before check-in
                    if (
                        checkOut.value &&
                        checkOut.value <= checkIn.value
                    ) {

                        checkOut.value = "";

                    }

                }

            }
        );

    }


    /* =====================================
       BOOKING FORM VALIDATION
    ===================================== */

    const bookingForm =
        document.querySelector(".booking-form");

    if (bookingForm) {

        bookingForm.addEventListener(
            "submit",
            function (event) {

                const checkInValue =
                    document.getElementById("check_in");

                const checkOutValue =
                    document.getElementById("check_out");

                if (
                    checkInValue &&
                    checkOutValue
                ) {

                    if (
                        checkInValue.value &&
                        checkOutValue.value &&
                        checkOutValue.value <= checkInValue.value
                    ) {

                        event.preventDefault();

                        alert(
                            "Check-out date must be after check-in date."
                        );

                    }

                }

            }
        );

    }


    /* =====================================
       CONFIRM DELETE
    ===================================== */

    const deleteButtons =
        document.querySelectorAll(".delete-btn");

    deleteButtons.forEach(function (button) {

        button.addEventListener(
            "click",
            function (event) {

                const confirmed = confirm(
                    "Are you sure you want to delete this item?"
                );

                if (!confirmed) {
                    event.preventDefault();
                }

            }
        );

    });


    /* =====================================
       ROOM PRICE CALCULATION
    ===================================== */

    const priceInput =
        document.getElementById("room_price");

    const nightsInput =
        document.getElementById("nights");

    const totalInput =
        document.getElementById("total_amount");

    if (
        priceInput &&
        nightsInput &&
        totalInput
    ) {

        function calculateTotal() {

            const price =
                parseFloat(priceInput.value) || 0;

            const nights =
                parseInt(nightsInput.value) || 0;

            const total =
                price * nights;

            totalInput.value =
                total.toFixed(2);

        }


        priceInput.addEventListener(
            "input",
            calculateTotal
        );

        nightsInput.addEventListener(
            "input",
            calculateTotal
        );

    }


    /* =====================================
       MOBILE NAVIGATION
    ===================================== */

    const navLinks =
        document.querySelectorAll("nav a");

    navLinks.forEach(function (link) {

        link.addEventListener(
            "click",
            function () {

                navLinks.forEach(function (item) {
                    item.classList.remove("active");
                });

                this.classList.add("active");

            }
        );

    });


    /* =====================================
       BUTTON LOADING
    ===================================== */

    const forms =
        document.querySelectorAll("form");

    forms.forEach(function (form) {

        form.addEventListener(
            "submit",
            function () {

                const submitButton =
                    form.querySelector(
                        'button[type="submit"], input[type="submit"]'
                    );

                if (submitButton) {

                    submitButton.dataset.originalText =
                        submitButton.innerText ||
                        submitButton.value;

                    if (submitButton.tagName === "BUTTON") {

                        submitButton.innerText =
                            "Please wait...";

                    } else {

                        submitButton.value =
                            "Please wait...";

                    }

                    setTimeout(function () {

                        submitButton.disabled = false;

                    }, 3000);

                }

            }
        );

    });


    /* =====================================
       CONSOLE MESSAGE
    ===================================== */

    console.log(
        "Hotel Booking System loaded successfully."
    );

});