<?php
// Start the session
session_start();

// Include the database connection file
require("../databaseconnection.php");
$db = connectDatabase();

// Initialize an error message variable
$error_message = "";

// Fetch all media items
$result = $db->query("SELECT * FROM media");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Manage Media Inventory - Advanced Library Management System</title>
    <link rel="stylesheet" href="../styles/styles.css">
</head>
<body>
    <div class="container">

        <!-- Title Section -->
        <header>
            <div class="title">
                <h1>Manage Media Inventory</h1>
            </div>
        </header>

        <!-- Navigation Menu -->
        <?php require("branch_managerstartnavbar.php"); ?>
        <?php require("../styles/darkmodeandreader.php"); ?>

        <!-- Error Message -->
        <?php if (!empty($error_message)): ?>
            <p class="error" style="color: red;"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <!-- Media Table -->
        <table>
            <tr>
                <th>Media ID</th>
                <th>Title</th>
                <th>Author</th>
                <th>Type</th>
                <th>Actions</th>
            </tr>
            <?php while ($media = $result->fetchArray(SQLITE3_ASSOC)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($media['media_id']); ?></td>
                    <td><?php echo htmlspecialchars($media['title']); ?></td>
                    <td><?php echo htmlspecialchars($media['author']); ?></td>
                    <td><?php echo htmlspecialchars($media['type']); ?></td>
                    <td>
                        <form action="update_media.php" method="get" target="_blank" style="display:inline;">
                            <input type="hidden" name="media_id" value="<?php echo htmlspecialchars($media['media_id']); ?>">
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
