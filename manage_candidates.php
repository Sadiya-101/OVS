<?php
$conn = new mysqli("localhost", "root", "", "voting_system");

// Handle Add Candidate
if (isset($_POST['add_candidate'])) {
    $name = $_POST['candidate_name'];
    $position_id = $_POST['position_id'];
    $conn->query("INSERT INTO candidates (name, position_id) VALUES ('$name', $position_id)");
}

// Handle Update Candidate
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $position_id = $_POST['position_id'];
    $conn->query("UPDATE candidates SET name='$name', position_id=$position_id WHERE id=$id");
}

// Handle Delete Candidate
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $conn->query("DELETE FROM candidates WHERE id=$id");
}

// Fetch Positions for Dropdown
$positions = $conn->query("SELECT * FROM positions");

// Fetch All Candidates
$candidates = $conn->query("SELECT c.id, c.name, c.position_id, p.name AS position_name 
                            FROM candidates c 
                            LEFT JOIN positions p ON c.position_id = p.id");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Candidates</title>
    <style>
        body {
            min-height: 100vh;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Arial, sans-serif;
            background: url('managecandidates-bg.jpg') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            align-items: flex-start;
            justify-content: center;
        }
        .main-container {
            background: rgba(19, 9, 9, 0.18); /* Transparent glass effect */
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(24,90,157,0.18);
            padding: 38px 32px 32px 32px;
            max-width: 900px;
            width: 100%;
            margin: 40px auto;
            animation: fadeIn 0.8s;
            backdrop-filter: blur(8px);           /* Optional: glass effect */
            -webkit-backdrop-filter: blur(8px);   /* For Safari */
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px);}
            to { opacity: 1; transform: translateY(0);}
        }
        h1 {
            text-align: center;
            color: #fff;
            margin-bottom: 32px;
            font-size: 2rem;
            letter-spacing: 1px;
            font-weight: 700;
        }
        .top-bar {
            display: flex;
            justify-content: flex-start;
            margin-bottom: 18px;
        }
        .form-section {
            background: #f7fafc;
            border-radius: 12px;
            padding: 24px 18px 18px 18px;
            margin-bottom: 28px;
        }
        .form-section input[type="text"],
        .form-section select {
            width: 100%;
            padding: 12px;
            border: 1px solid #e3eaf1;
            border-radius: 6px;
            font-size: 1.13rem;
            background: #fff;
            font-weight: 500;
            margin-bottom: 12px;
        }
        .form-section button {
            width: 100%;
            padding: 12px 0;
            background: #43b04a;
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 1.13rem;
            font-weight: normal;
            cursor: pointer;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            margin-top: 18px;
        }
        th, td {
            padding: 12px 10px;
            border-bottom: 1px solid #e3eaf1;
            text-align: left;
            font-size: 1.08rem;
        }
        th {
            background: #0074e8;
            color: #fff;
            font-weight: 700;
        }
        tr:last-child td {
            border-bottom: none;
        }
        .action-btn {
            border: none;
            border-radius: 4px;
            padding: 6px 16px;
            font-size: 1rem;
            font-weight: normal;
            cursor: pointer;
            margin-right: 6px;
        }
        .action-btn.edit {
            background: #ffc107;
            color: #222;
        }
        .action-btn.delete {
            background: #dc3545;
            color: #fff;
        }
        @media (max-width: 700px) {
            .main-container {
                padding: 18px 4px;
            }
            th, td {
                font-size: 0.98rem;
                padding: 8px 4px;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="top-bar">
            <a href="admin_dashboard.php" style="background:#2196f3;color:#fff;border:none;border-radius:6px;padding:10px 24px;font-size:1rem;font-weight:bold;cursor:pointer;text-decoration:none;display:inline-block;">Back to Dashboard</a>
        </div>
        <h1>Manage Candidates</h1>
        <div class="form-section">
            <!-- Add Candidate Form -->
            <form method="POST">
                <input type="text" name="candidate_name" placeholder="Candidate Name" required>
                <select name="position_id" required>
                    <option value="">Select Position</option>
                    <?php
                    // Fetch positions from DB
                    $positions = $conn->query("SELECT * FROM positions");
                    while ($pos = $positions->fetch_assoc()) {
                        echo "<option value='{$pos['id']}'>" . htmlspecialchars($pos['name']) . "</option>";
                    }
                    ?>
                </select>
                <button type="submit" name="add_candidate">Add Candidate</button>
            </form>
        </div>
        <!-- Candidates Table -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Position</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch candidates with position names
                $candidates = $conn->query("SELECT candidates.id, candidates.name, positions.name AS position_name, candidates.position_id FROM candidates JOIN positions ON candidates.position_id = positions.id");
                while ($row = $candidates->fetch_assoc()) {
                    // If this row is being edited, show the edit form
                    if (isset($_GET['edit_id']) && $_GET['edit_id'] == $row['id']) {
                        echo "
                        <tr>
                            <form method='POST'>
                                <td>{$row['id']}<input type='hidden' name='id' value='{$row['id']}'></td>
                                <td><input type='text' name='name' value='" . htmlspecialchars($row['name']) . "' required></td>
                                <td>
                                    <select name='position_id' required>";
                                    $positions2 = $conn->query("SELECT * FROM positions");
                                    while ($pos2 = $positions2->fetch_assoc()) {
                                        $selected = $pos2['id'] == $row['position_id'] ? "selected" : "";
                                        echo "<option value='{$pos2['id']}' $selected>" . htmlspecialchars($pos2['name']) . "</option>";
                                    }
                        echo "      </select>
                                </td>
                                <td>
                                    <button class='action-btn edit' type='submit' name='update'>Save</button>
                                    <a href='manage_candidates.php' class='action-btn delete' style='background:#aaa;color:#fff;text-decoration:none;padding:6px 16px;border-radius:4px;'>Cancel</a>
                                </td>
                            </form>
                        </tr>";
                    } else {
                        echo "
                        <tr>
                            <td>{$row['id']}</td>
                            <td>{$row['name']}</td>
                            <td>{$row['position_name']}</td>
                            <td>
                                <a href='manage_candidates.php?edit_id={$row['id']}'><button class='action-btn edit' type='button'>Edit</button></a>
                                <a href='manage_candidates.php?delete_id={$row['id']}' onclick='return confirm(\"Are you sure you want to delete this candidate?\");' style='text-decoration:none;'>
                                    <button class='action-btn delete' type='button'>Delete</button>
                                </a>
                            </td>
                        </tr>";
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
