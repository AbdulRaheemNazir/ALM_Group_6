<?php
// Start the session and check if the user is logged in
session_start();
if (!isset($_SESSION['member_id'])) {
    // If the user is not logged in, redirect to the login page
    header("Location: login_member.php");
    exit();
}

// Include the database connection file
require("../databaseconnection.php");
$db = connectDatabase();

// Get the logged-in user's member ID
$member_id = $_SESSION['member_id'];

// Fetch the user's account details from the library_member table
$stmt = $db->prepare("SELECT membership_status, fines_due, registration_date, borrowed_media_count, overdue_items_count 
                      FROM library_member 
                      WHERE member_id = :member_id");
$stmt->bindParam(':member_id', $member_id, SQLITE3_INTEGER);
$result = $stmt->execute();
$user = $result->fetchArray(SQLITE3_ASSOC);

?>
<style>
    

    .account-status-section {
    margin: 20px 0;
    padding: 20px;
    background-color: #f5f5f5;
    border-radius: 10px;
}

.account-status-section h2 {
    font-size: 1.8em;
    margin-bottom: 20px;
}

.account-status-section ul {
    list-style-type: none;
    padding: 0;
}

.account-status-section li {
    font-size: 1.2em;
    margin-bottom: 10px;
}

.account-status-section li strong {
    color: black;
}

</style>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Account Status - Advanced Library Management System</title>
    <link rel="stylesheet" href="../styles/styles.css">
</head>
<body>
    <div class="container">

        <!-- Title Section -->
        <header>
            <div class="title">
                <h1>Account Status</h1>
            </div>
        </header>

        <!-- Navigation Menu -->
        <?php require("memberstartnavbar.php"); ?>
        <?php require("../styles/darkmodeandreader.php"); ?>

        <!-- Account Status Section -->
        <div class="account-status-section">
            <h2>Membership Details</h2>
            <ul>
                <li><strong>Membership Status:</strong> <?php echo htmlspecialchars($user['membership_status']); ?></li>
                <li><strong>Fines Due:</strong> $<?php echo number_format($user['fines_due'], 2); ?></li>
            </ul>

            <h2>Statistics</h2>
            <ul>
                <li><strong>Registration Date:</strong> <?php echo htmlspecialchars($user['registration_date']); ?></li>
                <li><strong>Borrowed Media Count:</strong> <?php echo htmlspecialchars($user['borrowed_media_count']); ?></li>
                <li><strong>Overdue Items Count:</strong> <?php echo htmlspecialchars($user['overdue_items_count']); ?></li>
            </ul>
        </div>

        <!-- Footer -->
        <?php require("../footer.php"); ?>
    </div>
</body>
</html>
