<?php
session_start();
require("../databaseconnection.php");
$db = connectDatabase();

// Handle adding a new branch and branch manager
if (isset($_POST['add_branch'])) {
    // Retrieve branch details
    $branch_name = $_POST['branch_name'];
    $city = $_POST['city'];
    $address = $_POST['address'];
    $postal_code = $_POST['postal_code'];
    $phone_number = $_POST['phone_number'];
    $branch_email = $_POST['branch_email'];
    $operating_hours = $_POST['operating_hours'];

    // Retrieve branch manager details
    $manager_first_name = $_POST['manager_first_name'];
    $manager_last_name = $_POST['manager_last_name'];
    $manager_email = $_POST['manager_email'];
    $password_hash = password_hash("password123", PASSWORD_DEFAULT); // Default password

    // Insert the new branch manager
    $insert_manager_query = "INSERT INTO branch_manager (first_name, last_name, email, password_hash) 
                            VALUES (?, ?, ?, ?)";
    $stmt_insert_manager = $db->prepare($insert_manager_query);
    $stmt_insert_manager->bindValue(1, $manager_first_name, SQLITE3_TEXT);
    $stmt_insert_manager->bindValue(2, $manager_last_name, SQLITE3_TEXT);
    $stmt_insert_manager->bindValue(3, $manager_email, SQLITE3_TEXT);
    $stmt_insert_manager->bindValue(4, $password_hash, SQLITE3_TEXT);
    $stmt_insert_manager->execute();

    // Get the newly inserted branch_manager_id
    $branch_manager_id = $db->lastInsertRowID();

    // Insert the new branch with branch_manager_id
    $insert_branch_query = "INSERT INTO branch (branch_name, city, address, postal_code, phone_number, email, operating_hours, branch_manager_id) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_insert_branch = $db->prepare($insert_branch_query);
    $stmt_insert_branch->bindValue(1, $branch_name, SQLITE3_TEXT);
    $stmt_insert_branch->bindValue(2, $city, SQLITE3_TEXT);
    $stmt_insert_branch->bindValue(3, $address, SQLITE3_TEXT);
    $stmt_insert_branch->bindValue(4, $postal_code, SQLITE3_TEXT);
    $stmt_insert_branch->bindValue(5, $phone_number, SQLITE3_TEXT);
    $stmt_insert_branch->bindValue(6, $branch_email, SQLITE3_TEXT);
    $stmt_insert_branch->bindValue(7, $operating_hours, SQLITE3_TEXT);
    $stmt_insert_branch->bindValue(8, $branch_manager_id, SQLITE3_INTEGER);
    $stmt_insert_branch->execute();

    // Redirect to the management page
    header("Location: manage_users.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Branches - Advanced Library Management System</title>
    <link rel="stylesheet" href="../styles/styles.css">
</head>
<body>
    <div class="container">
        <header>
            <div class="title">
                <h1>Manage Branches</h1>
            </div>
        </header>

        <?php require("adminstartnavbar.php"); ?>
        <?php require("../styles/darkmodeandreader.php"); ?>

        <div class="dashboard-section">

            <!-- Add a new branch -->
            <h2>Add New Branch</h2>
            <form method="POST">
                <label>Branch Name: <input type="text" name="branch_name" required></label><br>
                <label>City: <input type="text" name="city" required></label><br>
                <label>Address: <input type="text" name="address" required></label><br>
                <label>Postal Code: <input type="text" name="postal_code" required></label><br>
                <label>Phone Number: <input type="text" name="phone_number" required></label><br>
                <label>Email: <input type="email" name="branch_email" required></label><br>
                <label>Operating Hours: <input type="text" name="operating_hours" required></label><br>

                <h2>Branch Manager Details</h2>
                <label>First Name: <input type="text" name="manager_first_name" required></label><br>
                <label>Last Name: <input type="text" name="manager_last_name" required></label><br>
                <label>Manager Email: <input type="email" name="manager_email" required></label><br>

                <button type="submit" name="add_branch">Add Branch and Manager</button>
            </form>
        </div>

        <?php require("../footer.php"); ?>
    </div>
</body>
</html>
