<?php
$conn = new mysqli("localhost", "root", "", "voting_system");
$username = 'admin';
$password = password_hash('admin123', PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
$stmt->bind_param("ss", $username, $password);
$stmt->execute();
echo "Admin added successfully.";
?>
