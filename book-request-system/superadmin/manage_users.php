<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['superadmin'])) {
    header("Location: login.php");
    exit();
}

$message = "";

/* DELETE USER */
if (isset($_POST['delete'])) {

    try {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id=? AND role='user'");
        $stmt->execute([$_POST['id']]);

        $message = "User deleted successfully.";

    } catch (Exception $e) {
        error_log($e->getMessage());
        $message = "Error deleting user.";
    }
}

/* RESET PASSWORD */
if (isset($_POST['reset'])) {

    try {
        $newPass = password_hash("123456", PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("UPDATE users SET password=? WHERE id=?");
        $stmt->execute([$newPass, $_POST['id']]);

        $message = "Password reset to 123456.";

    } catch (Exception $e) {
        error_log($e->getMessage());
        $message = "Error resetting password.";
    }
}

/* FETCH USERS */
$users = $pdo->query("SELECT * FROM users WHERE role='user' ORDER BY id DESC")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Users</title>

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
            background: #1e293b;
            color: white;
            text-align: left;
            padding: 14px;
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

        /* FORM */
        form {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        button {
            padding: 6px 10px;
            border: none;
            border-radius: 6px;
            font-size: 13px;
            cursor: pointer;
            transition: 0.2s;
        }

        .reset-btn {
            background: #f59e0b;
            color: white;
        }

        .reset-btn:hover {
            background: #d97706;
        }

        .delete-btn {
            background: #ef4444;
            color: white;
        }

        .delete-btn:hover {
            background: #dc2626;
        }

        .empty {
            text-align: center;
            padding: 20px;
            color: #6b7280;
        }
    </style>
</head>

<body>

<h2>Manage Users</h2>

<div class="container">

    <?php if (!empty($message)): ?>
        <div class="msg"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <div class="card">

        <table>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Action</th>
            </tr>

            <?php if (count($users) == 0): ?>
                <tr>
                    <td colspan="4" class="empty">No users found</td>
                </tr>
            <?php endif; ?>

            <?php foreach ($users as $u): ?>
            <tr>
                <td><?php echo $u['id']; ?></td>
                <td><?php echo htmlspecialchars($u['username']); ?></td>
                <td><?php echo htmlspecialchars($u['email']); ?></td>
                <td>

                    <form method="POST">

                        <input type="hidden" name="id" value="<?php echo $u['id']; ?>">

                        <button class="reset-btn" type="submit" name="reset">
                            Reset Password
                        </button>

                        <button class="delete-btn" type="submit" name="delete">
                            Delete
                        </button>

                    </form>

                </td>
            </tr>
            <?php endforeach; ?>

        </table>

    </div>

</div>

</body>
</html>