<?php
// Start the session and check if the user is logged in
session_start();
if (!isset($_SESSION['first_name'])) {
    // If the user is not logged in, redirect to the login page
    header("Location: login_purchasemanager.php");
    exit();
}

// Connect to the database
require("../databaseconnection.php");
$db = connectDatabase();

// Get the logged-in user's first name and email
$first_name = $_SESSION['first_name'];
$email = $_SESSION['email']; // Assuming email is stored in the session

// Fetch the purchase manager details from the database
$stmt = $db->prepare("SELECT vendor_list, budget_allocation, purchased_media FROM purchase_manager WHERE email = :email");
$stmt->bindParam(':email', $email, SQLITE3_TEXT);
$result = $stmt->execute();
$manager = $result->fetchArray(SQLITE3_ASSOC);

// Check if the manager's data was retrieved successfully
if ($manager) {
    $vendor_list = $manager['vendor_list'];
    $budget_allocation = $manager['budget_allocation'];
    $purchased_media = $manager['purchased_media'];
} else {
    echo "Error: Unable to fetch data for the purchase manager.";
    exit();
}

// If purchased_media is a comma-separated string, convert it into an array
$media_ids = explode(',', $purchased_media);

// Generate placeholders for SQL IN clause
$placeholders = implode(',', array_fill(0, count($media_ids), '?'));

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Track Media Purchase/Budgets - Advanced Library Management System</title>
    <link rel="stylesheet" href="../styles/styles.css">
</head>
<body>
    <div class="container">

        <!-- Title Section -->
        <header>
            <div class="title">
                <h1>Track Media Purchase/Budgets</h1>
            </div>
        </header>

        <!-- Navigation Menu -->
        <?php require("purchasemanagerstartnavbar.php"); ?>
        <?php require("../styles/darkmodeandreader.php"); ?>

        <!-- Dashboard Section -->
        <div class="dashboard-section">

            <!-- Vendor List Section -->
            <h2>Your Vendors</h2>
            <p><?php echo nl2br($vendor_list); ?></p> <!-- Displaying the vendor list -->

            <!-- Budget Allocation Section -->
            <h2>Budget Allocation</h2>
            <p>Total Budget: £<?php echo number_format($budget_allocation, 2); ?></p>

            <!-- Media Purchase Section -->
            <h2>Purchased Media</h2>
            <?php
            // Prepare the SQL query to fetch media details by IDs
            $media_stmt = $db->prepare("SELECT title, cost, purchase_date FROM media WHERE media_id IN ($placeholders)");

            // Bind media IDs to the query
            foreach ($media_ids as $index => $media_id) {
                $media_stmt->bindValue($index + 1, trim($media_id), SQLITE3_INTEGER);
            }

            $media_result = $media_stmt->execute();

            // Initialize variables to track total spending
            $total_spent = 0;
            ?>
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Cost (£)</th>
                        <th>Purchase Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($media = $media_result->fetchArray(SQLITE3_ASSOC)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($media['title']); ?></td>
                            <td>£<?php echo number_format($media['cost'], 2); ?></td>
                            <td><?php echo htmlspecialchars($media['purchase_date']); ?></td>
                        </tr>
                        <?php $total_spent += $media['cost']; ?>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <!-- Remaining Budget Section -->
            <h3>Total Spent: £<?php echo number_format($total_spent, 2); ?></h3>
            <h3>Remaining Budget: £<?php echo number_format($budget_allocation - $total_spent, 2); ?></h3>

        </div>

        <!-- Footer -->
        <?php require("../footer.php"); ?>
    </div>
</body>
</html>
