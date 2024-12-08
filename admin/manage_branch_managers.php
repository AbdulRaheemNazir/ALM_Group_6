<?php
session_start();
require("../databaseconnection.php");
$db = connectDatabase();

// Check if the user is logged in as an admin
if (!isset($_SESSION['first_name'])) {
    header("Location: login_admin.php");
    exit();
}

// Fetch the branch ID from the URL
$branch_id = $_GET['branch_id'];

// Fetch branch managers for the specific branch
$managers_query = "SELECT * FROM branch_manager WHERE branch_id = ?";
$stmt_managers = $db->prepare($managers_query);
$stmt_managers->bindValue(1, $branch_id, SQLITE3_INTEGER);
$managers_result = $stmt_managers->execute();

// Handle adding a new branch manager
if (isset($_POST['add_branch_manager'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $branch_id = $_POST['branch_id'];
    $password_hash = password_hash("password123", PASSWORD_DEFAULT); // Default password

    // Insert the new branch manager
    $insert_manager_query = "INSERT INTO branch_manager (first_name, last_name, email, branch_id, password_hash) 
                            VALUES (?, ?, ?, ?, ?)";
    $stmt_insert = $db->prepare($insert_manager_query);
    $stmt_insert->bindValue(1, $first_name, SQLITE3_TEXT);
    $stmt_insert->bindValue(2, $last_name, SQLITE3_TEXT);
    $stmt_insert->bindValue(3, $email, SQLITE3_TEXT);
    $stmt_insert->bindValue(4, $branch_id, SQLITE3_INTEGER);
    $stmt_insert->bindValue(5, $password_hash, SQLITE3_TEXT);
    $stmt_insert->execute();

    header("Location: manage_branch_managers.php?branch_id=".$branch_id);
    exit();
}

// Handle removal of a branch manager
if (isset($_POST['remove_branch_manager'])) {
    $branch_manager_id = $_POST['branch_manager_id'];
    $remove_query = "DELETE FROM branch_manager WHERE branch_manager_id = ?";
    $stmt_remove = $db->prepare($remove_query);
    $stmt_remove->bindValue(1, $branch_manager_id, SQLITE3_INTEGER);
    $stmt_remove->execute();

    header("Location: manage_branch_managers.php?branch_id=".$branch_id);
    exit();
}

// Handle updating a branch manager
if (isset($_POST['update_branch_manager'])) {
    $branch_manager_id = $_POST['branch_manager_id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];

    // Update the branch manager's details
    $update_query = "UPDATE branch_manager SET first_name = ?, last_name = ?, email = ? WHERE branch_manager_id = ?";
    $stmt_update = $db->prepare($update_query);
    $stmt_update->bindValue(1, $first_name, SQLITE3_TEXT);
    $stmt_update->bindValue(2, $last_name, SQLITE3_TEXT);
    $stmt_update->bindValue(3, $email, SQLITE3_TEXT);
    $stmt_update->bindValue(4, $branch_manager_id, SQLITE3_INTEGER);
    $stmt_update->execute();

    header("Location: manage_branch_managers.php?branch_id=".$branch_id);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Branch Managers - Advanced Library Management System</title>
    <link rel="stylesheet" href="../styles/styles.css">
</head>
<body>
    <div class="container">
        <header>
            <div class="title">
                <h1>Manage Branch Managers</h1>
            </div>
        </header>

        <?php require("adminstartnavbar.php"); ?>
        <?php require("../styles/darkmodeandreader.php"); ?>

        <div class="dashboard-section">
            <h2>Branch Managers for Branch ID: <?php echo $branch_id; ?></h2>
            <table>
                <thead>
                    <tr>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($manager = $managers_result->fetchArray(SQLITE3_ASSOC)): ?>
                    <tr>
                        <form method="POST">
                            <td>
                                <input type="text" name="first_name" value="<?php echo $manager['first_name']; ?>" required>
                            </td>
                            <td>
                                <input type="text" name="last_name" value="<?php echo $manager['last_name']; ?>" required>
                            </td>
                            <td>
                                <input type="email" name="email" value="<?php echo $manager['email']; ?>" required>
                            </td>
                            <td>
                                <input type="hidden" name="branch_manager_id" value="<?php echo $manager['branch_manager_id']; ?>">
                                <button type="submit" name="update_branch_manager">Update</button>
                                <button type="submit" name="remove_branch_manager" onclick="return confirm('Are you sure?')">Delete</button>
                            </td>
                        </form>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <!-- Add a new branch manager -->
            <h2>Add New Branch Manager</h2>
            <form method="POST">
                <label>First Name: <input type="text" name="first_name" required></label><br>
                <label>Last Name: <input type="text" name="last_name" required></label><br>
                <label>Email: <input type="email" name="email" required></label><br>
                <input type="hidden" name="branch_id" value="<?php echo $branch_id; ?>">
                <button type="submit" name="add_branch_manager">Add Branch Manager</button>
            </form>
        </div>

        <?php require("../footer.php"); ?>
    </div>
</body>
</html>
