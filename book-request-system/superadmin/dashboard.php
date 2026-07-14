<?php

$totalUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE role='user'")->fetchColumn();
$totalAdmins = $pdo->query("SELECT COUNT(*) FROM users WHERE role='admin'")->fetchColumn();
$totalRequests = $pdo->query("SELECT COUNT(*) FROM book_requests")->fetchColumn();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Super Admin Dashboard</title>

    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #eef2f7, #dbeafe);
            color: #1f2937;
        }

        /* HEADER */
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
            text-decoration: none;
            background: #ef4444;
            color: white;
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 13px;
            transition: 0.3s;
        }

        .logout:hover {
            background: #dc2626;
        }

        /* CONTAINER */
        .container {
            width: 90%;
            margin: 30px auto;
        }

        /* WELCOME */
        .welcome {
            font-size: 16px;
            margin-bottom: 20px;
        }

        /* STATS CARDS */
        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
        }

        .card {
            background: white;
            padding: 20px;
            border-radius: 14px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            text-align: center;
            transition: 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card h3 {
            margin: 0;
            font-size: 16px;
            color: #6b7280;
        }

        .card p {
            font-size: 28px;
            margin: 10px 0 0;
            font-weight: bold;
            color: #1e293b;
        }

        /* MANAGEMENT LINKS */
        .section {
            margin-top: 30px;
        }

        .links {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .links a {
            text-decoration: none;
            background: #3b82f6;
            color: white;
            padding: 12px 18px;
            border-radius: 10px;
            font-size: 14px;
            transition: 0.3s;
        }

        .links a:hover {
            background: #1d4ed8;
            transform: translateY(-2px);
        }

    </style>
</head>

<body>

<div class="header">
    <h2>Super Admin Dashboard</h2>
    <a class="logout" href="logout.php">Logout</a>
</div>

<div class="container">

    <div class="welcome">
        Welcome, <b><?php echo htmlspecialchars($_SESSION['superadmin']['username']); ?></b>
    </div>

    <div class="cards">

        <div class="card">
            <h3>Total Users</h3>
            <p><?php echo $totalUsers; ?></p>
        </div>

        <div class="card">
            <h3>Total Admins</h3>
            <p><?php echo $totalAdmins; ?></p>
        </div>

        <div class="card">
            <h3>Total Requests</h3>
            <p><?php echo $totalRequests; ?></p>
        </div>

    </div>

    <div class="section">
        <h3>Management Panel</h3>

        <div class="links">
            <a href="manage_requests.php">Manage Requests</a>
            <a href="manage_users.php">Manage Users</a>
            <a href="manage_admins.php">Manage Admins</a>
        </div>
    </div>

</div>

</body>
</html>