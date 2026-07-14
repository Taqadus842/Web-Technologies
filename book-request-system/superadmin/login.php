<?php
session_start();
require_once "../config/db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email=?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && $user['role'] === 'superadmin' && password_verify($password, $user['password'])) {

            $_SESSION['superadmin'] = $user;
            header("Location: dashboard.php");
            exit();

        } else {
            $error = "Invalid super admin credentials";
        }

    } catch (Exception $e) {
        error_log($e->getMessage());
        $error = "System error. Try again.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Super Admin Login</title>

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

        /* LOGIN CARD */
        .login-box {
            width: 380px;
            padding: 35px;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(12px);
            box-shadow: 0 20px 50px rgba(0,0,0,0.4);
            color: white;
            animation: fadeIn 0.6s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
            letter-spacing: 1px;
        }

        /* ERROR */
        .error {
            background: rgba(239, 68, 68, 0.2);
            color: #f87171;
            padding: 10px;
            border-radius: 10px;
            text-align: center;
            font-size: 13px;
            margin-bottom: 12px;
        }

        /* INPUT */
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

        /* BUTTON */
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

        /* FOOTER */
        .footer {
            text-align: center;
            margin-top: 15px;
            font-size: 12px;
            color: #cbd5e1;
        }
    </style>

</head>

<body>

<div class="login-box">

    <h2>Super Admin Panel</h2>

    <?php if (!empty($error)): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST">

        <input type="email" name="email" placeholder="Admin Email" required>

        <input type="password" name="password" placeholder="Password" required>

        <button type="submit">Login</button>

    </form>

    <div class="footer">
        Secure Super Admin Access Only
    </div>

</div>

</body>
</html>