<?php
    $success = "";
    $error = "";

    // Check if the form was submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Validate form fields
        $name = trim($_POST["name"]);
        $email = trim($_POST["email"]);
        $message = trim($_POST["message"]);

        // Validate email
        if (empty($name) || empty($email) || empty($message)) {
            $error = "All fields are required.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Please enter a valid email address.";
        } else {
            // Prepare email
            $to = "thereisonlyonebigboss@gmail.com"; // Set your email address here
            $subject = "New Contact Us Message from $name";
            $email_body = "You have received a new message from the user $name.\n\n".
                          "Here is the message:\n$message";

            $headers = "From: $email\n";
            $headers .= "Reply-To: $email";

            // Send email
            if (mail($to, $subject, $email_body, $headers)) {
                $success = "Your message has been sent successfully!";
            } else {
                $error = "Sorry, something went wrong. Please try again.";
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Contact Us - Advanced Library Management System</title>
    <link rel="stylesheet" href="./styles/styles.css">
</head>
<body>
    <div class="container">

        <!-- Title Section -->
        <header>
            <div class="title">
                <h1>Contact Us</h1>
            </div>
        </header>

        <!-- Navigation Menu -->
        <?php require("startnavbar.php"); ?>
        <?php require("./styles/darkmodeandreader.php"); ?>

        <!-- Contact Us Section -->
        <div class="about-section contact-form">
            <h2>We'd love to hear from you!</h2>
            <p>Feel free to reach out to us with any questions, feedback, or inquiries regarding the Advanced Library Management System.</p>

            <h3>Contact Information</h3>
            <p>Email: <a href="mailto:info@advancedlibrary.com">info@advancedlibrary.com</a></p>
            <p>Phone: +44 123 456 7890</p>

            <h3>Send Us a Message</h3>

            <!-- Display Success or Error Message -->
            <?php if (!empty($success)): ?>
                <div style="background-color: green; color: white; padding: 10px; margin-bottom: 20px;">
                    <?php echo $success; ?>
                </div>
            <?php elseif (!empty($error)): ?>
                <div style="background-color: red; color: white; padding: 10px; margin-bottom: 20px;">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form action="contactuspage.php" method="post">
                <div class="form-group">
                    <input type="text" name="name" placeholder="Your Name" class="search-bar" value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>" required>
                </div>
                <div class="form-group">
                    <input type="email" name="email" placeholder="Your Email" class="search-bar" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
                </div>
                <div class="form-group">
                    <textarea name="message" rows="5" placeholder="Your Message" class="search-bar" required><?php echo isset($message) ? htmlspecialchars($message) : ''; ?></textarea>
                </div>
                <div class="form-group">
                    <button type="submit" class="search-button">Send Message</button>
                </div>
            </form>
        </div>

        <?php require("footer.php"); ?>
    </div>

</body>
</html>
