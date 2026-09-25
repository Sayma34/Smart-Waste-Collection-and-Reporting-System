<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit();
}

$role = $_SESSION['role'];

if ($role === 'user') {
    // Redirect regular users to user.php
    header("Location: user.php");
    exit();
} elseif ($role === 'collector') {
    header("Location: collector.php");
    exit();
} elseif ($role === 'admin') {
    header("Location: admin_dashboard.php");
    exit();
} else {
    // Unknown role fallback
    echo "Access Denied: Unknown Role";
    exit();
}
?>
