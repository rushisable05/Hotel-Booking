<div id="hotel-chatbot">

    <!-- Chatbot Button -->
    <button id="chatbot-toggle" type="button">
        🤖
    </button>


    <!-- Chatbot Window -->
    <div id="chatbot-window">

        <!-- Header -->
        <div class="chatbot-header">

            <div>
                <strong>Hotel Assistant</strong>
                <small>Online</small>
            </div>

            <button
                id="chatbot-close"
                type="button"
            >
                ×
            </button>

        </div>


        <!-- Messages -->
        <div
            id="chatbot-messages"
            class="chatbot-messages"
        >

            <div class="bot-message">
                👋 Hello! Welcome to our hotel.
                <br><br>
                How can I help you?
            </div>

        </div>


        <!-- Quick Questions -->
        <div class="chatbot-quick-buttons">

            <button
                type="button"
                onclick="sendQuickMessage('What rooms are available?')"
            >
                🛏️ Available Rooms
            </button>

            <button
                type="button"
                onclick="sendQuickMessage('How can I book a room?')"
            >
                📅 How to Book?
            </button>

            <button
                type="button"
                onclick="sendQuickMessage('What are your prices?')"
            >
                💰 Room Prices
            </button>

        </div>


        <!-- Input -->
        <div class="chatbot-input">

            <input
                type="text"
                id="chatbot-input"
                placeholder="Type your message..."
                autocomplete="off"
            >

            <button
                type="button"
                id="chatbot-send"
            >
                ➤
            </button>

        </div>

    </div>

</div>


<script src="chatbot/chatbot.js"></script>
<link
    rel="stylesheet"
    href="chatbot/chatbot.css"
>