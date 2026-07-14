<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['superadmin'])) {
    header("Location: login.php");
    exit();
}

/* UPDATE STATUS */
if (isset($_POST['update'])) {

    try {
        $stmt = $pdo->prepare("UPDATE book_requests SET status=? WHERE id=?");
        $stmt->execute([$_POST['status'], $_POST['id']]);

        $_SESSION['msg'] = "Status updated successfully.";

    } catch (Exception $e) {
        error_log($e->getMessage());
        $_SESSION['msg'] = "Error updating status.";
    }
}

/* DELETE REQUEST */
if (isset($_POST['delete'])) {

    try {
        $stmt = $pdo->prepare("DELETE FROM book_requests WHERE id=?");
        $stmt->execute([$_POST['id']]);

        $_SESSION['msg'] = "Request deleted successfully.";

    } catch (Exception $e) {
        error_log($e->getMessage());
        $_SESSION['msg'] = "Error deleting request.";
    }
}

/* FETCH DATA */
$requests = $pdo->query("SELECT * FROM book_requests ORDER BY id DESC")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Requests</title>

    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #eef2f7, #dbeafe);
            color: #1f2937;
        }

        h2 {
            margin: 0;
            padding: 20px;
            background: #1e293b;
            color: white;
        }

        .container {
            width: 90%;
            margin: 30px auto;
        }

        .msg {
            background: #ecfdf5;
            color: #047857;
            padding: 10px;
            border-radius: 10px;
            margin-bottom: 15px;
        }

        /* CARD */
        .card {
            background: white;
            padding: 20px;
            border-radius: 14px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
        }

        /* TABLE */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            padding: 14px;
            background: #1e293b;
            color: white;
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

        /* STATUS BADGES */
        .status {
            padding: 5px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: bold;
            text-transform: capitalize;
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

        /* FORM */
        form {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        select {
            padding: 6px;
            border-radius: 6px;
            border: 1px solid #ddd;
        }

        button {
            padding: 6px 10px;
            border: none;
            border-radius: 6px;
            font-size: 13px;
            cursor: pointer;
            transition: 0.2s;
        }

        .update-btn {
            background: #3b82f6;
            color: white;
        }

        .update-btn:hover {
            background: #1d4ed8;
        }

        .delete-btn {
            background: #ef4444;
            color: white;
        }

        .delete-btn:hover {
            background: #dc2626;
        }
    </style>
</head>

<body>

<h2>Manage Book Requests</h2>

<div class="container">

    <?php if (isset($_SESSION['msg'])): ?>
        <div class="msg">
            <?php echo $_SESSION['msg']; unset($_SESSION['msg']); ?>
        </div>
    <?php endif; ?>

    <div class="card">

        <table>
            <tr>
                <th>ID</th>
                <th>Book</th>
                <th>Status</th>
                <th>Action</th>
            </tr>

            <?php foreach ($requests as $r): ?>
            <tr>
                <td><?php echo $r['id']; ?></td>
                <td><?php echo htmlspecialchars($r['book_title']); ?></td>

                <td>
                    <span class="status <?php echo $r['status']; ?>">
                        <?php echo $r['status']; ?>
                    </span>
                </td>

                <td>

                    <form method="POST">

                        <input type="hidden" name="id" value="<?php echo $r['id']; ?>">

                        <select name="status">
                            <option value="pending" <?php if($r['status']=='pending') echo 'selected'; ?>>Pending</option>
                            <option value="in_progress" <?php if($r['status']=='in_progress') echo 'selected'; ?>>In Progress</option>
                            <option value="completed" <?php if($r['status']=='completed') echo 'selected'; ?>>Completed</option>
                        </select>

                        <button class="update-btn" type="submit" name="update">Update</button>

                        <button class="delete-btn" type="submit" name="delete">Delete</button>

                    </form>

                </td>
            </tr>
            <?php endforeach; ?>

        </table>

    </div>

</div>

</body>
</html>