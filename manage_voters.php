<?php
session_start();
$conn = new mysqli("localhost", "root", "", "voting_system");

// Handle Add Voter
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_voter'])) {
    $name = trim($_POST['name']);
    $dob = trim($_POST['dob']);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);

    // Calculate age
    $today = date("Y-m-d");
    $age = date_diff(date_create($dob), date_create($today))->y;

    if ($age < 18) {
        echo "<script>alert('Voter must be at least 18 years old.'); window.location.href='manage_voters.php';</script>";
        exit();
    }

    // Insert voter into the database
    $stmt = $conn->prepare("INSERT INTO voters (name, dob, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $dob, $password);

    if ($stmt->execute()) {
        echo "<script>alert('Voter added successfully!'); window.location.href='manage_voters.php';</script>";
    } else {
        echo "<script>alert('Error adding voter. Try again.'); window.location.href='manage_voters.php';</script>";
    }
}

// Handle Update Voter
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_voter'])) {
    $id = $_POST['id'];
    $name = trim($_POST['name']);
    $dob = trim($_POST['dob']);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);

    // Update voter in the database
    $stmt = $conn->prepare("UPDATE voters SET name = ?, dob = ?, password = ? WHERE id = ?");
    $stmt->bind_param("sssi", $name, $dob, $password, $id);

    if ($stmt->execute()) {
        echo "<script>alert('Voter updated successfully!'); window.location.href='manage_voters.php';</script>";
    } else {
        echo "<script>alert('Error updating voter. Try again.'); window.location.href='manage_voters.php';</script>";
    }
}

// Handle Delete Voter
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];

    // Delete voter from the database
    $stmt = $conn->prepare("DELETE FROM voters WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<script>alert('Voter deleted successfully!'); window.location.href='manage_voters.php';</script>";
    } else {
        echo "<script>alert('Error deleting voter. Try again.'); window.location.href='manage_voters.php';</script>";
    }
}

// Fetch Voters
$voters = $conn->query("SELECT * FROM voters");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Voters</title>
    <style>
        body {
            min-height: 100vh;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Arial, sans-serif;
            background: url('managevoters-bg.jpg') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            align-items: flex-start;
            justify-content: center;
        }
        .main-container {
            background: rgba(20, 9, 9, 0.18); /* Transparent glass effect */
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(24,90,157,0.18);
            padding: 38px 32px 32px 32px;
            max-width: 1100px;
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
        .form-section input[type="text"],
        .form-section input[type="date"],
        .form-section input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #e3eaf1;
            border-radius: 6px;
            font-size: 1.13rem;
            background: #fff;
            font-weight: 500;
            margin-bottom: 12px;
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
            background: #f4f8fb;
            color: #185a9d;
            font-weight: 700;
        }
        tr:last-child td {
            border-bottom: none;
        }
        /* No changes to button styles, keep as in your screenshot */
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
        <h1>Manage Voters</h1>
        <div class="form-section">
            <!-- Your add voter form here -->
            <form method="POST">
                <input type="text" name="name" placeholder="Full Name" required minlength="3">
                <input type="date" name="dob" placeholder="Date of Birth" required>
                <input type="password" name="password" placeholder="Password" required minlength="5">
                <button type="submit" name="add_voter" style="width:100%;padding:12px 0;background:#43b04a;color:#fff;border:none;border-radius:6px;font-size:1.13rem;font-weight:normal;cursor:pointer;">Add Voter</button>
            </form>
        </div>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Date of Birth</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row = $voters->fetch_assoc()) {
                    // If editing this voter, show the edit form
                    if (isset($_GET['edit_id']) && $_GET['edit_id'] == $row['id']) {
                        echo "
                        <tr>
                            <form method='POST'>
                                <td>{$row['id']}<input type='hidden' name='id' value='{$row['id']}'></td>
                                <td><input type='text' name='name' value='" . htmlspecialchars($row['name']) . "' required minlength='3'></td>
                                <td><input type='date' name='dob' value='{$row['dob']}' required></td>
                                <td>
                                    <input type='password' name='password' placeholder='New Password' required minlength='5'>
                                    <button type='submit' name='update_voter' style='background:#ffc107;color:#222;border:none;border-radius:4px;padding:6px 16px;font-size:1rem;font-weight:normal;margin-right:6px;cursor:pointer;'>Save</button>
                                    <a href='manage_voters.php' style='background:#aaa;color:#fff;text-decoration:none;padding:6px 16px;border-radius:4px;'>Cancel</a>
                                </td>
                            </form>
                        </tr>";
                    } else {
                        echo "
                        <tr>
                            <td>{$row['id']}</td>
                            <td>{$row['name']}</td>
                            <td>{$row['dob']}</td>
                            <td>
                                <a href='manage_voters.php?edit_id={$row['id']}'><button type='button' style='background:#eee;color:#222;border:none;border-radius:4px;padding:6px 16px;font-size:1rem;font-weight:normal;margin-right:6px;cursor:pointer;'>Edit</button></a>
                                <a href='manage_voters.php?delete_id={$row['id']}' onclick='return confirm(\"Are you sure you want to delete this voter?\");' style='text-decoration:none;'>
                                    <button type='button' style='background:#dc3545;color:#fff;border:none;border-radius:4px;padding:6px 16px;font-size:1rem;font-weight:normal;cursor:pointer;'>Delete</button>
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
