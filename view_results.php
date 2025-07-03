<?php
$conn = new mysqli("localhost", "root", "", "voting_system");

// Fetch candidates, their positions, and vote counts
$sql = "SELECT c.name AS candidate_name, p.name AS position_name, COUNT(v.id) AS vote_count 
        FROM candidates c
        LEFT JOIN positions p ON c.position_id = p.id
        LEFT JOIN votes v ON v.position_id = p.id
        GROUP BY c.id, c.name, p.name";
$result = $conn->query($sql);

$candidates = [];
$votes = [];

while ($row = $result->fetch_assoc()) {
    $candidates[] = $row['candidate_name'] . " (" . $row['position_name'] . ")";
    $votes[] = $row['vote_count'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Voting Results</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body::before {
            content: "";
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            z-index: -1;
            background: url('results-bg.jpg') no-repeat center center fixed;
            background-size: cover;
            filter: blur(8px); /* Adjust blur strength as needed */
            opacity: 1;
        }
        body {
            position: relative;
            z-index: 0;
            background: none !important;
            min-height: 100vh;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Arial, sans-serif;
            display: flex;
            align-items: flex-start;
            justify-content: center;
        }
        .main-container {
            background: rgba(12, 5, 5, 0.19);
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(24,90,157,0.18);
            padding: 38px 32px 32px 32px;
            max-width: 1300px;
            min-height: 700px;
            width: 100%;
            margin: 40px auto;
            animation: fadeIn 0.8s;
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            font-size: 1.3rem;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px);}
            to { opacity: 1; transform: translateY(0);}
        }
        h2 { color: #fff; margin-bottom: 20px; }
        canvas { margin-top: 20px; }
        a.button { display: inline-block; margin-top: 20px; padding: 10px 20px; background: #4CAF50; color: white; text-decoration: none; border-radius: 5px; }
        a.button:hover { background: #45a049; }
        .no-data {
            color: #fff;
            text-align: center;
            font-size: 2rem;
            margin-top: 100px;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <h2>Voting Results</h2>
        <?php if (empty($candidates)): ?>
            <div class="no-data">No voting data available.</div>
        <?php else: ?>
            <canvas id="voteChart" width="600" height="400"></canvas>
        <?php endif; ?>

        <a href="admin_dashboard.php" class="button">Back to Dashboard</a>
    </div>

    <?php if (!empty($candidates)): ?>
    <script>
        const ctx = document.getElementById('voteChart').getContext('2d');
        const voteChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($candidates); ?>,
                datasets: [{
                    label: 'Votes',
                    data: <?= json_encode($votes); ?>,
                    backgroundColor: 'rgba(235, 232, 54, 0.7)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2
                }]
            },
            options: {
                plugins: {
                    legend: {
                        labels: {
                            font: {
                                size: 24,
                                weight: 'bold'
                            },
                            color: '#fff'
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            font: {
                                size: 20,
                                weight: 'bold'
                            },
                            color: '#fff'
                        }
                    },
                    x: {
                        ticks: {
                            font: {
                                size: 20,
                                weight: 'bold'
                            },
                            color: '#fff'
                        }
                    }
                }
            }
        });
    </script>
    <?php endif; ?>
</body>
</html>
