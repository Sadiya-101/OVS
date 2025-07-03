<?php
session_start();
$conn = new mysqli("localhost", "root", "", "voting_system");

// Handle Login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $dob = trim($_POST['dob']);
    $password = trim($_POST['password']);

    // Check if dob exists in the database
    $stmt = $conn->prepare("SELECT * FROM voters WHERE dob = ?");
    $stmt->bind_param("s", $dob);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo "<script>alert('Invalid Date of Birth or Password.'); window.location.href='voter_login.php';</script>";
        exit();
    }

    $voter = $result->fetch_assoc();

    // Verify the password
    if (!password_verify($password, $voter['password'])) {
        echo "<script>alert('Invalid Date of Birth or Password.'); window.location.href='voter_login.php';</script>";
        exit();
    }

    // Login successful
    $_SESSION['voter_id'] = $voter['id'];
    echo "<script>alert('Login successful!'); window.location.href='voter_dashboard.php';</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Voter Login</title>
    <style>
        body::before {
            content: "";
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            z-index: -1;
            background: url('voter-background.jpg') no-repeat center center fixed;
            background-size: cover;
            filter: blur(8px); /* Adjust blur strength as needed */
            opacity: 1;
        }
        body {
            position: relative;
            z-index: 0;
            background: none !important;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .login-container {
            background: rgba(34, 14, 14, 0.5); /* Less transparent (was 0.18) */
            box-shadow: 0 8px 32px 0 rgba(31,38,135,0.18);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-radius: 18px;
            padding: 48px 48px 48px 48px;   /* Increased padding */
            max-width: 500px;               /* Increased width */
            min-height: 500px;              /* Increased height */
            width: 100%;
            text-align: center;
            animation: fadeIn 0.8s;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px);}
            to { opacity: 1; transform: translateY(0);}
        }
        .login-container h2 {
            color:rgb(249, 250, 252);
            margin-bottom: 24px;
            font-size: 1.6rem;
            font-weight: 700;
            letter-spacing: 1px;
        }
        .alert {
            background: #eafaf1;
            color: #185a9d;
            padding: 10px 14px;
            border-radius: 6px;
            margin-bottom: 18px;
            font-size: 1.08rem;
            font-weight: 500;
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }
        input[type="date"], input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #e3eaf1;
            border-radius: 6px;
            font-size: 1.13rem;
            background: rgba(255,255,255,0.7);
            font-weight: 500;
        }
        input[type="date"]:focus, input[type="password"]:focus {
            border: 1.5px solid #43cea2;
            outline: none;
        }
        button {
            padding: 12px 0;
            background: linear-gradient(90deg, #43cea2 0%, #185a9d 100%);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 1.13rem;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.2s, box-shadow 0.2s;
            box-shadow: 0 2px 8px rgba(67,206,162,0.08);
        }
        button:hover {
            background: linear-gradient(90deg, #185a9d 0%, #43cea2 100%);
            box-shadow: 0 4px 16px rgba(24,90,157,0.12);
        }
        .top-bar {
            margin-bottom: 18px;
            width: 100%;
            display: flex;
            justify-content: flex-start;
        }
        .top-bar a {
            background: #43cea2;
            color: #fff;
            border: none;
            border-radius: 6px;
            padding: 10px 24px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        .voter-icon {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 18px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="top-bar">
            <a href="index.php">Back to Home Page</a>
        </div>
        <!-- Voter/User PNG Icon -->
        <div class="voter-icon">
            <img src="voter-icon.png" alt="Voter Icon" width="60" height="60" style="margin-bottom:18px;">
        </div>
        <h2>Voter Login</h2>
        <?php if (isset($_GET['msg'])): ?>
            <div class="alert"><?= htmlspecialchars($_GET['msg']) ?></div>
        <?php endif; ?>
        <form method="POST">
            <input type="date" name="dob" placeholder="Date of Birth" required>
            <input type="password" name="password" placeholder="Password" required minlength="5">
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>
