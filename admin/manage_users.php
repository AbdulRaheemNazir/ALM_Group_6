<?php
session_start();
require("../databaseconnection.php");
$db = connectDatabase();

// Fetch all branches from the branch table
$query = "SELECT * FROM branch";
$branch_result = $db->query($query);



// Handle updating a branch
if (isset($_POST['update_branch'])) {
    $branch_id = $_POST['branch_id'];
    $branch_name = $_POST['branch_name'];
    $city = $_POST['city'];
    $address = $_POST['address'];
    $postal_code = $_POST['postal_code'];
    $phone_number = $_POST['phone_number'];
    $email = $_POST['email'];
    $operating_hours = $_POST['operating_hours'];

    // Update the branch details
    $update_branch_query = "UPDATE branch SET branch_name = ?, city = ?, address = ?, postal_code = ?, phone_number = ?, email = ?, operating_hours = ? WHERE branch_id = ?";
    $stmt_update = $db->prepare($update_branch_query);
    $stmt_update->bindValue(1, $branch_name, SQLITE3_TEXT);
    $stmt_update->bindValue(2, $city, SQLITE3_TEXT);
    $stmt_update->bindValue(3, $address, SQLITE3_TEXT);
    $stmt_update->bindValue(4, $postal_code, SQLITE3_TEXT);
    $stmt_update->bindValue(5, $phone_number, SQLITE3_TEXT);
    $stmt_update->bindValue(6, $email, SQLITE3_TEXT);
    $stmt_update->bindValue(7, $operating_hours, SQLITE3_TEXT);
    $stmt_update->bindValue(8, $branch_id, SQLITE3_INTEGER);
    $stmt_update->execute();

    header("Location: manage_users.php");
    exit();
}

// Handle removal of a branch
if (isset($_POST['remove_branch'])) {
    $branch_id = $_POST['branch_id'];
    $remove_query = "DELETE FROM branch WHERE branch_id = ?";
    $stmt_remove = $db->prepare($remove_query);
    $stmt_remove->bindValue(1, $branch_id, SQLITE3_INTEGER);
    $stmt_remove->execute();

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
            <h2>Branches</h2>
            <table>
                <thead>
                    <tr>
                        <th>Branch Name</th>
                        <th>City</th>
                        <th>Address</th>
                        <th>Phone Number</th>
                        <th>Manager ID</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $branch_result->fetchArray(SQLITE3_ASSOC)): ?>
                    <tr>
                        <form method="POST">
                            <td>
                                <input type="text" name="branch_name" value="<?php echo $row['branch_name']; ?>" required>
                            </td>
                            <td>
                                <input type="text" name="city" value="<?php echo $row['city']; ?>" required>
                            </td>
                            <td>
                                <input type="text" name="address" value="<?php echo $row['address']; ?>" required>
                            </td>
                            <td>
                                <input type="text" name="phone_number" value="<?php echo $row['phone_number']; ?>" required>
                            </td>
                            <td>
                                <input type="text" name="branch_manager_id" value="<?php echo $row['branch_manager_id']; ?>" disabled>
                            </td>
                            <td>
                                <input type="hidden" name="branch_id" value="<?php echo $row['branch_id']; ?>">
                                <button type="submit" name="update_branch">Update</button>
                                <button type="submit" name="remove_branch" onclick="return confirm('Are you sure you want to delete this branch?')">Delete</button>
                            </td>
                        </form>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>


        <?php require("../footer.php"); ?>
    </div>
</body>
</html>
