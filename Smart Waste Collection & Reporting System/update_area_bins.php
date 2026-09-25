<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $total_bins = intval($_POST['total_bins']);
    $stmt = $conn->prepare("UPDATE area SET total_bins=? WHERE name=?");
    $stmt->bind_param("is", $total_bins, $name);
    $stmt->execute();
    $stmt->close();
}

$conn->close();
header("Location: admin_dashboard.php");
exit();
?>
