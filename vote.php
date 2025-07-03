<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $candidate_id = intval($_POST['candidate_id']);
    $position_id = intval($_POST['position_id']);
    $voter_id = $_SESSION['voter_id'] ?? null;

    if ($voter_id && $candidate_id && $position_id) {
        // Check if voter already voted for this position
        $check = $conn->prepare("SELECT id FROM votes WHERE voter_id=? AND position_id=?");
        $check->bind_param("ii", $voter_id, $position_id);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $msg = "You have already voted for this position.";
            $success = false;
        } else {
            // Insert vote
            $stmt = $conn->prepare("INSERT INTO votes (voter_id, candidate_id, position_id) VALUES (?, ?, ?)");
            $stmt->bind_param("iii", $voter_id, $candidate_id, $position_id);
            if ($stmt->execute()) {
                $msg = "Your vote has been cast successfully!";
                $success = true;
            } else {
                $msg = "Error casting vote.";
                $success = false;
            }
            $stmt->close();
        }
        $check->close();
    } else {
        $msg = "Invalid vote request.";
        $success = false;
    }
} else {
    $msg = "Invalid access.";
    $success = false;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Vote Status</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f4f4f4;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(24,90,157,0.12);
            padding: 40px 32px;
            text-align: center;
        }
        h2 {
            color: <?= isset($success) && $success ? '#185a9d' : '#d32f2f' ?>;
            margin-bottom: 24px;
        }
        .btn {
            background: linear-gradient(90deg, #43cea2 0%, #185a9d 100%);
            color: #fff;
            border: none;
            padding: 12px 32px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1.1rem;
            font-weight: bold;
            text-decoration: none;
            margin: 10px 8px 0 8px;
            display: inline-block;
        }
        .btn:hover {
            background: linear-gradient(90deg, #185a9d 0%, #43cea2 100%);
        }
    </style>
</head>
<body>
    <div class="container">
        <h2><?= htmlspecialchars($msg) ?></h2>
        <a href="voter_dashboard.php" class="btn">Back to Dashboard</a>
        <?php if (!$success): ?>
            <a href="javascript:history.back()" class="btn">Try Again</a>
        <?php endif;