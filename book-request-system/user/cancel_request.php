<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id = $_POST['id'];

    try {
        $stmt = $pdo->prepare("
            DELETE FROM book_requests 
            WHERE id=? AND user_id=? AND status='pending'
        ");

        $stmt->execute([$id, $_SESSION['user']['id']]);

    } catch (Exception $e) {
        error_log($e->getMessage());
    }
}

header("Location: dashboard.php");
exit();