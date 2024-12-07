<?php
require("../databaseconnection.php");

// Establish database connection
$db = connectDatabase();

// Fetch media details based on media_id
$media_id = $_GET['media_id'] ?? '';
$sql = "SELECT * FROM media WHERE media_id = :media_id";
$stmt = $db->prepare($sql);
$stmt->bindValue(':media_id', $media_id, SQLITE3_INTEGER);
$result = $stmt->execute();
$media = $result->fetchArray(SQLITE3_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($media['title']); ?> - Media Details</title>
    <link rel="stylesheet" href="../styles/styles.css">
</head>
<body>
    <div class="container">
        <header>
            <div class="title">
                <h1>Media Details</h1>
            </div>
        </header>

        <div class="media-details">
            <h2><?php echo htmlspecialchars($media['title']); ?></h2>
            <p><strong>Author:</strong> <?php echo htmlspecialchars($media['author']); ?></p>
            <p><strong>Type:</strong> <?php echo htmlspecialchars($media['type']); ?></p>
            <p><strong>Genre:</strong> <?php echo htmlspecialchars($media['genre']); ?></p>
            <p><strong>Publication Year:</strong> <?php echo htmlspecialchars($media['publication_year']); ?></p>
            <p><strong>ISBN:</strong> <?php echo htmlspecialchars($media['isbn']); ?></p>
            <p><strong>Language:</strong> <?php echo htmlspecialchars($media['language']); ?></p>
            <p><strong>Available Copies:</strong> <?php echo htmlspecialchars($media['available_copies']); ?></p>
            <p><strong>Status:</strong> <?php echo htmlspecialchars($media['status']); ?></p>
            <p><strong>Rating:</strong> <?php echo htmlspecialchars($media['rating']); ?></p>
            <p><strong>Reviews:</strong> <?php echo nl2br(htmlspecialchars($media['reviews'])); ?></p>
            <p><strong>Cost:</strong> $<?php echo htmlspecialchars($media['cost']); ?></p>
        </div>

        <a href="media_search.php" class="back-button">Back to Search</a>
    </div>
</body>
</html>
