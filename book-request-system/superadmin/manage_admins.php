<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['superadmin'])) {
    header("Location: login.php");
    exit();
}

$message = "";

/* ADD ADMIN */
if (isset($_POST['add'])) {

    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("
            INSERT INTO users (username, email, password, role)
            VALUES (?, ?, ?, 'admin')
        ");

        $stmt->execute([$username, $email, $password]);

        $message = "Admin created successfully.";

    } catch (Exception $e) {
        error_log($e->getMessage());
        $message = "Error creating admin.";
    }
}

/* DELETE ADMIN */
if (isset($_POST['delete'])) {

    $id = intval($_POST['id']);

    try {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id=? AND role='admin'");
        $stmt->execute([$id]);

        $message = "Admin deleted successfully.";

    } catch (Exception $e) {
        error_log($e->getMessage());
        $message = "Error deleting admin.";
    }
}

/* FETCH ADMINS */
$admins = $pdo->query("SELECT * FROM users WHERE role='admin' ORDER BY id DESC")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Admins</title>

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
            font-size: 22px;
        }

        h3 {
            margin: 20px;
        }

        .msg {
            margin: 15px 20px;
            padding: 10px;
            background: #ecfdf5;
            color: #047857;
            border-radius: 10px;
        }

        form {
            background: white;
            padding: 15px;
            margin: 20px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
        }

        input {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            outline: none;
            flex: 1;
            min-width: 200px;
        }

        input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 6px rgba(59,130,246,0.2);
        }

        button {
            padding: 10px 16px;
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background: #1d4ed8;
        }

        table {
            width: 95%;
            margin: 20px auto;
            border-collapse: collapse;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
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

        td form {
            margin: 0;
            padding: 0;
            background: none;
            box-shadow: none;
        }

        td button {
            background: #ef4444;
            padding: 6px 10px;
            font-size: 13px;
        }

        td button:hover {
            background: #dc2626;
        }

        hr {
            border: none;
            height: 1px;
            background: #e5e7eb;
            margin: 20px;
        }
    </style>
</head>

<body>

<h2>Manage Admins</h2>

<?php if (!empty($message)): ?>
    <div class="msg"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>

<h3>Add New Admin</h3>

<form method="POST">

    <input type="text" name="username" placeholder="Username" required>

    <input type="email" name="email" placeholder="Email" required>

    <input type="password" name="password" placeholder="Password" required>

    <button type="submit" name="add">Create Admin</button>

</form>

<hr>

<h3>All Admins</h3>

<table>
    <tr>
        <th>ID</th>
        <th>Username</th>
        <th>Email</th>
        <th>Action</th>
    </tr>

    <?php if (count($admins) == 0): ?>
        <tr>
            <td colspan="4" style="text-align:center;">No admins found</td>
        </tr>
    <?php endif; ?>

    <?php foreach ($admins as $a): ?>
    <tr>
        <td><?php echo $a['id']; ?></td>
        <td><?php echo htmlspecialchars($a['username']); ?></td>
        <td><?php echo htmlspecialchars($a['email']); ?></td>
        <td>

            <form method="POST">
                <input type="hidden" name="id" value="<?php echo $a['id']; ?>">
                <button type="submit" name="delete">Delete</button>
            </form>

        </td>
    </tr>
    <?php endforeach; ?>

</table>

</body>
</html>