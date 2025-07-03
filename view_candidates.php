<?php
session_start();
include 'db.php';

// Check if voter is logged in
if (!isset($_SESSION['voter_id'])) {
    header("Location: voter_login.php");
    exit();
}

$voter_id = $_SESSION['voter_id'];
$position_id = $_GET['position_id'] ?? null;

// Check if position_id is valid
if (!$position_id) {
    header("Location: voter_dashboard.php?msg=Invalid position selected.");
    exit();
}

// Check if voter already voted for this position
$checkVote = $conn->prepare("SELECT id FROM votes WHERE voter_id = ? AND position_id = ?");
$checkVote->bind_param("ii", $voter_id, $position_id);
$checkVote->execute();
$voteResult = $checkVote->get_result();
$alreadyVoted = $voteResult->num_rows > 0;

// Handle voting
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['candidate_id']) && !$alreadyVoted) {
    $candidate_id = intval($_POST['candidate_id']);

    // Insert vote
    $stmt = $conn->prepare("INSERT INTO votes (voter_id, position_id, candidate_id) VALUES (?, ?, ?)");
    $stmt->bind_param("iii", $voter_id, $position_id, $candidate_id);
    if ($stmt->execute()) {
        $msg = "Your vote has been cast successfully!";
        $alreadyVoted = true;

        // Check if voter has now voted for all positions
        $totalPositionsResult = $conn->query("SELECT COUNT(*) as total FROM positions");
        $totalPositions = $totalPositionsResult->fetch_assoc()['total'];

        $votedPositionsStmt = $conn->prepare("SELECT COUNT(DISTINCT position_id) as voted FROM votes WHERE voter_id = ?");
        $votedPositionsStmt->bind_param("i", $voter_id);
        $votedPositionsStmt->execute();
        $votedPositionsResult = $votedPositionsStmt->get_result();
        $votedPositions = $votedPositionsResult->fetch_assoc()['voted'] ?? 0;

        if ($votedPositions >= $totalPositions) {
            session_destroy();
            header("Location: index.php?msg=Thank you for voting! You have been logged out.");
            exit();
        }
    } else {
        $msg = "Error casting vote. Please try again.";
    }
    $stmt->close();
}

// Fetch candidates for the position
$stmt = $conn->prepare("SELECT * FROM candidates WHERE position_id = ?");
$stmt->bind_param("i", $position_id);
$stmt->execute();
$candidates = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Candidates List</title>
    <style>
        body {
            min-height: 100vh;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Arial, sans-serif;
            background: url('candidates-bg.jpg') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            background: rgba(255,255,255,0.18);
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(24,90,157,0.18);
            padding: 48px 48px 48px 48px;
            max-width: 700px;
            min-height: 500px;
            width: 100%;
            animation: fadeIn 0.8s;
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(8px);
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px);}
            to { opacity: 1; transform: translateY(0);}
        }
        h2 {
            text-align: center;
            color:rgb(254, 255, 255);
            margin-bottom: 32px;
            font-size: 2rem;
            letter-spacing: 1px;
            font-weight: 700;
        }
        .candidate-list {
            margin-bottom: 28px;
        }
        .candidate-item {
            display: flex;
            align-items: center;
            padding: 14px 0;
            border-bottom: 1px solid #e3eaf1;
            transition: background 0.2s;
        }
        .candidate-item:last-child {
            border-bottom: none;
        }
        .candidate-item:hover {
            background: #f4f8fb;
        }
        .candidate-radio {
            margin-right: 16px;
            accent-color: #43cea2;
            transform: scale(1.2);
        }
        .candidate-name {
            font-size: 1.13rem;
            color: #222;
            font-weight: 500;
        }
        .vote-btn {
            width: 100%;
            background: linear-gradient(90deg, #43cea2 0%, #185a9d 100%);
            color: #fff;
            border: none;
            padding: 14px 0;
            border-radius: 8px;
            font-size: 1.13rem;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.2s, box-shadow 0.2s;
            margin-top: 22px;
            box-shadow: 0 2px 8px rgba(67,206,162,0.08);
        }
        .vote-btn:hover {
            background: linear-gradient(90deg, #185a9d 0%, #43cea2 100%);
            box-shadow: 0 4px 16px rgba(24,90,157,0.12);
        }
        .msg {
            text-align: center;
            font-size: 1.2rem;
            color: #185a9d;
            margin-bottom: 18px;
            font-weight: bold;
        }
        .already-voted {
            color: #d32f2f;
            font-weight: bold;
            text-align: center;
            margin-bottom: 18px;
        }
        .btn-dashboard {
            display: block;
            width: 100%;
            margin-top: 24px;
            background: linear-gradient(90deg, #185a9d 0%, #43cea2 100%);
            color: #fff;
            border: none;
            padding: 12px 0;
            border-radius: 8px;
            font-size: 1.13rem;
            font-weight: bold;
            text-align: center;
            text-decoration: none;
            transition: background 0.2s, box-shadow 0.2s;
        }
        .btn-dashboard:hover {
            background: linear-gradient(90deg, #43cea2 0%, #185a9d 100%);
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Candidates List</h2>
        <?php if ($msg): ?>
            <div class="msg"><?= htmlspecialchars($msg) ?></div>
        <?php endif; ?>
        <?php if ($alreadyVoted): ?>
            <div class="already-voted">You have already voted for this position.</div>
        <?php else: ?>
            <form method="POST">
                <div class="candidate-list">
                    <?php while ($row = $candidates->fetch_assoc()): ?>
                        <div class="candidate-item">
                            <input class="candidate-radio" type="radio" name="candidate_id" value="<?= $row['id'] ?>" required>
                            <span class="candidate-name"><?= htmlspecialchars($row['name']) ?></span>
                        </div>
                    <?php endwhile; ?>
                </div>
                <button class="vote-btn" type="submit">Vote</button>
            </form>
        <?php endif; ?>
        <a href="voter_dashboard.php" class="btn-dashboard">Back to Dashboard</a>
    </div>
</body>
</html>