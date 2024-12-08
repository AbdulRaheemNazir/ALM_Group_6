<?php
session_start();
require("../databaseconnection.php");
$db = connectDatabase();

if (!isset($_SESSION['first_name'])) {
    header("Location: login_librarian.php");
    exit();
}

$media_id = $_GET['id'];
$success_message = '';
$error_message = '';

// Fetch the media details
$query = "SELECT * FROM media WHERE media_id = :media_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':media_id', $media_id, SQLITE3_INTEGER);
$result = $stmt->execute();
$media = $result->fetchArray(SQLITE3_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Update the media details
    $title = $_POST['title'];
    $author = $_POST['author'];
    $type = $_POST['type'];
    $genre = $_POST['genre'];
    $publication_year = $_POST['publication_year'];
    $isbn = $_POST['isbn'];
    $language = $_POST['language'];
    $number_of_copies = $_POST['number_of_copies'];
    $location = $_POST['location'];
    $media_format = $_POST['media_format'];
    $cost = $_POST['cost'];

    // Prepare SQL to update media
    $stmt = $db->prepare("UPDATE media 
                          SET title = :title, author = :author, type = :type, genre = :genre, publication_year = :publication_year,
                              isbn = :isbn, language = :language, number_of_copies = :number_of_copies, location = :location, 
                              media_format = :media_format, cost = :cost
                          WHERE media_id = :media_id");
    
    $stmt->bindParam(':title', $title, SQLITE3_TEXT);
    $stmt->bindParam(':author', $author, SQLITE3_TEXT);
    $stmt->bindParam(':type', $type, SQLITE3_TEXT);
    $stmt->bindParam(':genre', $genre, SQLITE3_TEXT);
    $stmt->bindParam(':publication_year', $publication_year, SQLITE3_INTEGER);
    $stmt->bindParam(':isbn', $isbn, SQLITE3_TEXT);
    $stmt->bindParam(':language', $language, SQLITE3_TEXT);
    $stmt->bindParam(':number_of_copies', $number_of_copies, SQLITE3_INTEGER);
    $stmt->bindParam(':location', $location, SQLITE3_TEXT);
    $stmt->bindParam(':media_format', $media_format, SQLITE3_TEXT);
    $stmt->bindParam(':cost', $cost, SQLITE3_FLOAT);
    $stmt->bindParam(':media_id', $media_id, SQLITE3_INTEGER);

    if ($stmt->execute()) {
        $success_message = "Media updated successfully!";
    } else {
        $error_message = "Error updating media. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Media - Advanced Library Management System</title>
    <link rel="stylesheet" href="../styles/styles.css">
</head>
<body>
    <div class="container">

        <!-- Title Section -->
        <header>
            <div class="title">
                <h1>Edit Media</h1> <!-- Page Title -->
            </div>
        </header>

        <?php require("librarianstartnavbar.php"); ?>
        <?php require("../styles/darkmodeandreader.php"); ?>

        <div class="dashboard-section">
            <h2>Edit Media Details</h2>

            <!-- Success or Error Message Display -->
            <?php if (!empty($success_message)): ?>
                <p style="color: green;"><?php echo $success_message; ?></p>
            <?php endif; ?>

            <?php if (!empty($error_message)): ?>
                <p style="color: red;"><?php echo $error_message; ?></p>
            <?php endif; ?>

            <!-- Media Edit Form -->
            <form action="edit_media.php?id=<?php echo $media_id; ?>" method="post">
                <div class="form-group">
                    <label for="title">Title:</label>
                    <input type="text" name="title" class="search-bar" value="<?php echo $media['title']; ?>" required>
                </div>

                <div class="form-group">
                    <label for="author">Author:</label>
                    <input type="text" name="author" class="search-bar" value="<?php echo $media['author']; ?>">
                </div>

                <div class="form-group">
                    <label for="type">Type:</label>
                    <input type="text" name="type" class="search-bar" value="<?php echo $media['type']; ?>" required>
                </div>

                <div class="form-group">
                    <label for="genre">Genre:</label>
                    <input type="text" name="genre" class="search-bar" value="<?php echo $media['genre']; ?>">
                </div>

                <div class="form-group">
                    <label for="publication_year">Publication Year:</label>
                    <input type="number" name="publication_year" class="search-bar" value="<?php echo $media['publication_year']; ?>">
                </div>

                <div class="form-group">
                    <label for="isbn">ISBN:</label>
                    <input type="text" name="isbn" class="search-bar" value="<?php echo $media['isbn']; ?>">
                </div>

                <div class="form-group">
                    <label for="language">Language:</label>
                    <input type="text" name="language" class="search-bar" value="<?php echo $media['language']; ?>">
                </div>

                <div class="form-group">
                    <label for="number_of_copies">Number of Copies:</label>
                    <input type="number" name="number_of_copies" class="search-bar" value="<?php echo $media['number_of_copies']; ?>" required>
                </div>

                <div class="form-group">
                    <label for="location">Location:</label>
                    <input type="text" name="location" class="search-bar" value="<?php echo $media['location']; ?>">
                </div>

                <div class="form-group">
                    <label for="media_format">Format:</label>
                    <input type="text" name="media_format" class="search-bar" value="<?php echo $media['media_format']; ?>" required>
                </div>

                <div class="form-group">
                    <label for="cost">Cost (£):</label>
                    <input type="number" name="cost" class="search-bar" value="<?php echo $media['cost']; ?>" step="0.01" required>
                </div>

                <div class="form-group">
                    <button type="submit" class="search-button">Update Media</button>
                </div>
            </form>
        </div>

        <?php require("../footer.php"); ?>
    </div>
</body>
</html>
