<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user']['id'];

$msg = "";
if (isset($_SESSION['msg'])) {
    $msg = $_SESSION['msg'];
    unset($_SESSION['msg']);
}

$stmt = $pdo->prepare("SELECT * FROM book_requests WHERE user_id=? ORDER BY id DESC");
$stmt->execute([$user_id]);
$rows = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #0f172a, #1e293b);
            color: #e2e8f0;
            min-height: 100vh;
        }
        .header {
            background: rgba(255,255,255,0.05);
            backdrop-filter: blur(10px);
            padding: 18px 30px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .header h2 {
            font-size: 20px;
        }

        .container {
            width: 88%;
            margin: 40px auto;
        }

        .welcome {
            margin-bottom: 15px;
            font-size: 15px;
        }

        .msg {
            background: rgba(59, 130, 246, 0.15);
            color: #93c5fd;
            padding: 12px;
            border-radius: 10px;
            margin-bottom: 15px;
            font-size: 14px;
        }

        .nav {
            display: flex;
            gap: 12px;
            margin-bottom: 20px;
        }

        .nav a {
            text-decoration: none;
            padding: 10px 16px;
            border-radius: 10px;
            background: #3b82f6;
            color: white;
            font-size: 14px;
            transition: 0.3s;
        }

        .nav a:hover {
            background: #2563eb;
            transform: translateY(-2px);
        }

        .card {
            background: rgba(255,255,255,0.06);
            backdrop-filter: blur(12px);
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.3);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            overflow: hidden;
            border-radius: 12px;
        }

        th {
            text-align: left;
            padding: 14px;
            font-size: 13px;
            color: #94a3b8;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        td {
            padding: 14px;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            font-size: 14px;
        }

        tr:hover {
            background: rgba(255,255,255,0.05);
        }

        .status {
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: bold;
            text-transform: capitalize;
            display: inline-block;
        }

        .pending {
            background: rgba(245, 158, 11, 0.2);
            color: #fbbf24;
        }

        .in_progress {
            background: rgba(59, 130, 246, 0.2);
            color: #60a5fa;
        }

        .completed {
            background: rgba(34, 197, 94, 0.2);
            color: #4ade80;
        }

        .cancelled {
            background: rgba(239, 68, 68, 0.2);
            color: #f87171;
        }

        .btn {
            padding: 7px 12px;
            border: none;
            border-radius: 8px;
            background: #ef4444;
            color: white;
            font-size: 13px;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn:hover {
            background: #dc2626;
            transform: scale(1.05);
        }

        .empty {
            text-align: center;
            padding: 30px;
            color: #94a3b8;
        }

    </style>
</head>

<body>

<div class="header">
    <h2>User Dashboard</h2>
</div>

<div class="container">

    <div class="welcome">
        Welcome, <b><?php echo htmlspecialchars($_SESSION['user']['username']); ?></b>
    </div>

    <?php if (!empty($msg)): ?>
        <div class="msg"><?php echo htmlspecialchars($msg); ?></div>
    <?php endif; ?>

    <div class="nav">
        <a href="request_book.php">+ Request Book</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="card">

        <h3>Your Book Requests</h3>

        <?php if (count($rows) == 0): ?>
            <div class="empty">No book requests found.</div>
        <?php else: ?>

        <table>
            <tr>
                <th>Book</th>
                <th>Category</th>
                <th>Status</th>
                <th>Action</th>
            </tr>

            <?php foreach ($rows as $row): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['book_title']); ?></td>
                <td><?php echo htmlspecialchars($row['category']); ?></td>

                <td>
                    <span class="status <?php echo $row['status']; ?>">
                        <?php echo $row['status']; ?>
                    </span>
                </td>

                <td>
                    <?php if ($row['status'] == 'pending'): ?>
                        <form method="POST" action="cancel_request.php">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <button class="btn" type="submit">Cancel</button>
                        </form>
                    <?php else: ?>
                        —
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>

        </table>

        <?php endif; ?>

    </div>

</div>

</body>
</html>