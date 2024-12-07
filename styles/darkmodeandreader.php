<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Theme & Screen Reader Example</title>
    <link id="theme-link" rel="stylesheet" href="./styles/styles.css"> <!-- Default (Light Mode) CSS -->
    <style>
        /* Floating Button Styles */
        .theme-switcher, .screen-reader {
            position: fixed;
            width: 50px;
            height: 50px;
            color: #fff;
            border-radius: 50%;
            text-align: center;
            line-height: 50px;
            font-size: 20px;
            cursor: pointer;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            z-index: 1000;
        }

        /* Position for Theme Switcher */
        .theme-switcher {
            bottom: 20px;
            left: 20px;
            background-color: whitesmoke;
        }

        /* Position for Screen Reader */
        .screen-reader {
            bottom: 80px;
            left: 20px;
            background-color: #007bff;
        }
    </style>
</head>
<body>

    <!-- Floating Dark Mode Toggle Icon -->
    <div class="theme-switcher" id="themeSwitcher">🌓</div>

    <!-- Floating Screen Reader Icon -->
    <div class="screen-reader" id="screenReader">🔊</div>



    <!-- JavaScript for Dark Mode and Screen Reader -->
    <script>
// Theme Switcher Functionality
const themeSwitcher = document.getElementById("themeSwitcher");
const themeLink = document.getElementById("theme-link");

// Check for saved theme in localStorage and apply it
const savedTheme = localStorage.getItem("theme");
if (savedTheme) {
    themeLink.setAttribute("href", savedTheme);
}

// Toggle theme and save to localStorage
themeSwitcher.addEventListener("click", () => {
    if (themeLink.getAttribute("href") === "/alm/styles/styles.css") {
        themeLink.setAttribute("href", "/alm/styles/styles2.css"); // Switch to Dark Mode
        localStorage.setItem("theme", "/alm/styles/styles2.css");  // Save preference to localStorage
    } else {
        themeLink.setAttribute("href", "/alm/styles/styles.css"); // Switch back to Light Mode
        localStorage.setItem("theme", "/alm/styles/styles.css");  // Save preference to localStorage
    }
});



        // Screen Reader Functionality
        const screenReaderButton = document.getElementById("screenReader");

        function readContent() {
            const text = document.body.innerText;  // Get all text on the page
            const speech = new SpeechSynthesisUtterance(text);  // Create speech object
            speech.rate = 1;  // Set speed
            speech.pitch = 1;  // Set pitch
            speechSynthesis.speak(speech);  // Speak the content
        }

        screenReaderButton.addEventListener("click", readContent);
    </script>

</body>
</html>
