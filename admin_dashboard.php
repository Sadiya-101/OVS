<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
ob_start(); // Start output buffering
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "voting_system");

// Check database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch counts for dashboard
$voters_result = $conn->query("SELECT COUNT(*) AS total_voters FROM voters");
$voters = $voters_result ? $voters_result->fetch_assoc()['total_voters'] : 0;

$candidates_result = $conn->query("SELECT COUNT(*) AS total_candidates FROM candidates");
$candidates = $candidates_result ? $candidates_result->fetch_assoc()['total_candidates'] : 0;

$votes_result = $conn->query("SELECT COUNT(*) AS total_votes FROM votes");
$votes = $votes_result ? $votes_result->fetch_assoc()['total_votes'] : 0;

$positions_result = $conn->query("SELECT COUNT(*) AS total_positions FROM positions");
$positions = $positions_result ? $positions_result->fetch_assoc()['total_positions'] : 0;

// Fetch data for bar graph (example: votes per position)
$graph_data = $conn->query("SELECT positions.name, COUNT(votes.id) AS total_votes 
                            FROM positions 
                            LEFT JOIN votes ON positions.id = votes.position_id 
                            GROUP BY positions.name");
$graph_labels = [];
$graph_values = [];
while ($row = $graph_data->fetch_assoc()) {
    $graph_labels[] = $row['name'];
    $graph_values[] = $row['total_votes'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body::before {
            content: "";
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            z-index: -2;
            background: url('admin_dashboard.jpg') no-repeat center center fixed;
            background-size: cover;
            opacity: 1;
        }
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            color: #fff;
            position: relative;
            overflow-x: hidden;
            background: url('admin_dashboard.jpg') no-repeat center center fixed;
            background-size: cover;
        }

        h1 {
            text-align: center;
            margin: 20px 0;
            font-size: 2.5rem;
            font-weight: bold;
        }

        .header {
            background: rgba(255,255,255,0.18);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            display: flex;
            justify-content: center;      /* Center content horizontally */
            align-items: center;
            padding: 20px;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            position: relative;
        }

        .header h1 {
            margin: 0 auto;
            font-size: 2rem;
            flex: 1;
            text-align: center;           /* Center text inside h1 */
        }

        .btn-logout {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            background: #dc3545;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: bold;
            transition: 0.3s;
        }

        .btn-logout:hover {
            background: #c82333;
        }

        .dashboard {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
        }

        .card {
            background: rgba(253, 249, 249, 0.18); /* Transparent glass effect */
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            text-align: center;
            transition: 0.3s;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            font-weight: bold;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        }

        .card img {
            width: 60px;
            height: 60px;
            margin-bottom: 10px;
        }

        .number {
            font-size: 3rem;
            margin-bottom: 10px;
            color: #ffdd57;
        }

        .label {
            font-size: 1.2rem;
            margin-bottom: 15px;
        }

        .more-info {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 20px;
            background: #ffdd57;
            color: #000;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: 0.3s;
        }

        .more-info:hover {
            background: #ffc107;
        }

        .chart-container {
            background: rgba(255,255,255,0.18); /* Transparent glass effect */
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            max-width: 900px;
            margin: 40px auto;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            font-weight: bold;
        }

        .chart-container h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 1.8rem;
        }

        .top-bar {
            margin-bottom: 18px;
        }

        .top-bar a {
            background:rgb(241, 247, 245);
            color: #000;
            border: none;
            border-radius: 6px;
            padding: 10px 24px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-right: 10px;
        }

        .btn-end-voting {
            background: #ff5722;
            color: #fff;
            border: none;
            padding: 18px 40px;
            border-radius: 8px;
            font-size: 1.5rem;
            font-weight: bold;
            cursor: pointer;
            margin-top: 20px;
            transition: background 0.2s, box-shadow 0.2s;
            box-shadow: 0 4px 16px rgba(255,87,34,0.15);
        }

        .btn-end-voting:hover {
            background: #e64a19;
        }
    </style>
</head>
<body>

<!-- Header Section -->
<div class="header">
    <h1>Admin Dashboard</h1>
    <a href="admin_logout.php" class="btn-logout">Logout</a>
</div>

<div class="top-bar">
    <a href="index.php">Back to Home Page</a>
</div>

<!-- Dashboard Content -->
<div class="dashboard">
    <div class="card">
        <img src="voters.png" alt="Voters Icon">
        <div class="number"><?php echo $voters; ?></div>
        <div class="label">Total Voters</div>
        <a class="more-info" href="manage_voters.php">Manage Voters</a>
    </div>

    <div class="card">
        <img src="candidates.png" alt="Candidates Icon">
        <div class="number"><?php echo $candidates; ?></div>
        <div class="label">Total Candidates</div>
        <a class="more-info" href="manage_candidates.php">Manage Candidates</a>
    </div>

    <div class="card">
        <img src="votes.png" alt="Votes Icon">
        <div class="number"><?php echo $votes; ?></div>
        <div class="label">Total Votes Cast</div>
        <a class="more-info" href="view_results.php">View Results</a>
    </div>

    <div class="card">
        <img src="positions.png" alt="Positions Icon">
        <div class="number"><?php echo $positions; ?></div>
        <div class="label">Total Positions</div>
        <a class="more-info" href="manage_positions.php">Manage Positions</a>
    </div>
</div>

<div class="chart-container">
    <h2>Votes Per Positions</h2>
    <canvas id="votesChart"></canvas>
</div>

<div style="text-align:center; margin: 30px 0;">
    <button id="endVotingBtn" class="btn-end-voting">End Voting & Show Results</button>
</div>

<script>
    const ctx = document.getElementById('votesChart').getContext('2d');
    const votesChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($graph_labels); ?>,
            datasets: [{
                label: 'Votes',
                data: <?php echo json_encode($graph_values); ?>,
                backgroundColor: 'rgba(48, 224, 236, 0.7)',
                borderColor: 'rgba(255, 221, 87, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        color: '#000',
                        font: {
                            size: 18,           // Increase Y axis font size
                            weight: 'bold'      // Make Y axis font bold
                        }
                    }
                },
                x: {
                    ticks: {
                        color: '#000',
                        font: {
                            size: 18,           // Increase X axis font size
                            weight: 'bold'      // Make X axis font bold
                        }
                    }
                }
            }
        }
    });

    document.getElementById('endVotingBtn').onclick = function() {
        // Open the results page in a new window and request fullscreen
        let win = window.open('view_results.php', '_blank');
        win.onload = function() {
            win.document.documentElement.requestFullscreen?.();
        };
    }
</script>

</body>
</html>