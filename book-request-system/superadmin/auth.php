<?php
session_start();

if (!isset($_SESSION['superadmin'])) {
    header("Location: login.php");
    exit();
}
?>