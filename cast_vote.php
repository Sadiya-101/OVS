<?php
session_start();
if (!isset($_SESSION['voter_id'])) {
    header("Location: voter_login.php");
    exit();
}

include 'db_connect.php';

// Fetch all positions
$positions = $conn->query("SELECT * FROM positions");
if (!$positions) {
    die("Error fetching positions: " . $conn->error);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cast Your Vote</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Cast Your Vote</h2>
    <!-- Display Positions -->
    <form method="GET">
        <h3>Select a Position</h3>
        <?php while ($pos = $positions->fetch_assoc()): ?>
            <label>
                <input type="radio" name="position_id" value="<?= $pos['id'] ?>" required>
                <?= htmlspecialchars($pos['name']) ?>
            </label><br>
        <?php endwhile; ?>
        <br>
        <button type="submit">Submit</button>
    </form>
</body>
</html>