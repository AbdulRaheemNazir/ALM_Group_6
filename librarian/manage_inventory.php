<?php
session_start();
require("../databaseconnection.php");
$db = connectDatabase();

if (!isset($_SESSION['first_name'])) {
    header("Location: login_librarian.php");
    exit();
}

$first_name = $_SESSION['first_name'];

// Handle the delete request from AJAX
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['media_id'])) {
    $media_id = $_POST['media_id'];

    // Prepare the delete statement
    $stmt = $db->prepare("DELETE FROM media WHERE media_id = :media_id");
    $stmt->bindParam(':media_id', $media_id, SQLITE3_INTEGER);

    // Execute the statement and return success or error
    if ($stmt->execute()) {
        echo "success";
    } else {
        // Return detailed error message
        echo "error: " . $db->lastErrorMsg();
    }
    exit(); // Prevent the rest of the script from running
}

// Fetch all media items
$query = "SELECT * FROM media";
$media_result = $db->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Inventory - Advanced Library Management System</title>
    <link rel="stylesheet" href="../styles/styles.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<body>
    <div class="container">
        <header>
            <div class="title">
                <h1>Manage Inventory</h1>
            </div>
        </header>

        <?php require("librarianstartnavbar.php"); ?>
        <?php require("../styles/darkmodeandreader.php"); ?>

        <div class="dashboard-section">
            <h2>Media Inventory</h2>
            <table>
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Type</th>
                        <th>Genre</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="media-table-body">
                    <?php while ($row = $media_result->fetchArray(SQLITE3_ASSOC)): ?>
                    <tr id="media-<?php echo $row['media_id']; ?>">
                        <td><?php echo $row['title']; ?></td>
                        <td><?php echo $row['author']; ?></td>
                        <td><?php echo $row['type']; ?></td>
                        <td><?php echo $row['genre']; ?></td>
                        <td>
                            <a href="edit_media.php?id=<?php echo $row['media_id']; ?>">Edit</a> |
                            <a href="javascript:void(0);" onclick="deleteMedia(<?php echo $row['media_id']; ?>)">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <a href="add_media.php" class="button">Add New Media</a>
        </div>

        <?php require("../footer.php"); ?>
    </div>

    <!-- JavaScript for Deleting Media -->
    <script>
        function deleteMedia(media_id) {
            if (confirm("Are you sure you want to delete this media?")) {
                $.ajax({
                    url: '',  // Use the same page for handling deletion
                    type: 'POST',
                    data: { media_id: media_id },
                    success: function(response) {
                        if (response === "success") {
                            $("#media-" + media_id).remove();
                            alert("Media deleted successfully.");
                        } else {
                            alert("Error deleting media: " + response); // Show detailed error
                        }
                    }
                });
            }
        }
    </script>
</body>
</html>
