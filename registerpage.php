<?php
// Start the session
session_start();

// Connect to the SQLite database
require("databaseconnection.php");
$db = connectDatabase();

// Function to generate a random token
function generateToken() {
    return bin2hex(random_bytes(16)); // Generates a 32-character token
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $preference = $_POST['preference'];
    $address = $_POST['address'];
    $dob = $_POST['dob'];
    $password = $_POST['password'];

    // Hash the password for security
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Default values
    $membership_status = 'Active';
    $email_verified = 0;
    $verification_token = generateToken();

    // Insert the new member into the database
    $sql = "INSERT INTO library_member (
                first_name, last_name, email, password_hash, phone_number, 
                address, date_of_birth, membership_status, registration_date, 
                email_verified, verification_token
            ) VALUES (
                :first_name, :last_name, :email, :password_hash, :phone, 
                :address, :dob, :membership_status, CURRENT_TIMESTAMP, 
                :email_verified, :verification_token
            )";

    $stmt = $db->prepare($sql);

    // Bind parameters
    $stmt->bindParam(':first_name', $first_name, SQLITE3_TEXT);
    $stmt->bindParam(':last_name', $last_name, SQLITE3_TEXT);
    $stmt->bindParam(':email', $email, SQLITE3_TEXT);
    $stmt->bindParam(':password_hash', $password_hash, SQLITE3_TEXT);
    $stmt->bindParam(':phone', $phone, SQLITE3_TEXT);
    $stmt->bindParam(':address', $address, SQLITE3_TEXT);
    $stmt->bindParam(':dob', $dob, SQLITE3_TEXT);
    $stmt->bindParam(':membership_status', $membership_status, SQLITE3_TEXT);
    $stmt->bindParam(':email_verified', $email_verified, SQLITE3_INTEGER);
    $stmt->bindParam(':verification_token', $verification_token, SQLITE3_TEXT);

    // Execute the statement and handle errors
    if ($stmt->execute()) {
        // Prepare the verification email
        $verification_link = "http://localhost/alm/verifyemail.php?token=$verification_token";
        $subject = "Email Verification";
        $message = "Hi $first_name,\n\nClick the link below to verify your email:\n$verification_link\n\nThank you!";
        $headers = "From: no-reply@localhost";

        if (mail($email, $subject, $message, $headers)) {
            $_SESSION['message'] = "Registration successful! Check your email to verify your account.";
        } else {
            $_SESSION['message'] = "Registration successful, but email could not be sent. Contact support.";
        }
    } else {
        // Capture detailed database error
        $_SESSION['message'] = "Error: Registration failed. " . $db->lastErrorMsg();
    }

    header("Location: registerpage.php");
    exit();
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Register - Advanced Library Management System</title>
    <link rel="stylesheet" href="./styles/styles.css">
    <style>
        /* Styling for success and error message boxes */
        .message-box {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-weight: bold;
            text-align: center;
        }

        .message-box.success {
            background-color: #4CAF50; /* Green */
            color: white;
        }

        .message-box.error {
            background-color: #f44336; /* Red */
            color: white;
        }

        /* Styling for the label of Date of Birth */
        .dob-label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px; /* Add some space between label and input */
            margin-right: 325px;
        }
    </style>
</head>
<body>
    <div class="container">

        <!-- Title Section -->
        <header>
            <div class="title">
                <h1>Register</h1>
            </div>
        </header>

        <!-- Navigation Menu -->
        <?php require("startnavbar.php"); ?>
        <?php require("./styles/darkmodeandreader.php"); ?>

        <!-- Display Success or Error Message -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="message-box <?php  $_SESSION['message_type']; ?>">
                <?php
                echo $_SESSION['message'];
                unset($_SESSION['message']); // Clear message after displaying
                unset($_SESSION['message_type']); // Clear message type after displaying
                ?>
            </div>
        <?php endif; ?>

        <!-- Register Section -->
        <div class="about-section">
            <h2>Create Your Account</h2>
            <form action="registerpage.php" method="post">

                <!-- First Name Field -->
                <div class="form-group">
                    <input type="text" name="first_name" placeholder="First Name" class="search-bar" required>
                </div>

                <!-- Last Name Field -->
                <div class="form-group">
                    <input type="text" name="last_name" placeholder="Last Name" class="search-bar" required>
                </div>

                <!-- Email Field -->
                <div class="form-group">
                    <input type="email" name="email" placeholder="Email Address" class="search-bar" required>
                </div>

                <!-- Phone Number Field -->
                <div class="form-group">
                    <input type="tel" name="phone" placeholder="Phone Number" class="search-bar" required>
                </div>

                <!-- Communication Preference Field (Email or SMS) -->
                <div class="form-group">
                    <select name="preference" class="search-bar" required>
                        <option value="" disabled selected hidden>Preferred Communication Method</option>
                        <option value="email">Email</option>
                        <option value="sms">SMS</option>
                    </select>
                </div>

                <!-- Address Field -->
                <div class="form-group">
                    <input type="text" name="address" placeholder="Address" class="search-bar" required>
                </div>

                <!-- Password Field -->
                <div class="form-group">
                    <input type="password" name="password" placeholder="Password" class="search-bar" required>
                </div>

                <!-- Date of Birth Field with Label on Top -->
                <div class="form-group">
                    <label for="dob" class="dob-label">*Date of Birth</label>
                    <input type="date" name="dob" class="search-bar" required>
                </div>

                <!-- Register Button -->
                <div class="form-group">
                    <button type="submit" class="search-button">Register</button>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <?php require("footer.php"); ?>
    </div>
</body>
</html>
