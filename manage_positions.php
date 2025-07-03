<?php
include 'db.php';

// Handle Add
if (isset($_POST['add_position'])) {
    $name = trim($_POST['position_name']);
    if (!empty($name)) {
        $stmt = $conn->prepare("INSERT INTO positions (name) VALUES (?)");
        if ($stmt) {
            $stmt->bind_param("s", $name);
            $stmt->execute();
            $stmt->close();
        } else {
            echo "Error: " . $conn->error;
        }
    }
    header("Location: manage_positions.php");
    exit;
}

// Handle Edit
if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $name = trim($_POST['name']);
    if (!empty($name)) {
        $stmt = $conn->prepare("UPDATE positions SET name=? WHERE id=?");
        if ($stmt) {
            $stmt->bind_param("si", $name, $id);
            if ($stmt->execute()) {
                echo "<script>alert('Position updated successfully!');</script>";
            } else {
                echo "<script>alert('Error updating position: " . $stmt->error . "');</script>";
            }
            $stmt->close();
        } else {
            echo "<script>alert('Error preparing statement: " . $conn->error . "');</script>";
        }
    } else {
        echo "<script>alert('Position name cannot be empty.');</script>";
    }
    header("Location: manage_positions.php");
    exit;
}

// Handle Delete
if (isset($_POST['delete'])) {
    $id = $_POST['id'];
    $stmt = $conn->prepare("DELETE FROM positions WHERE id=?");
    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    } else {
        echo "Error: " . $conn->error;
    }
    header("Location: manage_positions.php");
    exit;
}

// Fetch Positions with candidate count
$sql = "SELECT p.*, COUNT(c.id) AS candidate_count
        FROM positions p
        LEFT JOIN candidates c ON c.position_id = p.id
        GROUP BY p.id, p.name
        ORDER BY p.id ASC";
$result = $conn->query($sql);

// Fetch candidates by position for displaying in the table
$candidates_by_position = [];
$candidates_result = $conn->query("SELECT id, position_id, GROUP_CONCAT(name SEPARATOR ', ') AS candidate_names FROM candidates GROUP BY position_id");
while ($row = $candidates_result->fetch_assoc()) {
    $candidates_by_position[$row['position_id']][] = $row['candidate_names'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Positions</title>
    <style>
        body {
            min-height: 100vh;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Arial, sans-serif;
            background: url('managepositions-bg.jpg') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            align-items: flex-start;
            justify-content: center;
        }
        .main-container {
            background: rgba(10, 3, 3, 0.18); /* Transparent glass effect */
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(24,90,157,0.18);
            padding: 38px 32px 32px 32px;
            max-width: 900px;
            min-height: 700px;
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
        .form-section input[type="text"] {
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
        .candidate-names {
            font-style: italic;
            color: #444;
        }
        @media (max-width: 900px) {
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
        <h1>Manage Positions</h1>
        <div class="form-section">
            <form method="POST">
                <input type="text" name="position_name" placeholder="Position Name" required>
                <button type="submit" name="add_position">Add Position</button>
            </form>
        </div>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Position Name</th>
                    <th>No. of Candidates</th>
                    <th>Candidate Names</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $positions = $conn->query("SELECT * FROM positions");
                while ($pos = $positions->fetch_assoc()) {
                    $pos_id = $pos['id'];
                    $cands = $conn->query("SELECT name FROM candidates WHERE position_id = $pos_id");
                    $cand_names = [];
                    while ($cand = $cands->fetch_assoc()) {
                        $cand_names[] = htmlspecialchars($cand['name']);
                    }
                    $cand_count = count($cand_names);
                    $cand_names_str = $cand_count ? implode(', ', $cand_names) : "<span class='candidate-names'>No candidates</span>";

                    // If editing this row, show the edit form
                    if (isset($_GET['edit_id']) && $_GET['edit_id'] == $pos_id) {
                        echo "
                        <tr>
                            <form method='POST'>
                                <td>{$pos['id']}<input type='hidden' name='id' value='{$pos['id']}'></td>
                                <td><input type='text' name='name' value='" . htmlspecialchars($pos['name']) . "' required></td>
                                <td>{$cand_count}</td>
                                <td>{$cand_names_str}</td>
                                <td>
                                    <button class='action-btn edit' type='submit' name='edit'>Save</button>
                                    <a href='manage_positions.php' class='action-btn delete' style='background:#aaa;color:#fff;text-decoration:none;padding:6px 16px;border-radius:4px;'>Cancel</a>
                                </td>
                            </form>
                        </tr>";
                    } else {
                        echo "
                        <tr>
                            <td>{$pos['id']}</td>
                            <td>{$pos['name']}</td>
                            <td>{$cand_count}</td>
                            <td>{$cand_names_str}</td>
                            <td>
                                <a href='manage_positions.php?edit_id={$pos['id']}'><button class='action-btn edit' type='button'>Edit</button></a>
                                <form method='POST' action='manage_positions.php' style='display:inline;' onsubmit=\"return confirm('Are you sure you want to delete this position?');\">
                                    <input type='hidden' name='id' value='{$pos['id']}'>
                                    <button class='action-btn delete' type='submit' name='delete'>Delete</button>
                                </form>
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