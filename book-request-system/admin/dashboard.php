<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

try {
    $stmt = $pdo->query("SELECT * FROM book_requests ORDER BY id DESC");
    $requests = $stmt->fetchAll();
} catch (Exception $e) {
    error_log($e->getMessage());
    $requests = [];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>

    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #eef2f7, #dbeafe);
            color: #1f2937;
        }

        .header {
            background: #1e293b;
            color: white;
            padding: 18px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h2 {
            margin: 0;
            font-size: 20px;
        }

        .logout {
            background: #ef4444;
            color: white;
            padding: 8px 14px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 13px;
            transition: 0.3s;
        }

        .logout:hover {
            background: #dc2626;
        }

        .container {
            width: 90%;
            margin: 30px auto;
        }

        .welcome {
            margin-bottom: 20px;
            font-size: 16px;
        }

        .card {
            background: white;
            padding: 20px;
            border-radius: 14px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            overflow: hidden;
            border-radius: 12px;
        }

        th {
            background: #1e293b;
            color: white;
            text-align: left;
            padding: 12px;
            font-size: 14px;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 14px;
        }

        tr:hover {
            background: #f8fafc;
        }

        .status {
            padding: 5px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: bold;
            text-transform: capitalize;
            display: inline-block;
        }

        .pending {
            background: #fff7ed;
            color: #c2410c;
        }

        .in_progress {
            background: #eff6ff;
            color: #1d4ed8;
        }

        .completed {
            background: #ecfdf5;
            color: #047857;
        }

        .empty {
            text-align: center;
            padding: 20px;
            color: #6b7280;
        }
    </style>
</head>

<body>

<div class="header">
    <h2>Admin Dashboard</h2>
    <a class="logout" href="logout.php">Logout</a>
</div>

<div class="container">

    <div class="welcome">
        Welcome, <b><?php echo htmlspecialchars($_SESSION['admin']['username']); ?></b>
    </div>

    <div class="card">

        <h3>All Book Requests</h3>

        <table>
            <tr>
                <th>ID</th>
                <th>User ID</th>
                <th>Book Title</th>
                <th>Category</th>
                <th>Status</th>
            </tr>

            <?php if (count($requests) == 0): ?>
                <tr>
                    <td colspan="5" class="empty">No requests found</td>
                </tr>
            <?php endif; ?>

            <?php foreach ($requests as $r): ?>
            <tr>
                <td><?php echo $r['id']; ?></td>
                <td><?php echo $r['user_id']; ?></td>
                <td><?php echo htmlspecialchars($r['book_title']); ?></td>
                <td><?php echo htmlspecialchars($r['category']); ?></td>

                <td>
                    <span class="status <?php echo $r['status']; ?>">
                        <?php echo $r['status']; ?>
                    </span>
                </td>
            </tr>
            <?php endforeach; ?>

        </table>

    </div>

</div>

</body>
</html>