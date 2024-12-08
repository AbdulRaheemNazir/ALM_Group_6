<?php
// Start session
session_start();

// Include database connection file
require("../databaseconnection.php");
$db = connectDatabase();

// Get the operator's email from the session (assuming they are logged in)
$email = $_SESSION['email'] ?? null;  // Using null coalescing to avoid undefined errors

// Check if the user is logged in
if (!$email) {
    echo "Error: You must be logged in to view account details.";
    exit();
}

// Fetch the operator's details from the database using their email
$query = $db->prepare("SELECT * FROM call_centre_operator WHERE email = :email");
$query->bindParam(':email', $email, SQLITE3_TEXT);
$result = $query->execute();
$operator = $result->fetchArray(SQLITE3_ASSOC);

// Check if the operator exists in the database
if (!$operator) {
    echo "Error: No operator found with this email.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Account Details - Call Center Operator</title>
    <link rel="stylesheet" href="../styles/styles.css">
</head>
<body>
    <div class="container">

        <!-- Title Section -->
        <header>
            <div class="title">
                <h1>Account Details</h1>
            </div>
        </header>
        <?php require("callcentrestartnavbar.php"); ?>

        <!-- Navigation Menu -->
        <?php require("../styles/darkmodeandreader.php"); ?>

        <!-- Operator Details Section -->
        <div class="account-details-section">
            <h2>Your Account Details</h2>

            <!-- Display the operator's details -->
            <table>
                <tr>
                    <th>Operator ID:</th>
                    <td><?php echo htmlspecialchars($operator['operator_id']); ?></td>
                </tr>
                <tr>
                    <th>First Name:</th>
                    <td><?php echo htmlspecialchars($operator['first_name']); ?></td>
                </tr>
                <tr>
                    <th>Last Name:</th>
                    <td><?php echo htmlspecialchars($operator['last_name']); ?></td>
                </tr>
                <tr>
                    <th>Email:</th>
                    <td><?php echo htmlspecialchars($operator['email']); ?></td>
                </tr>
                <tr>
                    <th>Phone Number:</th>
                    <td><?php echo htmlspecialchars($operator['phone_number']); ?></td>
                </tr>
                <tr>
                    <th>Branch ID:</th>
                    <td><?php echo htmlspecialchars($operator['branch_id']); ?></td>
                </tr>
                <tr>
                    <th>Role:</th>
                    <td>Call Center Operator</td>
                </tr>
            </table>


        </div>

        <!-- Footer -->
        <?php require("../footer.php"); ?>
    </div>
</body>
</html>
