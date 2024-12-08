<?php
// Start the session
session_start();

// Include the database connection file
require("../databaseconnection.php");
$db = connectDatabase();

// Initialize an error message variable
$error_message = "";

// Check if media_id is set
if (isset($_GET['media_id'])) {
    $media_id = intval($_GET['media_id']);

    // Fetch the current details of the media
    $stmt = $db->prepare("SELECT * FROM media WHERE media_id = :media_id");
    $stmt->bindValue(':media_id', $media_id, SQLITE3_INTEGER);
    $result = $stmt->execute();
    $media = $result->fetchArray(SQLITE3_ASSOC);

    // If media not found, redirect or show an error
    if (!$media) {
        header("Location: manage_media_inventory.php?error=Media not found");
        exit();
    }
}

// Handle the update request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_title = $_POST['new_title'];
    $new_author = $_POST['new_author'];
    $new_type = $_POST['new_type'];

    // Prepare the SQL query to update the media item
    $stmt = $db->prepare("UPDATE media SET title = :title, author = :author, type = :type WHERE media_id = :media_id");
    $stmt->bindValue(':title', $new_title, SQLITE3_TEXT);
    $stmt->bindValue(':author', $new_author, SQLITE3_TEXT);
    $stmt->bindValue(':type', $new_type, SQLITE3_TEXT);
    $stmt->bindValue(':media_id', $media_id, SQLITE3_INTEGER);
    
    // Execute the statement
    if ($stmt->execute()) {
        header("Location: manage_media_inventory.php?message=Media updated successfully");
        exit();
    } else {
        $error_message = "Error updating media: " . $db->lastErrorMsg();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Update Media - Advanced Library Management System</title>
    <link rel="stylesheet" href="../styles/styles.css">
</head>
<body>
    <div class="container">

        <!-- Title Section -->
        <header>
            <div class="title">
                <h1>Update Media</h1>
            </div>
        </header>

        <!-- Error Message -->
        <?php if (!empty($error_message)): ?>
            <p class="error" style="color: red;"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <!-- Update Form -->
        <form action="update_media.php?media_id=<?php echo htmlspecialchars($media['media_id']); ?>" method="post">
            <div class="form-group">
                <label for="title">Title:</label>
                <input type="text" name="new_title" value="<?php echo htmlspecialchars($media['title']); ?>" required>
            </div>
            <div class="form-group">
                <label for="author">Author:</label>
                <input type="text" name="new_author" value="<?php echo htmlspecialchars($media['author']); ?>" required>
            </div>
            <div class="form-group">
                <label for="type">Type:</label>
                <input type="text" name="new_type" value="<?php echo htmlspecialchars($media['type']); ?>" required>
            </div>
            <div class="form-group">
                <button type="submit">Update Media</button>
            </div>
        </form>

        <!-- Footer -->
        <?php require("../footer.php"); ?>
    </div>
</body>
</html>
