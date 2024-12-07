<?php
// Start the session and check if the user is logged in
session_start();
if (!isset($_SESSION['first_name'])) {
    // If the user is not logged in, redirect to the login page
    header("Location: login_purchasemanager.php");
    exit();
}

// Get the logged-in user's first name
$first_name = $_SESSION['first_name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Purchase Manager Dashboard - Advanced Library Management System</title>
    <link rel="stylesheet" href="../styles/styles.css">
</head>
<body>
    <div class="container">

        <!-- Title Section -->
        <header>
            <div class="title">
                <h1>Welcome, <?php echo htmlspecialchars($first_name); ?>!</h1> <!-- Personalized greeting -->
            </div>
        </header>

        <!-- Navigation Menu -->
        <?php require("purchasemanagerstartnavbar.php"); ?>
        <?php require("../styles/darkmodeandreader.php"); ?>

        <!-- Dashboard Section -->
        <div class="dashboard-section">


        <!-- Footer -->
        <?php require("../footer.php"); ?>
    </div>
</body>
</html>
