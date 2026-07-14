<?php
require_once "../config/db.php";

$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $passwordRaw = $_POST['password'];

    if (empty($username) || empty($email) || empty($passwordRaw)) {
        $message = "All fields are required.";
        $messageType = "error";
    } else {
        try {

            // Check if user already exists
            $check = $pdo->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
            $check->execute([$email, $username]);

            if ($check->rowCount() > 0) {
                $message = "Username or Email already exists.";
                $messageType = "error";
            } else {

                $password = password_hash($passwordRaw, PASSWORD_DEFAULT);

                $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?,?,?,'user')");
                $stmt->execute([$username, $email, $password]);

                $message = "Registration successful! You can now login.";
                $messageType = "success";
            }

        } catch (Exception $e) {
            error_log($e->getMessage());
            $message = "Something went wrong. Please try again.";
            $messageType = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register Account</title>

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

        /* GLASS CARD */
        .card {
            width: 380px;
            padding: 35px;
            border-radius: 16px;
            background: rgba(255,255,255,0.08);
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
            margin-bottom: 20px;
            font-size: 24px;
            letter-spacing: 1px;
        }

        /* INPUTS */
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

        /* MESSAGE BOX */
        .msg {
            text-align: center;
            padding: 10px;
            margin-bottom: 12px;
            border-radius: 10px;
            font-size: 13px;
        }

        .success {
            background: rgba(34, 197, 94, 0.2);
            color: #22c55e;
        }

        .error {
            background: rgba(239, 68, 68, 0.2);
            color: #f87171;
        }

        /* FOOTER */
        .footer {
            text-align: center;
            margin-top: 15px;
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

<div class="card">

    <h2>Create Account</h2>

    <?php if (!empty($message)): ?>
        <div class="msg <?php echo $messageType; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <form method="POST">

        <input type="text" name="username" placeholder="Username" required>

        <input type="email" name="email" placeholder="Email Address" required>

        <input type="password" name="password" placeholder="Password" required>

        <button type="submit">Register</button>

    </form>

    <div class="footer">
        Already have an account? <a href="login.php">Login</a>
    </div>

</div>

</body>
</html>