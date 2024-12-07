<?php
// Start the session
session_start();

// Include the database connection file
require("../databaseconnection.php");
$db = connectDatabase();

// Initialize an error message variable
$error_message = "";

// Your reCAPTCHA secret key
$secret_key = '6LcQQJQqAAAAADp8RcgqJVyEAv8Wcv4_KZkYwWdR';

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get form data
    $email = $_POST['email'];
    $password = $_POST['password'];
    $recaptcha_response = $_POST['g-recaptcha-response'];

    // Verify the reCAPTCHA response
    $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
    $recaptcha_response_data = file_get_contents($recaptcha_url . '?secret=' . $secret_key . '&response=' . $recaptcha_response);
    $recaptcha_result = json_decode($recaptcha_response_data, true);

    if ($recaptcha_result['success']) { // Check only the success field for v2

        // Prepare a SQL query to find the user by email in the 'accountant' table
        $stmt = $db->prepare("SELECT * FROM accountant WHERE email = :email");
        $stmt->bindParam(':email', $email, SQLITE3_TEXT);
        $result = $stmt->execute();
        $user = $result->fetchArray(SQLITE3_ASSOC);

        // Check if a user with the provided email was found
        if ($user) {
            // Check if the email has been verified
                // Verify the password
                if (password_verify($password, $user['password_hash'])) {
                    // Password is correct, set session variables
                    $_SESSION['accountant_id'] = $user['accountant_id'];
                    $_SESSION['first_name'] = $user['first_name'];

                    // Redirect to the accountant's dashboard or homepage
                    header("Location: accountant_dashboard.php");
                    exit();
                } else {
                    // Password is incorrect
                    $error_message = "Invalid password. Please try again.";
                }
            } else {
                // Email has not been verified
                $error_message = "Your email address has not been verified. Please check your inbox for the verification email.";
            }
        } else {
            // User with that email does not exist
            $error_message = "No account found with that email. Please try again.";
        }
    } else {
        $error_message = "Captcha verification failed. Please try again.";
    }

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login - Advanced Library Management System</title>
    <link rel="stylesheet" href="../styles/styles.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
    <div class="container">

        <!-- Title Section -->
        <header>
            <div class="title">
                <h1>Login</h1>
            </div>
        </header>

        <!-- Navigation Menu -->
        <?php require("../styles/darkmodeandreader.php"); ?>

        <!-- Login Section -->
        <div class="about-section">
            <h2>Accountant</h2>
            
            <!-- Show error message if login fails -->
            <?php if (!empty($error_message)): ?>
                <p class="error" style="color: red;"><?php echo $error_message; ?></p>
            <?php endif; ?>

            <form action="login_accountant.php" method="post">
                <div class="form-group">
                    <input type="email" name="email" placeholder="Email Address" class="search-bar" required>
                </div>
                <div class="form-group">
                    <input type="password" name="password" placeholder="Password" class="search-bar" required>
                </div>
                <div class="form-group">
                    <div class="g-recaptcha" data-sitekey="6LcQQJQqAAAAAKxJy_5ZzoGGu2wS77AncQvEwMQu"></div>
                </div>
                <div class="form-group">
                    <button type="submit" class="search-button">Login</button>
                </div>
            </form>
            <div class="back-button-section">
                <a href="../loginpage.php" class="back-link">Not you? (Back)</a>
            </div>
        </div>

        <!-- Footer -->
        <?php require("../footer.php"); ?>
    </div>
</body>
</html>
