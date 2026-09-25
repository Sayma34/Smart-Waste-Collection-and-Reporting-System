<?php
session_start();
include 'db_connect.php';

// Only allow admins
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reportID = intval($_POST['reportID']);
    $status = $_POST['status'];
    $stmt = $conn->prepare("UPDATE reports SET status=? WHERE reportID=?");
    $stmt->bind_param("si", $status, $reportID);
    $stmt->execute();
    $stmt->close();
}
$conn->close();
header("Location: admin_dashboard.php");
exit();
?>
