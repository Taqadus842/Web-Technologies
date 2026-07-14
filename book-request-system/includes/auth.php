<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: /book-request-system/user/login.php");
    exit();
}
?>