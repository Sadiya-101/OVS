<?php
$conn = new mysqli("localhost", "root", "", "voting_system");

if (isset($_POST['close_voting'])) {
    $conn->query("UPDATE settings SET voting_status='closed' WHERE id=1");
    header("Location: admin_dashboard.php");
    exit();
}
?>
