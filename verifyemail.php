<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Connect to the database
require("databaseconnection.php");
$db = connectDatabase();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Email Verification</title>
    <link rel="stylesheet" href="./styles/styles.css">
</head>
<body>
    <div class="container">
        <header>
            <div class="title">
                <h1>Email Verification</h1>
            </div>
        </header>

        <div class="about-section">
            <?php
            // Check if the token is provided
            if (isset($_GET['token'])) {
                $token = $_GET['token'];

                // Validate the token
                $stmt = $db->prepare("SELECT member_id FROM library_member WHERE verification_token = :token AND email_verified = 0");
                $stmt->bindParam(':token', $token, SQLITE3_TEXT);
                $result = $stmt->execute();

                if ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                    // Token is valid, update the email_verified status
                    $updateStmt = $db->prepare("UPDATE library_member SET email_verified = 1 WHERE verification_token = :token");
                    $updateStmt->bindParam(':token', $token, SQLITE3_TEXT);

                    if ($updateStmt->execute()) {
                        echo "<h2>Email Verified Successfully!</h2>";
                        echo "<p>Your email has been verified. You can now log in to your account.</p>";
                        echo "<a class='search-button' href='member/login_member.php'>Log In</a>";
                    } else {
                        echo "<h2>Error</h2>";
                        echo "<p>There was an issue updating your verification status. Please try again later or contact support.</p>";
                        echo "<a class='back-link' href='registerpage.php'>Go Back to Registration</a>";
                    }
                } else {
                    echo "<h2>Invalid or Expired Token</h2>";
                    echo "<p>The verification link is invalid or has already been used. Please try registering again or contact support.</p>";
                    echo "<a class='back-link' href='registerpage.php'>Go Back to Registration</a>";
                }
            } else {
                echo "<h2>Token Missing</h2>";
                echo "<p>No verification token was provided. Please check your email and try again.</p>";
                echo "<a class='back-link' href='registerpage.php'>Go Back to Registration</a>";
            }
            ?>
        </div>
    </div>

    <footer>
        <p>&copy; 2024 Advanced Library Management System. All Rights Reserved.</p>
    </footer>
</body>
</html>
