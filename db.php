<?php
$servername = "localhost"; // Change as needed
$username = "root";        // Change as needed
$password = "";            // Change as needed
$dbname = "voting_system"; // Change to your database name

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
