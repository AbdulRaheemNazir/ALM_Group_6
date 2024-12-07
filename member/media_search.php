<?php
session_start(); // Start the session
require("../databaseconnection.php");

// Establish database connection
$db = connectDatabase();

// Initialize variables
$searchQuery = '';
$mediaType = '';
$genre = '';
$status = '';

// Handle the form submission for search and filters
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $searchQuery = isset($_POST['search']) ? $_POST['search'] : '';
    $mediaType = isset($_POST['media_type']) ? $_POST['media_type'] : '';
    $genre = isset($_POST['genre']) ? $_POST['genre'] : '';
    $status = isset($_POST['status']) ? $_POST['status'] : '';

    // Construct the SQL query with filters
    $sql = "SELECT * FROM media WHERE title LIKE :searchQuery";

    // Add filter conditions
    if ($mediaType !== '') {
        $sql .= " AND type = :mediaType";
    }
    if ($genre !== '') {
        $sql .= " AND genre = :genre";
    }
    if ($status !== '') {
        $sql .= " AND status = :status";
    }

    // Prepare and execute the query
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':searchQuery', '%' . $searchQuery . '%', SQLITE3_TEXT);

    // Bind the filters if they are set
    if ($mediaType !== '') {
        $stmt->bindValue(':mediaType', $mediaType, SQLITE3_TEXT);
    }
    if ($genre !== '') {
        $stmt->bindValue(':genre', $genre, SQLITE3_TEXT);
    }
    if ($status !== '') {
        $stmt->bindValue(':status', $status, SQLITE3_TEXT);
    }

    // Execute the query and get the results
    $results = $stmt->execute();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Advanced Library Management System</title>
    <link rel="stylesheet" href="../styles/styles.css">

</head>
<body>
    <div class="container">

        <!-- Title Section -->
        <header>
            <div class="title">
                <h1>Advanced Library Management System</h1>
            </div>
        </header>

        <!-- Navigation Menu -->
        <?php require("./memberstartnavbar.php"); ?>
        <?php require("../styles/darkmodeandreader.php"); ?>

        <!-- Search and Filter Form -->
        <form method="POST" action="">
            <div class="search-section">
                <input type="text" name="search" placeholder="Search library resources..." class="search-bar" value="<?php echo htmlspecialchars($searchQuery); ?>">
                <button type="submit" class="search-button">Search</button>
            </div>

            <!-- Filter Options -->
            <div class="filter-section">
                <label for="media_type">Media Type:</label>
                <select name="media_type" id="media_type">
                    <option value="">All</option>
                    <option value="Book" <?php echo $mediaType === 'Book' ? 'selected' : ''; ?>>Book</option>
                    <option value="Journal" <?php echo $mediaType === 'Journal' ? 'selected' : ''; ?>>Journal</option>
                    <option value="DVD" <?php echo $mediaType === 'DVD' ? 'selected' : ''; ?>>DVD</option>
                </select>

                <label for="genre">Genre:</label>
                <input type="text" name="genre" placeholder="Genre" value="<?php echo htmlspecialchars($genre); ?>">

                <label for="status">Availability:</label>
                <select name="status" id="status">
                    <option value="">All</option>
                    <option value="Available" <?php echo $status === 'Available' ? 'selected' : ''; ?>>Available</option>
                    <option value="Borrowed" <?php echo $status === 'Borrowed' ? 'selected' : ''; ?>>Borrowed</option>
                </select>
            </div>
        </form>

        <!-- Results Section (Tiled Layout) -->
        <div class="results-section">
            <?php if (isset($results)): ?>
                <?php while ($row = $results->fetchArray(SQLITE3_ASSOC)): ?>
                    <div class="card">
                        <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                        <p><strong>Type:</strong> <?php echo htmlspecialchars($row['type']); ?></p>
                        <p><strong>Genre:</strong> <?php echo htmlspecialchars($row['genre']); ?></p>
                        <p><strong>Availability:</strong> 
                            <?php 
                            // Display the status as either 'Available' or 'Borrowed'
                            echo htmlspecialchars($row['status']); 
                            ?>
                        </p>
                        
                        <a href="media_details.php?media_id=<?php echo $row['media_id']; ?>" class="details-button">View Details</a>

                        <?php
                        // Check if the member has already reserved this media
                        $member_id = $_SESSION['member_id']; // Assuming member_id is stored in session
                        $reservation_check_sql = "SELECT * FROM reservation WHERE media_id = :media_id AND member_id = :member_id";
                        $reservation_stmt = $db->prepare($reservation_check_sql);
                        $reservation_stmt->bindValue(':media_id', $row['media_id'], SQLITE3_INTEGER);
                        $reservation_stmt->bindValue(':member_id', $member_id, SQLITE3_INTEGER);
                        $reservation_result = $reservation_stmt->execute();
                        $has_reserved = $reservation_result->fetchArray(SQLITE3_ASSOC) !== false;
                        ?>

                        <form method="POST" action="reserve_media.php" style="display:inline;">
                            <input type="hidden" name="media_id" value="<?php echo $row['media_id']; ?>">
                            <button type="submit" class="reserve-button" <?php echo $has_reserved ? 'disabled' : ''; ?>>
                                <?php echo $has_reserved ? 'Reserved' : 'Reserve'; ?>
                            </button>
                        </form>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No results found.</p>
            <?php endif; ?>
        </div>

        <?php require("../footer.php"); ?>
    </div>
</body>
</html>
