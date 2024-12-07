<?php
// Start the session and check if the user is logged in
session_start();


// Get the logged-in user's first name
$first_name = $_SESSION['first_name'];

// Include database connection file
require("../databaseconnection.php");
$db = connectDatabase();

// Fetch the purchase manager's details (assuming email is stored in session)
$email = $_SESSION['email'];
$query = $db->prepare("SELECT * FROM purchase_manager WHERE email = :email");
$query->bindParam(':email', $email, SQLITE3_TEXT);
$result = $query->execute();
$manager = $result->fetchArray(SQLITE3_ASSOC);

// Check if the purchase manager exists
if (!$manager) {
    echo "Error: No purchase manager found with this email.";
    exit();
}

// Fetch purchased media and budget details
$purchases_query = $db->prepare("SELECT * FROM media WHERE purchase_manager_id = :manager_id");
$purchases_query->bindParam(':manager_id', $manager['purchase_manager_id'], SQLITE3_INTEGER);
$purchases_result = $purchases_query->execute();

// Calculate remaining budget
$remaining_budget = $manager['budget_allocation'] - ($manager['total_spent'] ?? 0);
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
                <h1>Track Media Purchase/Budgets</h1> <!-- Personalized greeting -->
            </div>
        </header>

        <!-- Navigation Menu -->
        <?php require("purchasemanagerstartnavbar.php"); ?>
        <?php require("../styles/darkmodeandreader.php"); ?>

        <!-- Budget Information Section -->
        <div class="dashboard-section">
            <h2>Budget Information</h2>
            <p><strong>Total Budget Allocation:</strong> £<?php echo number_format($manager['budget_allocation'], 2); ?></p>
            <p><strong>Total Spent:</strong> £<?php echo number_format($manager['total_spent'] ?? 0, 2); ?></p>
            <p><strong>Remaining Budget:</strong> £<?php echo number_format($remaining_budget, 2); ?></p>
        </div>

        <!-- Media Purchases Section -->
        <div class="dashboard-section">
            <h2>Purchased Media</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Type</th>
                        <th>Cost</th>
                        <th>Purchase Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($purchase = $purchases_result->fetchArray(SQLITE3_ASSOC)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($purchase['title']); ?></td>
                            <td><?php echo htmlspecialchars($purchase['author']); ?></td>
                            <td><?php echo htmlspecialchars($purchase['type']); ?></td>
                            <td>£<?php echo number_format($purchase['cost'], 2); ?></td>
                            <td><?php echo htmlspecialchars($purchase['purchase_date']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <?php require("../footer.php"); ?>
    </div>
</body>
</html>
