<?php
session_start();
include 'db.php';

// Check if voter is logged in
if (!isset($_SESSION['voter_id'])) {
    header("Location: voter_login.php");
    exit();
}

// Fetch all positions
$sql = "SELECT * FROM positions ORDER BY id ASC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voter Dashboard</title>
    <style>
        body {
            min-height: 100vh;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Arial, sans-serif;
            background: url('voter-dashboard-bg.jpg') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            font-size: 1.25rem; /* Increase base font size */
        }
        .dashboard-container {
            background: rgba(15, 8, 8, 0.18);
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(24,90,157,0.18);
            padding: 48px 48px 48px 48px;
            max-width: 900px;      /* Increased width */
            min-height: 600px;     /* Set a minimum height */
            width: 100%;
            animation: fadeIn 0.8s;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            font-size: 1.2rem; /* Increase font size inside container */
            color: #fff; /* <-- Change this to your desired font color */
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px);}
            to { opacity: 1; transform: translateY(0);}
        }
        h2 {
            text-align: center;
            color:rgb(248, 249, 250);
            margin-bottom: 32px;
            font-size: 2.3rem; /* Increase heading size */
            letter-spacing: 1px;
            font-weight: 700;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        td {
            padding: 14px 0;
            border-bottom: 1px solid #e3eaf1;
            font-size: 1.5rem; /* Increase table cell font size */
            font-weight: 600;
        }
        tr:last-child td {
            border-bottom: none;
        }
        .btn {
            background: linear-gradient(90deg, #43cea2 0%, #185a9d 100%);
            color: #fff;
            border: none;
            padding: 10px 0;
            border-radius: 6px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: bold;
            width: 160px;
            transition: background 0.2s, box-shadow 0.2s;
            box-shadow: 0 2px 8px rgba(67,206,162,0.08);
            text-align: center;
            text-decoration: none;
            display: inline-block;
        }
        .btn:hover {
            background: linear-gradient(90deg, #185a9d 0%, #43cea2 100%);
            box-shadow: 0 4px 16px rgba(24,90,157,0.12);
        }
        .btn-home {
            margin-bottom: 28px;
            width: 220px;
            font-size: 1.1rem;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <a href="index.php" class="btn btn-home">Back to Home Page</a>
        <h2>Select a Position to View Candidates</h2>
        <table>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td style="text-align:right;">
                        <a href="view_candidates.php?position_id=<?= $row['id'] ?>" class="btn">View Candidates</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>