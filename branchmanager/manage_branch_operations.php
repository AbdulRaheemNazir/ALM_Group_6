<?php
// Start the session
session_start();

// Include the database connection file
require("../databaseconnection.php");
$db = connectDatabase();

// Initialize an error message variable
$error_message = "";

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['librarian_id'])) {
    $librarian_id = intval($_POST['librarian_id']);
    $new_working_hours = $_POST['working_hours'];

    // Prepare the SQL query to update the librarian's working hours
    $stmt = $db->prepare("UPDATE librarian SET working_hours = :working_hours WHERE librarian_id = :librarian_id");
    $stmt->bindValue(':working_hours', $new_working_hours, SQLITE3_TEXT);
    $stmt->bindValue(':librarian_id', $librarian_id, SQLITE3_INTEGER);
    
    // Execute the statement and check for errors
    if ($stmt->execute()) {
        $success_message = "Working hours updated successfully!";
    } else {
        $error_message = "Error updating working hours: " . $db->lastErrorMsg();
    }
}

// Fetch all librarians for display
$librarians = $db->query("SELECT * FROM librarian");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Manage Librarian Operations - Advanced Library Management System</title>
    <link rel="stylesheet" href="../styles/styles.css">
</head>
<body>
    <div class="container">

        <!-- Title Section -->
        <header>
            <div class="title">
                <h1>Manage Librarian Operations</h1>
            </div>
        </header>

        <!-- Navigation Menu -->
        <?php require("branch_managerstartnavbar.php"); ?>
        <?php require("../styles/darkmodeandreader.php"); ?>

        <!-- Show success or error message -->
        <?php if (!empty($error_message)): ?>
            <p class="error" style="color: red;"><?php echo $error_message; ?></p>
        <?php elseif (!empty($success_message)): ?>
            <p class="success" style="color: green;"><?php echo $success_message; ?></p>
        <?php endif; ?>

        <!-- Librarian Management Form -->
        <h2>Update Working Hours</h2>
        <table>
            <tr>
                <th>Librarian ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Current Working Hours</th>
                <th>Update Working Hours</th>
            </tr>
            <?php while ($librarian = $librarians->fetchArray(SQLITE3_ASSOC)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($librarian['librarian_id']); ?></td>
                    <td><?php echo htmlspecialchars($librarian['first_name']); ?></td>
                    <td><?php echo htmlspecialchars($librarian['last_name']); ?></td>
                    <td><?php echo htmlspecialchars($librarian['working_hours']); ?></td>
                    <td>
                        <form action="manage_branch_operations.php" method="post">
                            <input type="hidden" name="librarian_id" value="<?php echo htmlspecialchars($librarian['librarian_id']); ?>">
                            <input type="text" name="working_hours" placeholder="Mon-Fri: 8AM-4PM" required>
                            <button type="submit">Update</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>

        <!-- Footer -->
        <?php require("../footer.php"); ?>
    </div>
</body>
</html>
