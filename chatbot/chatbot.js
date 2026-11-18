/* =====================================
   HOTEL CHATBOT JAVASCRIPT
===================================== */

document.addEventListener("DOMContentLoaded", function () {

    const toggleButton =
        document.getElementById("chatbot-toggle");

    const chatbotWindow =
        document.getElementById("chatbot-window");

    const closeButton =
        document.getElementById("chatbot-close");

    const sendButton =
        document.getElementById("chatbot-send");

    const input =
        document.getElementById("chatbot-input");

    const messages =
        document.getElementById("chatbot-messages");


    /* =====================================
       OPEN CHATBOT
    ===================================== */

    toggleButton.addEventListener("click", function () {

        chatbotWindow.style.display = "block";

        input.focus();

    });


    /* =====================================
       CLOSE CHATBOT
    ===================================== */

    closeButton.addEventListener("click", function () {

        chatbotWindow.style.display = "none";

    });


    /* =====================================
       SEND MESSAGE
    ===================================== */

    sendButton.addEventListener("click", function () {

        sendMessage();

    });


    /* =====================================
       ENTER KEY
    ===================================== */

    input.addEventListener("keypress", function (event) {

        if (event.key === "Enter") {

            sendMessage();

        }

    });


    /* =====================================
       SEND MESSAGE FUNCTION
    ===================================== */

    function sendMessage() {

        const message =
            input.value.trim();


        if (message === "") {

            return;

        }


        addUserMessage(message);


        input.value = "";


        setTimeout(function () {

            const response =
                getBotResponse(message);

            addBotMessage(response);

        }, 500);

    }


    /* =====================================
       ADD USER MESSAGE
    ===================================== */

    function addUserMessage(message) {

        const div =
            document.createElement("div");

        div.className =
            "user-message";

        div.textContent =
            message;

        messages.appendChild(div);

        scrollToBottom();

    }


    /* =====================================
       ADD BOT MESSAGE
    ===================================== */

    function addBotMessage(message) {

        const div =
            document.createElement("div");

        div.className =
            "bot-message";

        div.innerHTML =
            message;

        messages.appendChild(div);

        scrollToBottom();

    }


    /* =====================================
       SCROLL
    ===================================== */

    function scrollToBottom() {

        messages.scrollTop =
            messages.scrollHeight;

    }


    /* =====================================
       BOT RESPONSE
    ===================================== */

    function getBotResponse(message) {

        const text =
            message.toLowerCase();


        /* Greeting */

        if (
            text.includes("hello") ||
            text.includes("hi") ||
            text.includes("hey") ||
            text.includes("namaste")
        ) {

            return `
                👋 Hello!
                <br>
                Welcome to our Hotel.
                <br><br>
                How can I help you?
            `;

        }


        /* Available rooms */

        if (
            text.includes("available room") ||
            text.includes("rooms available") ||
            text.includes("room available")
        ) {

            return `
                🛏️ You can check all available
                rooms from our Rooms page.
                <br><br>

                <a href="rooms.php">
                    👉 View Available Rooms
                </a>
            `;

        }


        /* Booking */

        if (
            text.includes("book") ||
            text.includes("booking") ||
            text.includes("reserve")
        ) {

            return `
                📅 Booking a room is easy!
                <br><br>

                1️⃣ Select your room.
                <br>
                2️⃣ Select check-in and check-out dates.
                <br>
                3️⃣ Login/Register.
                <br>
                4️⃣ Confirm your booking.
                <br><br>

                <a href="rooms.php">
                    👉 Start Booking
                </a>
            `;

        }


        /* Price */

        if (
            text.includes("price") ||
            text.includes("cost") ||
            text.includes("rate") ||
            text.includes("charges")
        ) {

            return `
                💰 Room prices are different
                according to room type.
                <br><br>

                You can see the price
                on each room.
                <br><br>

                <a href="rooms.php">
                    👉 Check Room Prices
                </a>
            `;

        }


        /* Check-in */

        if (
            text.includes("check in") ||
            text.includes("check-in")
        ) {

            return `
                🕐 Check-in time is
                generally from 12:00 PM.
                <br><br>

                Please contact hotel reception
                for special requests.
            `;

        }


        /* Check-out */

        if (
            text.includes("check out") ||
            text.includes("checkout") ||
            text.includes("check-out")
        ) {

            return `
                🕐 Check-out time is
                generally before 11:00 AM.
                <br><br>

                Please contact reception if
                you need late checkout.
            `;

        }


        /* Facilities */

        if (
            text.includes("facility") ||
            text.includes("facilities") ||
            text.includes("amenities")
        ) {

            return `
                🏨 Our hotel provides
                comfortable rooms and
                essential facilities.
                <br><br>

                You can check the room
                details for more information.
            `;

        }


        /* Location */

        if (
            text.includes("location") ||
            text.includes("address") ||
            text.includes("where")
        ) {

            return `
                📍 Our hotel location details
                can be provided by the
                hotel reception.
                <br><br>

                Please contact us for
                exact directions.
            `;

        }


        /* Cancel */

        if (
            text.includes("cancel") ||
            text.includes("cancellation")
        ) {

            return `
                ❌ Cancellation depends on
                your booking status and
                hotel policy.
                <br><br>

                Please contact the hotel
                reception for cancellation
                assistance.
            `;

        }


        /* Thank you */

        if (
            text.includes("thank") ||
            text.includes("thanks")
        ) {

            return `
                😊 You're welcome!
                <br><br>
                Have a wonderful stay! 🏨
            `;

        }


        /* Default */

        return `
            🤔 Sorry, I didn't understand that.
            <br><br>

            You can ask me about:
            <br>
            🛏️ Available rooms
            <br>
            📅 Booking
            <br>
            💰 Room prices
            <br>
            🕐 Check-in / Check-out
            <br>
            🏨 Facilities
        `;

    }


    /* =====================================
       QUICK MESSAGE
    ===================================== */

    window.sendQuickMessage =
        function (message) {

            input.value = message;

            sendMessage();

        };

});