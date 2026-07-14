<?php
session_start();

if (isset($_SESSION['user'])) {
    header("Location: user/dashboard.php");
    exit();
}

header("Location: user/login.php");
exit();
?>
