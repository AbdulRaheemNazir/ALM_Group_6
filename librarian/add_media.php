<?php
session_start();
require("../databaseconnection.php");
$db = connectDatabase();

if (!isset($_SESSION['first_name'])) {
    // If the user is not logged in, redirect to the login page
    header("Location: login_librarian.php");
    exit();
}

$first_name = $_SESSION['first_name'];
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $title = $_POST['title'];
    $author = $_POST['author'];
    $type = $_POST['type'];
    $genre = $_POST['genre'];
    $publication_year = $_POST['publication_year'];
    $isbn = $_POST['isbn'];
    $language = $_POST['language'];
    $number_of_copies = $_POST['number_of_copies'];
    $media_format = $_POST['media_format'];
    $cost = $_POST['cost'];

    // Prepare SQL to insert the new media into the database
    $stmt = $db->prepare("INSERT INTO media (title, author, type, genre, publication_year, isbn, language, number_of_copies, media_format, status, cost, purchase_date)
                            VALUES (:title, :author, :type, :genre, :publication_year, :isbn, :language, :number_of_copies,  :media_format, 'Available', :cost, CURRENT_TIMESTAMP)");
    
    $stmt->bindParam(':title', $title, SQLITE3_TEXT);
    $stmt->bindParam(':author', $author, SQLITE3_TEXT);
    $stmt->bindParam(':type', $type, SQLITE3_TEXT);
    $stmt->bindParam(':genre', $genre, SQLITE3_TEXT);
    $stmt->bindParam(':publication_year', $publication_year, SQLITE3_INTEGER);
    $stmt->bindParam(':isbn', $isbn, SQLITE3_TEXT);
    $stmt->bindParam(':language', $language, SQLITE3_TEXT);
    $stmt->bindParam(':number_of_copies', $number_of_copies, SQLITE3_INTEGER);
    $stmt->bindParam(':media_format', $media_format, SQLITE3_TEXT);
    $stmt->bindParam(':cost', $cost, SQLITE3_FLOAT);

    // Execute the query and provide feedback
    if ($stmt->execute()) {
        $success_message = "Media added successfully!";
    } else {
        $error_message = "Error adding media. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Media - Advanced Library Management System</title>
    <link rel="stylesheet" href="../styles/styles.css">
</head>
<body>
    <div class="container">

        <!-- Title Section -->
        <header>
            <div class="title">
                <h1>Add New Media</h1> <!-- Page Title -->
            </div>
        </header>

        <!-- Navigation Menu -->
        <?php require("librarianstartnavbar.php"); ?>
        <?php require("../styles/darkmodeandreader.php"); ?>

        <!-- Form Section -->
        <div class="dashboard-section">
            <h2>Fill in the details of the media</h2>

            <!-- Success or Error Message Display -->
            <?php if (!empty($success_message)): ?>
                <p style="color: green;"><?php echo $success_message; ?></p>
            <?php endif; ?>

            <?php if (!empty($error_message)): ?>
                <p style="color: red;"><?php echo $error_message; ?></p>
            <?php endif; ?>

            <!-- Media Addition Form -->
            <form action="add_media.php" method="post">
                <div class="form-group">
                    <label for="title">Title:</label>
                    <input type="text" name="title" class="search-bar" required>
                </div>

                <div class="form-group">
                    <label for="author">Author:</label>
                    <input type="text" name="author" class="search-bar">
                </div>

                <div class="form-group">
                    <label for="type">Type:</label>
                    <select name="type" class="search-bar" required>
                        <option value="Book">Book</option>
                        <option value="Journal">Journal</option>
                        <option value="DVD">DVD</option>
                        <option value="Magazine">Magazine</option>
                        <option value="E-book">E-book</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="genre">Genre:</label>
                    <input type="text" name="genre" class="search-bar">
                </div>

                <div class="form-group">
                    <label for="publication_year">Publication Year:</label>
                    <input type="number" name="publication_year" class="search-bar">
                </div>

                <div class="form-group">
                    <label for="isbn">ISBN:</label>
                    <input type="text" name="isbn" class="search-bar">
                </div>

                <div class="form-group">
                    <label for="language">Language:</label>
                    <input type="text" name="language" class="search-bar">
                </div>

                <div class="form-group">
                    <label for="number_of_copies">Number of Copies:</label>
                    <input type="number" name="number_of_copies" class="search-bar" required>
                </div>



                <div class="form-group">
                    <label for="media_format">Format:</label>
                    <select name="media_format" class="search-bar" required>
                        <option value="Hardcover">Hardcover</option>
                        <option value="Paperback">Paperback</option>
                        <option value="Blu-ray">Blu-ray</option>
                        <option value="DVD">DVD</option>
                        <option value="Printed">Printed</option>
                        <option value="E-book">E-book</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="cost">Cost (£):</label>
                    <input type="number" name="cost" class="search-bar" step="0.01" required>
                </div>

                <div class="form-group">
                    <button type="submit" class="search-button">Add Media</button>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <?php require("../footer.php"); ?>
    </div>
</body>
</html>
