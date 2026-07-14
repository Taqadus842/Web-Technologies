<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $user_id = $_SESSION['user']['id'];

    $book_title = trim($_POST['book_title'] ?? '');
    $category   = trim($_POST['category'] ?? '');

    if (empty($book_title) || empty($category)) {
        $message = "All fields are required.";
    } else {

        try {

            $check = $pdo->prepare("
                SELECT id FROM book_requests 
                WHERE user_id=? AND book_title=? AND status='pending'
            ");
            $check->execute([$user_id, $book_title]);

            if ($check->rowCount() > 0) {
                $message = "You already have a pending request for this book.";
            } else {

                $stmt = $pdo->prepare("
                    INSERT INTO book_requests (user_id, book_title, category, status)
                    VALUES (?, ?, ?, 'pending')
                ");

                $stmt->execute([$user_id, $book_title, $category]);

                $_SESSION['msg'] = "Book request submitted successfully!";
                header("Location: dashboard.php");
                exit();
            }

        } catch (Exception $e) {
            error_log($e->getMessage());
            $message = "Something went wrong. Try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Request Book</title>

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

        /* CARD */
        .container {
            width: 400px;
            padding: 35px;
            border-radius: 16px;
            background: rgba(255,255,255,0.08);
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
            font-size: 22px;
            letter-spacing: 1px;
        }

        /* INPUTS */
        input, select {
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

        input:focus, select:focus {
            background: rgba(255,255,255,0.2);
            transform: scale(1.02);
        }

        option {
            color: black;
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

        /* MESSAGE */
        .msg {
            background: rgba(239, 68, 68, 0.2);
            color: #f87171;
            padding: 10px;
            border-radius: 10px;
            margin-bottom: 12px;
            font-size: 13px;
            text-align: center;
        }

        /* BACK LINK */
        .back {
            text-align: center;
            margin-top: 15px;
            font-size: 13px;
        }

        .back a {
            color: #60a5fa;
            text-decoration: none;
            font-weight: bold;
        }

        .back a:hover {
            text-decoration: underline;
        }

    </style>
</head>

<body>

<div class="container">

    <h2>Request a Book</h2>

    <?php if (!empty($message)): ?>
        <div class="msg"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <form method="POST">

        <input type="text" name="book_title" placeholder="Enter Book Title" required>

        <select name="category" required>
            <option value="">Select Category</option>
            <option value="Fiction">Fiction</option>
            <option value="Science">Science</option>
            <option value="Technology">Technology</option>
            <option value="History">History</option>
            <option value="Other">Other</option>
        </select>

        <button type="submit">Submit Request</button>

    </form>

    <div class="back">
        <a href="dashboard.php">← Back to Dashboard</a>
    </div>

</div>

</body>
</html>