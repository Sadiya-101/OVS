<?php
session_start();
ob_start(); // Start output buffering
$conn = new mysqli("localhost", "root", "", "voting_system");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Check if the admin exists
    $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $row['password'])) {
            $_SESSION['admin_logged_in'] = true; // Set session variable
            $_SESSION['admin'] = $row['username']; // Store admin username
            header("Location: admin_dashboard.php"); // Redirect to dashboard
            exit();
        } else {
            echo "<script>alert('Invalid password.'); window.location.href='admin_login.php';</script>";
        }
    } else {
        echo "<script>alert('Admin not found.'); window.location.href='admin_login.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <style>
        body {
            min-height: 100vh;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Arial, sans-serif;
            background: url('admin-background.jpg') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            align-items: center;
            justify-content: center;
            /* Remove any filter or blur here */
        }
        .login-container {
            background: rgba(24, 9, 9, 0.7);
            box-shadow: 0 8px 32px 0 rgba(31,38,135,0.18);
            /* backdrop-filter: blur(12px); */
            /* -webkit-backdrop-filter: blur(12px); */
            border-radius: 18px;
            padding: 38px 32px 32px 32px;
            max-width: 450px;
            width: 100%;
            text-align: center;
            animation: fadeIn 0.8s;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px);}
            to { opacity: 1; transform: translateY(0);}
        }
        .login-container h1 {
            color:rgb(244, 246, 248);
            margin-bottom: 24px;
            font-size: 1.6rem;
            font-weight: 700;
            letter-spacing: 1px;
        }
        .login-container input[type="text"],
        .login-container input[type="password"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 18px;
            border: 1px solid #e3eaf1;
            border-radius: 6px;
            font-size: 1.08rem;
            background: rgba(255,255,255,0.7);
            font-weight: 500;
        }
        .login-container input[type="text"]:focus,
        .login-container input[type="password"]:focus {
            border: 1.5px solid #43cea2;
            outline: none;
        }
        .login-container button {
            width: 100%;
            padding: 12px 0;
            background: linear-gradient(90deg, #43cea2 0%, #185a9d 100%);
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 1.13rem;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.2s;
        }
        .login-container button:hover {
            background: linear-gradient(90deg, #185a9d 0%, #43cea2 100%);
        }
        @media (max-width: 500px) {
            .login-container {
                padding: 18px 8px;
                max-width: 98vw;
            }
        }
        .admin-icon {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 18px;
        }
        .admin-icon svg {
            display: block;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Admin/User PNG Icon -->
        <div class="admin-icon">
            <img src="admin-icon.png" alt="Admin Icon" width="60" height="60" style="margin-bottom:18px;">
        </div>
        <h1>Admin Login</h1>
        <form method="post" action="">
            <input type="text" name="username" placeholder="Enter Admin Username" required>
            <input type="password" name="password" placeholder="Enter Password" required>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>
