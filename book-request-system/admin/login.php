<?php
session_start();
require_once "../config/db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (
            $user &&
            isset($user['role']) &&
            $user['role'] === 'admin' &&
            password_verify($password, $user['password'])
        ) {
            $_SESSION['admin'] = $user;

            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid admin credentials";
        }

    } catch (Exception $e) {
        error_log($e->getMessage());
        $error = "System error occurred";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>

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
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #0f172a, #1e293b);
        }

        .login-box {
            width: 380px;
            padding: 35px;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(12px);
            box-shadow: 0 20px 50px rgba(0,0,0,0.4);
            color: white;
            text-align: center;
        }

        h2 {
            margin-bottom: 20px;
        }

        .error {
            background: rgba(239, 68, 68, 0.2);
            color: #f87171;
            padding: 10px;
            border-radius: 10px;
            margin-bottom: 12px;
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
        }

        input::placeholder {
            color: #cbd5e1;
        }

        button {
            width: 100%;
            padding: 12px;
            margin-top: 10px;
            border: none;
            border-radius: 10px;
            background: #3b82f6;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background: #2563eb;
        }

        .footer {
            margin-top: 15px;
            font-size: 12px;
            color: #cbd5e1;
        }
    </style>
</head>

<body>

<div class="login-box">

    <h2>Admin Login</h2>

    <?php if (!empty($error)): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST">

        <input type="email" name="email" placeholder="Admin Email" required>

        <input type="password" name="password" placeholder="Password" required>

        <button type="submit">Login</button>

    </form>

    <div class="footer">
        Secure Admin Access Panel
    </div>

</div>

</body>
</html>