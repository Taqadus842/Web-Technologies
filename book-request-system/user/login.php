<?php
session_start();
require_once "../config/db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email=?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {

            $_SESSION['user'] = $user;
            header("Location: dashboard.php");
            exit();

        } else {
            $error = "Invalid login details";
        }

    } catch (Exception $e) {
        error_log($e->getMessage());
        $error = "Something went wrong";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Login</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif;
        }

        body {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0f172a, #1e293b);
        }

        .container {
            width: 380px;
            padding: 35px;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(12px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.4);
            color: white;
            animation: fadeIn 0.6s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            font-size: 24px;
            letter-spacing: 1px;
        }

        .error {
            background: rgba(255, 0, 0, 0.15);
            color: #ff6b6b;
            padding: 10px;
            border-radius: 10px;
            margin-bottom: 15px;
            text-align: center;
            font-size: 13px;
        }

        input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: none;
            border-radius: 10px;
            outline: none;
            background: rgba(255,255,255,0.1);
            color: white;
            font-size: 14px;
            transition: 0.3s;
        }

        input::placeholder {
            color: #cbd5e1;
        }

        input:focus {
            background: rgba(255,255,255,0.2);
            transform: scale(1.02);
        }

        button {
            width: 100%;
            padding: 12px;
            margin-top: 10px;
            border: none;
            border-radius: 10px;
            background: #3b82f6;
            color: white;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background: #2563eb;
            transform: translateY(-2px);
        }

        .footer {
            text-align: center;
            margin-top: 18px;
            font-size: 13px;
            color: #cbd5e1;
        }

        .footer a {
            color: #60a5fa;
            text-decoration: none;
            font-weight: bold;
        }

        .footer a:hover {
            text-decoration: underline;
        }
    </style>

</head>

<body>

<div class="container">

    <h2>Welcome Back</h2>

    <?php if (!empty($error)): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST">

        <input type="email" name="email" placeholder="Enter your email" required>

        <input type="password" name="password" placeholder="Enter your password" required>

        <button type="submit">Login</button>

    </form>

    <div class="footer">
        Don't have an account? <a href="register.php">Create Account</a>
    </div>

</div>

</body>
</html>